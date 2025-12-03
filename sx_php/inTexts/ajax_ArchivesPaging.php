<?php
include dirname(__DIR__) . "/sx_config.php";
include dirname(__DIR__) . "/basic_MediaFunctions.php";
include_once dirname(__DIR__) . "/inText_Archives/archives_TextsPagingQuery.php";

if ($radio_UseRelatedTexts) {
    include __DIR__ . "/functions_texts_related.php";
}
include __DIR__ . "/default.php";
?>
<script>
    sxLoadUniversalAjax();
    jQuery(function($) {
        sxInitializeHyphen($);
    });
    if ($sx('.jqImgCyclerManual').length) {
        if ($sx('.jqImgCyclerManual').length) {
            $sx('.jqImgCyclerManual').each(function() {
                $sx(this).sxLoadManualImgCycler();
            });
        };
    };
</script>