<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";


$cleanedMemoText = "";
if (isset($_POST["strTextToForm"])) {
	$cleanedMemoText = sx_formatTextarea($_POST["strTextToForm"]);
} ?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>:: Public Sphere Content Management System - Clean Text</title>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2024">
	<?php
	if ($cleanedMemoText != "") { ?>
		<script src="../tinymce/tinymce.min.js?v=2024"></script>
		<script src="../tinymce/config/clean.js?v=2024"></script>
	<?php
	} ?>
</head>

<body>
	<div class="alignCenter padding">
		<h3><?= lngCleanText ?></h3>
		<p><?= lngPasteTextOnTextareaAndClickClear .' '. lngCleanTextDesciption ?></p>
		<?php
		if (empty($cleanedMemoText)) { ?>
			<div class="textBG">
				<form method="post" name="sxAddEdit" action="<?= $_SERVER["ORIG_PATH_INFO"] ?>">
					<textarea spellcheck="true" id="strTextToForm" name="strTextToForm" style="height: 680px; width: 100%"></textarea>
					<p><input type="submit" name="formText" value="<?= lngCleanText ?>"></p>
				</form>
			</div>
		<?php
		} else { ?>
			<div class="textBG">
				<textarea spellcheck="true" id="strFormedText" name="strFormedText" style="height: 480px; width: 100%"><?= $cleanedMemoText ?></textarea>
				<p><input type="button" onclick="window.location='sxCleanText.php'" value="<?= lngNewText ?>" name="NewText"></p>
			</div>
		<?php
		} ?>
	</div>
</body>

</html>