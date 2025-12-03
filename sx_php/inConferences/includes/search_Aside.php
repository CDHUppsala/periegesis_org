<?php
include_once dirname(__DIR__) . "/functions_Basic.php";

if ($radio_UseAdvertises) {
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

include dirname(__DIR__) . "/nav_Coming.php";
include dirname(__DIR__) . "/nav_Past.php";

if ($radio_UseAdvertises) :
	get_Main_Advertisements_Cycler("BottomSlider", "move_right_left");
	get_Main_Advertisements("Bottom");
endif;
