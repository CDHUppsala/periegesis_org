<header id="header">
    <?php
    function sx_breakTitle($title)
    {
        $pos = strpos($title, ' ');
        if ($pos !== false) {
            return substr_replace($title, '<br>', $pos, 1);
        } else {
            return $title;
        }
    } ?>
    <h2><?= lngLoadArchives ?></h2>
    <div>
        <a class="button" href="form_upload_files.php"><?= sx_breakTitle(lngUploadFiles) ?></a>
        <a class="button" href="form_upload_large_files.php"><?= sx_breakTitle(lngUploadLargeFiles) ?></a>
        <a class="button" href="form_resize_upload_images.php"><?= sx_breakTitle(lngUploadImages) ?></a>
        <a class="button" href="view_files.php"><?= sx_breakTitle(lngViewFolderFiles) ?></a>
        <a class="button" href="view_files.php?images=yes"><?= sx_breakTitle(lngViewFolderImages) ?></a>
        <!--a class="button" href="download_files.php">< ?= sx_breakTitle(lngDownloadFiles) ?></a-->
        <?php
        if (SX_allowSingleFolderCreation) { ?>
            <a class="button" href="folder_create.php?clear=yes"><?= sx_breakTitle(lngFolderCreate) ?></a>
            <a class="button" href="folder_delete.php"><?= sx_breakTitle(lngFolderDelete) ?></a>
        <?php
        } ?>
    </div>
</header>