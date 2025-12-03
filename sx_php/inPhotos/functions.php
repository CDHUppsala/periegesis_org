<?php
function return_Folder_ImagesAsInlineGallery($strSubFolderName)
{
    $strFysicalPhotoPath = realpath($_SERVER["DOCUMENT_ROOT"] . "/" . str_GalleryFolder . "/" . $strSubFolderName);
    $strRelativePhotoPath = sx_ROOT_HOST . "/" . str_GalleryFolder . "/" . $strSubFolderName . "/";
    if (!is_dir($strFysicalPhotoPath)) { ?>
        <p><?= lngTheRequestedFolderDoesNotExist ?></p>
        <?php
    } else {
        if ($dir = opendir($strFysicalPhotoPath)) {
            while (($file = readdir($dir)) !== false) {
                $ext  = pathinfo($file, PATHINFO_EXTENSION);
                if (in_array($ext, arr_ImageTypes)) { ?>
                    <figure><img src="<?= $strRelativePhotoPath . $file ?>" alt="<?= $file ?>" />
                        <figcaption><?= get_Link_Title_From_File_Name($file) ?></figcaption>
                    </figure>
<?php
                }
            }
        }
        closedir($dir);
    }
}
