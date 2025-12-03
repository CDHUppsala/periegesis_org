<?php
/**
 * Define wich table is used for texts
 */

if (empty(sx_TextTableVersion) || sx_TextTableVersion === 'texts') {
    $strTextTableVersion = "texts";
} else {
    $strTextTableVersion = sx_TextTableVersion;
}

define("STR_TextTableVersion", $strTextTableVersion);



$strXMLTrueSiteURL = sx_ROOT_HOST;

$xmlSiteURL = $strXMLTrueSiteURL . "/" . sx_DefaultSiteLang;
$strXMLSufix = "";

$xmlFolderPath = "/sitemap/";
$levelsBack = "../../sitemap/";
$strSitemapFolder = $_SERVER["DOCUMENT_ROOT"] . "/sitemap/";

/**
 * To view created XML Files
 */
$urlViewXMLFile = "";

/**
 * Date variables
 */
$intYear = date('Y');
$intLinkYear = "";
$arrListXML = null;

/**
 * Get Languages
 */
$languageXML = null;
$sql = "SELECT LanguageID, LanguageCode, LanguageName
	FROM languages
	WHERE Hidden = False
	ORDER BY LanguageID ASC ";
$rs = $conn->query($sql)->fetchAll();
if ($rs) {
    $languageXML = $rs;
}
$rs = null;


/** Check the use of $rows
 * 
 * 
 */
$rows = -1;
$radioUseLanguages = false;
if (is_array($languageXML)) {
    $rows = count($languageXML);
}

if ($rows > 1) {
    $radioUseLanguages = true;
}

/**
 * Define language
 */

$strWhere = "";
$strLangXML = @$_POST["langXML"];
if ($radioUseLanguages && !empty($strLangXML)) {
    $arrTempt = explode("_", $strLangXML);
    $iLang = $arrTempt[0];
    $sLang = $arrTempt[1];
    $xmlSiteURL = $strXMLTrueSiteURL . "/" . $sLang;
    $strXMLSufix = "_" . $sLang;
    $strWhere = " AND (LanguageID = " . $iLang . " OR LanguageID = 0) ";
}

/**
 * Get Distinct years from the Used Text Table
 */
$arrPublishYears = null;
if (sx_radioUseTextMapsByYear) {
    if (STR_TextTableVersion === 'texts') {
        $sql = "SELECT DISTINCT YEAR(PublishedDate) AS AsYear FROM " . sx_TextTableVersion . " WHERE Publish = 1 ORDER BY Year(PublishedDate) DESC";
    } else {
        $sql = "SELECT DISTINCT YEAR(InsertDate) AS AsYear FROM " . sx_TextTableVersion . " WHERE Hidden = 0 ORDER BY Year(InsertDate) DESC";
    }
    $rs = $conn->query($sql)->fetchAll(PDO::FETCH_NUM);
    if ($rs) {
        $arrPublishYears = $rs;
        $intYear = -1;
    }
    $rs = null;
}
