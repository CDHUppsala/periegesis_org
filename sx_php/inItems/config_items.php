<?php

if (sx_radioUseItems == false || $radio_IncludeItems == false) {
    Header("Location: index.php");
    exit;
}

$int_ItemID = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? (int) $_GET['itemid'] : 0;

// For statistics
$int_StatisticsTextID = $int_ItemID;

include __DIR__ . "/queries.php";

/**
 * Queries used for META TAGS and Page Navigation Titles
 */

$str_ItemTitle = "";
$str_MetaMedia = "";
$str_MetaNotes = "";

if (intval($int_ItemID) > 0) {
    $sql = "SELECT
        ItemTitle" . str_LangNr . " AS ItemTitle,
        MetaMedia,
        MetaNotes
    FROM items
    WHERE ItemID = ?
        AND Hidden = 0 
    ORDER BY Sorting DESC, ItemID ASC ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$int_ItemID]);
    $aRows = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!empty($aRows)) {
        $str_ItemTitle = $aRows['ItemTitle'];
        $str_MetaMedia = $aRows['MetaMedia'];
        $str_MetaNotes = $aRows['MetaNotes'];
    }
    $stmt = null;
    $aRows = null;
}

// For metadata

if (!empty($str_ItemTitle)) {
    $str_SiteTitle = $str_ItemTitle . ' - ' . $str_SiteTitle;
}
$str_MetaTitle = $str_SiteTitle;
if (!empty($str_MetaNotes)) {
    $str_MetaDescription = return_Left_Part_FromText(strip_tags($str_MetaNotes), 160);
}
if (!empty($str_MetaMedia)) {
    $str_PropertyImage = 'images/' . $str_MetaMedia;
}
