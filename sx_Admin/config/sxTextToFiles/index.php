<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_reporting', E_ALL);

include realpath(dirname(dirname(__DIR__)) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";

// Take text from textarea, split it according to a criteria, and crete separate files for each array 
$strToFolder = $_SERVER['DOCUMENT_ROOT'] . "\\imgPG\\temporal";
echo $strToFolder . '<br>';

$copyMsg = '';
$strTextToFiles = $_POST["TextToFiles"] ?? '';
if (!empty($strTextToFiles)) {
	$arrFiles = explode("<svg", $strTextToFiles);

	// Trim spaces from each array value
	$arrFiles = array_map('trim', $arrFiles);

	// Remove empty values
	$arrFiles = array_filter($arrFiles);

	// Re-index the array to start from 0
	$arrFiles = array_values($arrFiles);

	if (!empty($arrFiles) && is_array($arrFiles)) {
		$iRows = count($arrFiles);
		for ($r = 0; $r < $iRows; $r++) {
			$sFile = trim($arrFiles[$r]);
			$arrFile = [];
			if (strpos($sFile, 'id=') !== false) {
				$arrFile = explode('id="', $sFile);
			} else {
				$arrFile = explode('glyph-name="', $sFile);
			}
			if (!empty($arrFile)) {
				$iPos = strpos($arrFile[1], '"');
				$sFileName = substr($arrFile[1], 0, $iPos);

				$fh = fopen($strToFolder . "/" . $sFileName . ".svg", 'w');
				fwrite($fh, "<svg " . $sFile . "\n");
				fclose($fh);
			}
		}
	}

	$copyMsg = "The files have been copied successfully!";
}
?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Public Sphere Content Management System - Back up Databases</title>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
	<style>
		textarea {
			width: 96%;
			height: 320px
		}
	</style>
</head>

<body class="body">
	<header id="header">
		<h2>Create separate svg-files</h2>
	</header>

	<h2>Create separate svg-files from a text-file that include multiple svg-paths</h2>
	<section>
		<?php if (!empty($copyMsg)) { ?>
			<p class="textMsg"><?= $copyMsg ?></p>
		<?php } ?>
		<h3>The content of existed svg-files</h3>
		<form action="index.php" method="post" name="createFiles">
			<textarea>
<?php

$arrFiles = scandir($strToFolder);
if (is_array($arrFiles)) {
	$iFiles = count($arrFiles);
	for ($f = 0; $f < $iFiles; $f++) {
		$f_Name = $arrFiles[$f];
		$f_Ext = strtolower(pathinfo($f_Name)["extension"]);
		if ($f_Ext == "svg" || $f_Ext == "html" || $f_Ext == "txt" || $f_Ext == "css" || $f_Ext == "js") {
			/*
				echo readfile($strToFolder."/".$f_Name);
				Reads and writes the file content and the Number of Characters ;
			*/

			$filePath = $strToFolder . "/" . $f_Name;
			$myfile = fopen($filePath, "r");
			
			if ($myfile && filesize($filePath) > 0) {
				echo sprintf("%s :\n", $f_Name);
				echo fread($myfile, filesize($filePath));
				fclose($myfile);
				echo "\n\n";
			} else {
				echo "Failed to open the file or file is empty.\n";
			}
		}
	}
}
?>
			</textarea>
			<h3>Copy and Paste a text with multiple svg-paths in the text area</h3>
			<textarea name="TextToFiles"></textarea>
			<p><input type="submit" name="Create Files"></p>
		</form>
	</section>
</body>

</html>