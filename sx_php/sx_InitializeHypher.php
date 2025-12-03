<?php
$strElPolyton = "el-polyton";
$strHypherScript = "en-us";
$strHypherLang = "en-us";
if (sx_CurrentLanguage == "el") :
    $strHypherScript = "el-monoton";
    $strHypherLang = "el";
elseif (sx_CurrentLanguage == "sv") :
    $strHypherScript = "sv";
    $strHypherLang = "sv";
elseif (sx_CurrentLanguage == "fr") :
    $strHypherScript = "fr";
    $strHypherLang = "fr";
elseif (sx_CurrentLanguage == "fi") :
    $strHypherScript = "fi";
    $strHypherLang = "fi";
endif;
?>

<script src="../sxScripts/hypher/jquery.hypher.js"></script>
<script src="../sxScripts/hypher/browser/<?= $strHypherScript ?>.js"></script>
<script src="../sxScripts/hypher/browser/<?= $strElPolyton ?>.js"></script>
<script>
    jQuery(function($) {
        sxInitializeHyphen($);
    });
    var sxInitializeHyphen = function($) {
        $(".text, .text *").hyphenate("<?= $strHypherLang ?>");
        $(".polytonic_font, .polytonic_font *").hyphenate("<?= $strElPolyton ?>");
    };
</script>
