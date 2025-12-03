<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";

include __DIR__ . '/config_upload.php';
include __DIR__ . '/config_variables.php';

$arr_allowedImageTypes = ['jpg', 'jpeg', 'webp', 'png', 'svg'];
?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>SX CMS - Upload Files (Free ASP Upload 2.0)</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2024">
    <style>
        #Preview img,
        #PreviewResized img {
            border: 2px solid #000;
            margin: 1px;
        }

        fieldset span {
            font-size: 0.95em;
        }

        table {
            margin-bottom: 10px;
        }

        th {
            padding: 5px 2px;
            font-size: 0.85em
        }

        tr:first-child th {
            text-align: center
        }

        input[type="number"] {
            max-width: 100px;
        }
    </style>
    <script src="../js/jq/jquery.min.js"></script>
    <script src="<?php echo sx_ADMIN_DEV ?>js/jqFunctions.js?v=2014-01-04"></script>
</head>

<body id="bodyUpload" class="body">
    <?php include __DIR__ . "/nav_top.php"; ?>
    <section class="maxWidthWide">
        <h2>Resize, Crop and Upload Multiple Images</h2>

        <form id="ImageUploadForm" name="ImageUploadForm" method="POST" action="upload_canvas_form.php" enctype="multipart/form-data">
            <div class="container">
                <fieldset style="width: 50%">
                    <b>Selec Destination Folder:</b><br>
                    <select id="DestinationFolder" name="DestinationFolder">
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
                                if ($x > 0) {
                                    echo "</optgroup>";
                                }
                                echo '<optgroup label="' . $strCurr . '">';
                            } ?>
                            <option value="<?= $strLoop ?>" <?= $strSelected ?>><?= $strLoop ?></option>
                        <?php
                            $strLast = $strCurr;
                        } ?>
                    </select>
                </fieldset>
                <fieldset style="width: 50%; padding-left: 20px;">
                    <b>Resize, Crop and Upload Multiple Images</b><br>
                    <b>Image Types</b>: jpg, jpeg, webp, png, svg.<br>
                    <b>SVG</b> images are converted to <b>PNG</b>.
                </fieldset>
            </div>

            <div class="container">
                <fieldset style="width: 50%">

                    <b>Select Image Files...</b><br>
                    <input class="button" id="imageFiles" name="imageFiles" type="file" multiple accept="image/*" />

                    <p class="container flex_justify_end">
                        <label class="alignRight">
                            <b>Add Prefix to File Names:</b><br><input style="width: 2rem; height: 2rem; vertical-align:middle" type="checkbox" id="AddPrefix" name="AddPrefix" value="Yes" />
                        </label>
                        <label><b>Prefix:</b><br><input type="text" id="Prefix" name="Prefix" value="<?= date('Y-m-d') ?>" size="8" /></label>
                    </p>
                </fieldset>
                <fieldset style="width: 50%">
                    <div id="dropbox" style="background: #fff; border: 1px solid #ddd; border-radius: 5px; text-align: center; padding: 40px 10px 0 20px; height: 100%;">
                        <b>...or Drop Image Files in this Area</b>
                    </div>
                </fieldset>
            </div>
            <div class="container">
                <fieldset style="width: 50%">
                    <p><b>Crop Position in Image</b>: <span style="color: #c00">Active</span> with Cropping
                        <span class="tooltip jq_Tooltip" data-title="Cropping is applied on resized images. All images are by default cropped around their Center/Middle position (CM). You can change it by using the following table. If cropping starts from Top/Bottom or Left/Right, you can calibrate the cropping position with exact pixels."> [?] </span>
                    </p>
                    <table style="text-align:center; vertical-align:middle">
                        <tr>
                            <th>Positions</th>
                            <th>Left End</th>
                            <th>Center</th>
                            <th>Right End</th>
                        </tr>
                        <tr>
                            <th>Top End</th>
                            <td><input type="radio" name="CropPosition" value="LT" title="Left Top" />LT</td>
                            <td><input type="radio" name="CropPosition" value="CT" title="Centered Top" />CT</td>
                            <td><input type="radio" name="CropPosition" value="RT" title="Right Top" />RT</td>
                        </tr>
                        <tr>
                            <th>Middle</th>
                            <td><input type="radio" name="CropPosition" value="LM" title="Left Middle" />LM</td>
                            <td><input type="radio" name="CropPosition" value="CM" title="Centered Middle" checked />CM</td>
                            <td><input type="radio" name="CropPosition" value="RM" title="Right Middle" />RM</td>
                        <tr>
                            <th>Bottom End</th>
                            <td><input type="radio" name="CropPosition" value="LB" title="Left Bottom" />LB</td>
                            <td><input type="radio" name="CropPosition" value="CB" title="Centered Bottom" />CB</td>
                            <td><input type="radio" name="CropPosition" value="RB" title="Right Bottom" />RB</td>
                        </tr>
                    </table>

                    <p><b>Increase distans from Selected <span style="color: #c00">End</span> Positions</b></p>

                    <div>
                        <span>Distans from
                            <code style="font-weight: bold; color: #c00" title="From LT, CT or RT">Top</code> or <code style="font-weight: bold; color: #c00" title="From LB, CB or RB">Bottom</code>:</span>
                        <input style="width: 4.5rem" title="From Top or Bottom Crop in pixels" type="number" id="MoveTB" name="MoveTB" value="0" max="400" min="0" step="1" required /> pixels <span class="tooltip jq_Tooltip" data-title="Not valid for Middle Positions. Used for Horizontal Images, when original Ratio > Crop Ration. You move the cropped area vertically, from the Top or Bottom.">[?]</span><br>
                        <span>Distans from
                            <code style="font-weight: bold; color: #c00" title="From LT, LM or LB">Left</code> or <code style="font-weight: bold; color: #c00" title="From RT, RM or RB">Right</code>:</span>
                        <input style="width: 4.5rem" title="From Left or Right Crop in pixels" type="number" id="MoveLR" name="MoveLR" value="0" max="600" min="0" step="1" required /> pixels <span class="tooltip jq_Tooltip" data-title="Not valid for Center Positions. Used for Vertical Images, when original Ratio < Crop Ration. You move the cropped area horizontally, from the Left or Right">[?]</span>
                    </div>
                </fieldset>
                <fieldset style="width: 50%">
                    <p><b>Image Quality in percent:</b> For JPG, JPEG, WEBP and PNG Images<br>
                        <input type="radio" name="Quality" value="86" checked /><span>86</span>
                        <?php
                        for ($i = 100; $i >= 20; $i += -10) { ?>
                            <input type="radio" name="Quality" value="<?= $i ?>" /><span><?= $i ?></span>
                        <?php
                        } ?>
                    </p>
                    <div>
                        <p><b>Max Pixel Width:</b> <span style="color: #c00">Active</span> when Image <span style="color: #09f">Width > Height</span>
                            <span class="tooltip_left jq_Tooltip" data-title="<p>All <b>Horizontal</b> images are <b>resized</b> to the Max-Width you define here, while their Height is automatically defined by the initial Height/Width ratio of the original image.</p><p>If you <b>crop</b> the images, their Width and Height are variously defined by the relation between the Cropping H/W Ratio and the initial H/W ratio of the original image (see bellow).</p>"> [?] </span><br>
                            <?php
                            for ($i = 400; $i < 2001; $i += 200) {
                                $strChecked = "";
                                if ($i == 1000) {
                                    $strChecked = "checked ";
                                } ?>
                                <input <?= $strChecked ?>type="radio" name="MaxWidth" value="<?= $i ?>" /><span><?= $i ?></span>
                            <?php } ?>
                        </p>
                        <p><b>Max Pixel Height:</b> <span style="color: #c00">Active</span> when Image <span style="color: #09f">Width < Height</span>
                                    <span class="tooltip_left jq_Tooltip" data-title="<p>All <b>Vertical</b> images are <b>resized</b> to the Max-Height you define here, while their Width is automatically defined by the initial Height/Width ratio of the original image.<p><p>If you <b>crop</b> the images, their Height and Width are variously defined by the relation between the Cropping H/W Ratio and the initial H/W ratio of the original image (see bellow).</p>"> [?] </span><br>
                                    <?php
                                    for ($i = 400; $i < 1201; $i += 100) {
                                        $strChecked = "";
                                        if ($i == 600) {
                                            $strChecked = "checked ";
                                        } ?>
                                        <input <?= $strChecked ?>type="radio" name="MaxHeight" value="<?= $i ?>" /><span><?= $i ?></span>
                                    <?php } ?>
                        </p>
                    </div>
                    <p><b>Crop Images</b> by the following Height/Width Aspect Ratio:
                        <span class="tooltip_left jq_Tooltip" data-title="<p>If the Cropping Aspect Ratio (H/W) is less than the initial H/W ratio of the original image, the cropped images will be <b>more Horizontal</b> (Wide) than the original one. The entire Width of the original image will then be resized to the above defined Max Width. The image will then be cropped along the vertical axis with the Height calculated by the cropping ratio.</p><p>If the Cropping Aspect Ratio (H/W) is greater then the initial H/W ratio of the original image, the cropped images will be <b>more Vertical</b> (Nerrow, Portrait) than the original one. The entire Height of the images will then be resized to the above defined Max Hight. The image will then be cropped along the horizontal axis with the Width calculated by the cropping ratio.</p><p>In both cases, the measure of cropping dimensions starts from the <b>center/middle</b> (CM) of the images. You can change that from the table on the left.</p>"> [?] </span><br>
                        <input type="radio" name="CropRatio" value="0" checked /><span>None</span>
                        <input type="radio" name="CropRatio" value="2500" /><span><?= 5 / 20 ?></span>
                        <input type="radio" name="CropRatio" value="3400" /><span><?= 0.34 ?></span>
                        <input type="radio" name="CropRatio" value="4000" /><span><?= 4 / 10 ?></span>
                        <input type="radio" name="CropRatio" value="5000" /><span><?= 1 / 2 ?></span>
                        <input type="radio" name="CropRatio" value="5625" /><span><?= 9 / 16 ?></span>
                        <input type="radio" name="CropRatio" value="6666" /><span><?= 0.66 ?></span>
                        <input type="radio" name="CropRatio" value="7500" /><span><?= 3 / 4 ?></span>
                        <input type="radio" name="CropRatio" value="10000" /><span><?= 1.0 ?></span>
                        <input type="radio" name="CropRatio" value="12500" /><span><?= 5 / 4 ?></span>
                        <input type="radio" name="CropRatio" value="15000" /><span><?= 3 / 2 ?></span>
                    </p>

                    <div class="container">
                        <input type="button" class="button" value="Change & Preview Images" onclick="sx_ResizeAndPreviewImage(false)" />
                        <input type="button" class="button" value="Change & Upload Images" onclick="sx_ResizeAndPreviewImage(true)" />
                    </div>
                </fieldset>
            </div>
        </form>
    </section>

    <section class="paddingL">
        <div id="Preview"></div>
        <div id="ListUploadedImages"></div>
        <div id="PreviewResizedTitle"></div>
        <p id="ListNewSizes"></p>
        <div id="PreviewResized"></div>
    </section>

    <section class="maxWidth">

        <h3>Max File Size and Allowed File Types</h3>
        <ul>
            <li><?= lngFileMultiple ?>,
                with <b>Max Size per File</b>: <?= number_format($maxFileSizeInBytes / 1024, 0, ' ', ' ') ?> KB (<?= $maxFileSize ?> MB).
            </li>
            <li>Files that exceed the max allowed size will be marked in red color and the Upload Button
                will be disabled. In that case, use the <b>Upload Large Files</b>.
            </li>
            <li><b><?= lngAllowedFileTypes ?>:</b> <?= implode(", ", $arr_allowedImageTypes) ?>.</li>
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
        <?php
        //include __DIR__ . '/resize_upload_images_js.php';
        include __DIR__ . '/resize_upload_images.js';
        ?>
    </script>

    <?php
    include __DIR__ . '/resize_upload_images_help.php';
    ?>

</body>

</html>