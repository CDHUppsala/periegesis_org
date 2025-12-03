<?php
function return_Folder_ImagesGallery($strSubFolderName)
{
    $strFysicalPhotoPath = realpath($_SERVER["DOCUMENT_ROOT"] . "/" . STR__GalleryFolder . "/" . $strSubFolderName);
    $strRelativePhotoPath = sx_ROOT_HOST . "/" . STR__GalleryFolder . "/" . $strSubFolderName . "/";
    if (!is_dir($strFysicalPhotoPath)) { ?>
        <p><?= lngTheRequestedFolderDoesNotExist ?></p>
        <?php
    } else {
        if ($dirFiles = opendir($strFysicalPhotoPath)) {
            while (($file = readdir($dirFiles)) !== false) {
                $ext  = pathinfo($file, PATHINFO_EXTENSION);
                if (in_array($ext, ARR__ImageTypes)) { ?>
                    <figure><img src="<?= $strRelativePhotoPath . $file ?>" alt="<?= $file ?>" />
                        <figcaption><?= get_Link_Title_From_File_Name($file) ?></figcaption>
                    </figure>
<?php
                }
            }
        }
        closedir($dirFiles);
    }
} ?>

<div class="scroll">
    <section class="headers">
        <?php
        if (intval($int1) == 0) { ?>
            <h1><?= $strGallerySetupTitle ?></h1>
            <figure><img src="../images/<?= $strGallerySetupImage ?>" /></figure>
            <?php if ($memoGallerySetupNote != "") { ?>
                <div class="text"><?= $memoGallerySetupNote ?></div>
            <?php }
        } else {
            if (intval($int0) > 0) { ?>
                <h1><?= $strGroupName ?></h1>
                <?php if (!empty($memoGroupNote)) { ?>
                    <div class="text"><?= $memoGroupNote ?></div>
            <?php }
            } ?>
            <h2><?= $strGalleryName ?></h2>
            <?php if (!empty($memoGalleryNote)) { ?>
                <div class="text"><?= $memoGalleryNote ?></div>
        <?php
            }
        } ?>
    </section>
    <?php
    if (intval($int1) > 0 && !empty($strSubFolderName)) { ?>
        <section>
            <div class="ps_inline_gallery jqps_inline_gallery">
                <?php return_Folder_ImagesGallery($strSubFolderName) ?>
            </div>
        </section>
    <?php
    } ?>
</div>