<?php
if ($radio_UseAdvertises) {
	get_Main_Advertisements("Top");
}

if ($radio_ShowTextClassesInMainMenu) {
	require dirname(__DIR__) ."/sxNav_Main/sxNavMain_BySubCategories.php";
}

$strMenuForm = $str_MenuFormForByTexts;
if ($strMenuForm != "None") {
	require dirname(__DIR__) ."/sxNav_Asides/sole_Themes_Authors_YearMonth.php";
}

if (sx_IncludeTextByCalender) {
	require dirname(__DIR__) ."/inText_Calendar/textsByCalendar_Nav.php";
}

if ($radio_UseAdvertises) {
	get_Main_Advertisements("Bottom");
}
