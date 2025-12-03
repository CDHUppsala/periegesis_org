<?php
include __DIR__ . "/sx_ConfigSiteData.php";

/**
 * Get the Form Source and open the corresponding file
 */
$strFormName = "";
if (!empty($_POST["FormName"])) {
	$strFormName = $_POST["FormName"];
} ?>

<?php
if ($strFormName == "NewsLetter") {
    include __DIR__ . "/sx_NewsLetter/sx_NewsLetterSubmit.php";
//}elseif ($strFormName == "NewsLetterAdvanced") {
  //  include __DIR__ . "/sx_NewsLetter/sx_NewsLetterSubmitAdvanced.php";
}elseif ($strFormName == "EventParticipation") {
	include __DIR__ . "/inEvents/participants/participation_form_submit.php";
} else { ?>
	<h2><?= str_SiteTitle ?></h2>
<?php
} ?>
<script>
	sx_load_modal_window();
</script>