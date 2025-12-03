<?php
/**
 * Define wich table is used for texts
 */

$strTextTableVersion = '';
$strLinkTextPath = '';

 if (sx_TextTableVersion === 'texts') {
    $strTextTableVersion = sx_TextTableVersion;
    $strLinkTextPath = "/texts.php?tid=";
 } elseif (sx_TextTableVersion === 'items') {
    $strTextTableVersion = sx_TextTableVersion;
    $strLinkTextPath = "/items.php?itemid=";

} elseif (sx_TextTableVersion === 'articles')  {
    $strTextTableVersion = sx_TextTableVersion;
    $strLinkTextPath = "/articles.php?aid=";
}

define("STR_TextTableVersion", $strTextTableVersion);
define("STR_LinkTextPath", $strLinkTextPath);

function sx_getLanguageIDCodes()
{
	$conn = dbconn();
	$sql = "SELECT LanguageID, LanguageCode FROM languages ";
	$frs = $conn->query($sql)->fetchAll();
	if ($frs) {
		return  $frs;
	} else {
		return null;
	}
}

function sx_getLanguageCodesFromID($id)
{
	static $arrLanguageIDCodes;
	if (empty($arrLanguageIDCodes) || !is_array($arrLanguageIDCodes)) {
		$arrLanguageIDCodes = sx_getLanguageIDCodes();
	}
	if (is_array($arrLanguageIDCodes)) {
		/**
		 * If not multilingual, language ID might be 0
		 * So, get the language code for the first (and propably only) language
		 */
		if (intval($id) == 0) {
			return  $arrLanguageIDCodes[0][1];
		} else {
			$iRows = count($arrLanguageIDCodes);
			for ($r = 0; $r < $iRows; $r++) {
				if (intval($arrLanguageIDCodes[$r][0]) == intval($id)) {
					return  $arrLanguageIDCodes[$r][1];
				}
			}
		}
	}
}
