<?php

$intBookID = $getBooks[$r][0];
$strISBN = $getBooks[$r][1];
$intBookGroupID = $getBooks[$r][2];
if (intval($intBookGroupID) == 0) {
	$intBookGroupID = 0;
}
$strBookGroupName = $getBooks[$r][3];
$intBookCategoryID = $getBooks[$r][4];
if (intval($intBookCategoryID) == 0) {
	$intBookCategoryID = 0;
}
$strBookCategoryName = $getBooks[$r][5];
$intBookSubCategoryID = $getBooks[$r][6];
if (intval($intBookSubCategoryID) == 0) {
	$intBookSubCategoryID = 0;
}
$strBookSubCategoryName = $getBooks[$r][7];
$strAuthorNames = $getBooks[$r][8];
$intPlaceID = $getBooks[$r][9];
$strPlaceCode = $getBooks[$r][10];
$strPlaceName = $getBooks[$r][11];
$strPublicationYear = $getBooks[$r][12];
$strTitle = $getBooks[$r][13];
$strSubTitle = $getBooks[$r][14];
$radioEditors = $getBooks[$r][15];
$strIntroduction = $getBooks[$r][16];
$strTranslator = $getBooks[$r][17];
$strPublisher = $getBooks[$r][18];
$strJournalName = $getBooks[$r][19];
$strJournalIssue = $getBooks[$r][20];
$strJournalPages = $getBooks[$r][21];
$strPages = $getBooks[$r][22];
$strBookImage = $getBooks[$r][23];
$strEditionNumber = $getBooks[$r][24];
$strEditionForm = $getBooks[$r][25];
$strReviewURL = $getBooks[$r][26];
$strReviewTitle = $getBooks[$r][27];
$strExtractURL = $getBooks[$r][28];
$strExtractTitle = $getBooks[$r][29];
$strExternalLink = $getBooks[$r][30];
$strExternalLinkTitle = $getBooks[$r][31];
$memoNotes = "";
$memoPromotionNotes = "";
if (intval($iBookID) > 0 || $radioPromotion) {
	$memoPromotionNotes = $getBooks[$r][32];
	$memoNotes = $getBooks[$r][33];
}

/*
if ($r == 0) {
	if (intval($iPlaceID) > 0) {
		$strRequestTitle = lngPlace . ": " . $strPlaceName;
	} elseif (strlen($strRequestTitle) == 0) {
		if (intval($intBookGroupID) > 0) {
			$strRequestTitle = " " . $strBookGroupName;
		}
		if (intval($intBookCategoryID) > 0) {
			$strRequestTitle .= " / " . $strBookCategoryName;
		}
		if (intval($intBookSubCategoryID) > 0) {
			$strRequestTitle .= " / " . $strBookSubCategoryName;
		}
	}
}
*/

$sAuthorsName = "";
if (!empty($strAuthorNames)) {
	if (strpos($strAuthorNames, ";") > 0) {
		$aTemp = explode(";", $strAuthorNames);
		for ($z = 0; $z < count($aTemp); $z++) {
			$arrTemp = explode(":", $aTemp[$z]);
			$iID = trim($arrTemp[1]);
			$iName = trim($arrTemp[0]);
			if (!empty($sAuthorsName)) {
				$sAuthorsName = $sAuthorsName . ", ";
			}
			$sAuthorsName = $sAuthorsName . '<a href="' . sx_LANGUAGE_PATH . "books.php?wid=" . $iID . "&wn=" . urlencode($iName) . '">' . $iName . "</a>";
		}
	} else {
		$aTemp = explode(":", $strAuthorNames);
		$sAuthorsName = '<a href="' . sx_LANGUAGE_PATH . "books.php?wid=" . trim($aTemp[1]) . "&wn=" . urlencode(trim($aTemp[0])) . '">' . trim($aTemp[0]) . "</a>";
	}
}

$strEditors = "";
if ($radioEditors == True) {
if (!empty($sAuthorsName)) {
    $strEditors = ", (" . lngEditors . ")";
}
}
if ($strSubTitle != "") {
	$strSubTitle = " - " . $strSubTitle;
}
if ($strJournalName != "") {
	$strJournalName = ", <i>" . $strJournalName . "</i>";
}
if ($strJournalIssue != "") {
	$strJournalIssue = ", " . $strJournalIssue;
}
if ($strIntroduction != "") {
	$strIntroduction = ", " . lngIntroduction . ": " . $strIntroduction;
}
if ($strTranslator != "") {
	$strTranslator = ", " . lngTranslator . ": " . $strTranslator;
}
if ($strPublisher != "") {
	$strPublisher = ", " . $strPublisher;
}
if ($strPublicationYear != "") {
	if (!empty($sAuthorsName) || !empty($strEditors)) {
		$strPublicationYear = ", " . $strPublicationYear;
	}
}

$sPages = ".";
if ($strJournalPages != "") {
	$sPages = ", " . $strJournalPages . ".";
} elseif ($strPages <> "") {
	$sPages = ", " . $strPages . "pp.";
}

if (!empty($strPlaceCode)) {
	$strPlaceCode = " (" . $strPlaceCode . ")";
}

//To set book titles in italics within lists
if (!empty($strJournalName)) {
	$leftMark = '"';
	$rightMark = '"';
} else {
	$leftMark = "";
	$rightMark = "";
}
