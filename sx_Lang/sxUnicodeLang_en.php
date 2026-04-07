<?php 
require realpath(dirname(__DIR__) ."/sx_SiteConfig/sx_languages.php");
require realpath(__DIR__ ."/sxDates_en.php");
require realpath(__DIR__ . "/sxLangEn.php");
const sx_CurrentLanguage = "en";

$lang_Suffix = "";

if (defined('sx_LangArr') && is_array(sx_LangArr)) {
    $lang_Suffix = sx_LangArr[sx_CurrentLanguage][1] ?? "";
}
