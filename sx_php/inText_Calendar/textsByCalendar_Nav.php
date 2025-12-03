<?php
$strCurrentURL = "texts.php";
if (empty($str_TextsByCalenderTitle)) {$str_TextsByCalenderTitle = lngTextCalendar;}
	$displayCalendar = "block";
?>
<section class="jqNavSideToBeCloned">
	<h2 class="head"><span><?=$str_TextsByCalenderTitle ?></span></h2>
	<?php
	include __DIR__ . "/textsByCalendar.php";
	?>
</section>