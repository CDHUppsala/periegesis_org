<?php
include __DIR__ . "/upload_functions.php";

if ($radio_LoggedParticipant == false || (int) $int_ParticipantID == 0) {
    header('Location: index.php');
    exit();
}

$target_dir = realpath($_SERVER['DOCUMENT_ROOT']) . "/imgPDF/conf_" . $int_RightsConferenceID . "/";

if (!file_exists($target_dir)) {
    echo "<h2>The Upload Folder for the Conference $int_RightsConferenceID Does Not Exists</h2>
	<p>Please contact the administration of the site.</p>";
} else {
    $succesMsg = null;
    $errMsg = array();
    $arrAllowedExtensions = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odp', 'odt', 'ods');
    $filePath = "";

    $sMaxFileSize = ini_get("upload_max_filesize");
    if (intval($sMaxFileSize) == 0) {
        $sMaxFileSize = '10MB';
    }
    $iMaxFileSize = intval($sMaxFileSize) * 1024 * 1024;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_FILES['TextFile']) && $_FILES['TextFile']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['TextFile']['name'];
            $file_size = $_FILES['TextFile']['size'];
            $file_tmp    = $_FILES['TextFile']['tmp_name'];
            $file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            if (in_array($file_ext, $arrAllowedExtensions) === false) {
                $errMsg[] = "Extension not allowed! Please choose an allowed file.";
            }

            if ($file_size > $iMaxFileSize) {
                $errMsg[]  = 'The file size of your photo is ' . intval($file_size / 1024) . 'Kb. with maximun ' . ($iMaxFileSize / 1024) . 'Kb. Please try again.';
            }

            /**
             *  Check if Max number of allowed file uploads has been reached
             */
            if (empty($errMsg)) {
                $prefix = 'pid_' . $int_ParticipantID . "__";
                $file_name = $prefix . str_replace(" ", "-", $file);
                $filePath = $target_dir . $file_name;

                $radioFileExists = sx_checkIfFileExists($filePath);
                if ($radioFileExists === false) {
                    $arrCheckMaxUploads = sx_checkMaxUploadsIsReached($target_dir, $prefix, $int_MaxDocummentUploads);
                    if ($arrCheckMaxUploads[0]) {
                        $errMsg[] = 'Max number of allowed file uploads has been reached! ';
                        $errMsg[] = 'Existing File Names:';
                        for ($f = 1; $f < count($arrCheckMaxUploads); $f++) {
                            $errMsg[] = ' - ' . $arrCheckMaxUploads[$f];
                        }
                        $errMsg[] = 'Please, use the same File Name if you want to replace or update a file.';
                    }
                }
            }
            if (empty($errMsg)) {
                if (move_uploaded_file($file_tmp, $filePath)) {
                    $succesMsg = "The File $file has been successfully uploaded as $file_name.";
                } else {
                    $errMsg[] = 'There was some error in auploading the file to upload directory. Please contact the administration of the site.';
                }
            }
        } else {
            $errMsg[]  = "Sorry, not readable file or the original file exceeds the Max Allowed Upload Size.";
        }
    } ?>
    <section>
        <h1 class="head"><span><?php echo lngUploadConferenceDocumments ?></span></h1>
        <h4>Active Conference: <i><?= $str_RightsConferenceTitle ?></i></h4>
        <p>Please, contact the administration if the conference is not correct as files are uploaded in a conference specific folder!</p>
        <?php
        if (!empty($succesMsg)) { ?>
            <p class="bg_success"><?= $succesMsg ?></p>
        <?php
        }
        if (!empty($errMsg)) { ?>
            <p class="bg_error"><?= implode("<br>", $errMsg) ?></p>
        <?php
        } ?>

        <form name="UploadFiles" method="POST" enctype="multipart/form-data" action="<?= sx_PATH ?>?pg=docs">
            <fieldset class="flex_between">
                <input type="file" name="TextFile" />
                <input type="submit" name="Upload" value="Upload" />
            </fieldset>
        </form>
        <p class="text_small">
            <b>Allowed File Extentions:</b> <?= implode(", ", $arrAllowedExtensions) ?>.
            <b>Max allowed File Size:</b> <?= $sMaxFileSize ?> (<?= number_format(intval($iMaxFileSize / 1024), 0, ' ', ' ') ?>KB).
        </p>
        <p>You can totally upload <b>max <?= $int_MaxDocummentUploads ?> files</b> per conference with the above file extentions. However, you can upload a file with the same name multiple times, as it will replace the previous one.</p>
        <p class="align_right">
            <a class="button-border" href="<?= sx_PATH ?>?pg=docs">Reload the Page</a>
        </p>

        <h3><?= lngHelp ?></h3>
        <div class="text text_small">
            <div class="text_max_width">
                <p>Use this application to upload <b>PDF, WORD and Spreadsheet Documents</b> (not Images, Presentations or Media).
                    Files with the <b>same name</b> replace each other, so, you can <b>reupload</b> a file if you have revised it.</p>
                <ul>
                    <li>Please, use short, <b>meaningful</b> initial names, with <b>Latin or Greek</b> characters and occasionally <b>numbers</b>.
                        Use a single <b>hyphen</b> (-) between words, <b>not spaces</b>!</li>
                    <li>The file keeps its initial name with the addition of the Participant's ID as prefix: <b><i>pid_xx_Initial-Name.xxx</i></b>.</li>
                    <li>The <b>file name</b> will be transformed in the website to a <b>Title</b> for the download link.</li>
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
<?php
} ?>