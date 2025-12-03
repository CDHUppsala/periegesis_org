<?php

/**
 * Placed on the top of all Conference pages
 * Get information from Paper ID, Session ID and Conference ID
 * to change the content of metatags from sx_config.php
 */

$m_Title = null;
$m_SubTitle = null;
$m_MediaURL = null;

$int_PaperID = 0;
if (isset($_GET['paperid']) && intval($_GET['paperid']) > 0) {
    $int_PaperID = intval($_GET['paperid']);
}

$int_SessionID = 0;
if (isset($_GET['sesid']) && intval($_GET['sesid']) > 0) {
    $int_SessionID = intval($_GET['sesid']);
}
$int_ConferenceID = 0;
if (isset($_GET['confid']) && intval($_GET['confid']) > 0) {
    $int_ConferenceID = intval($_GET['confid']);
}

if ($int_PaperID > 0) {
    $sql = "SELECT
        ConferenceID,
        SessionID,
        PaperTitle,
        PaperSubTitle,
        PaperAuthors,
        Speakers,
        MediaURL,
        Abstract
    FROM conf_papers
    WHERE PaperID = ?
    AND Hidden = 0 ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$int_PaperID]);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $int_ConferenceID = $rs['ConferenceID'];
        $int_SessionID = $rs['SessionID'];
        $m_Title = $rs['PaperTitle'];
        $m_SubTitle = $rs['PaperSubTitle'];
        $sPaperAuthors = $rs['PaperAuthors'];
        $sSpeakers = $rs['Speakers'];
        $m_MediaURL = $rs['MediaURL'];
        $sAbstract = $rs['Abstract'];

        if (!empty($sPaperAuthors)) {
            $m_Title .= ", " . $sPaperAuthors;
        }
        if (!empty($sSpeakers)) {
            $m_Title .= ", " . $sSpeakers;
        }
        if (empty($m_SubTitle) && !empty($sAbstract)) {
            $m_SubTitle = return_Left_Part_FromText($sAbstract, 160);
        }
    }else{
        header('Location: index.php');
        exit();
    }
    $rs = null;
    $stmt = null;
}

$date_SessionDate = null;
$str_WebinarURL = "";

if (intval($int_SessionID) > 0) {
    $sql = "SELECT
        ConferenceID,
        SessionTitle,
        SessionSubTitle,
        SessionDate,
        StartTime,
        PlaceName,
        EndTime,
        MediaURL,
        PDFAttachements,
        SessionWebinarURL,
        Notes
    FROM conf_sessions
    WHERE SessionID = ? 
    AND Hidden = 0 ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$int_SessionID]);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $int_ConferenceID = $rs["ConferenceID"];
        $str_SessionTitle = $rs["SessionTitle"];
        $str_SessionSubTitle = $rs["SessionSubTitle"];
        $date_SessionDate = $rs["SessionDate"];
        $date_SessionStartTime = return_Time_Minutes($rs["StartTime"]);
        $date_SessionEndTime = return_Time_Minutes($rs["EndTime"]);
        $str_SessionPlaceName = $rs["PlaceName"];
        $str_SessionMediaURL = $rs["MediaURL"];
        $str_SessionPDFAttachements = $rs["PDFAttachements"];
        $str_SessionWebinarURL = $rs["SessionWebinarURL"];
        $memo_SessionNotes = $rs["Notes"];

        if ($int_PaperID == 0) {
            $m_Title = $str_SessionTitle;
            $m_SubTitle = $str_SessionSubTitle;
            $m_MediaURL = $str_SessionMediaURL;
        }
        if (!empty($str_SessionWebinarURL)) {
            $str_WebinarURL = $str_SessionWebinarURL;
        }
    }else{
        header('Location: index.php');
        exit();
    }

    $rs = null;
    $stmt = null;
}

if (intval($int_ConferenceID) > 0) {
    $sql = "SELECT 
        Title,
        SubTitle,
        StartDate,
        MetaDescription,
        EndDate,
        MediaURL,
        ConferenceWebinarURL
    FROM conferences
    WHERE ConferenceID = ? 
    AND Hidden = 0 ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$int_ConferenceID]);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $str_ConferenceTitle = $rs["Title"];
        $str_ConferenceSubTitle = $rs["SubTitle"];
        $strMetaDescription = $rs["MetaDescription"];
        $date_ConferenceStartDate = $rs["StartDate"];
        $date_ConferenceEndDate = $rs["EndDate"];
        $str_ConferenceMediaURL = $rs["MediaURL"];
        $str_ConferenceWebinarURL = $rs["ConferenceWebinarURL"];

        if (intval($int_PaperID) == 0 && intval($int_SessionID) == 0) {
            $m_Title = $str_ConferenceTitle;
            if (!empty($strMetaDescription)) {
                $m_SubTitle = $strMetaDescription;
            } else {
                $m_SubTitle = $str_ConferenceSubTitle;
            }
            $m_MediaURL = $str_ConferenceMediaURL;
        }
        if (empty($str_WebinarURL) && !empty($str_ConferenceWebinarURL)) {
            $str_WebinarURL = $str_ConferenceWebinarURL;
        }
    }else{
        header('Location: index.php');
        exit();
    }
    $rs = null;
    $stmt = null;
}

if (!empty($m_MediaURL) && (strpos($m_MediaURL, ".jpg", 0) > 0 || strpos($m_MediaURL, ".png", 0) > 0 || strpos($m_MediaURL, ".gif", 0) > 0)) {
    if (strpos($m_MediaURL, ";", 0) > 0) {
        $m_MediaURL = substr($m_MediaURL, 0, strpos($m_MediaURL, ";", 0) - 1);
    }
    $str_PropertyImage = "/images/" . $m_MediaURL;
}

if (!empty($m_Title)) {
    $str_MetaTitle = $m_Title;
}

if (!empty($m_SubTitle)) {
    $str_MetaDescription = $m_SubTitle;
}
