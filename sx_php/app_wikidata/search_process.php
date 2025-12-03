<?php

const ARR_WikidataFields = [
	'PersonID',
	'person',
	'personLabel',
	"PausaniasPerson",
	'genderLabel',
	'personDescription',
	'entityLabel',
	'citizen',
	'death',
	'fatherLabel',
	'motherLabel',
	'birthplaceLabel',
	'article',
	'father',
	'mother',
	'birthplace',
	'gender'
];
$arrExcludeFieldIndex = [12, 16, 13, 14, 15];
$arrRelatedFieldIndex = [2, 4, 9, 10, 11];

const ARR_SearchableFields = ['personLabel', 'PausaniasPerson', 'fatherLabel', 'motherLabel', 'personDescription', 'entityLabel'];

$arr_BindSearchWhere = [];

$strSearchWhere = "";
$strSearchText = "";

$strSearchPlace = "All";
$strSearchType = "Exact";

$strOrderByColumn = "";
$strOrderByWhere = "";

$strSort = "ASC";

/**
 * _POST may come from Search TEXT Form and from PAGE and SIZE Form
 * The one form should not change the sessions of the other
 */
$radioSearchRequested = False;
$radioShowPausaniasPersons = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["SearchTextSubmit"])) {
	if (!empty($_POST["SearchText"])) {
		$strSearchText = sx_Sanitize_Search_Text($_POST["SearchText"]);
		$strSearchPlace = sx_Sanitize_Search_Text($_POST["SearchPlace"]);
		$strSearchType = sx_Sanitize_Search_Text($_POST["SearchType"]);

		$_SESSION["SearchText"] = $strSearchText;
		$_SESSION["SearchPlace"] = $strSearchPlace;
		$_SESSION["SearchType"] = $strSearchType;

		// To Create new request statement for text
		$radioSearchRequested = True;
	}

	if (isset($_POST["ShowPausaniasPersons"]) && $_POST["ShowPausaniasPersons"] == 'Yes') {
		$_SESSION['ShowPausaniasPersons'] = true;
		$radioShowPausaniasPersons = true;
	} else {
		unset($_SESSION["ShowPausaniasPersons"]);
	}

	// If search with empty text, clean all sessions and show the entire table
	if (empty($strSearchText)) {
		unset($_SESSION["SearchText"]);
		unset($_SESSION["SearchType"]);
		unset($_SESSION["SearchPlace"]);
		unset($_SESSION["SearchWhere"]);
		unset($_SESSION["BindSearchWhere"]);
	}
} elseif (!empty($_SESSION["SearchText"])) {
	$strSearchText = $_SESSION["SearchText"] ?? '';
	$strSearchPlace = $_SESSION["SearchPlace"] ?? "InTitle";
	$strSearchType = $_SESSION["SearchType"] ?? "Exact";
	$strSearchWhere = $_SESSION["SearchWhere"] ?? '';
	$arr_BindSearchWhere = $_SESSION["BindSearchWhere"] ?? '';
	$radioShowPausaniasPersons = $_SESSION['ShowPausaniasPersons'] ?? false;
} else { // Show Recogito is independen of search text
	if (isset($_SESSION['ShowPausaniasPersons'])) {
		$radioShowPausaniasPersons = $_SESSION['ShowPausaniasPersons'];
	}
}

if ($radioSearchRequested && !empty($strSearchText)) {
	// Create new request statement for text
	$intCount = count(ARR_SearchableFields);
	if ($strSearchPlace == 'All') {
		for ($i = 0; $i < $intCount; $i++) {
			$sFiledValue = ARR_SearchableFields[$i];
			if ($i == 0) {
				$strSearchWhere = " WHERE ((" . get_SearchWhereString(($sFiledValue), $strSearchText, $strSearchType) . ")";
			} else {
				$strSearchWhere .= " OR (" . get_SearchWhereString(($sFiledValue), $strSearchText, $strSearchType) . ")";
			}
		}
		$strSearchWhere .= ")";
		$_SESSION["SearchWhere"] = $strSearchWhere;
	} else {
		for ($i = 0; $i < $intCount; $i++) {
			$sFiledValue = ARR_SearchableFields[$i];
			if ($sFiledValue == $strSearchPlace) {
				$strSearchWhere = " WHERE (" . get_SearchWhereString(($strSearchPlace), $strSearchText, $strSearchType) . ")";
				$_SESSION["SearchWhere"] = $strSearchWhere;
				break;
			}
		}
	}
	// Global variable comes from the function get_SearchWhereString()
	$_SESSION["BindSearchWhere"] = $arr_BindSearchWhere;
}

if ($radioShowPausaniasPersons) {
	if (empty($strSearchWhere)) {
		$strSearchWhere = " WHERE InPausaniasPerson = 1 ";
		$_SESSION["SearchWhere"] = $strSearchWhere;
	} elseif ($radioSearchRequested) {
		$strSearchWhere .= " AND InPausaniasPerson = 1 ";
		$_SESSION["SearchWhere"] = $strSearchWhere;
	}
}

$strOrderByColumn = !empty($_GET["OrderByColumn"]) ? trim($_GET["OrderByColumn"]) : '';

if (!empty($strOrderByColumn) && in_array($strOrderByColumn, ARR_WikidataFields)) {

	if (!isset($_SESSION["sort"])) {
		$_SESSION["sort"] = "ASC";
		$strSort = "ASC";
	} else {
		$strSort = $_SESSION["sort"];
	}

	if (isset($_SESSION["OrderByColumn"]) && $strOrderByColumn === $_SESSION["OrderByColumn"]) {
		if ($_SESSION["sort"] == "DESC") {
			$_SESSION["sort"] = "ASC";
			$strSort = "ASC";
		} else {
			$_SESSION["sort"] = "DESC";
			$strSort = "DESC";
		}
		$strOrderByWhere = " ORDER BY " . $strOrderByColumn . " " . $strSort;
	} else {
		$_SESSION["OrderByColumn"] = $strOrderByColumn;
		$strOrderByWhere = " ORDER BY " . $strOrderByColumn . " " . $_SESSION["sort"];
	}
} elseif (isset($_SESSION["OrderByColumn"]) && isset($_SESSION["sort"])) {
	$strOrderByColumn = $_SESSION["OrderByColumn"];
	$strOrderByWhere = " ORDER BY " . $strOrderByColumn . " " . $_SESSION["sort"];
	$strSort = $_SESSION["sort"];
} else {
	$strSort = "ASC";
	$strOrderByColumn = "PersonID";
	$strOrderByWhere = " ORDER BY $strOrderByColumn $strSort";

	$_SESSION["sort"] = $strSort;
	$_SESSION["OrderByColumn"] = $strOrderByColumn;
}

/**
 * Define Page Size Session
 * You cannot chage Page Size and Current Page simultaneously
 */

$intPageSize = 200;
if (!empty($_POST["PageSize"]) && $_POST["page"] == 0) {
	$intPageSize = (int) $_POST["PageSize"];
	if (intval($intPageSize) > 0) {
		$intPageSize = intval($intPageSize);
		$_SESSION["PageSize"] = $intPageSize;
	}
} elseif (isset($_SESSION["PageSize"])) {
	$intPageSize = $_SESSION["PageSize"];
}

/**
 * Define the Current Page Session - Must allways be > 0
 * Request can come both as GET (next-previous page) and POST (any page)
 */
$iCurrentPage = 0;
if (isset($_POST["page"]) && intval($_POST["page"]) > 0) {
	$iCurrentPage = (int) ($_POST["page"]);
} elseif (isset($_GET["page"]) && intval($_GET["page"]) > 0) {
	$iCurrentPage = (int) ($_GET["page"]);
}
if (intval($iCurrentPage) == 0) {
	$iCurrentPage = 1;
}
