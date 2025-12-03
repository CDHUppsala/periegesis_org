<?php

if ($radio_LoggedParticipant == false || (int) $int_ParticipantID == 0) {
    header('Location: index.php');
    exit();
}

/**
 * resize and crop image by center
 */
function resize_crop_image($max_width, $max_height, $source_file, $dst_dir, $rotate, $quality = 80)
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
     * But only for Portraits
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
        $max_height = $max_width *  $iRatio_HW;
    }

    $mime = $imgsize['mime'];

    switch ($mime) {
        case 'image/gif':
            $image_create = "imagecreatefromgif";
            $image = "imagegif";
            break;

        case 'image/png':
            $image_create = "imagecreatefrompng";
            $image = "imagepng";
            $quality = 7;
            break;

        case 'image/jpeg':
            $image_create = "imagecreatefromjpeg";
            $image = "imagejpeg";
            $quality = 80;
            break;

        default:
            return false;
    }

    $dst_img = imagecreatetruecolor($max_width, $max_height);
    $src_img = $image_create($source_file);

    if ($rotate) {
        $src_img = imagerotate($src_img, -90, 0);
    }

    $width_new = $height * $max_width / $max_height;
    $height_new = $width * $max_height / $max_width;
    //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
    if ($width_new > $width) {
        //cut point by height
        $h_point = (($height - $height_new) / 2);
        imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
    } else {
        //cut point by width
        $w_point = (($width - $width_new) / 2);
        imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
    }

    $image($dst_img, $dst_dir, $quality);

    if ($dst_img) imagedestroy($dst_img);
    if ($src_img) imagedestroy($src_img);
}

$succesMsg = null;
$errMsg = array();
$arrAllowedExtensions = array('gif', 'png', 'jpg', 'jpeg');

$sMaxFileSize = ini_get("upload_max_filesize");
if (intval($sMaxFileSize) == 0) {
    $sMaxFileSize = '4MB';
}
$iMaxFileSize = intval($sMaxFileSize) * 1024 * 1024;


$target_file = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES['ImageFile']) && $_FILES['ImageFile']['error'] === UPLOAD_ERR_OK) {

        $file = $_FILES['ImageFile']['name'];
        $file_size = $_FILES['ImageFile']['size'];
        $file_tmp    = $_FILES['ImageFile']['tmp_name'];
        $file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (in_array($file_ext, $arrAllowedExtensions) === false) {
            $errMsg[] = "Extension not allowed! Please choose an allowed file.";
        }

        if ($file_size > $iMaxFileSize) {
            $errMsg[]  = 'The file size of your photo is ' . intval($file_size / 1024) . 'Kb. with maximun ' . ($iMaxFileSize / 1024) . 'Kb. Please try again.';
        }

        $radioRotate = false;
        if (isset($_POST['Rotate']) && $_POST['Rotate'] == "Yes") {
            $radioRotate = true;
        }

        $target_dir = "../images/authors/";
        $file_name = $_SESSION["Part_LastName"] . '-' . $_SESSION["Part_FirstName"] . '_ID_' . $int_ParticipantID;
        $file_name .= "." . $file_ext;

        $target_file = $target_dir . $file_name;

        if (empty($errMsg)) {
            resize_crop_image(400, 400, $file_tmp, $target_file, $radioRotate);
            $succesMsg = 'The image has been successfully uploaded with the file name: ' . $file_name . '.';
            $sql = "UPDATE conf_participants SET Portrait = ?
                WHERE ParticipantID = ? ";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['authors/' . $file_name, $int_ParticipantID]);
        }
    } else {
        $errMsg[]  = "Sorry, not readable file or the original file exceeds the Max Allowed Upload Size.";
    }
} ?>

<section>
    <h1 class="head"><span><?php echo lngAddChangePortrait ?></span></h1>
    <?php
    if (!empty($errMsg)) { ?>
        <p class="bg_error"><?= implode("<br>", $errMsg) ?></p>
    <?php
    }
    if (!empty($succesMsg)) { ?>
        <p class="bg_success"><?= $succesMsg ?></p>
    <?php
    } ?>

    <form name="UploadFiles" method="POST" enctype="multipart/form-data" action="<?= sx_PATH ?>?pg=portrait">
        <fieldset class="flex_between">
            <input name="ImageFile" type="file">
            <input style="margin-left: 10px" type="submit" value="Upload">
        </fieldset>
        <fieldset>
            <label>Rotate the Image: <input type="checkbox" name="Rotate" value="Yes"></label>
        </fieldset>
    </form>
    <div>
        <b>Allowed File Extentions:</b> <?= implode(", ", $arrAllowedExtensions) ?>
        <b>Max allowed original File Size:</b> <?= $sMaxFileSize ?> (<?= number_format(intval($iMaxFileSize / 1024), 0, ' ', ' ') ?>KB).
    </div>

    <div class="align_right">
        <p><a class="button-border" href="<?= sx_PATH ?>?pg=portrait">Reload the Page</a></p>
    </div>


    <?php
    if (!empty($target_file)) { ?>
        <p><img style="max-width: 100%" src="<?= $target_file ?>" /></p>
    <?php
    } ?>

    <h3><?= lngHelp ?></h3>
    <div class="text text_small">
        <div class="text_max_width">
            <p>You can upload <b>only one</b> Photo Portrait - additional uploads <b>replace</b> each other.</p>
            <ul>
                <li>The Photo Portrait is <b>resized and cropped</b> around the center of the original image to 400X400 pixels.</li>
                <li>The Portrait is automatically <b>renamed</b> to the Participant's Name and ID-Number (<b><i>LastName-FirstName_ID_xx.jpg</i></b>)
                    and added to the Participant's Profile.</li>
                <li>If you have a vertically oriented photo that appears horizontally, consider the possibility to rotate it.</li>
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