<?php

$intFilmID = $getFilms[$r][0];
$intFilmGroupID = $getFilms[$r][1];
if (intval($intFilmGroupID) == 0) {
	$intFilmGroupID = 0;
}
$strFilmGroupName = $getFilms[$r][2];
$intFilmCategoryID = $getFilms[$r][3];
if (intval($intFilmCategoryID) == 0) {
	$intFilmCategoryID = 0;
}
$strFilmCategoryName = $getFilms[$r][4];
$intFilmSubCategoryID = $getFilms[$r][5];
if (intval($intFilmSubCategoryID) == 0) {
	$intFilmSubCategoryID = 0;
}
$strFilmSubCategoryName = $getFilms[$r][6];

$intPlaceID = $getFilms[$r][7];
$strPlaceCode = $getFilms[$r][8];
$strPlaceName = $getFilms[$r][9];
$strProductionYear = $getFilms[$r][10];
$strTitle = $getFilms[$r][11];
$strSubTitle = $getFilms[$r][12];

$strDirector = $getFilms[$r][13];
$strScriptwriter = $getFilms[$r][14];
$strActors = $getFilms[$r][15];

$strSpeakingLanguage = $getFilms[$r][16];
$strSubtitleLanguages = $getFilms[$r][17];
$strLengthInMinutes = $getFilms[$r][18];
$strFilmImage = $getFilms[$r][19];
$strReviewURL = $getFilms[$r][20];
$strReviewTitle = $getFilms[$r][21];
$strTrailerURL = $getFilms[$r][22];
$strTrailerTitle = $getFilms[$r][23];
$strExternalLink = $getFilms[$r][24];
$strExternalLinkTitle = $getFilms[$r][25];

$memoNotes = "";
if (intval($iFilmID) > 0) {
	$memoNotes = $getFilms[$r][26];
} elseif ($radioPromotion) {
	$memoNotes = $getFilms[$r][26];
}
//To get class names when they are requested - get them once
if ($r == 0) {
	if (intval($iPlaceID) > 0) {
		$strRequestTitle = lngPlace . ": " . $strPlaceName;
	} elseif (strlen($strRequestTitle) == 0) {
		if (intval($intFilmGroupID) > 0) {
			$strRequestTitle = " " . $strFilmGroupName;
		}
		if (intval($intFilmCategoryID) > 0) {
			$strRequestTitle = $strRequestTitle . " / " . $strFilmCategoryName;
		}
		if (intval($intFilmSubCategoryID) > 0) {
			$strRequestTitle = $strRequestTitle . " / " . $strFilmSubCategoryName;
		}
	}
}

/*
	if (!empty($strSubTitle)) {$strSubTitle = " - ".$strSubTitle;}
	if (!empty($strDirector)) {$strDirector = ", ".lngDirector.": ".$strDirector;}
	if (!empty($strScriptwriter)) {$strScriptwriter = ", ". lngScriptwriter .": ". $strScriptwriter;}
	if (!empty($strActors)) {$strActors = ", <i>".$strActors."</i>";}
	if (!empty($strSpeakingLanguage)) {$strSpeakingLanguage = ", ".$strSpeakingLanguage;}
	if (!empty($strProductionYear)) {$strProductionYear = ", ".$strProductionYear;}
 
	$strLengthInMinutes = "";
	if (!empty($strLengthInMinutes)) {
		$strLengthInMinutes = ", ".$strLengthInMinutes."min.";
	}
	*/

$str_FilmAbstract = "";
if ($strDirector != "") {
	$str_FilmAbstract = "<b>" . lngDirector . "</b>: " . $strDirector . ".";
}
if ($strScriptwriter != "") {
	$str_FilmAbstract .= "<br><b>" . lngScriptwriter . "</b>: " . $strScriptwriter . ".";
}
if ($strActors != "") {
	$str_FilmAbstract .= "<br><b>" . lngActors . "</b>: " . $strActors . ".";
}

if ($strProductionYear != "") {
	$str_FilmAbstract .= "<br><b>" . lngProductionYear . "</b>: " . $strProductionYear . ".";
}
if ($strSpeakingLanguage != "") {
	$str_FilmAbstract .= "<br><b>" . lngSpeakingLanguage . "</b>: " . $strSpeakingLanguage . ".";
}
if ($strSubtitleLanguages != "") {
	$str_FilmAbstract .= "<br><b>" . lngSubtitleLanguages . "</b>: " . $strSubtitleLanguages . ".";
}

if (intval($strLengthInMinutes) > 0) {
	$str_FilmAbstract .= "<br><b>" . lngLength . "</b>: " . $strLengthInMinutes . " min.";
}
if ($strPlaceName != "") {
	if (strlen($strPlaceCode) > 0) {
		$strPlaceCode = " (" . $strPlaceCode . ")";
	}
	$str_FilmAbstract .= "<br><b>" . lngPlaceToFind . "</b> " . $strPlaceName . $strPlaceCode;
}

$str_Titles = "";
if ($strTitle != "") {
    if (intval($iFilmID) > 0) {
        $str_Titles =  $strTitle;
    }else{
        $str_Titles = '<a title="' . lngViewDetails . '" href="films.php?filmID=' . $intFilmID . '">' . $strTitle . "</a>";
	}
}
if ($strSubTitle != "") {
	$str_Titles .= " - " . $strSubTitle;
}
