<?php

/**
 * sx_Nobody is used for test and for cancelling some functions
 * without deleting them
 */
const sx_Nobody = false;

/**
 * Check if User is logged in
 * Might add sx_Host in session key
 */
$radio__UserSessionIsActive = false;
if (!empty($_SESSION["User_Token"])) {
    if (isset($_SESSION["Users_" . $_SESSION["User_Token"]]) && $_SESSION["Users_" . $_SESSION["User_Token"]]) {
        $radio__UserSessionIsActive = true;
    }
}

/**
 * Check if Student is logged in
 */
$radio__StudentSessionIsActive = false;
if (!empty($_SESSION["Student_Token"])) {
    if (isset($_SESSION["Students_" . $_SESSION["Student_Token"]]) && $_SESSION["Students_" . $_SESSION["Student_Token"]]) {
        $radio__StudentSessionIsActive = true;
    }
}

/**
 * Check if Conference Participant is logged in
 */
$radio__ParticipantSessionIsActive = false;
if (!empty($_SESSION["Participant_Token"])) {
    if (isset($_SESSION["Participants_" . $_SESSION["Participant_Token"]]) && $_SESSION["Participants_" . $_SESSION["Participant_Token"]]) {
        $radio__ParticipantSessionIsActive = true;
    }
}

/**
 * Do not use Captcha for login Users
 * To hide/show Text Groups (including categories, subcategories and Texts)
 *   that are accessible only for logged in Users
 */

$radio_UseCaptcha = true;
$strLoginToReadAnd = " AND LoginToRead = False ";
$strLoginToReadWhere = " WHERE LoginToRead = False ";
$strLoginToReadAnd_Grupp = " AND g.LoginToRead = False ";

if ($radio__UserSessionIsActive) {
    $radio_UseCaptcha = false;
    $strLoginToReadAnd = "";
    $strLoginToReadWhere = "";
    $strLoginToReadAnd_Grupp = "";
}

define("str_LoginToReadAnd", $strLoginToReadAnd);
define("str_LoginToReadWhere", $strLoginToReadWhere);
define("str_LoginToReadAnd_Grupp", $strLoginToReadAnd_Grupp);

/**
 * The CONSTANT sx_includeMultilinqual is defined in sx_Design.php
 * The CONSTANT sx_CurrentLanguage is defined in sx_SiteConfig.php
 *   and gives the string definition of current language (e.g. "en")
 * Get the ID of current language from the language table using the
 *   string definition of current language
 */

$int_LanguageID = 0;
if (sx_includeMultilinqual) {
    $sql = "SELECT LanguageID FROM languages WHERE LanguageCode = ? LIMIT 1";
    $rs = $conn->prepare($sql);
    $rs->execute([sx_CurrentLanguage]);
    $int_LanguageID = $rs->fetchColumn();
    $rs = null;
}

$int_LanguageID = (int) $int_LanguageID;
define("int_LanguageID", $int_LanguageID);

/**
 * Define SELECT parameters for current language
 * replace gradually $str by $str_ and str_ by STR_
 */
$strLangNr = "";
$strLanguageWhere = "";
$strLanguageAnd = "";
$strLanguageAnd_Text = "";
if ($int_LanguageID > 0) {
    if ($int_LanguageID > 1) {
        $strLangNr = "_$int_LanguageID";
    }
    $strLanguageWhere = " WHERE (LanguageID = $int_LanguageID OR LanguageID = 0)";
    $strLanguageAnd = " AND (LanguageID = $int_LanguageID OR LanguageID = 0)";
    $strLanguageAnd_Text = " AND (t.LanguageID = $int_LanguageID OR t.LanguageID = 0)";
}

$str_LangNr = $strLangNr;
$str_LanguageWhere = $strLanguageWhere;
$str_LanguageAnd = $strLanguageAnd;
$str_LanguageAnd_Text = $strLanguageAnd_Text;


define("str_LangNr", $str_LangNr);
define("str_LanguageWhere", $str_LanguageWhere);
define("str_LanguageAnd", $str_LanguageAnd);
define("str_LanguageAnd_Text", $str_LanguageAnd_Text);

define("STR_LangNr", $str_LangNr);
define("STR_LanguageWhere", $str_LanguageWhere);
define("STR_LanguageAnd", $str_LanguageAnd);
define("STR_LanguageAnd_Text", $str_LanguageAnd_Text);

/**
 * Not show flags for hidden languages in site
 */

$arrTemp = array();
if (sx_includeMultilinqual && sx_RadioMultiLang) {
    $sql = "SELECT LanguageCode FROM languages WHERE Hidden = True ";
    $rs = $conn->query($sql);
    while (($row = $rs->fetch(PDO::FETCH_ASSOC)) !== false) {
        $arrTemp[] = $row['LanguageCode'];
    }
    $rs = null;
}
define("ARR_HiddenLanguages", $arrTemp);

/**
 * Get Used Applications
 */

$sql = "SELECT IncludeReports, ReportsLinkTitle,
    IncludeBooks, BooksLinkTitle, 
    IncludeFAQ, FAQLinkTitle, 
    UsePoll, PollTitle, 
    UseSurvey, SurveyTittle, 
    UseQuiz, QuizTitle,
    IncludePSQ, PSQLinkTitle,
    IncludeForum, ForumLinkTitle,
    IncludeMenu, MenuLinkTitle,
    IncludeMusic, MusicLinkTitle,
    IncludeFilms, FilmsLinkTitle,
    IncludeWeekProgram,
    IncludeCourses, 
    CoursesLinkTitle,
    IncludeArticles,
    ArticlesTitle,
    IncludePosts,
    PostsTitle,
    IncludeItems,
    ItemsTitle
FROM site_config_apps $str_LanguageWhere";
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
    $radio_IncludeReports = $rs["IncludeReports"];
    $str_ReportsLinkTitle = $rs["ReportsLinkTitle"];

    $radio_IncludeBooks = $rs["IncludeBooks"];
    $str_BooksLinkTitle = $rs["BooksLinkTitle"];
    $radio_IncludeFAQ = $rs["IncludeFAQ"];
    $str_FAQLinkTitle = $rs["FAQLinkTitle"];

    $radio_UsePoll = $rs["UsePoll"];
    $str_PollTitle = $rs["PollTitle"];
    $radio_UseSurvey = $rs["UseSurvey"];
    $str_SurveyTittle = $rs["SurveyTittle"];
    $radio_UseQuiz = $rs["UseQuiz"];
    $str_QuizTitle = $rs["QuizTitle"];
    $radio_IncludePSQ = $rs["IncludePSQ"];
    $str_PSQLinkTitle = $rs["PSQLinkTitle"];

    $radio_IncludeForum = $rs["IncludeForum"];
    $str_ForumLinkTitle = $rs["ForumLinkTitle"];
    $radio_IncludeMenu = $rs["IncludeMenu"];
    $str_MenuLinkTitle = $rs["MenuLinkTitle"];
    $radio_IncludeMusic = $rs["IncludeMusic"];
    $str_MusicLinkTitle = $rs["MusicLinkTitle"];
    $radio_IncludeFilms = $rs["IncludeFilms"];
    $str_FilmsLinkTitle = $rs["FilmsLinkTitle"];

    $radio_IncludeWeekProgram = $rs["IncludeWeekProgram"];
    $radio_IncludeCourses = $rs["IncludeCourses"];
    $str_CoursesLinkTitle = $rs["CoursesLinkTitle"];
    $radio_IncludeArticles = $rs["IncludeArticles"];
    $str_ArticlesTitle = $rs["ArticlesTitle"];
    $radio_IncludePosts = $rs["IncludePosts"];
    $str_PostsTitle = $rs["PostsTitle"];
    $radio_IncludeItems = $rs["IncludeItems"];
    $str_ItemsTitle = $rs["ItemsTitle"];
}

/**
 * Site SETUP
 */

$sql = "SELECT * FROM site_setup WHERE SubOffice = 0 $str_LanguageAnd";
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
    $int_SiteID = $rs["SiteID"];
    $str_SiteTitle = $rs["SiteTitle"];
    $str_MetaTitle = $rs["MetaTitle"];
    $str_MetaDescription = $rs["MetaDescription"];
    $str_LogoTitle = $rs["LogoTitle"];
    $str_LogoSubTitle = $rs["LogoSubTitle"];
    $str_LogoImage = $rs["LogoImage"];
    $str_LogoImageSmall = $rs["LogoImageSmall"];
    $str_LogoImageEmail = $rs["LogoImageEmail"];
    $str_LogoImagePrint = $rs["LogoImagePrint"];
    $str_LogoBGImage = $rs["LogoBGImage"];
    $str_MiddleBodyImage = $rs["MiddleBodyImage"] ?? '';
    $str_SiteAdministrator = $rs["SiteAdministrator"];
    $str_SiteAdminEmail = $rs["SiteAdminEmail"];
    $str_SiteOwnerName = $rs["SiteOwnerName"];
    $str_SiteAddress = $rs["SiteAddress"];
    $str_SitePostalCode = $rs["SitePostalCode"];
    $str_SiteCity = $rs["SiteCity"];
    $str_SiteCountry = $rs["SiteCountry"];
    $str_SitePhone = $rs["SitePhone"];
    $str_SiteMobile = $rs["SiteMobile"];
    $str_SiteFax = $rs["SiteFax"];
    $str_SiteEmail = $rs["SiteEmail"];
    $str_OfficeHours = $rs["OfficeHours"];
    $str_PhoneHours = $rs["PhoneHours"];
    $radio_UseMap = $rs["UseMap"];
    $str_MapLatitude = $rs["MapLatitude"];
    $str_MapLongitude = $rs["MapLongitude"];
    $str_GoogleFrameMapSource = $rs["GoogleFrameMapSource"];
}
$rs = null;
$stmt = null;

if (empty($str_MetaTitle)) {
    $str_MetaTitle = $str_SiteTitle;
}
if (empty($str_LogoTitle)) {
    $str_LogoTitle = $str_SiteTitle;
}

if (empty($str_SiteOwnerName)) {
    $str_SiteOwnerName = $str_SiteTitle;
}

if (!empty($str_MiddleBodyImage)) {
    if (str_contains($str_MiddleBodyImage, ';')) {
        $arrCardImages = explode(';', $str_MiddleBodyImage);
        $randomNumber = mt_rand(0, count($arrCardImages) - 1);
        $str_MiddleBodyImage = $arrCardImages[$randomNumber];
    }
} elseif (defined('sx_ReplaceListImage') && !empty(sx_ReplaceListImage)) {
    $str_MiddleBodyImage = sx_ReplaceListImage;
}

define('STR_ReplaceListImage', $str_MiddleBodyImage);

define('SX_imageAltName', $str_SiteOwnerName);

/**
 * Keep the Site Title as constant, for different used
 * The variable $str_SiteTitle is aso the initial Titel in Heder and Meta Tags.
 * Although these Titles change, depending on the open application and record,
 * The Site Title (Name) must be constant.
 */
define("str_SiteTitle", $str_SiteTitle);
define("str_SiteEmail", $str_SiteEmail);

/**
 * BASIC CONFIGURATIONS
 */
$sql = "SELECT IncludeUsersLogin, FixedTopMenu, 
    UseTextComments, LoginToAddComment, LoginToAddReadComments, CommentableDays, MaxCommentLength,
    MaxFirstPageArticles, MaxArticlesPerPage, MaxInListArticles, ImageHeightWidthRatio, 
    UseSearch, UseStatistics, UseLinks, LinksTitle, ShowFavorites, FavoritesTitles, 
    UseAdvertises, AdvertisesTitle, UseSlider, 
    ShowSocialMedia, ShowSocialMediaInText, 
    UseEmail, MaxEmailLength, UseNewsLetters, UseGoogleAnalytics, 
    GoogleAnalyticsPageTracker, GoogleAPIKey,
    UsePrivacyStatement, PrivacyStatementTitle, 
    UseConditions, ConditionsTitle, ShowAcceptCookies, CookiesTitle, CookiesNotes, CookiesPolicy
FROM site_config_basic $str_LanguageWhere";
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
    $radio_IncludeUsersLogin = $rs["IncludeUsersLogin"];
    $radio_FixedTopMenu = $rs["FixedTopMenu"];
    $radio_UseTextComments = $rs["UseTextComments"];
    $radio_LoginToAddComment = $rs["LoginToAddComment"];
    $radio_LoginToAddReadComments = $rs["LoginToAddReadComments"];
    $i_CommentableDays = $rs["CommentableDays"];
    $i_MaxCommentLength = $rs["MaxCommentLength"];
    $i_MaxFirstPageArticles = $rs["MaxFirstPageArticles"];
    $i_MaxArticlesPerPage = $rs["MaxArticlesPerPage"];
    $iMaxInListArticles = $rs["MaxInListArticles"];
    $radio_UseSearch = $rs["UseSearch"];
    $radio_UseStatistics = $rs["UseStatistics"];
    $radio_UseLinks = $rs["UseLinks"];
    $str_LinksTitle = $rs["LinksTitle"];
    $radio_ShowFavorites = $rs["ShowFavorites"];
    $str_FavoritesTitles = $rs["FavoritesTitles"];
    $radio_UseAdvertises = $rs["UseAdvertises"];
    $str_AdvertisesTitle = $rs["AdvertisesTitle"];
    $radio_UseSlider = $rs["UseSlider"];
    $radio_ShowSocialMedia = $rs["ShowSocialMedia"];
    $radio_ShowSocialMediaInText = $rs["ShowSocialMediaInText"];
    $radio_UseEmail = $rs["UseEmail"];
    $i_MaxEmailLength = $rs["MaxEmailLength"];
    $radio_UseNewsLetters = $rs["UseNewsLetters"];
    $radio_UseGoogleAnalytics = $rs["UseGoogleAnalytics"];
    $str_GoogleAnalyticsPageTracker = $rs["GoogleAnalyticsPageTracker"];
    $str_GoogleAPIKey = $rs["GoogleAPIKey"];
    $radio_UsePrivacyStatement = $rs["UsePrivacyStatement"];
    $str_PrivacyStatementTitle = $rs["PrivacyStatementTitle"];
    $radio_UseConditions = $rs["UseConditions"];
    $str_ConditionsTitle = $rs["ConditionsTitle"];
    $radio_ShowAcceptCookies = $rs["ShowAcceptCookies"];
    $str_CookiesTitle = $rs["CookiesTitle"];
    $str_CookiesNotes = $rs["CookiesNotes"];
    $str_CookiesPolicy = $rs["CookiesPolicy"];
}
$rs = null;
$stmt = null;

if (empty(($i_MaxFirstPageArticles)) || (int)($i_MaxFirstPageArticles) == 0) {
    $i_MaxFirstPageArticles = 8;
}
define("str_LimitFirstPage", " LIMIT " . $i_MaxFirstPageArticles);

if (empty($i_CommentableDays) || (int)($i_CommentableDays) == 0) {
    $i_CommentableDays = 3000;
}

if (empty($iMaxInListArticles) || (int)($iMaxInListArticles) == 0) {
    $iMaxInListArticles = 8;
}
define("int_MaxInListArticles", $iMaxInListArticles);
define("str_LimitInList", " LIMIT " . $iMaxInListArticles);


if (empty($i_MaxArticlesPerPage) || (int)($i_MaxArticlesPerPage) == 0) {
    $i_MaxArticlesPerPage = 10;
}
if (empty($i_MaxEmailLength) || (int)($i_MaxEmailLength) == 0) {
    $i_MaxEmailLength = 1000;
}
if (empty($i_MaxCommentLength) || (int)($i_MaxCommentLength) == 0) {
    $i_MaxCommentLength = 1200;
}

/*
 * TEXTS CONFIGURATIONS - Common to all text tables
 */

$sql = "SELECT
    ShowByPublishInFirstPage,
    ShowTextClassesInMainMenu, TextClassesInMainMenuTitle, 
    ShowPublishedTextsByClass, PublishedTextsByClassLevel, PublishedTextsByClassTitle,
    UseTextsAbout, TextAboutHeaderMenuByGroup, TextsAboutTitle, 
    ShowAboutTextsInHeader, ShowAboutTextsInFooter,
    ShowArchivesList, ArchivesListTitle, 
    ShowFirstPageTexts, FirstPageTextsTitle,
    ShowTextsByCalendar, TextsByCalenderTitle,
    ShowTextsByYearMonth, TextsByYearMonthTitle,
    ShowTextsByAuthors, TextsByAuthorsTitle, 
    ShowTextsByThemes, TextsByThemesTitle,
    MenuFormForByTexts, ByTextsMenuTitle,
    ShowRecentTexts, RecentTextsTitle, 
    ShowMostReadTexts, MostReadTextsTitle, 
    MenuFormForRecentAndMostRead, RecentAndMostReadMenuTitle, 
    ShowRecentComments, RecentCommentsTitle, 
    ShowMostCommented, MostCommentedTitle,
    MenuFormForRecentAndMostCommented, RecentAndMostCommentedMenuTitle, 
    UseRelatedTexts, UseAsideTexts, AsideTextsTitle, UseAsideTextImg, 
    UseAsideTextIntro
    FROM site_config_texts $str_LanguageWhere";
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
    $radio_ShowByPublishInFirstPage = $rs["ShowByPublishInFirstPage"];
    $radio_ShowTextClassesInMainMenu = $rs["ShowTextClassesInMainMenu"];
    $str_TextClassesInMainMenuTitle = $rs["TextClassesInMainMenuTitle"];

    $radio_ShowPublishedTextsByClass = $rs["ShowPublishedTextsByClass"];
    $str_PublishedTextsByClassLevel = $rs["PublishedTextsByClassLevel"];
    $str_PublishedTextsByClassTitle = $rs["PublishedTextsByClassTitle"];

    $radio_UseTextsAbout = $rs["UseTextsAbout"];
    $radio_TextAboutHeaderMenuByGroup = $rs["TextAboutHeaderMenuByGroup"];
    $str_TextsAboutTitle = $rs["TextsAboutTitle"];

    $radio_ShowAboutTextsInHeader = $rs["ShowAboutTextsInHeader"];
    $radio_ShowAboutTextsInFooter = $rs["ShowAboutTextsInFooter"];

    $radio_ShowArchivesList = $rs["ShowArchivesList"];
    $str_ArchivesListTitle = $rs["ArchivesListTitle"];

    $radio_ShowFirstPageTexts = $rs["ShowFirstPageTexts"];
    $str_FirstPageTextsTitle = $rs["FirstPageTextsTitle"];

    $radio_ShowTextsByCalendar = $rs["ShowTextsByCalendar"];
    $str_TextsByCalenderTitle = $rs["TextsByCalenderTitle"];

    $radio_ShowTextsByYearMonth = $rs["ShowTextsByYearMonth"];
    $str_TextsByYearMonthTitle = $rs["TextsByYearMonthTitle"];
    $radio_ShowTextsByAuthors = $rs["ShowTextsByAuthors"];
    $strTextsByAuthorsTitle = $rs["TextsByAuthorsTitle"];
    $radio_ShowTextsByThemes = $rs["ShowTextsByThemes"];
    $str_TextsByThemesTitle = $rs["TextsByThemesTitle"];
    $str_MenuFormForByTexts = $rs["MenuFormForByTexts"];
    $str_ByTextsMenuTitle = $rs["ByTextsMenuTitle"];

    $radio_ShowRecentTexts = $rs["ShowRecentTexts"];
    $str_RecentTextsTitle = $rs["RecentTextsTitle"];
    $radioShowMostReadTexts = $rs["ShowMostReadTexts"];
    $str_MostReadTextsTitle = $rs["MostReadTextsTitle"];
    $str_MenuFormForRecentAndMostRead = $rs["MenuFormForRecentAndMostRead"];
    $str_RecentAndMostReadMenuTitle = $rs["RecentAndMostReadMenuTitle"];

    $radio_ShowRecentComments = $rs["ShowRecentComments"];
    $str_RecentCommentsTitle = $rs["RecentCommentsTitle"];
    $radio_ShowMostCommented = $rs["ShowMostCommented"];
    $str_MostCommentedTitle = $rs["MostCommentedTitle"];
    $str_MenuFormForRecentAndMostCommented = $rs["MenuFormForRecentAndMostCommented"];
    $str_RecentAndMostCommentedMenuTitle = $rs["RecentAndMostCommentedMenuTitle"];

    $radio_UseRelatedTexts = $rs["UseRelatedTexts"];
    $radio_UseAsideTexts = $rs["UseAsideTexts"];
    $str_AsideTextsTitle = $rs["AsideTextsTitle"];
    $radio_UseAsideTextImg = $rs["UseAsideTextImg"];
    $radio_UseAsideTextIntro = $rs["UseAsideTextIntro"];
}
$rs = null;
$stmt = null;

if (empty($str_ArchivesListTitle)) {
    $str_ArchivesListTitle = lngRelatedTexts;
}
if (empty($strTextsByAuthorsTitle)) {
    $strTextsByAuthorsTitle = lngAuthors;
}
if (empty($str_TextsByThemesTitle)) {
    $str_TextsByThemesTitle = lngThemes;
}
if (empty($str_RecentTextsTitle)) {
    $str_RecentTextsTitle = lngRecentArticles;
}
if (empty($str_MostReadTextsTitle)) {
    $str_MostReadTextsTitle = lng_Nav_MostReadArticles;
}

$int_Year = 0;
$int_Month = 0;
$int_Week = 0;
$int_Day = 0;

if (!empty(return_Get_or_Post_Request("year"))) {
    $int_Year = (int) return_Get_or_Post_Request("year");
    if (strlen($int_Year) != 4 || intval($int_Year) < 1970) {
        $int_Year = 0;
    }
}
if (!empty(return_Get_or_Post_Request("month"))) {
    $int_Month = (int) return_Get_or_Post_Request("month");
    if (intval($int_Month) > 12 || intval($int_Month) < 1) {
        $int_Month = 0;
    }
}
if (!empty(return_Get_or_Post_Request("week"))) {
    $int_Week = (int) return_Get_or_Post_Request("week");
    if (intval($int_Week) > 53 || intval($int_Week) < 1) {
        $int_Week = 0;
    }
}
if (!empty(return_Get_or_Post_Request("day"))) {
    $int_Day = (int) return_Get_or_Post_Request("day");
    if (intval($int_Day) > 31 || intval($int_Day) < 1) {
        $int_Day = 0;
    }
}

/*
    SQL String for Searching and Pagination in Text Archives
    ====================================================
    The variable $radio_FirstArchiveRequest checks if ther is a First Request for Archive Texts
    - A First Request (by Date, Group ID, Theme ID or Author ID) gives $radio_FirstArchiveRequest = True
    - It is used in page inText_Archives/archives_TextsPagingQuery.php to create the SQL String 
      for searchin in Text Table and save the SQL Sring and related Variables in sessions.
    ====================================================
 */

$radio_FirstArchiveRequest = false;

if (strlen($int_Year) == 4 && return_Check_Int_Between($int_Month, 1, 12)) {

    $strMonth = $int_Month;
    if (strlen($strMonth) == 1) {
        $strMonth = "0" . $strMonth;
    }
    $strDay = $int_Day;
    if ($strDay == 0) {
        $strDay = '01';
    } elseif (strlen($strDay) == 1) {
        $strDay = "0" . $strDay;
    }

    $date_SearchByDate = $int_Year . "-" . $strMonth . "-01";
    $date_RequestedDate = $int_Year . "-" . $strMonth . "-" . $strDay;
    $radio_FirstArchiveRequest = true;
} else {
    $date_RequestedDate = date("Y-m-d");
    $date_SearchByDate = date("Y-m-d");
}

$int_TextID = 0;
$int_GroupID = 0;
$int_CatID = 0;
$int_SubCatID = 0;
$int_ThemeID = 0;
$int_AuthorID = 0;

if (!empty(return_Get_or_Post_Request("tid"))) {
    $int_TextID = (int) return_Get_or_Post_Request("tid");
}


if (!empty(return_Get_or_Post_Request("gid"))) {
    $int_GroupID = (int) return_Get_or_Post_Request("gid");
    if ($int_GroupID  > 0) {
        $radio_FirstArchiveRequest = true;
    }
}

if (!empty(return_Get_or_Post_Request("cid"))) {
    $int_CatID = (int) return_Get_or_Post_Request("cid");
    if ($int_CatID > 0) {
        $radio_FirstArchiveRequest = true;
    }
}

if (!empty(return_Get_or_Post_Request("scid"))) {
    $int_SubCatID = (int) return_Get_or_Post_Request("scid");
    if ($int_SubCatID > 0) {
        $radio_FirstArchiveRequest = true;
    }
}

if (!empty(return_Get_or_Post_Request("themeID"))) {
    $int_ThemeID = (int) return_Get_or_Post_Request("themeID");
    if ($int_ThemeID > 0) {
        $radio_FirstArchiveRequest = true;
    }
}

if (!empty(return_Get_or_Post_Request("authorID"))) {
    $int_AuthorID = (int) return_Get_or_Post_Request("authorID");
    if ($int_AuthorID > 0) {
        $radio_FirstArchiveRequest = true;
    }
}

$str_PropertyType = "website";
$str_PropertyImage = "";
$intStatsTextID = 0;  // For statistics to be deleted, used for old versions
$int_StatisticsTextID = 0;

if (intval($int_TextID) > 0) {

    /**
     * When an Event ID is described by a Text ID
     * To print text of any language that replaces Event information
     * See basic_PrintIncludes.php
     */
    $tmp_LanguageAnd = $str_LanguageAnd;
    $sxTextTableVersion = sx_TextTableVersion;
    $int__EventID = 0;
    if (!empty($_GET['eid'])) {
        $int__EventID = (int) $_GET['eid'];
        $tmp_LanguageAnd = "";
    }
    $sql = " SELECT t.GroupID, t.CategoryID, t.SubCategoryID, t.ThemeID,
        t.Title, t.SubTitle, t.AuthorID, t.Coauthors,
        t.PublishedDate, t.FirstPageMediaURL, t.TopMediaURL, t.FirstPageText,
        a.FirstName, a.LastName, a.Photo, t.UseAuthorPhoto 
        FROM $sxTextTableVersion AS t
        LEFT JOIN text_authors AS a ON t.AuthorID = a.AuthorID
        WHERE TextID = :TextID $tmp_LanguageAnd";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":TextID", $int_TextID, PDO::PARAM_INT);
    $stmt->execute();
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $int_GroupID = $rs["GroupID"];
        $int_CatID = $rs["CategoryID"];
        $int_SubCatID = $rs["SubCategoryID"];
        $int_ThemeID = $rs["ThemeID"];
        $strSubTitle = $rs["SubTitle"];
        $str_MetaTitle = $rs["Title"];
        $int_AuthorID = $rs["AuthorID"];
        $strCoauthors = $rs["Coauthors"];
        $datePublishedDate = $rs["PublishedDate"];
        $strMediaURL = $rs["FirstPageMediaURL"];
        if (empty($strMediaURL)) {
            $strMediaURL = $rs["TopMediaURL"];
        }
        $memoFirstPage = $rs["FirstPageText"];
        $strFirstName = $rs["FirstName"];
        $strLastName = $rs["LastName"];
        $strPhoto = $rs["Photo"];
        $radioUseAuthorPhoto = $rs["UseAuthorPhoto"];
    } else {
        header("Location: index.php?sx=cnf");
        exit();
    }
    $rs = null;
    $stmt = null;

    if (!empty($int_GroupID)) {
        $int_GroupID = (int) $int_GroupID;
    } else {
        $int_GroupID = 0;
    }

    if (!empty($int_CatID)) {
        $int_CatID = (int) $int_CatID;
    } else {
        $int_CatID = 0;
    }

    if (!empty($int_SubCatID)) {
        $int_SubCatID = (int) $int_SubCatID;
    } else {
        $int_SubCatID = 0;
    }

    if (!empty($int_ThemeID)) {
        $int_ThemeID = (int) $int_ThemeID;
    } else {
        $int_ThemeID = 0;
    }

    /**
     * Add author to meta title
     */
    if (return_Filter_Integer($int_AuthorID) > 0) {
        if (!empty($strCoauthors)) {
            $strCoauthors = ", $strCoauthors";
        }
        $str_MetaTitle = $strFirstName . " " . $strLastName . $strCoauthors . ": " . $str_MetaTitle;
        if (!empty($strPhoto) && $radioUseAuthorPhoto) {
            $strMediaURL = $strPhoto;
        }
    }
    /**
     * Select meta image
     */
    if (!empty($strMediaURL) && (strpos($strMediaURL, ".jpg", 0) > 0 || strpos($strMediaURL, ".png", 0) > 0 || strpos($strMediaURL, ".gif", 0) > 0)) {
        if (strpos($strMediaURL, ";", 0) > 0) {
            $strMediaURL = substr($strMediaURL, 0, strpos($strMediaURL, ";", 0) - 1);
        }
        $str_PropertyImage = "images/$strMediaURL";
    }

    /**
     * Metadescription
     */
    if (!empty($strSubTitle)) {
        $str_MetaDescription = sx_Remove_Quotes(strip_tags($strSubTitle));
    } elseif (!empty($memoFirstPage)) {
        $str_MetaDescription = return_Left_Part_FromText($memoFirstPage, 150);
    }

    $str_PropertyType = "article";

    // For statistics
    if (empty($datePublishedDate) || return_Is_Date($datePublishedDate) === false) {
        $dateStatsPublishedDate = date("Y-m-d");
    } else {
        $dateStatsPublishedDate = return_Date_From_Datetime($datePublishedDate);
    }
    // To be deleted, used for old versions of sx_counter.php
    $intStatsTextID = $int_TextID;
    /**
     * The variable $int_StatisticsTextID can be replaced by any ID of used text versions
     *  even from local config pages
     */
    $int_StatisticsTextID = $int_TextID;
}

/**
 * Text classifications
 */

$str_SubCategoryName = "";
if (intval($int_SubCatID) > 0) {
    $sql = "SELECT GroupID, CategoryID, 
            SubCategoryName{$str_LangNr} AS SubCategoryName
        FROM text_subcategories
        WHERE SubCategoryID = :iSubCatID AND Hidden = False ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":iSubCatID", $int_SubCatID, PDO::PARAM_INT);
    $stmt->execute();
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $int_GroupID = $rs["GroupID"];
        $int_CatID = $rs["CategoryID"];
        $str_SubCategoryName = $rs["SubCategoryName"];
    } else {
        header("Location: index.php?sx=x2");
        exit();
    }
    $stmt = null;
    $rs = null;
    if (return_Filter_Integer($int_GroupID) == 0) {
        $int_GroupID = 0;
    }
    if (return_Filter_Integer($int_CatID) == 0) {
        $int_CatID = 0;
    }
    if (!empty($str_SubCategoryName) && $int_TextID == 0) {
        $str_MetaTitle = $str_SubCategoryName;
        $str_MetaDescription = $str_SubCategoryName . " - " . lngTextArchive;
        $str_PropertyType = "articles";
    }
}

$str_CategoryName = "";
if (intval($int_CatID) > 0) {
    $sql = "SELECT GroupID, CategoryName{$str_LangNr} AS CategoryName
        FROM text_categories
        WHERE CategoryID = $int_CatID AND Hidden = False ";
    $stmt = $conn->query($sql);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $int_GroupID = $rs["GroupID"];
        $str_CategoryName = $rs["CategoryName"];
    } else {
        header("Location: index.php?sx=x30");
        exit();
    }
    $rs = null;
    $stmt = null;
    if (return_Filter_Integer($int_GroupID) == 0) {
        $int_GroupID = 0;
    }
    if (!empty($str_CategoryName) && $int_TextID == 0) {
        if (!empty($str_SubCategoryName)) {
            $str_MetaDescription = $str_CategoryName . ", " . $str_MetaDescription;
        } else {
            $str_MetaTitle = $str_CategoryName;
            $str_MetaDescription = $str_CategoryName . " - " . lngTextArchive;
        }
        $str_PropertyType = "articles";
    }
}

$str_GroupName = "";
$radioLoginToReadGroup = false;

if (intval($int_GroupID) > 0) {
    $sql = "SELECT GroupName{$str_LangNr} AS GroupName, LoginToRead
        FROM text_groups
        WHERE GroupID = :iGroupID
        AND Hidden = False $strLoginToReadAnd";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":iGroupID", $int_GroupID, PDO::PARAM_INT);
    $stmt->execute();
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $str_GroupName = $rs["GroupName"];
        $radioLoginToReadGroup = $rs["LoginToRead"];
    } else {
        header("Location: index.php?sx=x4");
        exit();
    }
    $rs = null;
    $stmt = null;

    if (!empty($str_GroupName) && $int_TextID == 0) {
        if (!empty($str_CategoryName)) {
            $str_MetaDescription = $str_GroupName . ", " . $str_MetaDescription;
        } else {
            $str_MetaTitle = $str_GroupName;
            $str_MetaDescription = $str_GroupName . " - " . lngTextArchive;
        }
        $str_PropertyType = "articles";
    }
}

/**
 * AUTHORS
 * Auhor ID is > 0 when Text ID > 0 or when an Author's texts archive is requested
 * Search in text_authors only when an Author's texts archive 
 *   is requested ($int_TextID == 0)
 */

$strAuthorName = "";
if (intval($int_AuthorID) > 0 && $int_TextID == 0) {
    $sql = "SELECT FirstName, LastName, Photo
        FROM text_authors
        WHERE AuthorID = $int_AuthorID AND Hidden = False ";
    $stmt = $conn->query($sql);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $strAuthorName = $rs["FirstName"] . " " . $rs["LastName"];
        $strAuhtorPhoto = $rs["Photo"];
    } else {
        header("Location: index.php?sx=x5");
        exit();
    }
    $rs = null;

    $str_MetaTitle = $strAuthorName . " - " . lngTextArchive . " - " . $str_MetaTitle;
    $str_MetaDescription = $str_MetaTitle;
    $str_PropertyType = "articles";
    if (!empty($strAuhtorPhoto)) {
        $str_PropertyImage = "images/" . $strAuhtorPhoto;
    }
}

/**
 * THEMES
 * Theme ID is > 0 when Text ID > 0 and the text is part of a theme
 *   or when a Themes's texts archive is requested
 * Search in themes in bothe case, to get ID and Name
 *   but inform mettags only in second case (when $int_TextID == 0)
 */
$intThemeGroupID = 0;
$strThemeName = "";
if ($radio_ShowTextsByThemes && intval($int_ThemeID) > 0) {
    $sql = "SELECT ThemeName{$str_LangNr} AS ThemeName,
            ThemeGroupID
        FROM themes
        WHERE ThemeID = $int_ThemeID AND Hidden = False ";
    $stmt = $conn->query($sql);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $strThemeName = $rs["ThemeName"];
        $intThemeGroupID = $rs["ThemeGroupID"];
    }
    $stmt = null;
    $rs = null;

    if (!empty($strThemeName) && $int_TextID == 0) {
        $str_MetaTitle = $strThemeName . " - " . lngTextArchive;
        $str_MetaDescription = $str_MetaTitle;
        $str_PropertyType = "articles";
    }
    if (!empty($intThemeGroupID)) {
        $intThemeGroupID = (int) $intThemeGroupID;
    } else {
        $intThemeGroupID = 0;
    }
}

/**
 * ABOUT TEXTS - For SITE DESCRIPTION
 */

$int_AboutGroupID = 0;
$int_AboutID = 0;
if (isset($_GET["aboutid"])) {
    $int_AboutID = $_GET["aboutid"];
    if (return_Filter_Integer($int_AboutID) == 0) {
        $int_AboutID = 0;
    }
} elseif (isset($_GET["agid"])) {
    $int_AboutGroupID = $_GET["agid"];
    if (return_Filter_Integer($int_AboutGroupID) == 0) {
        $int_AboutGroupID = 0;
    }
}

if ($radio_UseTextsAbout) {
    if (intval($int_AboutID) > 0) {
        $sql = "SELECT a.Title, a.SubTitle, 
            a.MediaTopURL, a.MediaRightURL, 
            a.AboutNotes, a.AboutGroupID,
            g.GroupName{$str_LangNr} AS AboutGroupName
        FROM about AS a
            LEFT JOIN about_groups AS g
            ON a.AboutGroupID = g.AboutGroupID
        WHERE AboutID = ? AND a.Hidden = False 
            AND (g.Hidden = False OR g.Hidden IS NULL) ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$int_AboutID]);
        $rs = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($rs) {
            $str_MetaTitle = $rs["Title"];
            $strSubTitle = $rs["SubTitle"];
            $strMediaTopURL = $rs["MediaTopURL"];
            $strMediaRightURL = $rs["MediaRightURL"];
            $memoAboutNotes = $rs["AboutNotes"];
            $int_AboutGroupID = $rs["AboutGroupID"];
            $str_AboutGroupName = $rs["AboutGroupName"];
        }
        $rs = null;
        $stmt = null;

        if (return_Filter_Integer($int_AboutGroupID) == 0) {
            $int_AboutGroupID = 0;
        }

        /**
         * Meta title
         */
        if (!empty($str_AboutGroupName)) {
            $str_MetaTitle .= ' - ' . $str_AboutGroupName;
        }
        /**
         * Meta description
         */
        if (!empty($strSubTitle)) {
            $str_MetaDescription = sx_Remove_Quotes(strip_tags($strSubTitle));
        } elseif (!empty($memoAboutNotes)) {
            $str_MetaDescription = return_Left_Part_FromText($memoAboutNotes, 140);
        }
        $str_PropertyType = "article";
        /**
         * Meta image
         */

        $strMediaURL = "";
        if (!empty($strMediaTopURL)) {
            $strMediaURL = $strMediaTopURL;
        } elseif (!empty($strMediaRightURL)) {
            $strMediaURL = $strMediaRightURL;
        }
        if (
            !empty($strMediaURL)
            && (strpos($strMediaURL, ".jpg", 0) > 0
                || strpos($strMediaURL, ".png", 0) > 0
                || strpos($strMediaURL, ".gif", 0) > 0)
        ) {
            if (strpos($strMediaURL, ";", 0) > 0) {
                $strMediaURL = substr($strMediaURL, 0, strpos($strMediaURL, ";", 0) - 1);
            }
            $str_PropertyImage = "images/" . $strMediaURL;
        }
    } elseif (intval($int_AboutGroupID) > 0) {
        $sql = "SELECT GroupName{$str_LangNr} AS AboutGroupName
        FROM about_groups
        WHERE AboutGroupID = ? AND (Hidden = False OR Hidden IS NULL) ";
        $rs = $conn->prepare($sql);
        $rs->execute([$int_AboutGroupID]);
        $str_AboutGroupName = $rs->fetchColumn();
        $rs = null;
        if (!empty($str_AboutGroupName)) {
            $str_MetaTitle = $str_AboutGroupName;
            $str_MetaDescription = $str_MetaTitle;
            $str_PropertyType = "article";
        }
    }
}

if (empty($str_PropertyImage) && defined('STR_ReplaceListImage') && !empty(STR_ReplaceListImage)) {
    $str_PropertyImage = "images/" . STR_ReplaceListImage;
}

/**
 * For Conferences
 */

if (sx_includeConferences) {
    $sql = "SELECT 
    UseConferences,
    ConferencesMenuTitle,
    ComingConferencesTitle,
    PassedConferencesTitle,
    LoginToViewConferenceAttachments,
    LoginToViewPaperAttachments,
    RegisterToViewPaperAttachments,
    ShowAttachmentsAlsoInCards,
    ProgramInTable,
    ProgramInTabs,
    ProgramInSubtabs,
    ProgramInTabsForMobile
FROM conf_setup $str_LanguageWhere";
    $stmt = $conn->query($sql);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $radio_UseConferences = $rs['UseConferences'];
        $str_ConferencesMenuTitle = $rs['ConferencesMenuTitle'];
        $str_ComingConferencesTitle = $rs['ComingConferencesTitle'];
        $str_PassedConferencesTitle = $rs['PassedConferencesTitle'];
        $radio_LoginToViewConferenceAttachments = $rs["LoginToViewConferenceAttachments"];
        $radio_LoginToViewPaperAttachments = $rs["LoginToViewPaperAttachments"];
        $radio_RegisterToViewPaperAttachments = $rs["RegisterToViewPaperAttachments"];
        $radio_ShowAttachmentsAlsoInCards = $rs["ShowAttachmentsAlsoInCards"];
        $radio_ProgramInTable = $rs["ProgramInTable"];
        $radio_ProgramInTabs = $rs["ProgramInTabs"];
        $radio_ProgramInSubtabs = $rs["ProgramInSubtabs"];
        $radio_ProgramInTabsForMobile = $rs["ProgramInTabsForMobile"];
    }
    $rs = null;
    $stmt = null;
}

/**
 * ========================================
 *    Get constant for Basic Text Variables that are also used in functions
 * ========================================
 */

define("int_TextID", $int_TextID);
define("int_GroupID", $int_GroupID);
define("int_CatID", $int_CatID);
define("int_SubCatID", $int_SubCatID);
define("int_ThemeID", $int_ThemeID);
define("int_AuthorID", $int_AuthorID);
define("int_ThemeGroupID", $intThemeGroupID);


/**
 * STOP SLIDER and other applications runnng IN FIRST PAGE when archives are open
 */
$radio_DefaultPage = true;
if (intval(int_GroupID) > 0 || intval($int_Year) > 0) {
    $radio_DefaultPage = false;
}

/**
 * ========================================
 *    Check for and GET ACTIVE Applications
 * ========================================
 */

/**
 * FILM-LIBRARY - Not Used
 */
$radio_UseFilmLibrary = false;
if (sx_includeFilms) {
    $sql = "SELECT UseFilmLibrary, FilmMenuTitle
        FROM film_setup $str_LanguageWhere";
    $stmt = $conn->query($sql);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $radio_UseFilmLibrary = $rs["UseFilmLibrary"];
        $str_FilmMenuTitle = $rs["FilmMenuTitle"];
    }
    $rs = null;
}

/**
 * GALLERY
 */
$radio_UseGallery = false;
if (sx_includeGallery) {
    $sql = "SELECT UseGallery, GalleryMenuTitle
        FROM gallery_setup  $str_LanguageWhere";
    $stmt = $conn->query($sql);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $radio_UseGallery = $rs["UseGallery"];
        $str_GalleryMenuTitle = $rs["GalleryMenuTitle"];
    }
    $rs = null;
}

/**
 * Folder GALLERY
 */
$radio_UseFolderGallery = false;
$radio_UseSeparateGallery = false;

if (sx_includeFolderGallery === true) {
    $sql = "SELECT UseFolderGallery,
            UseSeparateGallery, FolderGalleryMenuTitle
        FROM folder_gallery_setup  $str_LanguageWhere";
    $stmt = $conn->query($sql);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $radio_UseFolderGallery = $rs["UseFolderGallery"];
        $radio_UseSeparateGallery = $rs["UseSeparateGallery"];
        $str_FolderGalleryMenuTitle = $rs["FolderGalleryMenuTitle"];
    }
    $rs = null;
}

/**
 * MULTIMEDIA GALLERY
 */
$radio_UseMedia = false;
if (sx_includeMMGallery === true) {
    $sql = "SELECT UseMedia, MediaMenuTitle
        FROM media_setup  $str_LanguageWhere";
    $stmt = $conn->query($sql);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $radio_UseMedia = $rs["UseMedia"];
        $str_MediaMenuTitle = $rs["MediaMenuTitle"];
    }
    $rs = null;
}

/**
 * PDF ARCHIVES
 */
$radio_UsePDF = false;
if (sx_includePDFArchive === true) {
    $sql = "SELECT UsePDF, PDFMenuTitle, MenuTitleHidden
        FROM pdf_setup  $str_LanguageWhere";
    $stmt = $conn->query($sql);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $radio_UsePDF = $rs["UsePDF"];
        $str_PDFMenuTitle = $rs["PDFMenuTitle"];
        $str_MenuTitleHidden = $rs["MenuTitleHidden"];
    }
    $rs = null;
}

/**
 * FORUM
 * Variables comes from sx_design.php and the table site_config_apps
 * ROMOVE: Can be removed to the Forum Page, 
 *   as requested informtion is not used in othe pages
 */
$radio_UseForum = false;
if (sx_includeForum && $radio_IncludeForum) {
    $sql = "SELECT UseForum, ForumMenuTitle, LoginToReadForum, ShowRecentInForum, RecentInForumTitle
        FROM forum_setup  $str_LanguageWhere";
    $stmt = $conn->query($sql);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $radio_UseForum = $rs["UseForum"];
        $str_ForumMenuTitle = $rs["ForumMenuTitle"];
        $radio_LoginToReadForum = $rs["LoginToReadForum"];
        $radio_ShowRecentInForum = $rs["ShowRecentInForum"];
        $str_RecentInForumTitle = $rs["RecentInForumTitle"];
    }
    $rs = null;
}

/**
 * USERS Login 
 * Variables comes from design and the table site_config_basic
 */
$radio_UseUsersLogin = false;
if (sx_includeUsersLogin && $radio_IncludeUsersLogin) {
    $sql = "SELECT UseUsersLogin, UsersTextMenuTitle
        FROM users_setup  $str_LanguageWhere";
    $stmt = $conn->query($sql);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $radio_UseUsersLogin = $rs["UseUsersLogin"];
        $str_UsersTextMenuTitle = $rs["UsersTextMenuTitle"]; // Not Used
    }
    $rs = null;
}

/**
 * Login for Conference participants 
 */
$radio_UseParticipantsLogin = false;
if (sx_includeParticipantsLogin) {
    $sql = "SELECT UseParticipantsLogin
        FROM conf_participants_setup  $str_LanguageWhere";
    $stmt = $conn->query($sql);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $radio_UseParticipantsLogin = $rs["UseParticipantsLogin"];
    }
    $rs = null;
}

/**
 * MEMBERS area - Not Used
 * - INFORMATION about the organisation
 */
$radio_UseMembersArea = false;
$radio_UseMembersList = false;
if (sx_includeMembersArea === true) {
    $sql = "SELECT UseMembersArea, MembersAreaTitle, UseMembersList, MembersListTitle,
        UseBoardList, BoardListTitle, UseClubsList, ClubsListTitle
        FROM members_area_setup $str_LanguageWhere";
    $stmt = $conn->query($sql);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $radio_UseMembersArea = $rs["UseMembersArea"];
        $str_MembersAreaTitle = $rs["MembersAreaTitle"];
        $radio_UseMembersList = $rs["UseMembersList"];
        $str_MembersListTitle = $rs["MembersListTitle"];
        $radio_UseBoardList = $rs["UseBoardList"];
        $str_BoardListTitle = $rs["BoardListTitle"];
        $radio_UseClubsList = $rs["UseClubsList"];
        $str_ClubsListTitle = $rs["ClubsListTitle"];
    }
    $rs = null;
}

/**
 * Events Calendar
 */
$radio_UseEvents = false;
$radio_UseEventsSlider = false;
$int_NumberEventsInList = 0;
if (sx_includeEvents) {
    $sql = "SELECT UseEvents, EventsMenuTitle,
        UseEventsByWeek, EventsByWeekTitle,
        UseEventsByMonth, EventsByMonthTitle,
        UseEventsByCalendar, UseEventsByBigCalendar, EventsByCalendarTitle,
        UseEventsList, EventsListTitle, NumberEventsInList,UseEventsSlider
        FROM events_setup $str_LanguageWhere";
    $stmt = $conn->query($sql);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $radio_UseEvents = $rs["UseEvents"];
        $str_EventsMenuTitle = $rs["EventsMenuTitle"];
        $radio_UseEventsByWeek = $rs["UseEventsByWeek"];
        $str_EventsByWeekTitle = $rs["EventsByWeekTitle"];
        $radio_UseEventsByMonth = $rs["UseEventsByMonth"];
        $str_EventsByMonthTitle = $rs["EventsByMonthTitle"];
        $radio_UseEventsByCalendar = $rs["UseEventsByCalendar"];
        $radio_UseEventsByBigCalendar = $rs["UseEventsByBigCalendar"];
        $str_EventsByCalendarTitle = $rs["EventsByCalendarTitle"];
        $radio_UseEventsList = $rs["UseEventsList"];
        $str_EventsListTitle = $rs["EventsListTitle"];
        $int_NumberEventsInList = $rs["NumberEventsInList"];
        $radio_UseEventsSlider = $rs["UseEventsSlider"];
    }
    $rs = null;
    if (return_Filter_Integer($int_NumberEventsInList) == 0) {
        $int_NumberEventsInList = 4;
    }
}
define("int_NumberEventsInList", $int_NumberEventsInList);

/**
 * Site Information for all emails
 */
$strSiteInfo = $str_SiteTitle;
//$strSiteMailFoter = $str_SiteTitle;

if (!empty($str_SiteAddress)) {
    $strSiteInfo .= "<br>{$str_SiteAddress}";
}
if (!empty($str_SitePostalCode)) {
    $strSiteInfo .= "<br>$str_SitePostalCode";
}
if (!empty($str_SiteCity)) {
    $strSiteInfo .= ", $str_SiteCity";
}
if (!empty($str_SitePhone)) {
    $strSiteInfo .= "<br>" . lngPhone . ": " . $str_SitePhone;
}
if (!empty($str_SiteFax)) {
    $strSiteInfo .= "<br>" . lngFax . ": " . $str_SiteFax;
}

define("str_SiteInfo", $strSiteInfo);

/**
 * Site information for footer
 */

if (empty($str_SiteAdministrator)) {
    $str_SiteAdministrator = lngSiteAdministrator;
}

$str_Site_AllTelephones = "";
if ($str_SitePhone != "") {
    $str_Site_AllTelephones = lngPhone . ": " . $str_SitePhone . "<br>";
}
if ($str_SiteMobile != "") {
    $str_Site_AllTelephones .= lngMobile . ": " . $str_SiteMobile . "<br>";
}
if ($str_SiteFax != "") {
    $str_Site_AllTelephones .= lngFax . ": " . $str_SiteFax;
}

$str_Site_FullAddress = "";
if ($str_SiteAddress != "") {
    $str_Site_FullAddress = $str_SiteAddress . "<br>";
}
if ($str_SitePostalCode != "") {
    $str_Site_FullAddress .= $str_SitePostalCode . ", ";
}
if ($str_SiteCity != "") {
    $str_Site_FullAddress .= $str_SiteCity . "<br>";
}
