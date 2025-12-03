<?php
/*
    This file is placed in the HEAD Element of the articles.php
*/

if (sx_radioUseArticles == false || $radio_IncludeArticles == false) {
    Header("Location: index.php");
    exit;
}

$int_ArticleID = 0;
if (!empty($_GET['aid'])) {
    $int_ArticleID = (int) $_GET['aid'];
}
if (intval($int_ArticleID) == 0) {
    $int_ArticleID = 0;
}else{
    /**
     * Statistics for article visits
     * The variable is first defined in sx_config.php
     */
    $int_StatisticsTextID = $int_ArticleID;
}

$int_ArticleCategoryID = 0;
if (!empty($_GET['acid'])) {
    $int_ArticleCategoryID = $_GET['acid'];
}
if (intval($int_ArticleCategoryID) == 0) {
    $int_ArticleCategoryID = 0;
}

$int_ArticleGroupID = 0;
if (!empty($_GET['agid'])) {
    $int_ArticleGroupID = $_GET['agid'];
}
if (intval($int_ArticleGroupID) == 0) {
    $int_ArticleGroupID = 0;
}

/**
 * If all 3 IDs, for article, category and group, are zero, redirect
 * - or, show an introduction to all articles in pages
 */
$radio_RecuestedArticles = true;
if($int_ArticleID == 0 && $int_ArticleCategoryID == 0 && $int_ArticleGroupID == 0) {
    $radio_RecuestedArticles = false;
}

/**
 * Queries used for META TAGS and Page Navigation Titles
 */

$str_ArticleTitle = "";
$str_ArticleSubTitle = "";
$str_ArticleGroupName = "";
$str_MetaNotes = "";
$str_MetaImage = "";

if (intval($int_ArticleID) > 0) {
    $sql = "SELECT a.Title, a.SubTitle,
        a.ArticleGroupID AS ArticleGroupID, 
        a.ArticleCategoryID AS ArticleCategoryID,
        a.TopMediaPaths, 
        g.GroupName" . str_LangNr . " AS GroupName,
        c.CategoryName" . str_LangNr . " AS CategoryName
    FROM (articles AS a
    LEFT JOIN article_groups AS g 
        ON a.ArticleGroupID = g.ArticleGroupID)
    LEFT JOIN article_categories AS c
        ON a.ArticleCategoryID = c.ArticleCategoryID
	WHERE a.ArticleID = ?
        AND a.Hidden = False
        AND (g.Hidden = False OR g.Hidden IS NULL)
        AND (c.Hidden = False OR c.Hidden IS NULL) 
        " . str_LanguageAnd;
    $stmt = $conn->prepare($sql);
    $stmt->execute([$int_ArticleID]);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = null;
    if (!empty($rs)) {
        $str_ArticleTitle = $rs["Title"];
        $str_ArticleSubTitle = $rs["SubTitle"];
        $int_ArticleGroupID = $rs["ArticleGroupID"];
        $str_ArticleGroupName = $rs["GroupName"];
        $int_ArticleCategoryID = $rs["ArticleCategoryID"];
        $str_ArticleCategoryName = $rs["CategoryName"];
        $str_MetaImage = $rs["TopMediaPaths"];
    }
    $stmt = null;

    if (intval($int_ArticleGroupID) == 0) {
        $int_ArticleGroupID = 0;
        $str_ArticleGroupName = "";
    }
    if (intval($int_ArticleCategoryID) == 0) {
        $int_ArticleCategoryID = 0;
        $str_ArticleCategoryName = "";
    }
} elseif (intval($int_ArticleCategoryID) > 0) {
    $sql = "SELECT c.ArticleCategoryID AS ArticleCategoryID,
        c.CategoryName" . str_LangNr . " AS CategoryName,
        c.ArticleGroupID AS ArticleGroupID, 
        g.GroupName" . str_LangNr . " AS GroupName,
        c.MetaNotes" . str_LangNr . " AS MetaNotes,
        c.MetaImage
    FROM article_categories AS c
        INNER JOIN article_groups AS g 
        ON c.ArticleGroupID = g.ArticleGroupID
	WHERE c.ArticleCategoryID = ?
        AND (c.Hidden = False OR c.Hidden IS NULL)
        AND  (g.Hidden = False OR g.Hidden IS NULL)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$int_ArticleCategoryID]);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = null;
    if (!empty($rs)) {
        $int_ArticleGroupID = $rs["ArticleGroupID"];
        $str_ArticleGroupName = $rs["GroupName"];
        $int_ArticleCategoryID = $rs["ArticleCategoryID"];
        $str_ArticleCategoryName = $rs["CategoryName"];
        $str_MetaNotes = $rs["MetaNotes"];
        $str_MetaImage = $rs["MetaImage"];
    }
    $rs = null;

    if (intval($int_ArticleGroupID) == 0) {
        $int_ArticleGroupID = 0;
        $str_ArticleGroupName = "";
    }
    if (intval($int_ArticleCategoryID) == 0) {
        $int_ArticleCategoryID = 0;
        $str_ArticleCategoryName = "";
    }
} elseif (intval($int_ArticleGroupID) > 0) {
    $sql = "SELECT ArticleGroupID, 
        GroupName" . str_LangNr . " AS GroupName,
        MetaNotes" . str_LangNr . " AS MetaNotes,
        MetaImage
    FROM article_groups
	WHERE ArticleGroupID = ?
        AND Hidden = False ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$int_ArticleGroupID]);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = null;
    if (!empty($rs)) {
        $int_ArticleGroupID = $rs["ArticleGroupID"];
        $str_ArticleGroupName = $rs["GroupName"];
        $str_MetaNotes = $rs["MetaNotes"];
        $str_MetaImage = $rs["MetaImage"];
    }
    $rs = null;

    if (intval($int_ArticleGroupID) == 0) {
        $int_ArticleGroupID = 0;
        $str_ArticleGroupName = "";
    }
}

if (!empty($str_ArticleTitle)) {
    $str_SiteTitle = $str_ArticleTitle . ' - ' . $str_SiteTitle;
} elseif (!empty($str_ArticleTitle)) {
    $str_SiteTitle = $str_ArticleCategoryName . ' - ' . $str_SiteTitle;
} elseif (!empty($str_ArticleGroupName)) {
    $str_SiteTitle = $str_ArticleGroupName . ' - ' . $str_SiteTitle;
}

$str_MetaTitle = $str_SiteTitle;

if (!empty($str_MetaNotes)) {
    $str_MetaDescription = return_Left_Part_FromText(strip_tags($str_MetaNotes), 160);
} elseif (!empty($str_ArticleSubTitle)) {
    $str_MetaDescription = return_Left_Part_FromText(strip_tags($str_ArticleSubTitle), 160);
}

if (!empty($str_MetaImage)) {
    if(strpos($str_MetaImage,';') > 0) {
        $str_MetaImage = trim(explode(";",$str_MetaImage)[0]);
    }
    $str_PropertyImage = 'images/' . $str_MetaImage;
}