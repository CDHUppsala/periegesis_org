<?php
include dirname(__DIR__) ."/loginMenu.php";
include dirname(__DIR__) ."/loginNotes.php";

if ($radio_UseAdvertises) {
    get_Main_Advertisements_Cycler("BottomSlider", "move_right_left");
    get_Main_Advertisements("Bottom");
}?>