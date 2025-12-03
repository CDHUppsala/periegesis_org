<?php

/**
 * Basic META variables: 
 *      str_SiteTitle, $str_MetaTitle, $str_MetaDescription, $str_PropertySection ,$str_PropertyType ,$str_PropertyImage
 * These variables are defined in sx_config.php, 
 *      but can be changed by the config functions of any application,
 *      as they are, in every application page, placed between the sx_config.php and this file
 * Get constants for META variables also used in (Print) functions
 */

?>

<!DOCTYPE html>
<html lang="<?= sx_CurrentLanguage ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?php
    define("str_MetaTitle", $str_MetaTitle);
    require PROJECT_CONFIG . "/meta.php";
    ?>
    <link rel="icon" type="image/svg+xml" href="../images/logo/favicon.svg">
    <link rel="stylesheet" href="<?= sx_ROOT_HOST ?>/sxCss/root_Colors.css?v=2024-01">
    <link rel="stylesheet" href="<?= sx_ROOT_HOST ?>/sxCss/root_Variables.css?v=2024-01">
    <link rel="stylesheet" href="<?= sx_ROOT_HOST ?>/sxCss/sx_Buttons.css?v=2025-09-08?v=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Ysabeau+Office:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;0,1000;1,400;1,500;1,600;1,700;1,800;1,900;1,1000&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/sx_Structure.css?v=2025-09-08">
    <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/sx_Tables.css?v=2025-09-08">
    <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/sx_Texts.css?v=2025-09-08">
    <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/sx_Images.css?v=2025-09-08">
    <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/sx_svg.css?v=2025-09-08">
    <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/admin_templates.css?v=2025-09-08">
    <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/sx_FlexGrid.css?v=2025-09-08">
    <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/sx_Tabs.css?v=2025-09-08">
    <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/sx_Forms.css?v=2025-09-08">
    <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/sx_Sections.css?v=2025-09-08">
    <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/sx_GridCards.css?v=2025-09-08">
    <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/sx_Ads.css?v=2025-09-08">
    <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/sx_Calendar.css?v=2025-09-08">
    <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/sxNav_Top.css?v=2025-09-08">
    <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/sxNav_HeaderLogo.css?v=2025-09-08">
    <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/sxNav_Head.css?v=2025-09-08">
    <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/sxNav_Aside.css?v=2025-09-08">
    <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/sxNav_Accordion.css?v=2025-09-08">
    <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/sxNav_Markers.css?v=2025-09-08">
    <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/sx_Apps.css?v=2025-09-08">
    <link rel="stylesheet" href="<?= sx_ROOT_HOST ?>/sxCss/sx_Custom.css?v=2025-09-08">

    <script src="<?= sx_ROOT_DEV ?>/sxScripts/jq/jquery.min.js?v=2025-09-08"></script>
    <script src="<?= sx_ROOT_DEV ?>/sxScripts/js_ps_basic.js?v=2025-09-08"></script>
    <script src="<?= sx_ROOT_DEV ?>/sxScripts/jq_ps_basic.js?v=2025-09-08"></script>
    <script src="<?= sx_ROOT_DEV ?>/sxScripts/jq_ps_nav.js?v=2025-09-08"></script>

    <!--
    Public Sphere plugins with both .js and .css
    -->

    <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/ps/sx_gallery_inline.css?v=2025-09-08">
    <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/ps/sx_cycler.css?v=2025-09-08">
    <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/ps/sx_slider_manual.css?v=2025-09-08">

    <script src="<?= sx_ROOT_DEV ?>/sxScripts/ps/sx_gallery_inline.js?v=2025-09-08"></script>
    <script src="<?= sx_ROOT_DEV ?>/sxScripts/ps/sx_cycler.js?v=2025-09-08"></script>
    <script src="<?= sx_ROOT_DEV ?>/sxScripts/ps/sx_slider_manual.js?v=2025-09-08"></script>

    <?php
    if (sx_includeFooterSlider) { ?>
        <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/ps/sx_cycler_flex_cards.css?v=2025-09-08">
        <script src="<?= sx_ROOT_DEV ?>/sxScripts/ps/sx_cycler_flex_cards.js?v=2025-09-08"></script>
    <?php
    }

    if (sx_UseLightBox) { ?>
        <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/ps/sx_lightbox.css?v=2025-09-08">
        <script src="<?= sx_ROOT_DEV ?>/sxScripts/ps/sx_lightbox.js?v=2025-09-08"></script>
    <?php
    }

    if (sx_includeSlider && ($radio_UseEventsSlider || ($radio_UseSlider && $radio_DefaultPage))) { ?>
        <link rel="stylesheet" href="<?= sx_ROOT_DEV ?>/sxCss/ps/sx_slider.css?v=2024-02-10">
        <script src="<?= sx_ROOT_DEV ?>/sxScripts/ps/sx_slider.js?v=2025-09-08"></script>
    <?php
    }

    if (sx_radioUseHyphenator) {
        include __DIR__ . "/sx_InitializeHypher.php";
    }

    include __DIR__ . "/basic_TopFunctions.php";
    include __DIR__ . "/basic_AdsFunctions.php";
    include __DIR__ . "/basic_MediaFunctions.php";
    include __DIR__ . "/basic_PrintFunctions.php";

    /**
     * Queries and functions for the text applications About and Articles
     * that are used both in header and footer menus
     * - for About texts, functions are called from
     *      - all header menus
     *      - the footer menu in the file sx_Footer.php 
     * - for Article texts, functions are called from
     *      - the footer menu in the file sx_Footer.php
     * Add here functions for any text application that includes 
     * selected texts for footer menu
     */
    include __DIR__ . "/sxNav_Header_Footer/functions_Nav_Header_Footer.php";

    include __DIR__ . "/basic_ClearSessions.php";
    ?>