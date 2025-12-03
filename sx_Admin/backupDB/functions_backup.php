<?php
function sx_getMySQLTables($cn)
{
    $LoopTable = array();
    $stmt = $cn->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
    $rec = $stmt->fetchAll(PDO::FETCH_NUM);
    $stmt = null;
    foreach ($rec as $table) {
        $LoopTable[] = $table[0];
    }
    $rec = null;
    return $LoopTable;
}

// Open the file in 'w' mode to clear the content
function clear_file_content($path)
{
    $handle = fopen($path, 'w');

    if ($handle !== false) {
        fclose($handle);
    }
}

function append_to_file($path, $str)
{
    $fp = fopen($path, 'a');
    fwrite($fp, $str);
    fclose($fp);
}


/**
 * @param $file_path
 * @param int $level
 * @return string
 */
function compress_gzip_file($file_path, $level = 9)
{
    // Set the gzip compression method and define the new gz file path
    $method = "wb{$level}";
    $giz_file = "{$file_path}.gz";

    // Attempt to open the gz file for writing
    if ($giz_file_opened = gzopen($giz_file, $method)) {

        // Attempt to open the original file for reading
        if ($original_file = fopen($file_path, 'rb')) {
            // Write data from the original file to the gz file in chunks
            while (!feof($original_file)) {
                gzwrite($giz_file_opened, fread($original_file, 1024 * 256));
            }
            fclose($original_file); // Close the original file
        } else {
            gzclose($giz_file_opened); // Close gz file before returning
            return 'Error: Could not open the original file for reading.';
        }

        gzclose($giz_file_opened); // Close the gz file
    } else {
        return 'Error: Could not open the gz file for writing.';
    }

    // Try to delete the original file, return error if unable to do so
    if (!unlink($file_path)) {
        return 'Error: Could not delete the original file.';
    }

    return 'Success';
}


function sx_backup_database_in_file($str_BackupFilePath, $arr_tables, $str_BackupMode, $str_TableSchema, $radio_GZIP, $int_batch = 1000)
{
    $conn = dbconn();
    $output = "";

    /**
     * If a backup file name already exists (when its name is set manually)
     *  - Open the backup file in 'w' mode, if exists, to clear its content
     *    before appending new one
     */
    clear_file_content($str_BackupFilePath);


    // Backup header
    if ($str_BackupMode === 'Both' || $str_BackupMode === 'Structures') {
        $output .= "CREATE DATABASE IF NOT EXISTS `{$str_TableSchema}`;\n\n";
    }
    $output .= "USE `{$str_TableSchema}`;\n\n";
    $output .= "SET foreign_key_checks = 0;\n\n";
    append_to_file($str_BackupFilePath, $output);
    $output = "";

    foreach ($arr_tables as $table) {
        // Validate table name (prevent SQL injection)
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            continue;
        }

        // Backup structure
        if ($str_BackupMode === 'Both' || $str_BackupMode === 'Structures') {
            $output .= "DROP TABLE IF EXISTS `{$table}`;\n\n";
            $create_sql = $conn->query("SHOW CREATE TABLE `{$table}`;")->fetch(PDO::FETCH_NUM)[1];

            // Remove AUTO_INCREMENT for structure-only backups
            if ($str_BackupMode === 'Structures' && strpos($create_sql, "AUTO_INCREMENT=") !== false) {
                $create_sql = preg_replace('/AUTO_INCREMENT=\d+\s*/', '', $create_sql);
            }

            $output .= "{$create_sql};\n\n";
            append_to_file($str_BackupFilePath, $output);
            $output = "";
        } else {
            // For content-only backups, clear existing data
            $output .= "TRUNCATE TABLE `{$table}`;\n\n";
            append_to_file($str_BackupFilePath, $output);
            $output = "";
        }

        // Backup data
        if ($str_BackupMode !== 'Structures') {
            $query = $conn->query("SELECT * FROM `{$table}`");

            $insertHeader = "INSERT INTO `{$table}` VALUES\n";
            $row_count = 0;
            $batch = ""; // For batching multiple rows in a single INSERT

            while ($row = $query->fetch(PDO::FETCH_NUM)) {
                $values = array_map(function ($value) {
                    if (is_bool($value)) {
                        return $value ? 1 : 0;
                    } elseif (is_null($value)) {
                        return 'NULL';
                    } elseif (is_numeric($value)) {
                        return $value;
                    } else {
                        $value = addslashes($value);
                        $value = str_replace(
                            ["\n", "\r", "\f", "\t", "\v", "\a", "\b"],
                            ["\\n", "\\r", "\\f", "\\t", "\\v", "\\a", "\\b"],
                            $value
                        );
                        return '"' . $value . '"';
                    }
                }, $row);

                $batch .= "(" . implode(",", $values) . "),\n";
                $row_count++;

                // Write to file every 1000 rows to avoid memory overload
                if ($row_count % $int_batch === 0) {
                    $batch = rtrim($batch, ",\n") . ";\n";
                    append_to_file($str_BackupFilePath, $insertHeader . $batch);
                    $batch = "";
                }
            }

            // Write remaining rows (if any)
            if (!empty($batch)) {
                $batch = rtrim($batch, ",\n") . ";\n";
                append_to_file($str_BackupFilePath, $insertHeader . $batch);
            }
            append_to_file($str_BackupFilePath, "\n");
        }
    }

    // Re-enable foreign key checks
    append_to_file($str_BackupFilePath, "SET foreign_key_checks = 1;\n");
    if ($radio_GZIP) {
        $return_gzip = compress_gzip_file($str_BackupFilePath);
        if ($return_gzip !== 'Success') {
            return false;
        }
    }
    return true;
}


function sx_backup_database_in_folder($sql_BackupFolderPath, $arr_tables, $str_BackupMode, $str_TableSchema, $radio_GZIP, $int_batch = 1000)
{
    $conn = dbconn();
    $output = "";


    foreach ($arr_tables as $table) {

        // Validate table name (prevent SQL injection)
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            continue;
        }

        $str_BackupFilePath = "{$sql_BackupFolderPath}\\{$table}.sql";

        /**
         * If a backup folder name already exists (when its name is set manually)
         *  - Delete the content of existing backup files before appending new content 
         *  - Open the backup file in 'w' mode, if exists, to clear its content
         */
        clear_file_content($str_BackupFilePath);

        // Backup header
        if ($str_BackupMode === 'Both' || $str_BackupMode === 'Structures') {
            $output .= "CREATE DATABASE IF NOT EXISTS `{$str_TableSchema}`;\n\n";
        }
        $output .= "USE `{$str_TableSchema}`;\n\n";
        $output .= "SET foreign_key_checks = 0;\n\n";
        append_to_file($str_BackupFilePath, $output);
        $output = "";

        // Backup structure
        if ($str_BackupMode === 'Both' || $str_BackupMode === 'Structures') {
            $output .= "DROP TABLE IF EXISTS `{$table}`;\n\n";
            $create_sql = $conn->query("SHOW CREATE TABLE `{$table}`;")->fetch(PDO::FETCH_NUM)[1];

            // Remove AUTO_INCREMENT for structure-only backups
            if ($str_BackupMode === 'Structures' && strpos($create_sql, "AUTO_INCREMENT=") !== false) {
                $create_sql = preg_replace('/AUTO_INCREMENT=\d+\s*/', '', $create_sql);
            }

            $output .= "{$create_sql};\n\n";
            append_to_file($str_BackupFilePath, $output);
            $output = "";
        } else {
            // For content-only backups, clear existing data
            $output .= "TRUNCATE TABLE `{$table}`;\n\n";
            append_to_file($str_BackupFilePath, $output);
            $output = "";
        }

        // Backup data
        if ($str_BackupMode !== 'Structures') {
            $query = $conn->query("SELECT * FROM `{$table}`");

            $insertHeader = "INSERT INTO `{$table}` VALUES\n";
            $row_count = 0;
            $batch = ""; // For batching multiple rows in a single INSERT

            while ($row = $query->fetch(PDO::FETCH_NUM)) {
                $values = array_map(function ($value) {
                    if (is_bool($value)) {
                        return $value ? 1 : 0;
                    } elseif (is_null($value)) {
                        return 'NULL';
                    } elseif (is_numeric($value)) {
                        return $value;
                    } else {
                        $value = addslashes($value);
                        $value = str_replace(
                            ["\n", "\r", "\f", "\t", "\v", "\a", "\b"],
                            ["\\n", "\\r", "\\f", "\\t", "\\v", "\\a", "\\b"],
                            $value
                        );
                        return '"' . $value . '"';
                    }
                }, $row);

                $batch .= "(" . implode(",", $values) . "),\n";
                $row_count++;

                // Write to file every 1000 rows to avoid memory overload
                if ($row_count % $int_batch === 0) {
                    $batch = rtrim($batch, ",\n") . ";\n";
                    append_to_file($str_BackupFilePath, $insertHeader . $batch);
                    $batch = "";
                }
            }

            // Write remaining rows (if any)
            if (!empty($batch)) {
                $batch = rtrim($batch, ",\n") . ";\n";
                append_to_file($str_BackupFilePath, $insertHeader . $batch);
            }
            
            append_to_file($str_BackupFilePath, "\n");

            // Re-enable foreign key checks
            append_to_file($str_BackupFilePath, "SET foreign_key_checks = 1;\n");

            if ($radio_GZIP) {
                $return_gzip = compress_gzip_file($str_BackupFilePath);
                if ($return_gzip !== 'Success') {
                    return false;
                }
            }
        }
    }

    return true;
}


function zip_single_file($file_path)
{
    // Check if ZipArchive is installed
    if (!class_exists('ZipArchive')) {
        return 'Error: ZipArchive extension is not installed.';
    }

    $zipFilePath = "{$file_path}.zip";

    // Initialize the ZipArchive class
    $zip = new ZipArchive;

    // Try to create and open the zip file
    if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        return 'Error: Could not create or open zip file.';
    }

    // Try to add the file to the zip archive
    if (!$zip->addFile($file_path, basename($file_path))) {
        $zip->close();
        unlink($zipFilePath); // Clean up the zip file
        return 'Error: Could not add file to zip.';
    }

    // Close the zip archive
    if (!$zip->close()) {
        unlink($zipFilePath); // Clean up the zip file
        return 'Error: Could not close the zip file properly.';
    }

    // Check if the zip file exists and is readable before proceeding with the download
    if (!file_exists($zipFilePath) || !is_readable($zipFilePath)) {
        return 'Error: Zip file not found or unreadable.';
    }
    return 'Success';
}

function sx_zip_folder_files($folder_path, $zip_file)
{
    // Check if ZipArchive is installed
    if (!class_exists('ZipArchive')) {
        return 'Error: ZipArchive extension is not installed.';
    }

    $zip_file = STR_RemoteBackupDirectory . "\\{$zip_file}.zip";

    // Create new ZIP file
    $zip = new ZipArchive();
    if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        return 'Error: Could not create or open zip file.';
    }
    // Add files to ZIP
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder_path));
    foreach ($files as $file) {
        if (!$file->isDir()) {
            $file_path = $file->getRealPath();
            $relative_path = substr($file_path, strlen($folder_path) + 1);
            if (!$zip->addFile($file_path, $relative_path)) {
                return "Error: Could not add file $relative_path";;
            }
        }
    }
    $zip->close();

    //return ' First: ' . $zip_file .' Second: '. $file_path . ' Last: ' . $relative_path;
    return 'Success';
}
