<?php
$str_RemoteBackupDirectory = realpath(PROJECT_PATH. '/private/dbBackup/');

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

function sx_downloadBackupFolder($dir)
{
    $real_dir = realpath($dir);
    $strLastFolder = substr($real_dir, strrpos($real_dir, "\\") + 1);
    $download = "{$strLastFolder}.zip";

    $zip = new ZipArchive;
    $zip->open($download, ZipArchive::CREATE | ZipArchive::OVERWRITE);

    $files = scandir($real_dir);
    foreach ($files as $file) {
        if (substr($file, 0, 1) != ".") {
            $zip->addFile($real_dir . '\\' . $file, $file);
        }
    }
    $zip->close();

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($download));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($download));
    readfile($download);
    @unlink($download);
}

function sx_importToMySQL_SQL($file_path)
{
    $strSQL = "";
    $query = 1;
    $msg = "";
    if (!empty($file_path)) {
        $read_file = fopen($file_path, 'r');
        if ($read_file) {
            $conn = dbconn();
            while ($line = fgets($read_file)) {
                $start_character = substr(trim($line), 0, 2);
                if ($start_character != '--' || $start_character != '/*' || $start_character != '//' || !empty($line)) {
                    $strSQL .= $line;
                    $end_character = substr(trim($line), -1, 1);
                    if ($end_character == ';') {
                        try {
                            $conn->query($strSQL);
                        } catch (PDOException $e) {
                            $msg .= '<br>Error in Query ' . $query . ' : ' . $e->getMessage();
                        }
                        $strSQL = "";
                        $query++;
                    }
                }
            }
        }
        fclose($read_file);
    }
    return  $msg;
}

function sx_importToMySQL_GZIP($file_path)
{
    $strSQL = "";
    $query = 1;
    $msg = "";
    if (!empty($file_path)) {
        // Open file in binary mode
        $file = gzopen($file_path, 'rb');
        $conn = dbconn();
        while ($line = gzgets($file)) {
            $start_character = substr(trim($line), 0, 2);
            if ($start_character != '--' || $start_character != '/*' || $start_character != '//' || !empty($line)) {
                $strSQL .= $line;
                $end_character = substr(trim($line), -1, 1);
                if ($end_character == ';') {
                    try {
                        $conn->query($strSQL);
                    } catch (PDOException $e) {
                        $msg .= '<br>Error in Query ' . $query . ' : ' . $e->getMessage();
                    }
                    $strSQL = "";
                    $query++;
                }
            }
        }
        gzclose($file);
    }
    return $msg;
}


function sx_unzip_GZIP_files($dir)
{
    $real_dir = realpath($dir);
    $files = scandir($real_dir);

    foreach ($files as $file_name) {
        if (!is_dir($file_name)) {
            $buffer_size = 4096;
            $out_file_name = str_replace('.gz', '', $file_name);
            $file = gzopen($real_dir . '\\' . $file_name, 'rb', 1);
            $out_file = fopen($real_dir . '\\' . $out_file_name, 'wb', 1);

            while (!gzeof($file)) {
                fwrite($out_file, gzread($file, $buffer_size));
            }
            fclose($out_file);
            gzclose($file);
        }
    }
}
