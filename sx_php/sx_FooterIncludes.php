<?php
    //Load any hidden content with AJAX, e.g. Pausanias' books
?>
<div class="sx_load_hidden_ajax" id="js_load_hidden_ajax" style="display:none;"></div>

<div class="sx_modal" id="jq_Modal">
    <div class="sx_modal_content" id="jq_ModalContentWide">
        <div class="close" id="jq_ModalClose">&times;</div>
        <div id="jq_ModalContent"></div>
    </div>
</div>

<script src="<?= sx_ROOT_DEV ?>/sxScripts/jq_iframe_to_modal.js?v=2024-12-20"></script>

<?php if (defined('SX_includePlaceMaps') && SX_includePlaceMaps) : ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <div class="sx_modal_map" id="jq_ModalMap">
        <div class="flex_between flex_nowrap close_map">
            <div id="js_MapNotes"></div>
            <div><span id="jq_CloseModalMap">&times;</span></div>
        </div>
        <div id='js_ModalMapContainer'></div>
    </div>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="<?= sx_ROOT_DEV ?>/sxScripts/js_OpenStreetMap.js?v=2025-09-08"></script>
<?php endif;

/**
 * Check which of functions bellow should be used
 */
$strUseNewsLetterRegistration = true;
$strIncludeCookiesInfo  = true;

if (defined('SX_includeNewsLetterRegistration')) {
    $strUseNewsLetterRegistration = SX_includeNewsLetterRegistration;
}
if (defined('SX_includeCookiesInfo')) {
    $strIncludeCookiesInfo = SX_includeCookiesInfo;
}

if ($strUseNewsLetterRegistration) {
    // Check and runn the caching process only in index.php page
    if (str_contains(sx_PATH, 'index.php')) {
        include PROJECT_PATH . "/sx_Security/download_email_blocklist.php";
    }
    include __DIR__ . "/sx_NewsLetter/sx_NewsLetterResponse.php";
}

if ($strIncludeCookiesInfo) {
    include __DIR__ . "/sx_Cookies.php";
    include __DIR__ . "/sx_CookiesAds.php";
}

if (
    $radio_UseGoogleAnalytics &&
    !empty($str_GoogleAnalyticsPageTracker) &&
    SX_radioTestEnvironment == false
) {
    include dirname(__DIR__) . "/sx_Functions/sxGoogleAnalytics.php";
}

include __DIR__ . "/sx_Footer_Messages.php";

$check_screen = false;
if ($check_screen) { ?>
    <script>
        alert(window.screen.width + " pixels.");
    </script>
<?php
}

?>