<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";

$cleanedMemoTextFinal = "";
if (isset($_POST["strTextToForm"])) {
	$cleanedMemoTextFinal = $_POST["strTextToForm"];
}

if (!empty($cleanedMemoTextFinal)) {
	$cleanedMemoTextFinal = str_replace("<span><br /></span>", "", $cleanedMemoTextFinal);
	$cleanedMemoTextFinal = str_replace("<br />", "", $cleanedMemoTextFinal);
	$cleanedMemoTextFinal = str_replace("&nbsp;", " ", $cleanedMemoTextFinal);
	$cleanedMemoTextFinal = str_replace("  ", " ", $cleanedMemoTextFinal);
}
?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>:: Public Sphere Content Management System - Preserve Formated Text</title>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2024">
	<script src="../tinymce/tinymce.min.js?v=2024"></script>
	<script src="../tinymce/config/clean.js?v=2024"></script>
</head>

<body>
	<div class="alignCenter padding">
		<h3><?= lngCleanPreserveFormedText ?></h3>
		<?php
		if (empty($cleanedMemoTextFinal)) { ?>
			<p><?= lngCleanPreserveFormedTextDescription ?></p>
			<div class="textBG">
				<form method="post" name="sxAddEdit" action="<?= $_SERVER["ORIG_PATH_INFO"] ?>">
					<textarea spellcheck="true" id="strTextToForm" name="strTextToForm" style="height: 680px; width: 100%"></textarea>
					<p>
						<input type="submit" name="formText" value="<?= lngHTMLFormation ?>">
					</p>
				</form>
			</div>
		<?php
		} else { ?>
			<div class="textBG">
				<p><?= lngCompleteHTMLFormation ?></b>: <?= lngFormAndCopyTheCleanedText ?>
				<p>
					<textarea spellcheck="true" id="strFinalText" name="strFinalText" style="height: 680px; width: 100%"><?= $cleanedMemoTextFinal ?></textarea>
				<p>
					<input type="button" onclick="window.location='<?= $_SERVER["ORIG_PATH_INFO"] ?>'" value="<?= lngNewText ?>" name="NewText">
				</p>
			</div>
		<?php } ?>
	</div>
</body>

</html>