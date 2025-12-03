<?php
if ($radio_UseAdvertises) {
    //== Place: Top,  Bottom
    get_Main_Advertisements("Top");
}

include __DIR__ .  "/nav_faq.php";

if ($radio_UseAdvertises) {
    //== Place: Top,  Bottom
    get_Main_Advertisements("Bottom");
}
