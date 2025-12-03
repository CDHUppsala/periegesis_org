<?php
$strCurrentURL = "texts.php";
if (empty($str_TextsByCalenderTitle)) {
	$str_TextsByCalenderTitle = lngTextCalendar;
}
$displayCalendar = "block";
/*
$display_Parent = "none";
$strSlide =  "slide_down";
if (intval($int_Month) > 0) {
	$display_Parent = "block";
	$strSlide =  "slide_up";
} 
*/
$display_Parent = "block";
$strSlide =  "slide_up";

?>

<section class="jqNavSideToBeCloned">
	<h2 class="head <?= $strSlide ?> jqToggleNextRight"><span><?= $str_TextsByCalenderTitle ?></span></h2>
	<div class="sxAccordionNav jqAccordionNav" style="display: <?= $display_Parent ?>">
		<?php
		include __DIR__ . "/textsByYearMonth.php";
		?>
	</div>
</section>