<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsDBConn.php";
include __DIR__ . "/inc_variables.php";
include __DIR__ . "/inc_header.php";
?>
<div class="maxWidth paddingBottom">
    <?php
    $radioShowExplanations = false;
    $radioTypeError = false;
    $strRType = "";
    if (!empty($_GET["type"])) {
        $strRType = $_GET["type"];
    }

    if (!empty($strRType)) {
        if ($strRType == "Texts" && sx_radioUseTextMaps) {
            include __DIR__ . "/create_xml_texts.php";
        } elseif ($strRType == "Articles" && sx_radioUseArticleMaps) {
            include __DIR__ . "/create_xml_articles.php";
            exit();
        } elseif ($strRType == "About" && sx_radioUseAboutMaps) {
            include __DIR__ . "/create_xml_about.php";
        } elseif ($strRType == "Authors" && sx_radioUseAuthorMaps) {
            include __DIR__ . "/create_xml_authors.php";
        } elseif ($strRType == "Themes" && sx_radioUseThemesMaps) {
            include __DIR__ . "/create_xml_themes.php";
        } elseif ($strRType == "Events" && sx_radioUseEventMaps) {
            include __DIR__ . "/create_xml_events.php";
        } elseif ($strRType == "Conferences" && sx_radioUseConferneceMaps) {
            include __DIR__ . "/create_xml_conferences.php";
        } elseif ($strRType == "Sessions" && sx_radioUseConferneceMaps) {
            include __DIR__ . "/create_xml_conf_sessions.php";
        } elseif ($strRType == "Papers" && sx_radioUseConferneceMaps) {
            include __DIR__ . "/create_xml_conf_papers.php";
        } elseif ($strRType == "Products" && sx_radioUseProductMaps) {
            include __DIR__ . "/create_xml_products.php";
        } else {
            $radioTypeError = true;
        }
    }
    if (empty($strRType) || $radioTypeError) {
        /**
         * Show explanation only in Home (First) Page
         */
        $radioShowExplanations = true;
        include __DIR__ . "/create_xml_root.php";
    }
    include __DIR__ . "/inc_footer.php";
    ?>
</div>
</body>

</html>