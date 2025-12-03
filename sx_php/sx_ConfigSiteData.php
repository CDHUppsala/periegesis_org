<?php

/**
 * Basic information about the site (Titles, logos, emails. etc)
 * Used in all ocasions when the full configuration file (sx_Config.php) is Not Needed
 * Never use it together with sx_Config.php
 */
$int_LanguageID = 0;
if (sx_includeMultilinqual) {
    $sql = "SELECT LanguageID
        FROM languages 
        WHERE LanguageCode = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([sx_CurrentLanguage]);
    $int_LanguageID = $stmt->fetchColumn();

    if (intval($int_LanguageID) == 0) {
        $int_LanguageID = 0;
    }
}
$sql = "SELECT SiteTitle, LogoImage, LogoImageSmall, LogoImageEmail,
    SiteAddress, SitePostalCode, SiteCity, 
    SitePhone, SiteMobile, SiteEmail 
    FROM site_setup WHERE (LanguageID = " . $int_LanguageID . " OR LanguageID = 0) ";
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_NUM);
if (is_array($rs)) {
    $str_SiteTitle = $rs[0];
    $strLogoImage = $rs[1];
    $str_LogoImageSmall = $rs[2];
    $str_LogoImageEmail = $rs[3];
    $strSiteAddress = $rs[4];
    $strSitePostalCode = $rs[5];
    $strSiteCity = $rs[6];
    $strSitePhone = $rs[7];
    $strSiteMobile = $rs[8];
    $str_SiteEmail = $rs[9];
}
$rs = Null;
define('str_SiteTitle', $str_SiteTitle);
define('str_SiteEmail', $str_SiteEmail);

if (empty($str_LogoImageEmail)) {
    $str_LogoImageEmail = $str_LogoImageSmall;
}
if (empty($str_LogoImageEmail)) {
    $str_LogoImageEmail = $strLogoImage;
}

$strSiteInfo = $str_SiteTitle;
if (!empty($strSitePostalCode)) {
    $strSiteInfo .= '<br>' . $strSiteAddress;
}
if (!empty($strSitePostalCode)) {
    $strSiteInfo .= '<br>' . $strSitePostalCode . " " . $strSiteCity;
}
if (!empty($strSitePhone) || !empty($strSiteMobile)) {
    $strSiteInfo .= '<br>'. $strSitePhone . " " . $strSiteMobile;
}
define("str_SiteInfo", $strSiteInfo);

/**
 * Get Used Applications
 */

$sql = "SELECT IncludeReports, ReportsLinkTitle,
    IncludeBooks, BooksLinkTitle, 
    IncludeFAQ, FAQLinkTitle, 
    UseQuiz, QuizTitle, 
    UsePoll, PollTitle, 
    UseSurvey, SurveyTittle, IncludeWeekProgram, 
    IncludeForum, ForumLinkTitle,
    IncludeMenu, MenuLinkTitle,
    IncludeMusic, MusicLinkTitle,
    IncludeFilms, FilmsLinkTitle
FROM site_config_apps 
WHERE (LanguageID = " . $int_LanguageID . " OR LanguageID = 0)";
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
    $radio_IncludeReports = $rs["IncludeReports"];
    $str_ReportsLinkTitle = $rs["ReportsLinkTitle"];

    $radio_IncludeBooks = $rs["IncludeBooks"];
    $str_BooksLinkTitle = $rs["BooksLinkTitle"];

    $radio_IncludeFAQ = $rs["IncludeFAQ"];
    $str_FAQLinkTitle = $rs["FAQLinkTitle"];
    $radio_IncludeForum = $rs["IncludeForum"];
    $str_ForumLinkTitle = $rs["ForumLinkTitle"];
    $radio_IncludeMenu = $rs["IncludeMenu"];
    $str_MenuLinkTitle = $rs["MenuLinkTitle"];
    $radio_IncludeMusic = $rs["IncludeMusic"];
    $str_MusicLinkTitle = $rs["MusicLinkTitle"];
    $radio_IncludeFilms = $rs["IncludeFilms"];
    $str_FilmsLinkTitle = $rs["FilmsLinkTitle"];

    $radio_UseQuiz = $rs["UseQuiz"];
    $str_QuizTitle = $rs["QuizTitle"];
    $radio_UsePoll = $rs["UsePoll"];
    $str_PollTitle = $rs["PollTitle"];
    $radio_UseSurvey = $rs["UseSurvey"];
    $str_SurveyTittle = $rs["SurveyTittle"];
    $radio_IncludeWeekProgram = $rs["IncludeWeekProgram"];
}
