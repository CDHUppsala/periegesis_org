<?php
include __DIR__ . "/upload_functions.php";

if ($radio_LoggedParticipant == false || (int) $int_ParticipantID == 0) {
    header('Location: index.php');
    exit();
}

/**
 * resize and crop image by center
 */
function resize_crop_image($max_width, $max_height, $source_file, $dst_dir, $rotate, $iQuality = 80)
{

    $imgsize = getimagesize($source_file);
    $width = $imgsize[0];
    $height = $imgsize[1];

    if ($rotate) {
        $height = $imgsize[0];
        $width = $imgsize[1];
    }

    /**
     * Don't change images that are shorter both in width and height to the MAX
     * But onl for Portraits
     */
    if ($width < $max_width && $height < $max_height && $max_width == $max_height) {
        $max_width = $width;
        $max_height = $height;
    }

    /**
     * Keep the H/W ratio for images with shorter width than the MAX
     */
    if ($width < $max_width) {
        $iRatio_HW = $max_height / $max_width;
        $max_width = $width;
        $max_height = round($max_width *  $iRatio_HW);
    }

    $mime = $imgsize['mime'];

    switch ($mime) {
        case 'image/gif':
            $image_create = "imagecreatefromgif";
            $image = "imagegif";
            $quality = 0;
            break;

        case 'image/png':
            $image_create = "imagecreatefrompng";
            $image = "imagepng";
            $quality = $iQuality;
            break;

        case 'image/jpeg':
            $image_create = "imagecreatefromjpeg";
            $image = "imagejpeg";
            $quality = $iQuality;
            break;

        default:
            return false;
    }

    $dst_img = imagecreatetruecolor($max_width, $max_height);
    $src_img = $image_create($source_file);

    if ($rotate) {
        $src_img = imagerotate($src_img, -90, 0);
    }

    $width_new = round($height * $max_width / $max_height);
    $height_new = round($width * $max_height / $max_width);
    if ($quality > 0) { //Not include .gif filse
        //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
        if ($width_new > $width) {
            //cut point by height
            $h_point = round(($height - $height_new) / 2);
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
        } else {
            //cut point by width
            $w_point = round(($width - $width_new) / 2);
            imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
        }
    }

    if ($quality == 0) { // .gif files
        $image($src_img, $dst_dir);
    } else {
        $image($dst_img, $dst_dir, $quality);
    }

    if ($dst_img) imagedestroy($dst_img);
    if ($src_img) imagedestroy($src_img);
}

/**
 * Check if the conference subfolder in the default 
 *  image folder has been created
 */

$target_dir = realpath($_SERVER['DOCUMENT_ROOT']) . "/images/conf_" . $int_RightsConferenceID . "/";

if (!file_exists($target_dir) || !is_dir($target_dir)) {
    echo "<h2>The Upload Folder for the Conference $int_RightsConferenceID Does Not Exists</h2>
	<p>Please contact the administration of the site.</p>";
} elseif ($radio_ToUploadImages == false) {
    echo "<h2>You have no permision to Upload images for the Conference $int_RightsConferenceID </h2>
	<p>Please, update your registration and ask for permission.</p>";
} else {

    $succesMsg = '';
    $errMsg = '';
    $arrAllowedExtensions = array('gif', 'png', 'jpg', 'jpeg');

    $sMaxFileSize = ini_get("upload_max_filesize");
    if (intval($sMaxFileSize) == 0) {
        $sMaxFileSize = '10MB';
    }
    $iMaxFileSize = intval($sMaxFileSize) * 1024 * 1024;

    $showUploadedFile = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_FILES['ImageFile']) && $_FILES['ImageFile']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['ImageFile']['name'];
            $file_size = $_FILES['ImageFile']['size'];
            $file_type = $_FILES['ImageFile']['type'];
            $file_tmp    = $_FILES['ImageFile']['tmp_name'];
            $file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            if (in_array($file_ext, $arrAllowedExtensions) === false) {
                $errMsg = "Extension not allowed! Please choose a file with allowed format.";
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
                    $arrCheckMaxUploads = sx_checkMaxUploadsIsReached($target_dir, $prefix, $int_MaxImageUploads);
                    if ($arrCheckMaxUploads[0]) {
                        $errMsg = 'Max number of allowed file uploads has been reached! ';
                        $errMsg .= 'Existing File Names:<br>';
                        for ($f = 1; $f < count($arrCheckMaxUploads); $f++) {
                            $errMsg .= ' - ' . $arrCheckMaxUploads[$f] . '<br>';
                        }
                        $errMsg .= 'Please, use the same File Name if you want to replace or update a file.';
                    }
                }
            }

            $ingQuality = 6;
            if (empty($errMsg)) {
                $radioPortrait = false;
                if (isset($_POST['Portrait']) && $_POST['Portrait'] == "Yes") {
                    $radioPortrait = true;
                }
                $radioRotate = false;
                if (isset($_POST['Rotate']) && $_POST['Rotate'] == "Yes") {
                    $radioRotate = true;
                }

                if (strpos($file_type, "/jpeg") > 0) {
                    if ($file_size > ($iMaxFileSize * 1.25)) {
                        $ingQuality = 40;
                    } elseif ($file_size > $iMaxFileSize) {
                        $ingQuality = 50;
                    } elseif ($file_size > intval($iMaxFileSize * 0.75)) {
                        $ingQuality = 60;
                    } elseif ($file_size > intval($iMaxFileSize * 0.5)) {
                        $ingQuality = 70;
                    } else {
                        $ingQuality = 80;
                    }
                } else {
                    if ($file_size > ($iMaxFileSize * 1.25)) {
                        $ingQuality = 4;
                    } elseif ($file_size > $iMaxFileSize) {
                        $ingQuality = 5;
                    } elseif ($file_size > intval($iMaxFileSize * 0.75)) {
                        $ingQuality = 6;
                    } elseif ($file_size > intval($iMaxFileSize * 0.5)) {
                        $ingQuality = 7;
                    } else {
                        $ingQuality = 8;
                    }
                }

                if ($radioPortrait) {
                    resize_crop_image(500, 500, $file_tmp, $filePath, $radioRotate, $ingQuality);
                } else {
                    resize_crop_image(1200, 600, $file_tmp, $filePath, $radioRotate, $ingQuality);
                }
                $succesMsg = 'The image ' . $file . ' has been successfully uploaded as ' . $file_name;
                $showUploadedFile = '../images/conf_' . $int_RightsConferenceID . '/' . $file_name;
            }
        } else {
            $errMsg  = "Sorry, not readable file or the original file exceeds the Max Allowed Upload Size.";
        }
    } ?>
    <section>
        <h1 class="head"><span><?php echo lngUploadConferenceImages ?></span></h1>
        <h4>Active Conference: <i><?= $str_RightsConferenceTitle ?></i></h4>
        <p>Please, contact the administration if the conference is not correct as files are uploaded in a conference specific folder!</p>
        <?php
        if (!empty($succesMsg)) { ?>
            <p class="bg_success"><?= $succesMsg ?></p>
        <?php }
        if (!empty($errMsg)) { ?>
            <p class="bg_error"><?= $errMsg ?></p>
        <?php
        } ?>
        <form name="UploadFiles" method="POST" enctype="multipart/form-data" action="<?= sx_PATH ?>?pg=images">
            <fieldset class="flex_between">
                <input name="ImageFile" type="file">
                <input type="submit" value="Upload">
            </fieldset>
            <fieldset class="flex_between">
                <label>Portrait Image: <input type="checkbox" name="Portrait" value="Yes"></label>
                <label>Rotate the Image: <input type="checkbox" name="Rotate" value="Yes"></label>
            </fieldset>
        </form>
        <p>
            <b>Allowed File Extentions:</b> <?= implode(", ", $arrAllowedExtensions) ?>.
            <b>Max allowed File Size after resizing:</b> <?= $sMaxFileSize ?> (<?= number_format(intval($iMaxFileSize / 1024), 0, ' ', ' ') ?>KB).
        </p>
        <p>You can totally upload <b>max <?= $int_MaxImageUploads ?> files</b> per conference with the above file extentions. However, you can upload a file with the same name multiple times, as it will replace the previous one.</p>

        <div class="align_right">
            <p><a class="button-border" href="<?= sx_PATH ?>?pg=images">Reload the Page</a></p>
        </div>
        <?php
        if (!empty($showUploadedFile)) { ?>
            <p>
            <figure class="image_center">
                <img alt="" src="<?= $showUploadedFile ?>" />
            </figure>
            </p>
        <?php
        } ?>
        <h3><?= lngHelp ?></h3>
        <div class="text text_small">
            <div class="text_max_width">
                <p>Use this application to upload <b>Image Files</b>. Images are automatically <b>resized and cropped</b> to predefined dimensions and quality.
                    Files with the <b>same name</b> replace each other, so, you can <b>reupload</b> a file until you are satisfied with its shape.</p>
                <ul>
                    <li>You can create Portrait or Landscape images from any initial image.
                        <ul>
                            <li><b>Portrait</b> (vertically oriented) images are resized and cropped around their center to 500X500 pixels.</li>
                            <li><b>Landscape</b> (horizontally oriented) images are resized and cropped around their center to 1200X600 pixels.</li>
                        </ul>
                    </li>
                    <li>Please, use short, <b>meaningful</b> initial names, with <b>Latin or Greek</b> characters and occasionally <b>numbers</b>.
                        Use a single <b>hyphen</b> (-) between words, <b>Not spaces</b>!</li>
                    <li>The image keeps its initial name with the addition of the Participant's ID as prefix: <b><i>pid_xx_Initial-Name.jpg</i></b>.</li>
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