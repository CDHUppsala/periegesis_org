<?php

$cleanedMemoTextFinal = "";
if (isset($_POST["strTextToForm"])) {
	$cleanedMemoTextFinal = $_POST["strTextToForm"];
}

if (!empty($cleanedMemoTextFinal)) {
	//$cleanedMemoTextFinal = str_replace("<span><br /></span>", "", $cleanedMemoTextFinal);
	//$cleanedMemoTextFinal = str_replace("<br />", "", $cleanedMemoTextFinal);
	$cleanedMemoTextFinal = str_replace("&nbsp;", " ", $cleanedMemoTextFinal);
	$cleanedMemoTextFinal = str_replace("  ", " ", $cleanedMemoTextFinal);
}
?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>:: Public Sphere Content Management System - Preserve Formated Text</title>
	<link rel="stylesheet" href="../dbAdmin/css/sxCMS.css?v=2026-04">
	<script src="tinymce.min.js?v=2026-04"></script>
	<script src="config/remove_attributes.js?v=2026-04"></script>
</head>

<body>
	<div class="alignCenter padding">
		<?php
		if (empty($cleanedMemoTextFinal)) { ?>
			<p>Paste yor text here and click on HTML Formation to Remove all HTML (Attributes Classes, styles, IDs, etc.), preserving Pure HTML Formed</p>
			<div class="textBG">
				<form method="post" name="sxAddEdit" action="<?= $_SERVER["ORIG_PATH_INFO"] ?>">
					<textarea spellcheck="true" id="strTextToForm" name="strTextToForm" style="height: 680px; width: 100%"></textarea>
					<p>
						<input type="submit" name="formText" value="HTML Formation">
					</p>
				</form>
			</div>
		<?php
		} else { ?>
			<div class="textBG">
				<p>Copy the formated text
				<p>
					<textarea spellcheck="true" id="strFinalText" name="strFinalText" style="height: 680px; width: 100%"><?= $cleanedMemoTextFinal ?></textarea>
				<p>
					<input type="button" onclick="window.location='<?= $_SERVER["ORIG_PATH_INFO"] ?>'" value="New Text" name="NewText">
				</p>
			</div>
		<?php } ?>
	</div>
</body>

</html>