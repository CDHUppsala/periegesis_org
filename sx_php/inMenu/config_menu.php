<?php
if (sx_includeMenu == False) {
	header("location: index.php");
	exit();
}

$sql = "SELECT UseMenu, MenuNavTitle, MenuListTitle, UseDishImages,
		MenuTopNote, MenuBottomNote,
		UseDrinksMenu, DrinksMenuNavTitle, DrinksMenuListTitle, 
		DrinksMenuTopNotes, DrinksMenuBottomNotes
	FROM menu_setup " . str_LanguageWhere;
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
	$radioUseMenu = $rs["UseMenu"];
	$str_MenuNavTitle = $rs["MenuNavTitle"];
	$str_MenuListTitle = $rs["MenuListTitle"];
	$radio_UseDishImages = $rs["UseDishImages"];
	$memo_MenuTopNote = $rs["MenuTopNote"];
	$memo_MenuBottomNote = $rs["MenuBottomNote"];
    $radio_UseDrinksMenu = $rs["UseDrinksMenu"];
    $str_DrinksMenuNavTitle = $rs["DrinksMenuNavTitle"];
    $str_DrinksMenuListTitle = $rs["DrinksMenuListTitle"];
    $memo_DrinksMenuTopNotes = $rs["DrinksMenuTopNotes"];
    $memo_DrinksMenuBottomNotes = $rs["DrinksMenuBottomNotes"];

}
$stmt = null;
$rs = null;

if (!isset($radioUseMenu) || $radioUseMenu == False) {
	header("location: index.php");
	exit();
}

$sql = "SELECT UseLunchMenu,
	UseLunchImages,
	LunchMenuNavTitle, 
	LunchMenuLinkTitle, 
	ShowTodaysMenu, 
	TodaysMenuTitle,
	UseDailyLunchMenu,
	DailyLunchMenuTitle,
	LunchTopNote, 
	LunchBottomNote 
	FROM menu_lunch_setup " . str_LanguageWhere;
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
	$radio_UseLunchMenu = $rs["UseLunchMenu"];
	$radio_UseLunchImages = $rs["UseLunchImages"];
	$str_LunchMenuNavTitle = $rs["LunchMenuNavTitle"];
	$str_LunchMenuLinkTitle = $rs["LunchMenuLinkTitle"];
	$radio_ShowTodaysMenu = $rs["ShowTodaysMenu"];
	$str_TodaysMenuTitle = $rs["TodaysMenuTitle"];
	$radio_UseDailyLunchMenu = $rs["UseDailyLunchMenu"];
	$str_DailyLunchMenuTitle = $rs["DailyLunchMenuTitle"];
	$memo_LunchTopNote = $rs["LunchTopNote"];
	$memo_LunchBottomNote = $rs["LunchBottomNote"];
}
$stmt = null;
$rs = null;

$requestDate = Date('Y-m-d');
if (isset($_GET["date"]) && return_Is_Date($_GET["date"])) {
	$requestDate = $_GET["date"];
	if (return_Is_Date($requestDate) == False) {
		$requestDate = Date('Y-m-d');
	}
}

if(!isset($str_SiteTitle)) {
	$str_SiteTitle = "";
}
if (!empty($_GET["LunchMenu"]) && $radio_UseLunchMenu) {
    $str_SiteTitle .= ' - ' . $str_LunchMenuLinkTitle;
    $str_MetaTitle = $str_SiteTitle;
    if (!empty($memo_LunchTopNote)) {
        $str_MetaDescription = return_Left_Part_FromText(strip_tags($memo_LunchTopNote), 120);
    }
}else{
    $str_SiteTitle .= ' - ' . $str_MenuListTitle;
    $str_MetaTitle = $str_SiteTitle;
    if (!empty($memo_MenuTopNote)) {
        $str_MetaDescription = return_Left_Part_FromText(strip_tags($memo_MenuTopNote), 120);
    }
}