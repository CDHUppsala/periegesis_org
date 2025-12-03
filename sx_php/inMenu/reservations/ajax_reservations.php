<?php

/**
 * Get just language and site information
 */
include "siteLang/sxLang.php";

/**
 * Get the Form Source and open the corresponding file
 */

$intTableID = (int) $_POST["TableID"];
$dateReservationDate = $_POST["ReservationDate"];
$strStartTime = $_POST["StartTime"];
$strEndTime = $_POST["EndTime"];

$strName = "";
if (!empty($_POST["CustomerName"])) {
	$strName = $_POST["CustomerName"];
}
$strPhone = "";
if (!empty($_POST["CustomerPhone"])) {
	$strPhone = $_POST["CustomerPhone"];
}
$strNumbers = 0;
if (!empty($_POST["Numbers"])) {
	$strNumbers = $_POST["Numbers"];
}

if(intval($intTableID) > 0) {
    
}
?>

<?php
if ($strFormName == "NewsLetter") {
	include PROJECT_PHP . "/test.php";
} else { ?>
	<h2><?= str_SiteTitle ?></h2>
<?php
} ?>
<script>
	sx_load_modal_window();
</script>