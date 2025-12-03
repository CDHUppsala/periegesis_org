<?php

/**
 * Search in Default Text Table - Paging with rs.GetRows
 */

if (!isset($radioSearchAncientGreekText)) {
	$radioSearchAncientGreekText = False;
}

$intMaxTopSearch = 100;
if (intval(sx_intMaxTopSearch) > 0) {
	$intMaxTopSearch = sx_intMaxTopSearch;
}

$strSearchWhere = '';
$strSearch = '';

$strSearchPlace = "InTitle";
$strSearchType = "Exact";

$radioSearchRequested = False;
$strSearchLow = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$radioSearchRequested = True;
	if (!empty($_POST["SearchTextTop"])) {
		$strSearchLow = sx_Sanitize_Search_Text($_POST["SearchTextTop"]);
	} elseif (!empty($_POST["SearchText"])) {
		$strSearchLow = sx_Sanitize_Search_Text($_POST["SearchText"]);
	}
	if (empty($strSearchLow)) {
		$strSearchLow = "";
		$strSearch = "";
		unset($_SESSION["SearchType"]);
		unset($_SESSION["SearchLow"]);
		unset($_SESSION["SearchPlace"]);
	}
}

if (!empty($strSearchLow)) {
	$strSearch = strtoupper($strSearchLow);
	$strSearch = $strSearchLow;
	$_SESSION["SearchLow"] = $strSearchLow;
	if (isset($_POST["InnerSearch"])) {
		$strSearchPlace = sx_Sanitize_Search_Text($_POST["SearchPlace"]);
		$strSearchType = sx_Sanitize_Search_Text($_POST["SearchType"]);
	}
	$_SESSION["SearchType"] = $strSearchType;
	$_SESSION["SearchPlace"] = $strSearchPlace;
} elseif (isset($_SESSION["SearchLow"]) && !empty($_SESSION["SearchLow"])) {
	$strSearchType = $_SESSION["SearchType"];
	$strSearchLow = $_SESSION["SearchLow"];
	$strSearchPlace = $_SESSION["SearchPlace"];
} else {
	$strSearch = "";
	$strSearchLow = "";
}

/**
 * WHERE choices
 * Bind parameters for prepared statements
 */

$arr_BindSearchWhere = [];

if ($radioSearchRequested) {
	// 1. Text Search Where
	if (!empty($strSearch)) {
		switch ($strSearchPlace) {
			case "InAll";
				$strSearchWhere = " AND ((" . get_SearchWhereString(("p.PaperTitle"), $strSearch, $strSearchType) . ")";
				$strSearchWhere = $strSearchWhere . " OR (" . get_SearchWhereString(("p.PaperSubTitle"), $strSearch, $strSearchType) . ")";
				$strSearchWhere = $strSearchWhere . " OR (" . get_SearchWhereString(("p.Abstract"), $strSearch, $strSearchType) . ")";
				$strSearchWhere = $strSearchWhere . " OR (" . get_SearchWhereString(("p.PaperAuthors"), $strSearch, $strSearchType) . ")";
				$strSearchWhere = $strSearchWhere . " OR (" . get_SearchWhereString(("p.Speakers"), $strSearch, $strSearchType) . ")";
				$strSearchWhere = $strSearchWhere . " OR (" . get_SearchWhereString(("s.SessionTitle"), $strSearch, $strSearchType) . "))";
				$_SESSION["SearchWhere"] = $strSearchWhere;
				break;
			case "InText";
				$strSearchWhere = " AND ((" . get_SearchWhereString(("p.Abstract"), $strSearch, $strSearchType) . "))";
				$_SESSION["SearchWhere"] = $strSearchWhere;
				break;
			case "InTitle";
				$strSearchWhere = " AND ((" . get_SearchWhereString(("p.PaperTitle"), $strSearch, $strSearchType) . ")";
				$strSearchWhere = $strSearchWhere . " OR (" . get_SearchWhereString(("p.PaperSubTitle"), $strSearch, $strSearchType) . "))";
				$_SESSION["SearchWhere"] = $strSearchWhere;
				break;
			case "InName";
				$strSearchWhere = " AND ((" . get_SearchWhereString(("p.PaperAuthors"), $strSearch, $strSearchType) . ")";
				$strSearchWhere = $strSearchWhere . " OR (" . get_SearchWhereString(("p.Speakers"), $strSearch, $strSearchType) . ")";
				$strSearchWhere = $strSearchWhere . " OR (" . get_SearchWhereString(("s.Moderator"), $strSearch, $strSearchType) . "))";
				$_SESSION["SearchWhere"] = $strSearchWhere;
				break;
		}
		$_SESSION["BindSearchWhere"] = $arr_BindSearchWhere;
	} else {
		$strSearchWhere = null;
		unset($_SESSION["SearchWhere"]);
		unset($_SESSION["BindSearchWhere"]);
	}

	//## In all cases, clean the orderby session, if any
	$strOrderByWhere = null;
	unset($_SESSION["OrderBy"]);
} elseif (!empty(sx_QUERY)) {
	if (isset($_SESSION["SearchWhere"])) {
		$strSearchWhere = $_SESSION["SearchWhere"];
		$arrBindSearchWhere = $_SESSION["BindSearchWhere"];
	}
} else {
	unset($_SESSION["SearchWhere"]);
	unset($_SESSION["BindSearchWhere"]);
	$strSearchWhere = null;
	$arrBindSearchWhere = null;
}

/**
 * Define Page Size Session
 */

$intPageSize = 20;
if (!empty($_POST["PageSize"])) {
	$intPageSize = (int) $_POST["PageSize"];
	if (intval($intPageSize) > 0) {
		$intPageSize = intval($intPageSize);
		$_SESSION["PageSize"] = $intPageSize;
	}
} elseif (isset($_SESSION["PageSize"]) && intval($_SESSION["PageSize"]) > 0) {
	$intPageSize = $_SESSION["PageSize"];
}


//## Define Page Session - Must allways be > 0
$iCurrentPage =  return_Get_or_Post_Request("page");
if (isset($iCurrentPage) && intval($iCurrentPage) > 0) {
	$iCurrentPage = (int)($iCurrentPage);
	if ($iCurrentPage < 1) {
		$iCurrentPage = 1;
	}
	$_SESSION["Page"] = $iCurrentPage;
} else {
	if ($radioSearchRequested == False && empty($_POST["PageSize"]) && empty(return_Get_or_Post_Request("orderBy"))) {
		if (isset($_SESSION["Page"])) {
			$iCurrentPage = $_SESSION["Page"];
		} else { //When session is cleaned by a new field sorting
			$iCurrentPage = 1;
			$_SESSION["Page"] = $iCurrentPage;
		}
	} else {
		$iCurrentPage = 1;
		$_SESSION["Page"] = $iCurrentPage;
	}
} ?>