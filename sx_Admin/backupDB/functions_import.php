<?php
function sx_importToMySQL_GZ($file_path)
{
    // Open the gzipped file
    $gzHandle = gzopen($file_path, 'r');
    if (!$gzHandle) {
        return 'Could not open the gzipped file.';
    }
    $conn = dbconn();
    $count = 0;

    try {
        // Disable foreign key checks for smooth restoration
        $conn->exec('SET foreign_key_checks = 0;');

        $sql = '';
        while (!gzeof($gzHandle)) {
            $line = gzgets($gzHandle);

            // Skip comments and empty lines
            if (trim($line) === '' || strpos(trim($line), '--') === 0 || strpos(trim($line), '/*') === 0) {
                continue;
            }

            $sql .= $line;

            // Execute SQL when a complete statement is found
            if (substr(trim($line), -1) === ';') {
                $conn->exec($sql); // Use exec() for non-select queries
                $sql = ''; // Reset for the next statement
                $count++;
            }
        }

        return $count;
    } catch (PDOException $e) {
        return "Database error at Query $count with message: " . $e->getMessage();
    } finally {
        // Ensure foreign key checks are always re-enabled and file is closed
        $conn->exec('SET foreign_key_checks = 1;');
        gzclose($gzHandle);
    }
}

function sx_importToMySQL_SQL($file_path)
{
    if (empty($file_path) || !file_exists($file_path)) {
        return 'Invalid file path.';
    }

    $conn = dbconn();

    $strSQL = "";
    $count = 0;
    $handle = fopen($file_path, 'r');

    if (!$handle) {
        return 'Could not open the SQL file.';
    }

    try {
        $conn->exec('SET foreign_key_checks = 0;'); // Disable FK checks for smooth import

        while (($row = fgets($handle)) !== false) {
            $trimmedRow = trim($row);

            // Skip comments and empty lines
            if (
                $trimmedRow === '' ||
                str_starts_with($trimmedRow, '--') ||
                str_starts_with($trimmedRow, '/*') ||
                str_starts_with($trimmedRow, '//')
            ) {
                continue;
            }

            $strSQL .= $row;

            // Execute when a complete SQL statement is formed
            if (substr($trimmedRow, -1) === ';') {
                $conn->exec($strSQL);
                $count++;
                $strSQL = "";
            }
        }

        return $count;
    } catch (PDOException $e) {
        return "Database error at Query $count with message: " . $e->getMessage();
    } finally {
        fclose($handle);
        $conn->exec('SET foreign_key_checks = 1;');
    }
}
