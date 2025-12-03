<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include __DIR__ . '/config_upload.php';
include __DIR__ . '/config_variables.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chunking Upload Demo</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
    <title>Multiple File Upload</title>
    <style>
        #fileTable th:nth-child(2),
        #fileTable td:nth-child(2) {
            text-align: right;
        }

        button:disabled,
        button[disabled],
        button:disabled:hover,
        button[disabled]:hover {
            background-image: none;
        }
    </style>
    <script>
        const max_FileSizeInBytes = '<?php echo ($maxFileSizeInBytes) ?>';
    </script>
</head>

<body id="bodyUpload" class="body">
    <?php include __DIR__ . "/nav_top.php"; ?>
    <section class="maxWidthWide">
        <h2>Upload Multiple Files</h2>
        <fieldset class="container">
            <label>
                <b>Selec Destination Folder:</b><br>
                <select name="DestinationFolder" id="DestinationFolder">
                    <?php
                    $iCount = count(ARR_UploadableFolders);
                    $strLast = "";
                    for ($sx = 0; $sx < $iCount; $sx++) {
                        $strLoop = ARR_UploadableFolders[$sx];
                        $strSelected = "";
                        if ($strLoop == $str_DestinationFolder) {
                            $strSelected = " selected";
                        }
                        $strCurr = explode("/", $strLoop)[0];
                        if ($strCurr != $strLast) {
                            if ($sx > 0) {
                                echo "</optgroup>";
                            }
                            echo '<optgroup label="' . $strCurr . '">';
                        } ?>
                        <option value="<?= $strLoop ?>" <?= $strSelected ?>><?= $strLoop ?></option>
                    <?php
                        $strLast = $strCurr;
                    } ?>
                </select>
            </label>
            <div class="container flex_gap">
                <label class="alignRight">
                    <b>Add Prefix to File Names:</b><br>
                    <input style="width: 2rem; height: 2rem; vertical-align:middle" type="checkbox" id="AddPrefix" name="AddPrefix" value="Yes" />
                </label>
                <label>
                    <b>Prefix:</b><br>
                    <input type="text" id="Prefix" name="Prefix" value="<?= date('Y-m-d') ?>" size="8" maxlength="10" />
                </label>
            </div>
        </fieldset>

        <fieldset class="container">
            <input class="button" type="file" id="fileInput" multiple onchange="displayFiles()" />
            <button class="button" id="uploadButton" onclick="uploadFiles()" disabled><?= lngUploadFiles ?></button>
        </fieldset>

        <table id="fileTable" style="display:none;">
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>File Size</th>
                    <th>Upload Progress</th>
                </tr>
            </thead>
            <tbody id="fileTableBody"></tbody>
        </table>
        <h3>Max File Size and Allowed File Types</h3>
        <?php
        /*
        echo '$intMaxPostSize: ' . $intMaxPostSize . '<br>';
        echo '$intMaxFileSize: ' . $intMaxFileSize . '<br>';
        echo '$maxFileSize: ' . $maxFileSize . '<br>';
        echo '$maxFileSizeInBytes: ' . $maxFileSizeInBytes . '<br>';
        echo '<pre>';
        print_r(ARR_UploadableFolders);
        echo '</pre>';
        */
        ?>
        <ul>
            <li><?= lngFileMultiple ?>,
                with <b>Max Size per File</b>: <?= number_format($maxFileSizeInBytes / 1024, 0, ' ', ' ') ?> KB (<?= $maxFileSize ?> MB).
            </li>
            <li>Files that exceed the max allowed size will be marked in red color and the Upload Button
                will be disabled. In that case, use the <b>Upload Large Files</b>.
            </li>
            <li><b><?= lngAllowedFileTypes ?>:</b> <?= implode(", ", $arr_allowedFileTypes) ?>.</li>
        </ul>
        <h3>File Names</h3>
        <ul>
            <li><b class="markText">Obs!</b>
                File names should contain <b>Latin</b> characters and <b>numbers</b> without <b>spaces</b>.
            </li>
            <li>Separate words by <b>one</b> underscore (_) and, optionally, numbers (dates) by a dash (-).</li>
            <li>Use meaningful names as they can be transformed to titles, by replacing underscores with spaces.</li>
            <li><b>Check</b> the <b>Add Prefix to File Names</b> if you want to use prefixes to file names.
                <ul>
                    <li>You can use current date as <b>prefix</b> to file names or write your own prefix,
                        using up to 8 Latin characters, numbers or dashes (-).</li>
                    <li><b>Two underscores</b> (__) are automatically added to prefixes. Thereby, prefixes
                        are <b>removed</b> when file names are transformed to titles.</li>
                </ul>
            </li>
        </ul>

    </section>

    <script>
        function displayFiles() {
            const fileInput = document.getElementById('fileInput');
            const files = fileInput.files;
            const fileTableBody = document.getElementById('fileTableBody');
            const uploadButton = document.getElementById('uploadButton');

            // Clear existing rows
            fileTableBody.innerHTML = '';
            var radioAllowedFileSizes = true;
            for (let i = 0; i < files.length; i++) {
                const fileRow = document.createElement('tr');
                const fileNameCell = document.createElement('td');
                const fileSizeCell = document.createElement('td');
                const progressCell = document.createElement('td');

                fileNameCell.textContent = files[i].name;
                fileSizeCell.textContent = formatFileSize(files[i].size);

                // Check if file size exceeds a predefined limit (in bytes)

                if (files[i].size > max_FileSizeInBytes) {
                    fileSizeCell.classList.add('markText');
                    fileSizeCell.title = 'File size exceeds the allowed limit';
                    radioAllowedFileSizes = false;
                    uploadButton.disabled = true; // Disable upload button if any file is too large
                }

                progressCell.innerHTML = `<div class="file-${i}">0%</div>`;

                fileRow.appendChild(fileNameCell);
                fileRow.appendChild(fileSizeCell);
                fileRow.appendChild(progressCell);
                fileTableBody.appendChild(fileRow);
            }

            // Show the table
            const fileTable = document.getElementById('fileTable');
            fileTable.style.display = 'table';

            // Enable the upload button
            if (radioAllowedFileSizes) {
                uploadButton.disabled = false;
            }
        }

        function formatFileSize(size) {
            const units = ['B', 'KB', 'MB', 'GB', 'TB'];
            let unitIndex = 0;
            while (size >= 1024 && unitIndex < units.length - 1) {
                size /= 1024;
                unitIndex++;
            }
            return size.toFixed(2) + ' ' + units[unitIndex];
        }

        function updateProgress(fileIndex, value) {
            const progressContainer = document.querySelector(`.file-${fileIndex}`);
            progressContainer.textContent = `${value.toFixed(2)}%`;
        }

        function uploadFiles() {
            const fileInput = document.getElementById('fileInput');
            const files = fileInput.files;
            const destination = document.getElementById("DestinationFolder").value;
            const radioAddPrefix = document.getElementById("AddPrefix").checked;
            var prefix = document.getElementById('Prefix').value;
            if (radioAddPrefix == false) {
                prefix = '';
            }

            function uploadFile(file, fileIndex) {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('filename', file.name);
                formData.append('prefix', prefix);
                formData.append('destination', destination);

                fetch('uploader.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        console.log(data);
                        updateProgress(fileIndex, 100);
                        console.log(`Upload complete for ${file.name}`);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }

            for (let i = 0; i < files.length; i++) {
                uploadFile(files[i], i);
                if (i === (files.length - 1)) {
                    uploadButton.disabled = true;
                }
            }
        }
    </script>
</body>

</html>