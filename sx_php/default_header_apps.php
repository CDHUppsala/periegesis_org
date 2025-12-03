<!DOCTYPE html>
<html lang="<?= sx_CurrentLanguage ?>">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $strPageTitle ?></title>
    <meta name="description" content="<?= $strMetaDescription ?>" />

    <link rel="stylesheet" type="text/css" charset="utf-8" href="<?= sx_ROOT_HOST ?>/sxCss/root_Colors.css">
    <link rel="stylesheet" type="text/css" charset="utf-8" href="<?= sx_ROOT_HOST ?>/sxCss/root_Variables.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Jura:400,500,600,700&amp;subset=greek,greek-ext" rel="stylesheet" type="text/css">

    <link rel="stylesheet" type="text/css" charset="utf-8" href="<?= sx_ROOT_HOST ?>/sxCss/sxNav_Accordion.css">
    <script type="text/javascript" charset="utf-8" src="<?= sx_ROOT_HOST ?>/sxScripts/jq/jquery.min.js"></script>

    <?php
    include __DIR__  ."/basic_MediaFunctions.php";

    $radioLocalHostSite = false;
    if (str_contains(sx_ROOT_HOST_PATH, '//localhost:')) {
        $radioLocalHostSite = true;
    }
    if (str_contains(sx_ROOT_HOST_PATH, '/ps_gallery.php')) { ?>
        <link rel="stylesheet" href="../sxCss/ps/sx_Gallery.css">
        <?php
        if ($radioLocalHostSite) {
            echo '<script>';
            include PROJECT_PATH . "/sx_Scripts/ps/sx_gallery.js";
            echo '</script>';
        } else { ?>
            <script type="text/javascript" charset="utf-8" src="../sxScripts/ps/sx_gallery.js"></script>
        <?php
        }
    } elseif (str_contains(sx_ROOT_HOST_PATH, '/ps_gallery_byfolder.php')) { ?>
        <link rel="stylesheet" href="<?= sx_ROOT_HOST ?>/sxCss/ps/sx_PDF_MM.css">
        <link rel="stylesheet" href="<?= sx_ROOT_HOST ?>/sxCss/ps/sx_gallery_inline.css">
        <?php
        if ($radioLocalHostSite) {
            echo '<script>';
            include PROJECT_PATH . "/sx_Scripts/ps/sx_PDF_MM.js";
            include PROJECT_PATH . "/sx_Scripts/ps/sx_gallery_inline.js";
            echo '</script>';
        } else { ?>
            <script type="text/javascript" charset="utf-8" src="<?= sx_ROOT_HOST ?>/sxScripts/ps/sx_PDF_MM.js"></script>
            <script type="text/javascript" charset="utf-8" src="<?= sx_ROOT_HOST ?>/sxScripts/ps/sx_gallery_inline.js"></script>
        <?php
        }
    } elseif (
        str_contains(sx_ROOT_HOST_PATH, '/ps_media.php')
        || str_contains(sx_ROOT_HOST_PATH, '/ps_PDF.php')
    ) { ?>
        <link rel="stylesheet" href="<?= sx_ROOT_HOST ?>/sxCss/ps/sx_PDF_MM.css">
        <?php
        if ($radioLocalHostSite) {
            echo '<script>';
            include PROJECT_PATH . "/sx_Scripts/ps/sx_PDF_MM.js";
            echo '</script>';
        } else { ?>
            <script type="text/javascript" charset="utf-8" src="<?= sx_ROOT_HOST ?>/sxScripts/ps/sx_PDF_MM.js"></script>
    <?php
        }
    } ?>