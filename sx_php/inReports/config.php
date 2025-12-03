<?php

if (!$radio_IncludeReports || !sx_includeReports) {
    Header("Location: index.php");
    exit();
}

function sx_getProjects($id = 0)
{
    $conn = dbconn();
    $strWhere = "";
    $strOrderBy = " ORDER BY Sorting DESC, InsertDate ";
    if (intval($id) > 0) {
        $strWhere = " AND ProjectID = ? ";
        $strOrderBy = "";
    }

    $sql = "SELECT 
	ProjectID,
    ProjectName" . str_LangNr . " AS ProjectName,
    ProjectSubName" . str_LangNr . " AS ProjectSubName,
    MediaTopURL,
    ImagesFromFolder,
    ProjectNotes
    FROM report_projects 
    WHERE Hidden = False " . $strWhere . $strOrderBy;
    $stmt = $conn->prepare($sql);
    if (intval($id) > 0) {
        $stmt->execute([$id]);
        $rs = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = null;
    } else {
        $stmt->execute();
        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;
    }
    return $rs;
}


$rs = null;
$sql = " SELECT 
    LoginToRead,
    ReportMenuTitle,
    ReportNavigationTitle,
    ReportFirstPageTitle,
    ReportNotes
    FROM report_setup " . str_LanguageWhere;
$stmt = $conn->prepare($sql);
$stmt->execute();
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt = null;

if (is_array($rs)) {
    $radioLoginToRead = $rs["LoginToRead"];
    $strReportMenuTitle = $rs["ReportMenuTitle"];
    $strReportNavigationTitle = $rs["ReportNavigationTitle"];
    $strReportFirstPageTitle = $rs["ReportFirstPageTitle"];
    $memoReportNotes = $rs["ReportNotes"];

    /**
     * Change Metatag Variables
     */

    if (!empty($strReportNavigationTitle)) {
        if (!empty($str_MetaTitle)) {
            $str_MetaTitle = $strReportNavigationTitle . " - " . $str_MetaTitle;
        } else {
            $str_MetaTitle = $strReportNavigationTitle;
        }
    }

    if (!empty($strReportFirstPageTitle)) {
        if (!empty($str_MetaDescription)) {
            $str_MetaDescription = $strReportFirstPageTitle . " - " . $str_MetaDescription;
        } else {
            $str_MetaDescription = $strReportFirstPageTitle;
        }
    }
}
$rs = null;
if (empty($strReportFirstPageTitle)) {
    $strReportFirstPageTitle = $str_ReportsNavTitle;
}

/**
 * Get requested IDs
 */

$int_ProjectID = 0;
if (isset($_GET["projectid"]) && is_numeric($_GET["projectid"])) {
    $int_ProjectID = $_GET["projectid"];
}

$int_ReportID = 0;
if (isset($_GET["reportid"]) && is_numeric($_GET["reportid"])) {
    $int_ReportID = $_GET["reportid"];
}

/**
 * Basic queries to Change Metatag Variables
 */

if ($int_ProjectID > 0) {
    $rs_Projects = sx_getProjects($int_ProjectID);
    if (!empty($rs_Projects)) {
        $str_MetaTitle = $rs_Projects["ProjectName"];
        $str_PropertyType = "Research Report";
        if (!empty($rs_Projects["ProjectSubName"])) {
            $str_MetaDescription = $rs_Projects["ProjectSubName"];
        } else {
            $str_MetaDescription = return_Left_Part_FromText($rs_Projects["ProjectNotes"], 250);
        }
        if (!empty($rs_Projects["MediaTopURL"])) {
            $strTemp = $rs_Projects["MediaTopURL"];
            if (strpos($strTemp, ";") > 0) {
                $strTemp = trim(explode(";", $strTemp)[0]);
            }
            $str_PropertyImage = $strTemp;
        }
    }
    $rs_Projects = null;
}

/**
 * The variable $rsReport is also used in report.php and to Change Metatag Variables
 */

$rsReport = null;
if ($int_ReportID > 0) {
    $sql = "SELECT
	r.ProjectID,
	p.ProjectName" . str_LangNr . " AS ProjectName,
	p.ProjectSubName" . str_LangNr . " AS ProjectSubName,
	r.ChapterName,
	r.SubChapterName,
	r.Title,
	r.SubTitle,
	r.InsertDate,
	r.MediaTopURL,
	r.ImagesFromFolder,
	r.MediaTopNotes,
	r.MediaRightURL,
	r.MediaRightNotes,
	r.PDFArchiveID,
	r.FilesForDownload,
	r.ReportNotes
	FROM report_projects AS p 
		INNER JOIN reports as r
		ON p.ProjectID = r.ProjectID
	WHERE r.ReportID = ? AND r.Hidden = False " . str_LanguageAnd ."
    ORDER BY SortingChapters DESC, SortingSubChapters DESC, SortingTexts DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$int_ReportID]);
    $rsReport = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = null;

    /**
     * Change Metatag Variables
     */

    if (is_array($rsReport)) {
        $str_MetaTitle = $rsReport["Title"] . " - " . $rsReport["ProjectName"];
        $str_PropertyType = "Research Article";
        if (!empty($rsReport["SubTitle"])) {
            $str_MetaDescription = $rsReport["SubTitle"];
        }elseif (!empty($rsReport["ProjectSubName"])) {
            $str_MetaDescription = $rsReport["ProjectSubName"];
        }else {
            $str_MetaDescription = return_Left_Part_FromText($rsReport["ReportNotes"], 250);
        }
        if (!empty($rsReport["MediaTopURL"])) {
            $strTemp = $rsReport["MediaTopURL"];
            if (strpos($strTemp, ";") > 0) {
                $strTemp = trim(explode(";", $strTemp)[0]);
            }
            $str_PropertyImage = $strTemp;
        }
    }
}
