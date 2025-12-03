<?php
include __DIR__ . "/upload_functions.php";

if ($radio_LoggedParticipant == false || (int) $int_ParticipantID == 0) {
    header('Location: index.php');
    exit();
}
/** @var $strDestinationURL
 * Include the parent folder (imgMedia) here to check if the target folder exists
 * Remove it from the destination variable (the parent folder is redefined in the ajax file)
 */
$strDestinationURL = "/imgMedia/conf_" . $int_RightsConferenceID;

if (!file_exists(realpath($_SERVER['DOCUMENT_ROOT'] . $strDestinationURL))) {
    echo "<h2>The Upload Folder for the Conference " . $int_RightsConferenceID . " Does Not Exists</h2>
	<p>Please contact the administration of the site.</p>";
} else {
    $strDestinationURL = "conf_" . $int_RightsConferenceID;
    $strFilePrefix = 'pid_' . $int_ParticipantID . "_";
?>
    <style>
        #fileTable th:nth-child(2),
        #fileTable td:nth-child(2) {
            text-align: right;
        }

        .file-progress {
            width: 100%;
        }

        button:disabled,
        button[disabled],
        button:disabled:hover,
        button[disabled]:hover {
            background-image: none;
        }
    </style>

    <section>
        <h1 class="head"><span><?php echo lngUploadConferenceMedia ?></span></h1>
        <h4>Active Conference: <i>
                <?= $str_RightsConferenceTitle ?>
            </i></h4>
        <p>Please, contact the administration if the conference is not correct as files are uploaded in a conference specific folder!</p>

        <fieldset class="flex_between">
            <input class="button button-grey" type="file" id="fileInput" onchange="displayFiles()" />
            <button class="button-border-grey button-gradient-border" id="uploadButton" onclick="uploadFiles()" disabled>Upload the File</button>
            z
        </fieldset>

        <div class="bg_success display_none" id="msg_success"></div>
        <div class="bg_error display_none" id="msg_error"></div>

        <table id="fileTable" style="display:none;">
            <caption>
                <h3>File to upload</h3>
            </caption>
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>File Size</th>
                    <th>Upload Progress</th>
                </tr>
            </thead>
            <tbody id="fileTableBody"></tbody>
        </table>

        <p>Allowed File Extentions: <b>Media:</b> mp3, mp4, ogg, webm, <b>Presentation:</b> odp, ppt, pptx, ppsx</p>
        <p>You can totally upload <b>max <?= $int_MaxMediaUploads ?> files</b> per conference with the above file extentions. However, you can upload a file with the same name multiple times, as it will replace the previous one.</p>
        <p class="align_right"><a class="button" href="<?= sx_PATH ?>?pg=media">Reload the Page</a></p>

        <h3><?= lngHelp ?></h3>
        <div class="text text_small">
            <div class="text_max_width">
                <p>Use this application to upload <b>Media Files</b> up to <b>1000mb</b> (1gb) and <b>Presentation Files</b> (PowerPoint and OpenDocument) up to <b>100mb</b>.
                    Files with the <b>same name</b> replace each other, so, you can <b>reupload</b> a file if you have revised it.</p>
                <ul>
                    <li>Presentation Files greater than 100mb <b>cannot be shown</b> in the website, so you have to export them as Video Files, in <b>mp4 Format</b>.
                        <ul>
                            <li>Please, select the resolution level <b>HD 720p</b> when you export a Presentation File to Video.</li>
                            <li>Select the resolution level <b>HD 1080p</b> only for video files with a size up to about 300mb.</li>
                            <li>Uploading files with a size over 300mb requires a <b>very fast internet connection</b>.</li>
                            <li><b>Obs!</b> When you export a Presentation File to Video, it is a good practice to <b>remove</b> all background images and shapes
                                as they increase the size of the video and make contents less readable.</li>
                        </ul>
                    </li>
                    <li>Please, use short, <b>meaningful</b> initial names, with <b>Latin</b> characters and occasionally <b>numbers</b>.
                        Use a single <b>hyphen</b> (-) between words, <b>Not spaces</b>!</li>
                    <li>The file keeps its <b>initial name</b>
                        with the addition of the Participant's ID as prefix: <b><i>pid_xx_Initial-Name.mp4</i></b>.</li>
                </ul>
                <p>Please notice that uploaded files must first <b>be processed</b> by the administration of the site
                    before they can be visible in the site.</p>
                <ul>
                    <li>If a file with the same name is already visible, the uploaded file will replace it and be visible immediately,
                        although visitors might need to reload the page using <code>Ctrl + F5</code> till clear cached files.
                    </li>
                </ul>
            </div>
        </div>

    </section>

    <script>
        // send conference ID with POST and check permisions in the PHP file
        var sx_conferenceID = '<?php echo $int_RightsConferenceID ?>';

        function displayFiles() {
            const fileInput = document.getElementById('fileInput');
            const files = fileInput.files;
            const fileTableBody = document.getElementById('fileTableBody');
            const uploadButton = document.getElementById('uploadButton');

            // Clear existing rows
            fileTableBody.innerHTML = '';

            for (let i = 0; i < files.length; i++) {
                const fileRow = document.createElement('tr');
                const fileNameCell = document.createElement('td');
                const fileSizeCell = document.createElement('td');
                const progressCell = document.createElement('td');

                fileNameCell.textContent = files[i].name;
                fileSizeCell.textContent = formatFileSize(files[i].size);
                progressCell.innerHTML = `<div class="file-${i}">0%</div>
                <progress class="file-progress file-${i}" value="0" max="100"></progress>`;

                fileRow.appendChild(fileNameCell);
                fileRow.appendChild(fileSizeCell);
                fileRow.appendChild(progressCell);

                fileTableBody.appendChild(fileRow);
            }

            // Show the table
            const fileTable = document.getElementById('fileTable');
            fileTable.style.display = 'table';

            // Enable the upload button
            uploadButton.disabled = false;
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
            const progressContainer = document.querySelector(`div.file-${fileIndex}`);
            progressContainer.textContent = `${value.toFixed(2)}%`;
            const progressElement = document.querySelector(`progress.file-${fileIndex}`);
            progressElement.value = value;
        }

        function uploadFiles() {
            const fileInput = document.getElementById('fileInput');
            const files = fileInput.files;

            const chunkSize = 1024 * 1024; // 1 MB chunks (adjust as needed)
            var radio_continue = true;
            var msg_maxUploadExceeded = '';

            function uploadFile(file, currentChunk = 0, fileIndex) {
                const totalChunks = Math.ceil(file.size / chunkSize);

                const start = currentChunk * chunkSize;
                const end = Math.min(start + chunkSize, file.size);
                const chunk = file.slice(start, end);

                const formData = new FormData();
                formData.append('file', chunk);
                formData.append('currentChunk', currentChunk);
                formData.append('totalChunks', totalChunks);
                formData.append('filename', file.name);
                formData.append('conferenceID', sx_conferenceID);

                fetch('ajax_media_uploader.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        console.log(data);
                        if (data.indexOf('Session_timed_out!') > -1 || data.indexOf('No_Way_Home!') > -1) {
                            radio_continue = false;
                        } else if (data.indexOf('Max allowed files uploaded!') > -1) {
                            radio_continue = false;
                            msg_maxUploadExceeded = data;
                        }
                        // Do not continue with chunks if there is a warning
                        if (radio_continue) {
                            const progressValue = ((currentChunk + 1) / totalChunks) * 100;
                            updateProgress(fileIndex, progressValue);

                            if (currentChunk < totalChunks - 1) {
                                uploadFile(file, currentChunk + 1, fileIndex);
                            } else {
                                console.log(`Upload complete for ${file.name}`);
                            }
                        }
                        // Send a semi-final message to the visitor (when the proccess for uploading all files has started)
                        if (fileIndex == files.length - 1) {
                            if (radio_continue) {
                                const msg_success = document.getElementById('msg_success');
                                msg_success.innerHTML = `The program is successfully uploading ${(fileIndex + 1)} File(s).`;
                                msg_success.style.display = 'block';
                            } else {
                                const msg_error = document.getElementById('msg_error');
                                if (msg_maxUploadExceeded != '') {
                                    msg_error.innerHTML = msg_maxUploadExceeded;
                                } else {
                                    msg_error.innerHTML = 'File(s) have NOT been upploaded. Your session might have been timed out or you have no permissions!';
                                }
                                msg_error.style.display = 'block';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }

            for (let i = 0; i < files.length; i++) {
                uploadFile(files[i], 0, i);
                if (i === (files.length - 1)) {
                    uploadButton.disabled = true;
                }
            }
        }
    </script>
<?php
}
