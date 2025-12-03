<?php
include  __DIR__ . "/functions_books.php";

/**
 * Version that uses separate tables for Books, BookAuthors and BookToAuthors
 * The version is prepared for MS Access DB but can also be used in MySQL 
 * All queries to books are prepared here and use the functions in _functions_books.php
 */

/**
 * 0. GET BOOKSETUP VARIABLES
 **/

$sql = "SELECT UseBooks, BooksNavTitle, BooksFirstPageTitle, 
	LoginToRead, AllowComments, AllowSurveys, SurveyTitle, 
	LoginToUseAllows, ControlCommentsByEmail, 
	BibliographyNote 
	FROM book_setup " . str_LanguageWhere;
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
	$radioUseBooks = $rs["UseBooks"];
	$strBooksFirstPageTitle = $rs["BooksFirstPageTitle"];
	$str_BooksNavTitle = $rs["BooksNavTitle"];
	$radioLoginToRead = $rs["LoginToRead"];
	$radioAllowComments = $rs["AllowComments"];
	$radioAllowSurveys = $rs["AllowSurveys"];
	$strSurveyTitle = $rs["SurveyTitle"];
	$radioLoginToUseAllows = $rs["LoginToUseAllows"];
	$radioControlCommentsByEmail = $rs["ControlCommentsByEmail"];
	$memoBibliographyNote = $rs["BibliographyNote"];
}
$rs = null;
$stmt = null;

if (!isset($radioUseBooks) || $radioUseBooks == False) {
	header("Location: index.php?sx=1");
	exit();
}

if ($radioLoginToRead && !isset($radio__UserSessionIsActive)) {
	header("Location: login.php");
	exit();
}
if ($radioLoginToRead == False && $radioLoginToUseAllows && !isset($radio__UserSessionIsActive)) {
	$radioAllowComments = False;
	$radioAllowSurveys = False;
}
if ($radioLoginToRead || $radioLoginToUseAllows) {
	$radioControlCommentsByEmail = False;
}


/**
 * 1. GET REQUEST VARIABLES'
 **/

/**
 * To determin Form requests for Comments or Survey
 */
$intBookGroupID = 0;
$intBookCatID = 0;

$strFormType = null;
if (!empty($_GET["frm"])) {
	$strFormType = $_GET["frm"];
}

$iBookGroupID = 0;
if (!empty($_GET["bookGroupID"])) {
	$iBookGroupID = (int) ($_GET["bookGroupID"]);
}

$iBookCatID = 0;
if (!empty($_GET["bookCatID"])) {
	$iBookCatID = (int) ($_GET["bookCatID"]);
}

$iBookSubCatID = 0;
if (!empty($_GET["bookSubCatID"])) {
	$iBookSubCatID = (int) ($_GET["bookSubCatID"]);
}

$iBookID = 0;
if (!empty($_GET["bookID"])) {
	$iBookID = (int) ($_GET["bookID"]);
}

$iPlaceID = 0;
if (!empty($_GET["placeID"])) {
	$iPlaceID = (int) ($_GET["placeID"]);
}

/**
 * Form variables as GET
 */
$iWriterID = 0;
if (!empty($_GET["wid"])) {
	$iWriterID = (int) ($_GET["wid"]);
}

$iPublicYear = 0;
if (!empty($_GET["year"])) {
	$iPublicYear = (int) ($_GET["year"]);
}

$sPublisher = null;
if (!empty($_GET["publisher"])) {
	$sPublisher = sx_getSanitizedText($_GET["publisher"]);
}

$sTitle = null;
if (!empty($_POST["title"])) {
	$sTitle = sx_getSanitizedText($_POST["title"]);
}

/**
 * Names are only for printing in title, so do not sanitize
 */
$sWriterName = null;
if (!empty($_GET["wn"])) {
	$sWriterName = $_GET["wn"];
}

/**
 * To open First (promotion) Page or List Page in books.php
 */

$radioPromotion = False;
$radioGetBookLists = True;

////////////////////////////////////////////////////////////////////////////////'
// 2. USE REQUEST VARIABLES TO DEFINE PARAMETERS FOR SQL QUERIES
////////////////////////////////////////////////////////////////////////////////'


//	The page title for Form Requests
//	The title for Class Requests is created in books_variables.php from the first array row

$strRequestTitle = "";

$sBooksWhere = "";
$sOrderBy = "";
$sOrderAccessBy = "";
$arrExecute = [];
if (intval($iBookID) > 0) {
	$strRequestTitle = lngBookDetails . " ID: " . $iBookID;
	$sBooksWhere = " WHERE b.BookID = ? ";
	$arrExecute = [$iBookID];
	$sOrderBy = "";
} elseif (intval($iBookGroupID) > 0) {
	$sBooksWhere = " WHERE b.BookGroupID = ? ";
	$arrExecute = [$iBookGroupID];
	$sOrderBy = " ORDER BY AuthorNames, b.PublicationYear DESC ";
} elseif (intval($iBookCatID) > 0) {
	$sBooksWhere = " WHERE b.BookCategoryID = ? ";
	$arrExecute = [$iBookCatID];
	$sOrderBy = " ORDER BY AuthorNames, b.PublicationYear DESC ";
} elseif (intval($iBookSubCatID) > 0) {
	$sBooksWhere = " WHERE b.BookSubCategoryID = ? ";
	$arrExecute = [$iBookSubCatID];
	$sOrderBy = " ORDER BY AuthorNames, b.PublicationYear DESC ";
} elseif (intval($iWriterID) > 0) {
	$strRequestTitle = lngWriter . ": " . $sWriterName;
	$sBooksWhere = " WHERE a.AuthorID = ? ";
	$arrExecute = [$iWriterID];
	$sOrderBy = " ORDER BY b.PublicationYear DESC ";
	$sOrderAccessBy = " b.PublicationYear asc, ";
} elseif (intval($iPublicYear) > 0) {
	$strRequestTitle = lngPublicationYear . ": " . $iPublicYear;
	$sBooksWhere = " WHERE b.PublicationYear = ? ";
	$arrExecute = [$iPublicYear];
	$sOrderBy = " ORDER BY AuthorNames ";
} elseif (intval($iPlaceID) > 0) {
	$sBooksWhere = " WHERE b.PlaceID = ? ";
	$arrExecute = [$iPlaceID];
	$sOrderBy = " ORDER BY AuthorNames, b.PublicationYear DESC ";
} elseif (!empty($sPublisher)) {
	$strRequestTitle = lngPublisher . ": " . $sPublisher;
	$sBooksWhere = " WHERE b.Publisher = ? ";
	$arrExecute = [$sPublisher];
	$sOrderBy = " ORDER BY AuthorNames ";
} elseif (!empty($sTitle)) {
	$strRequestTitle = LNG__Title . ": " . $sTitle;
	$sBooksWhere = ' WHERE (LOCATE(?,UPPER(b.Title)) > 0 OR LOCATE(?,UPPER(b.SubTitle)) > 0 ) ';
	$arrExecute = [$sTitle, $sTitle];
	$sOrderBy = " ORDER BY AuthorNames ";
} else {
	$radioPromotion = True;
	$sBooksWhere = " WHERE b.Promotion = True ";
	$sOrderBy = " ORDER BY g.Ordering DESC, b.BookGroupID, b.BookID DESC ";
	$sOrderAccessBy = " g.Ordering DESC, b.BookGroupID, ";
}

if (intval($iBookID) > 0 || $radioPromotion) {
	$radioGetBookLists = False;
}

////////////////////////////////////////////////////////////////////////////////'
// 3. GET RESULTS FROM DATABASE
////////////////////////////////////////////////////////////////////////////////'

/**
 * When searching for books by an Author ID ($iWriterID > 0)
 * The function takes an Author ID and search in book_to_authors table all books including this Author ID
 * Returns a string with Book IDs separated by comma (,)
 * - WHERE a.AuthorID = ? is then replaced by WHERE b.BookID IN (ID,ID,etc.)
 * - $arrExecute  = [] (en empty array)
 */

if (intval($iWriterID) > 0) {
	$sAuthorToBookIDs = sxAuthorsByBookID($sBooksWhere, $iWriterID);
	if (!empty($sAuthorToBookIDs)) {
		$sBooksWhere = " WHERE b.BookID IN (" . $sAuthorToBookIDs . ") ";
		$arrExecute  = [];
	}
}

$sBooksWhere .= " AND g.Hidden = 0 AND (c.Hidden = 0 OR c.Hidden IS NULL) AND (sc.Hidden = 0 OR sc.Hidden IS NULL) ";

$strPromotionFields = "";
if (intval($iBookID) > 0 || $radioPromotion) {
	$strPromotionFields = ", b.PromotionNotes, b.Notes ";
}

$getBooks = null;
$sql = "SELECT b.BookID, 
	b.ISBN, 
	b.BookGroupID, 
	g.BookGroupName" . str_LangNr . " AS BookGroupName, 
	b.BookCategoryID, 
	c.BookCategoryName" . str_LangNr . " AS BookCategoryName, 
	b.BookSubCategoryID, 
	sc.BookSubCategoryName" . str_LangNr . " AS BookSubCategoryName, 
	GROUP_CONCAT(a.LastName, ' ', a.FirstName, ':', a.AuthorID ORDER BY ba.AuthorOrdinal ASC separator '; ') AS AuthorNames,
	b.PlaceID, 
	p.PlaceCode, 
	p.PlaceName" . str_LangNr . " AS PlaceName, 
	b.PublicationYear, 
	b.Title, 
	b.SubTitle, 
	b.Editors, 
	b.Introduction, 
	b.Translator, 
	b.Publisher, 
	b.JournalName, 
	b.JournalIssue, 
	b.JournalPages, 
	b.Pages, 
	b.BookImage, 
	b.EditionNumber, 
	b.EditionForm, 
	b.ReviewURL, 
	b.ReviewTitle, 
	b.ExtractURL, 
	b.ExtractTitle, 
	b.ExternalLink, 
	b.ExternalLinkTitle "
	. $strPromotionFields . "
FROM (((((books AS b 
	LEFT JOIN book_to_authors AS ba ON b.bookID = ba.bookID) 
	LEFT JOIN book_authors AS a ON ba.AuthorID = a.AuthorID) 
	INNER JOIN book_groups AS g ON b.BookGroupID = g.BookGroupID) 
	LEFT JOIN book_categories AS c ON b.BookCategoryID = c.BookCategoryID) 
	LEFT JOIN book_subcategories AS sc ON b.BookSubCategoryID = sc.BookSubCategoryID) 
	LEFT JOIN book_place AS p ON b.PlaceID = p.PlaceID " . $sBooksWhere . str_LanguageAnd . "
GROUP BY b.BookID " . $sOrderBy;

/*
echo $sql;
echo "<hr>";
print_r($arrExecute);
echo "<hr>";
echo $arrExecute;
*/
$stmt = $conn->prepare($sql);
$stmt->execute($arrExecute);
$rs = $stmt->fetchAll(PDO::FETCH_NUM);
if ($rs) {
	$getBooks = $rs;
}
$rs = null;
$stmt = null;

/**
 * Geta meta-information from the first row
 */

if (!empty($memoBibliographyNote)) {
	$str_MetaDescription = return_Left_Part_FromText(strip_tags($memoBibliographyNote), 120);
}

if (is_array($getBooks)) {
	if (intval($iPlaceID) > 0) {
		$strRequestTitle = lngPlace . ": " . $getBooks[0][11];
		$str_SiteTitle = $strRequestTitle;
	} elseif (intval($iBookID) > 0 || !empty($sTitle)) {
		$str_SiteTitle = $getBooks[0][13];
		$str_MetaDescription = return_Left_Part_FromText(strip_tags($getBooks[0][33]), 120);
	} elseif ($radioPromotion) {
		$str_SiteTitle = $strBooksFirstPageTitle;
	} elseif (empty($strRequestTitle)) {
		// Get names for groups, categories and subcategories
		if (intval($getBooks[0][2]) > 0) {
			$strRequestTitle = " " . $getBooks[0][3];
		}
		if (intval($getBooks[0][4]) > 0) {
			$strRequestTitle .= " / " . $getBooks[0][5];
		}
		if (intval($getBooks[0][6]) > 0) {
			$strRequestTitle .= " / " . $getBooks[0][7];
		}
		$str_SiteTitle = $strRequestTitle;
	} else {
		$str_SiteTitle = $strRequestTitle;
	}
	$str_MetaTitle = $str_SiteTitle;
}
