<?php
if (($handle = fopen('largefile.csv', 'r')) !== false) {
    $batchSize = 1000;
    $batchData = [];
    $lineCount = 0;

    while (($data = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
        $batchData[] = $data;
        $lineCount++;

        if ($lineCount % $batchSize == 0) {
            // Process batch
            processBatch($batchData);
            $batchData = []; // Reset batch
        }
    }

    // Process any remaining data
    if (!empty($batchData)) {
        processBatch($batchData);
    }

    fclose($handle);
}

function processBatch($batchData) {
    global $pdo; // Assuming $pdo is your PDO instance

    $pdo->beginTransaction();
    $stmt = $pdo->prepare('UPDATE your_table SET column = :value WHERE id = :id');

    foreach ($batchData as $data) {
        $stmt->execute([
            ':id'    => $data[0],
            ':value' => $data[1],
        ]);
    }

    $pdo->commit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['jsonFile'])) {
        $fileError = $_FILES['jsonFile']['error'];

        if ($fileError === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['jsonFile']['tmp_name'];
            $jsonData = file_get_contents($fileTmpPath);
            $decodedData = json_decode($jsonData, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                echo "JSON uploaded and parsed successfully!";
                // Process $decodedData as needed
            } else {
                echo "JSON parsing error: " . json_last_error_msg();
            }
        } else {
            echo "File upload error: " . fileUploadErrorMessage($fileError);
        }
    } else {
        echo "No file uploaded.";
    }
}

function fileUploadErrorMessage($errorCode) {
    $errors = [
        UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
        UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive specified in the HTML form.',
        UPLOAD_ERR_PARTIAL    => 'The file was only partially uploaded.',
        UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.'
    ];
    return $errors[$errorCode] ?? 'Unknown upload error.';
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['jsonFile'])) {
        $file = $_FILES['jsonFile'];

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            switch ($file['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    echo "File is too large.";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    echo "File was only partially uploaded.";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    echo "No file was uploaded.";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    echo "Missing a temporary folder.";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    echo "Failed to write file to disk.";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    echo "File upload stopped by extension.";
                    break;
                default:
                    echo "Unknown upload error.";
                    break;
            }
        } else {
            // Handle the uploaded file
            $fileType = mime_content_type($file['tmp_name']);
            if ($fileType === 'application/json') {
                $content = file_get_contents($file['tmp_name']);
                $json = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    echo "JSON file uploaded and decoded successfully.";
                } else {
                    echo "Failed to decode JSON: " . json_last_error_msg();
                }
            } else {
                echo "Uploaded file is not a JSON file.";
            }
        }
    }
}
?>
