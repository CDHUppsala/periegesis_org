<?php
const ARR_allowedFieldNames = [
	'TextID',
	'Title',
	'Coauthors',
	'PublishedDate',
	'GroupName',
	'AuthorID',
	'FirstName',
	'LastName'
];

if (!isset($radioSearchAncientGreekText)) {
	$radioSearchAncientGreekText = False;
}

$intMaxTopSearch = 100;
if (intval(sx_intMaxTopSearch) > 0) {
	$intMaxTopSearch = sx_intMaxTopSearch;
}

$strSearchWhere = "";
$strGroupWhere = "";
$strDatumWhere = "";
$strOrderByWhere = "";
$strSearch = "";

$intGroupID = 0;
$intDatum = 0;
if (sx_radioSearchFromLastThreeMonths) {
	$intDatum = 3;
}

$strSearchPlace = "InTitle";
$strSearchType = "Exact";

$radioSearchRequested = False;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$radioSearchRequested = True;
	if (!empty($_POST["SearchTextTop"])) {
		$strSearch = sx_Sanitize_Search_Text($_POST["SearchTextTop"]);
	} elseif (!empty($_POST["SearchText"])) {
		$strSearch = sx_Sanitize_Search_Text($_POST["SearchText"]);
	}
	if (strlen($strSearch) == 0) {
		$strSearch = "";
		unset($_SESSION["SearchType"]);
		unset($_SESSION["SearchLow"]);
		unset($_SESSION["SearchPlace"]);
	}
}

/**
 * Sessions are used bellow only when ordering the results (with GET request)
 */

if (!empty($strSearch)) {
	$_SESSION["SearchLow"] = $strSearch;
	if (!empty($_POST["InnerSearch"])) {
		$strSearchPlace = sx_Sanitize_Search_Text($_POST["SearchPlace"]);
		$strSearchType = sx_Sanitize_Search_Text($_POST["SearchType"]);
	}
	$_SESSION["SearchType"] = $strSearchType;
	$_SESSION["SearchPlace"] = $strSearchPlace;
} elseif (!empty($_SESSION["SearchLow"])) {
	$strSearchType = $_SESSION["SearchType"];
	$strSearch = $_SESSION["SearchLow"];
	$strSearchPlace = $_SESSION["SearchPlace"];
} else {
	$strSearch = "";
}

/**
 * Check for POST requests
 * Otherwise, when order the results with GET request, use sessions
 */

if (isset($_POST["GroupID"])) {
	$intGroupID = (int)($_POST["GroupID"]);
	$_SESSION["GroupID"] = $intGroupID;
} elseif (isset($_SESSION["GroupID"])) {
	$intGroupID = (int)($_SESSION["GroupID"]);
}

if (isset($_POST["Datum"])) {
	$intDatum = (int)($_POST["Datum"]);
	$_SESSION["Datum"] = $intDatum;
} elseif (isset($_SESSION["Datum"])) {
	$intDatum = (int)($_SESSION["Datum"]);
}

/**
 * WHERE choices
 * Bind parameters for prepared statements
 * $str_BindSearchWhere becomes global in the function creating the WHERE statements
 * 	- gives a string with all bind values to where statements, separated by a comma (,)
 * 	- some other bind values might be added to the strine
 * 	- before it is transformed to and array to execute the prepared statement
 */

$arr_BindSearchWhere = [];
$strBindDatumWhere = "";
$strBindGroupWhere = "";

if ($radioSearchRequested) {
	// 1. Text Search Where
	if (strlen($strSearch) > 0) {
		switch ($strSearchPlace) {
			case "InAll";
				$strSearchWhere = " AND ((" . get_SearchWhereString(("t.Title"), $strSearch, $strSearchType) . ")";
				$strSearchWhere = $strSearchWhere . " OR (" . get_SearchWhereString(("t.SubTitle"), $strSearch, $strSearchType) . ")";
				$strSearchWhere = $strSearchWhere . " OR (" . get_SearchWhereString(("t.MainText"), $strSearch, $strSearchType) . ")";
				$strSearchWhere = $strSearchWhere . " OR (" . get_SearchWhereString(("a.LastName"), $strSearch, $strSearchType) . ")";
				$strSearchWhere = $strSearchWhere . " OR (" . get_SearchWhereString(("a.FirstName"), $strSearch, $strSearchType) . ")";
				$strSearchWhere = $strSearchWhere . " OR (" . get_SearchWhereString(("t.Coauthors"), $strSearch, $strSearchType) . "))";
				$_SESSION["SearchWhere"] = $strSearchWhere;
				break;
			case "InText";
				$strSearchWhere = " AND ((" . get_SearchWhereString(("t.MainText"), $strSearch, $strSearchType) . "))";
				$_SESSION["SearchWhere"] = $strSearchWhere;
				break;
			case "InTitle";
				$strSearchWhere = " AND ((" . get_SearchWhereString(("t.Title"), $strSearch, $strSearchType) . ")";
				$strSearchWhere = $strSearchWhere . " OR (" . get_SearchWhereString(("t.SubTitle"), $strSearch, $strSearchType) . "))";
				$_SESSION["SearchWhere"] = $strSearchWhere;
				break;
			case "InName";
				$strSearchWhere = " AND ((" . get_SearchWhereString(("a.LastName"), $strSearch, $strSearchType) . ")";
				$strSearchWhere = $strSearchWhere . " OR (" . get_SearchWhereString(("a.FirstName"), $strSearch, $strSearchType) . ")";
				$strSearchWhere = $strSearchWhere . " OR (" . get_SearchWhereString(("t.Coauthors"), $strSearch, $strSearchType) . "))";
				$_SESSION["SearchWhere"] = $strSearchWhere;
				break;
		}
		$_SESSION["BindSearchWhere"] = $arr_BindSearchWhere;
	} else {
		$strSearchWhere = "";
		unset($_SESSION["SearchWhere"]);
		unset($_SESSION["BindSearchWhere"]);
	}

	//2. Groups: WHERE Choices
	if (intval($intGroupID) > 0) {
		$strGroupWhere = " AND t.GroupID = ?";
		$_SESSION["GroupWhere"] = $strGroupWhere;
		$strBindGroupWhere = $intGroupID;
		$_SESSION["BindGroupWhere"] = $strBindGroupWhere;
	} else {
		$strGroupWhere = "";
		unset($_SESSION["GroupWhere"]);
		unset($_SESSION["BindGroupWhere"]);
	}

	//3. Dates: WHERE Choices
	if (intval($intDatum) > 0) {
		if (intval($intDatum) <= 12) {
			$countDate = return_Add_To_Date(date('Y-m-d'), -$intDatum, 'month');
			$strDatumWhere = " AND (t.PublishedDate >= ?)";
			$_SESSION["DatumWhere"] = $strDatumWhere;
			$strBindDatumWhere = $countDate;
			$_SESSION["BindDatumWhere"] = $strBindDatumWhere;
		} else {
			$strDatumWhere = " AND YEAR(t.PublishedDate) = ?";
			$_SESSION["DatumWhere"] = $strDatumWhere;
			$strBindDatumWhere = $intDatum;
			$_SESSION["BindDatumWhere"] = $strBindDatumWhere;
		}
	} else {
		$strDatumWhere = "";
		unset($_SESSION["DatumWhere"]);
		unset($_SESSION["BindDatumWhere"]);
	}

	//## In all cases, clean the orderby session, if any
	$strOrderByWhere = "";
	unset($_SESSION["OrderBy"]);
} elseif (!empty(sx_QUERY)) {
	if (isset($_SESSION["SearchWhere"])) {
		$strSearchWhere = $_SESSION["SearchWhere"];
		$str_BindSearchWhere = $_SESSION["BindSearchWhere"];
	}
	if (isset($_SESSION["GroupWhere"])) {
		$strGroupWhere = $_SESSION["GroupWhere"];
		$strBindGroupWhere = $_SESSION["BindGroupWhere"];
	}
	if (isset($_SESSION["DatumWhere"])) {
		$strDatumWhere = $_SESSION["DatumWhere"];
		$strBindDatumWhere = $_SESSION["BindDatumWhere"];
	}
} else {
	unset($_SESSION["SearchWhere"]);
	$strSearchWhere = "";
	$str_BindSearchWhere = "";
	unset($_SESSION["BindSearchWhere"]);
	unset($_SESSION["GroupWhere"]);
	$strGroupWhere = "";
	$strBindGroupWhere = "";
	unset($_SESSION["BindGroupWhere"]);
	unset($_SESSION["DatumWhere"]);
	$strDatumWhere = "";
	$strBindDatumWhere = "";
	unset($_SESSION["BindDatumWhere"]);
}

$requestOrderBy = !empty($_GET["orderBy"]) ? trim($_GET["orderBy"]) : '';
if (!empty($requestOrderBy) && in_array($requestOrderBy, ARR_allowedFieldNames)) {
	if (!isset($_SESSION["sort"])) {
		$_SESSION["sort"] = "DESC ";
	}
	if ($_SESSION["OrderBy"] == "") {
		$_SESSION["OrderBy"] = " ORDER BY t.PublishedDate ";
	}
	if ($requestOrderBy == "sort" || $requestOrderBy == $_SESSION["Order"]) {
		if ($_SESSION["sort"] == "DESC ") {
			$_SESSION["sort"] = "ASC ";
		} else {
			$_SESSION["sort"] = "DESC ";
		}
		$strOrderByWhere = $_SESSION["OrderBy"] . " " . $_SESSION["sort"];
	} else {
		if ($requestOrderBy == "GroupID") {
			$strOrderByWhere = " ORDER BY t.GroupID ";
		} elseif ($requestOrderBy == "Title") {
			$strOrderByWhere = " ORDER BY t.Title ";
		} elseif ($requestOrderBy == "LastName") {
			$strOrderByWhere = " ORDER BY a.LastName ";
		} elseif ($requestOrderBy == "PublishedDate") {
			$strOrderByWhere = " ORDER BY t.PublishedDate ";
		}
		$_SESSION["Order"] = $requestOrderBy;
		$_SESSION["OrderBy"] = $strOrderByWhere;
		$strOrderByWhere = $strOrderByWhere . " " . $_SESSION["sort"];
	}
} else {
	if (isset($_SESSION["OrderBy"]) && isset($_SESSION["sort"])) {
		$strOrderByWhere = $_SESSION["OrderBy"] . " " . $_SESSION["sort"];
	} else {
		$_SESSION["sort"] = "DESC ";
		$_SESSION["Order"] = "PublishedDate";
		$_SESSION["OrderBy"] = " ORDER BY t.PublishedDate ";
		$strOrderByWhere = $_SESSION["OrderBy"] . " " . $_SESSION["sort"];
	}
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
}
