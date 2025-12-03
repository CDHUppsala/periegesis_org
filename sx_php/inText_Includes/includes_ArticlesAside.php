<?php
/*
	===================================================
	OPEN REQUESTED TEXT ARCHIVES - WITH PAGINATION 
	===================================================
*/
if ($radio_ShowArchivesList) {
	require dirname(__DIR__) ."/inText_Archives/archives_TextsPagingMenu.php";
}

if ($radio_UseAdvertises) {
	/*
	Parmeters: Cycler Place, Effect Mode, Thumbs Place
	Cycler Place:	TopSlider, BottomSlider, Footer
	Effect Mode:	fade_both, fade_active, 
					move_left_right, move_right_left, 
					move_top_bottom, start_top_left, 
					end_top_left, end_top_right
	Thumbs Place: bottom, bottom_margin
	*/
	get_Main_Advertisements_Cycler("TopSlider", "move_top_bottom");
	/*
	Parmeters for get_Main_Advertisements
    	Place: Top,  Bottom
	*/
	get_Main_Advertisements("Top");
}

/*
	===================================================
	Menu for Login Members 
	===================================================
	Recent text by the last classification level
*/

if ($radio__UserSessionIsActive && sx_IncludeLoginGroupsIn_SeparateMenu) {
	require dirname(__DIR__) ."/sxNav_Main/sxNavMain_Acc_PublishedBySubCategories_Login.php";
}

/*
	===================================================
	ALTERNATIVE NAVIGATION MENUS to Published Texts By Classification Level
	===================================================
	Links to X numbers of Published Text in the last classification Level
	Right Image on the Last Level Opens an Archive with pagination links to All Texts of that level  
	===================================================
	The classification level to be shown is Defined from the Text Configuration Table
*/
if ($radio_ShowPublishedTextsByClass) {
	$strLevel =  $str_PublishedTextsByClassLevel;
	if ($strLevel == "SubCategory") {
		require dirname(__DIR__) ."/sxNav_Main/sxNavMain_Acc_PublishedBySubCategories.php";
	} elseif ($strLevel == "Category") {
		require dirname(__DIR__) ."/sxNav_Main/sxNavMain_Acc_PublishedByCategories.php";
	} elseif ($strLevel == "Group") {
		require dirname(__DIR__) ."/sxNav_Main/sxNavMain_Acc_PublishedByGroups.php";
	} elseif ($strLevel == "Aside") {
		require dirname(__DIR__) ."/sxNav_Asides/tabs_RecentGroups_By_Aside.php";
	}
}

/*
	===================================================
	ALTERNATIVE NAVIGATION MENUS to Text Classification Levels
	===================================================
	The Navigation menus here have the same content as the horizontal navigation menues
	Use them to open archives with all texts belonging to the last classification level
	- sxNavMain_BySubCategories.php		| General menu, for 1, 2 or 3 levels, without links to text
	- sxNavMain_ByCategories.php		| Use it only if you have 3-levels and wand to show 2 levels
	- sxNavMain_ByGroups.php			| Use it only if you have 3 or 2 levels and wand to show 1 level 

*/

if ($radio_ShowTextClassesInMainMenu) {
	require dirname(__DIR__) ."/sxNav_Main/sxNavMain_BySubCategories.php";
}

/**
 * Show texts published in first page
 */

if ($radio_ShowFirstPageTexts) {
	require dirname(__DIR__) ."/sxNav_Asides/sole_PublishedFirstPage.php";
}

/*
	===================================================
	DIFFERENT MENU FORMS FOR ALTERNATIVE TEXT CLASSIFICATION BY: 
	Themes, Authors and Text Calendar
	===================================================
	The Menu Form is Defined from the Text Configuration Table
*/
$strMenuForm = $str_MenuFormForByTexts;
if ($strMenuForm != "None") {
	if ($strMenuForm == "Tabs") {
		require dirname(__DIR__) ."/sxNav_Asides/tabs_Themes_Authors_YearMonth.php";
	} elseif ($strMenuForm == "Accordion") {
		require dirname(__DIR__) ."/sxNav_Asides/acc_Themes_Authors_YearMonth.php";
	} elseif ($strMenuForm == "Sole") {
		require dirname(__DIR__) ."/sxNav_Asides/sole_Themes_Authors_YearMonth.php";
	}
}

/*
	===================================================
	DIFFERENT MENU FORMS FOR Recent and Most Read Texts: 
	===================================================
	The Menu Form is Defined from the Text Configuration Table
*/
$strMenuForm = $str_MenuFormForRecentAndMostRead;
if ($strMenuForm != "None") {
	if ($strMenuForm == "Tabs") {
		require dirname(__DIR__) ."/sxNav_Asides/tabs_Recent_Most_Texts.php";
	} elseif ($strMenuForm == "Accordion") {
		require dirname(__DIR__) ."/sxNav_Asides/acc_Recent_Most_Texts.php";
	} elseif ($strMenuForm == "Sole") {
		require dirname(__DIR__) ."/sxNav_Asides/sole_Recent_Most_Texts.php";
	}
}

/*
	===================================================
	DIFFERENT MENU FORMS FOR Recently Commented and Most Commented Texts: 
	===================================================
	The Menu Form is Defined from the Text Configuration Table
*/
$strMenuForm = $str_MenuFormForRecentAndMostCommented;
if ($strMenuForm != "None") {
	if ($strMenuForm == "Tabs") {
		require dirname(__DIR__) ."/sxNav_Asides/tabs_Recent_Most_Comments.php";
	} elseif ($strMenuForm == "Accordion") {
		require dirname(__DIR__) ."/sxNav_Asides/acc_Recent_Most_Comments.php";
	} elseif ($strMenuForm == "Sole") {
		require dirname(__DIR__) ."/sxNav_Asides/sole_Recent_Most_Comments.php";
	}
}

/*
	===================================================
	SHOW RECENT AND MOST FOR BOTH Texts and Comments
	===================================================
	Combines the above 2 menus, where the Menu Form must be None
	Variables are defined in sx_design.php
*/
if (sx_ShowRecentMost_BothTextsAndComments_Tabs) {
	require dirname(__DIR__) ."/sxNav_Asides/tabs_RecentMost_TextsAndComments.php";
} elseif (sx_ShowRecentMost_BothTextsAndComments_Accordion) {
	require dirname(__DIR__) ."/sxNav_Asides/acc_RecentMost_TextsAndComments.php";
}

/*
	===================================================
	TEXT CALENDAR
	===================================================
	Defined initially from the sx_design.php and dynamically from SITE_CONFIG_TEXTS
	textsByCalendar_Nav.php		| Shows links to Year Months that include texts, links open the Archive of texts by Month 
	textsByYearMonth_Nav.php	| A full calendar with links that open archives by month, week and day, if they include texts.
*/

if ($radio_ShowTextsByCalendar) {
	if (sx_IncludeTextByCalender) {
		require dirname(__DIR__) ."/inText_Calendar/textsByCalendar_Nav.php";
	} elseif (sx_IncludeTextByYearMonth) {
		require dirname(__DIR__) ."/inText_Calendar/textsByYearMonth_Nav.php";
	}
}

if ($radio_UseAdvertises) {
	get_Main_Advertisements_Cycler("BottomSlider", "move_right_left");
	get_Main_Advertisements("Bottom");
}
