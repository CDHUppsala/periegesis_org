<?php

if (!$radio_IncludeFAQ || !sx_includeFAQ) {
    Header("Location: index.php");
    exit();
}

/**
 * The functions is called from subject.php and occasionally from nav_faq.php
 */
function sx_getSubjects()
{
    $conn = dbconn();
    $sql = "SELECT SubjectID,
        SubjectName" . str_LangNr . " AS SubjectName,
        InsertDate,
        SubjectNotes
    FROM faq_subjects
	WHERE Hidden = False ORDER BY Sorting DESC, InsertDate ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($rs) {
        return $rs;
    }else{
        return null;
    }
}

/**
 * FAQ - set upp and Metatags
 */
$rs = null;
$sql = " SELECT 
    FAQMenuTitle,
    FAQNavigationTitle,
    FAQFirstPageTitle,
    FAQNotes
    FROM faq_setup " . str_LanguageWhere;
$stmt = $conn->prepare($sql);
$stmt->execute();
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt = null;

if (is_array($rs)) {
    $strFAQMenuTitle = $rs["FAQMenuTitle"];
    $strFAQNavigationTitle = $rs["FAQNavigationTitle"];
    $strFAQFirstPageTitle = $rs["FAQFirstPageTitle"];
    $memoFAQNotes = $rs["FAQNotes"];

    /**
     * Change Metatag Variables
     */

    if (!empty($strFAQNavigationTitle)) {
        if (!empty($str_MetaTitle)) {
            $str_MetaTitle = $strFAQNavigationTitle . " - " . $str_MetaTitle;
        } else {
            $str_MetaTitle = $strFAQNavigationTitle;
        }
    }

    if (!empty($strFAQFirstPageTitle)) {
        if (!empty($str_MetaDescription)) {
            $str_MetaDescription = $strFAQFirstPageTitle . " - " . $str_MetaDescription;
        } else {
            $str_MetaDescription = $strFAQFirstPageTitle;
        }
    }
}
$rs = null;

/**
 * Get requested ID
 */

$int_SubjectID = 0;
if (isset($_GET["subjectid"]) && is_numeric($_GET["subjectid"])) {
    $int_SubjectID = (int) $_GET["subjectid"];
}

/**
 * The variable $arr_Answers is also used in answers.php and to Change Metatag Variables
 */
$arr_Answers = null;
if ($int_SubjectID > 0) {
    $sql = "SELECT
    a.AnswerID,
	s.SubjectName" . str_LangNr . " AS SubjectName,
	s.SubjectNotes" . str_LangNr . " AS SubjectNotes,
    a.SubjectID,
    a.Question,
    a.SubQuestion,
    a.InsertDate,
    a.MediaURL,
    a.MediaPlace,
    a.MediaNotes,
    a.PDFArchiveID,
    a.FilesForDownload,
    a.AnswerText
    FROM faq_subjects AS s 
		INNER JOIN faq_answers as a
		ON s.SubjectID = a.SubjectID
	WHERE a.SubjectID = ? AND a.Hidden = False " . str_LanguageAnd;
    $stmt = $conn->prepare($sql);
    $stmt->execute([$int_SubjectID]);
    $arr_Answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = null;
    /**
     * Get subject variables for use in answer.php
     * Change Metatag Variables
     */
    $str_SubjectName = "";
    if (is_array($arr_Answers)) {
        $str_SubjectName = $arr_Answers[0]["SubjectName"];
        $str_SubjectNotes =  $arr_Answers[0]["SubjectNotes"];
        $str_MetaTitle = $arr_Answers[0]["Question"] . " - " . $str_SubjectName;
        $str_PropertyType = "Frequently Asked Questions";

        if (!empty($str_SubjectNotes)) {
            $str_MetaDescription = return_Left_Part_FromText($str_SubjectNotes, 250);
        }
        if (!empty($arr_Answers[0]["MediaURL"])) {
            $strTemp = $arr_Answers[0]["MediaURL"];
            if (strpos($strTemp, ";") > 0) {
                $strTemp = trim(explode(";", $strTemp)[0]);
            }
            $str_PropertyImage = $strTemp;
        }
    }
}
