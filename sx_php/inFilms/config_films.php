<?php
include __DIR__ . "/functions_films.php";

/**
 * Version that uses separate tables for Films, FilmAuthors and FilmToAuthors
 * The version is prepared for MS Access DB but can also be used in MySQL 
 * All queries to films are prepared here and use the functions in functions_films.php
 */

/**
 * 0. GET BOOKSETUP VARIABLES
 **/

$sql = "SELECT UseFilmLibrary, 
	LoginToView, AllowComments, AllowSurveys, 
	SurveyTitle, LoginToUseAllows, ControlCommentsByEmail, 
	FilmMenuTitle, FilmFirstPageTitle, FilmNote 
	FROM film_setup " . str_LanguageWhere;
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
	$radio_UseFilmLibrary = $rs["UseFilmLibrary"];
	$radioLoginToView = $rs["LoginToView"];
	$radioAllowComments = $rs["AllowComments"];
	$radioAllowSurveys = $rs["AllowSurveys"];
	$strSurveyTitle = $rs["SurveyTitle"];
	$radioLoginToUseAllows = $rs["LoginToUseAllows"];
	$radioControlCommentsByEmail = $rs["ControlCommentsByEmail"];
	$str_FilmMenuTitle = $rs["FilmMenuTitle"];
	$strFilmFirstPageTitle = $rs["FilmFirstPageTitle"];
	$memoFilmNote = $rs["FilmNote"];
}
$rs = null;
$stmt = null;

if ($radio_UseFilmLibrary == False) {
	header("Location: index.php?sx=1");
	exit();
}

if ($radioLoginToView && $radio__UserSessionIsActive == false) {
	header("Location: login.php");
	exit();
}
if ($radioLoginToView == False && $radioLoginToUseAllows && $radio__UserSessionIsActive == false) {
	$radioAllowComments = False;
	$radioAllowSurveys = False;
}
if ($radioLoginToView || $radioLoginToUseAllows) {
	$radioControlCommentsByEmail = False;
}

/**
 * 1. GET REQUEST VARIABLES'
 **/

/**
 * To determin Form requests for Comments or Survey
 */
$intFilmGroupID = 0;
$intFilmCatID = 0;

$strFormType = '';
if (!empty($_GET["frm"])) {
	$strFormType = $_GET["frm"];
}

$iFilmGroupID = 0;
if (!empty($_GET["filmGroupID"])) {
	$iFilmGroupID = intval($_GET["filmGroupID"]);
}

$iFilmCatID = 0;
if (!empty($_GET["filmCatID"])) {
	$iFilmCatID = intval($_GET["filmCatID"]);
}

$iFilmSubCatID = 0;
if (!empty($_GET["filmSubCatID"])) {
	$iFilmSubCatID = intval($_GET["filmSubCatID"]);
}

$iFilmID = 0;
if (!empty($_GET["filmID"])) {
	$iFilmID = intval($_GET["filmID"]);
}

$iPlaceID = 0;
if (!empty($_GET["placeID"])) {
	$iPlaceID = intval($_GET["placeID"]);
}

/**
 * Form variables as GET
 */

$iProductionYear = 0;
if (!empty($_GET["year"])) {
	$iProductionYear = intval($_GET["year"]);
}

$sDirector = '';
if (!empty($_GET["director"])) {
	$sDirector = sx_getSanitizedText($_GET["director"]);
}

$sScriptwriter = '';
if (!empty($_GET["writer"])) {
	$sScriptwriter = sx_getSanitizedText($_GET["writer"]);
}

$sActor = '';
if (!empty($_GET["actor"])) {
	$sActor = sx_getSanitizedText($_GET["actor"]);
}

$sTitle = '';
if (!empty($_POST["title"])) {
	$sTitle = sx_getSanitizedText($_POST["title"]);
}

/**
 * To open First (promotion) Page or List Page in films.php
 */

$radioPromotion = False;
$radioGetFilmLists = True;

$bSortAsc = True;

//	The page title for Form Requests
//	The title for Class Requests is created in films_variables.php from the first array row

$strRequestTitle = '';


$sFilmsWhere = "";
$sOrderBy = "";
if (intval($iFilmID) > 0) {
	$strRequestTitle = "ID: " . $iFilmID;
	$sFilmsWhere = " WHERE f.FilmID = " . $iFilmID;
	$sOrderBy = "";
} elseif (intval($iFilmGroupID) > 0) {
	$sFilmsWhere = " WHERE f.FilmGroupID = " . $iFilmGroupID;
	$sOrderBy = " ORDER BY f.ProductionYear DESC ";
} elseif (intval($iFilmCatID) > 0) {
	$sFilmsWhere = " WHERE f.FilmCategoryID = " . $iFilmCatID;
	$sOrderBy = " ORDER BY f.FilmGroupID, f.ProductionYear DESC ";
} elseif (intval($iFilmSubCatID) > 0) {
	$sFilmsWhere = " WHERE f.FilmSubCategoryID = " . $iFilmSubCatID;
	$sOrderBy = " ORDER BY f.FilmGroupID, f.FilmCategoryID, f.ProductionYear DESC ";
} elseif (intval($iProductionYear) > 0) {
	$strRequestTitle = lngProductionYear . ": " . $iProductionYear;
	$sFilmsWhere = " WHERE f.ProductionYear = " . $iProductionYear;
	$sOrderBy = " ORDER BY f.FilmGroupID, Title ";
} elseif (intval($iPlaceID) > 0) {
	$sFilmsWhere = " WHERE f.PlaceID = " . $iPlaceID;
	$sOrderBy = " ORDER BY f.FilmGroupID, f.ProductionYear DESC ";
} elseif (!empty($sDirector)) {
	$strRequestTitle = lngDirector . ": " . $sDirector;
	$sFilmsWhere = ' WHERE (LOCATE("' . $sDirector . '",UPPER(f.Director)) > 0) ';
	$sOrderBy = " ORDER BY f.FilmGroupID, ProductionYear DESC ";
} elseif (!empty($sScriptwriter)) {
	$strRequestTitle = lngScriptwriter . ": " . $sScriptwriter;
	$sFilmsWhere = ' WHERE (LOCATE("' . $sScriptwriter . '",UPPER(f.Scriptwriter)) > 0) ';
	$sOrderBy = " ORDER BY f.FilmGroupID, ProductionYear DESC ";
} elseif (!empty($sActor)) {
	$strRequestTitle = lngDirector . ": " . $sActor;
	$sFilmsWhere = ' WHERE (LOCATE("' . $sActor . '",UPPER(f.Actors)) > 0) ';
	$sOrderBy = " ORDER BY f.FilmGroupID, ProductionYear DESC ";
} elseif (!empty($sTitle)) {
	$strRequestTitle = LNG__Title . ": " . $sTitle;
	$sFilmsWhere = ' WHERE (LOCATE("' . $sTitle . '",UPPER(f.Title)) > 0 OR LOCATE("' . $sTitle . '",UPPER(f.SubTitle)) > 0 ) ';
	$sOrderBy = " ORDER BY f.FilmGroupID, ProductionYear DESC ";
} else {
	$radioPromotion = True;
	$sFilmsWhere = " WHERE f.ShowInPromotion = True ";
	$sOrderBy = " ORDER BY g.Ordering DESC, f.FilmGroupID, f.FilmID DESC ";
}

if (intval($iFilmID) > 0 || $radioPromotion) {
	$radioGetFilmLists = False;
}

////////////////////////////////////////////////////////////////////////////////'
// 3. GET RESULTS FROM DATABASE
////////////////////////////////////////////////////////////////////////////////'

$sFilmsWhere .= " AND g.Hidden = 0 AND (c.Hidden = 0 OR c.Hidden IS NULL) AND (sc.Hidden = 0 OR sc.Hidden IS NULL) ";
$getFilms = '';
$sql = "SELECT f.FilmID, 
	f.FilmGroupID, 
	g.FilmGroupName" . str_LangNr . " AS FilmGroupName, 
	f.FilmCategoryID, 
	c.FilmCategoryName" . str_LangNr . " AS FilmCategoryName, 
	f.FilmSubCategoryID, 
	sc.FilmSubCategoryName" . str_LangNr . " AS FilmSubCategoryName,
	f.PlaceID, 
	p.PlaceCode, 
	p.PlaceName" . str_LangNr . " AS PlaceName, 
	f.ProductionYear, 
	f.Title, 
	f.SubTitle, 
	f.Director,
	f.Scriptwriter,
	f.Actors,
	f.SpeakingLanguage,
	f.SubtitleLanguages,
	f.LengthInMinutes,
	f.FilmImage,
	f.ReviewURL,
	f.ReviewTitle,
	f.TrailerURL,
	f.TrailerTitle,
	f.ExternalLink,
	f.ExternalLinkTitle ";
if (intval($iFilmID) > 0) {
	$sql = $sql . ", f.Notes";
} elseif ($radioPromotion) {
	$sql = $sql . ", f.PromotionNotes ";
}
$sql = $sql . " FROM (((films AS f
	INNER JOIN film_groups AS g ON f.FilmGroupID = g.FilmGroupID) 
	LEFT JOIN film_categories AS c ON f.FilmCategoryID = c.FilmCategoryID) 
	LEFT JOIN film_subcategories AS sc ON f.FilmSubCategoryID = sc.FilmSubCategoryID) 
	LEFT JOIN film_place AS p ON f.PlaceID = p.PlaceID " . $sFilmsWhere . str_LanguageAnd . $sOrderBy;

//echo $sql;
$stmt = $conn->prepare($sql);
$stmt->execute();
$rs = $stmt->fetchAll(PDO::FETCH_NUM);
if ($rs) {
	$getFilms = $rs;
}
$rs = null;
$stmt = null;
