<?php
include __DIR__ . "/sx_config.php";
include __DIR__ . "/basic_MediaFunctions.php";
include __DIR__ . "/basic_PrintFunctions.php";

if (isset($_GET["export"])) {
    $strExport = $_GET["export"];
    $strSiteURL = $_SERVER["HTTP_HOST"];
    $sQuery = $_SERVER["QUERY_STRING"];
    $pos = strpos($sQuery, "&");
    if ($pos > 0) {
        $sQuery = substr($sQuery, 0, $pos);
        $sQuery = str_replace("=", "_", $sQuery);
        $strSiteURL .= "_" . $sQuery;
    }
} else {
    $strExport = "";
}

if (!empty($strExport)) {
    if ($strExport == "word") {
        header('Content-Description: File Transfer');
        header("Content-type: application/msword; charset=utf-8");
        header("Content-Disposition: attachment;Filename=" . $strSiteURL . ".doc");
    }
    if ($strExport == "html") {
        header("Content-Type: text/html");
        header("Content-Disposition: attachment; filename=" . $strSiteURL . ".html;");
        header("Content-Transfer-Encoding: binary");
    }
}

$radioPrintPage = false;

$sPrint = null;
if (isset($_GET["print"])) {
    $sPrint = $_GET["print"];
}
if (!empty($sPrint)) {
    $radioPrintPage = true;
}

$intTextID = 0;
if (isset($_GET["tid"])) {
    $intTextID = (int) ($_GET["tid"]);
}
if ($intTextID > 0) {
    $radioPrintPage = true;
}

$intAboutID = 0;
if (isset($_GET["aboutid"])) {
    $intAboutID = (int) ($_GET["aboutid"]);
}
if ($intAboutID > 0) {
    $radioPrintPage = true;
}

$intArticleID = 0;
if (isset($_GET["aid"])) {
    $intArticleID = (int) ($_GET["aid"]);
}
if ($intArticleID > 0) {
    $radioPrintPage = true;
}

$intForumArticleID = 0;
if (isset($_GET["articleID"])) {
    $intForumArticleID = (int) ($_GET["articleID"]);
}
if ($intForumArticleID > 0) {
    $radioPrintPage = true;
}

$intCourseID = 0;
if (isset($_GET["courseid"])) {
    $intCourseID = (int) ($_GET["courseid"]);
}
if ($intCourseID > 0) {
    $radioPrintPage = true;
}

$int_ReportID = 0;
if (isset($_GET["reportid"])) {
    $int_ReportID = (int) ($_GET["reportid"]);
}
if ($int_ReportID > 0) {
    $radioPrintPage = true;
}

$int_SubjectID = 0;
if (isset($_GET["subjectid"])) {
    $int_SubjectID = (int) ($_GET["subjectid"]);
}
if ($int_SubjectID > 0) {
    $radioPrintPage = true;
}

$intMemberID = 0;
if (isset($_GET["mid"])) {
    $intMemberID = (int) ($_GET["mid"]);
}
if ($intMemberID > 0) {
    $radioPrintPage = true;
}

$int_ConferenceID = 0;
if (isset($_GET["confid"])) {
    $int_ConferenceID = (int) ($_GET["confid"]);
}
if ($int_ConferenceID > 0) {
    $radioPrintPage = true;
}

?>

<!DOCTYPE html>
<html lang="<?= sx_CurrentLanguage ?>">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= str_SiteTitle ?></title>
    <style>
        html,
        body,
        table {
            font-family: "Trebuchet MS", Tahoma, Helvetica, sans-serif;
            font-size: 14pt;
            line-height: 140%;
        }

        h1,
        h2,
        h3,
        h4 {
            line-height: 120%;
            padding: 0;
            margin: 10pt 0
        }

        h1 {
            font-size: 24pt;
        }

        h2 {
            font-size: 20pt;
        }

        h3 {
            font-size: 16pt;
        }

        h4 {
            font-size: 12pt;
        }

        th {
            background: #ddd;
            color: #000;
            vertical-align: top;
        }

        td {
            vertical-align: top;
            padding: 2pt;
            border-bottom: 1px solid #ddd;
            ;
        }

        hr {
            clear: both;
        }

        a {
            text-decoration: none;
        }

        li {
            padding: 6pt;
        }

        img {
            width: 100%;
            height: auto;
        }

        .maxWidth {
            width: 40%;
            float: left;
        }

        .clear,
        .clearSpace,
        .line {
            clear: both;
            width: 100%;
        }
    </style>
</head>
<?php

if ($radioPrintPage == false) {
    echo '<h2>No record found. Please contact the administration of the site</h2>';
    exit();
}

if ($sPrint == "books") {
    include __DIR__ . "/inBooks/sx_PrintBookList.php";
} elseif ($sPrint == "films") {
    include __DIR__ . "/inFilms/sx_PrintFilmList.php";
} elseif ($sPrint == "weekly") {
    include __DIR__ . "/inProgramWeek/print_weekly.php";
} elseif ($sPrint == "events") {
    include __DIR__ . "/inEvents/sx_PrintEvents.php";
} elseif ($sPrint == "privacy" || $sPrint == "conditions" || $sPrint == "cookies") {
    include __DIR__ . "/sx_PrintSiteInfo.php";
} elseif ($sPrint == "dinnermenu" || $sPrint == "lunchmenu") {
    include __DIR__ . "/inMenu/sx_PrintMenu.php";
} elseif (intval($intAboutID) > 0) {
    include __DIR__ . "/inAbout/sx_print_about.php";
} elseif (intval($intCourseID) > 0) {
    include __DIR__ . "/inCourses/print_course.php";
} elseif (intval($int_ReportID) > 0) {
    include __DIR__ . "/inReports/print_report.php";
} elseif (intval($int_SubjectID) > 0) {
    include __DIR__ . "/inFAQ/sx_PrintAnswers.php";
} elseif (intval($intTextID) > 0) {
    include __DIR__ . "/inTexts/sx_print_text.php";
} elseif (intval($intMemberID) > 0) {
    include __DIR__ . "/inMembers/sx_PrintMember.php";
} elseif (intval($intForumArticleID) > 0) {
    include __DIR__ . "/inForum/sx_PrintForum.php";
} elseif (intval($intArticleID) > 0) {
    include __DIR__ . "/inArticles/sx_print_article.php";
} elseif (intval($int_ConferenceID) > 0) {
    include __DIR__ . "/inConferences/print_program.php";
} else { ?>
    <h2>No record found. Please contact the administration of the site</h2>
<?php }

$conn = null;

?>