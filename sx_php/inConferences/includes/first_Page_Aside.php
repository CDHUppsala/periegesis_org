<?php
/*
	===================================================
	Event callendars in First Page 
	===================================================
*/

if ($radio_UseEvents) {
	include PROJECT_PHP . "/inEvents/events_FirstPage.php";
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
	FORM OF ACCORDION EFFECTS
	===================================================
*/

include dirname(__DIR__) ."/nav_Coming.php";
include dirname(__DIR__) ."/nav_Past.php";

if ($radio_ShowFavorites) :
	include PROJECT_PHP . "/inLinks/link_favorites.php";
endif;
if ($radio_UseAdvertises) :
	get_Main_Advertisements_Cycler("BottomSlider", "move_right_left");
	get_Main_Advertisements("Bottom");
endif;
