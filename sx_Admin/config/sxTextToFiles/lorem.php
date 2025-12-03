<?php
include realpath(dirname(dirname(__DIR__)) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

function sx_UpdateRow($sTitle, $sSubTitle, $sFirstPage, $sMainText, $textID)
{
	$conn = dbconn();
	$sql = "UPDATE texts
		SET
		LanguageID = ?,
		Title = ?,
		SubTitle = ?,
		FirstPageText = ?,
		MainText = ?
	WHERE TextID = ?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([1, $sTitle, $sSubTitle, $sFirstPage, $sMainText, $textID]);
	$stmt = null;
}

// Take text from textarea, split it according to a criteria, and crete separate files for each array 
//$strToFolder = $_SERVER['DOCUMENT_ROOT'] . "/imgPG/lorem";
$strToFolder = realpath(__DIR__ . "/lorem");

$sx_AddIpsumLorem = false;

if (isset($_POST["IpsumLorem"]) && $sx_AddIpsumLorem) {
	$strTextToFiles = $_POST["IpsumLorem"];
	$arParagraphs = explode("<p>", $strTextToFiles);
	$arTexts = null;

	if (is_array($arParagraphs)) {
		$sql = "SELECT TextID FROM texts ";
		$rs = $conn->query($sql)->fetchAll(PDO::FETCH_NUM);
		if ($rs) {
			$arTexts = $rs;
		}
		$rs = null;
	}

	if (is_array($arTexts)) {
		$iTextRows = count($arTexts);
		$iRows = count($arParagraphs);
		$loop = 0;
		$sMainText = "";
		$id = 0;
		for ($r = 1; $r < $iRows; $r++) {
			$sPar = trim($arParagraphs[$r]);
			if ($loop < 4) {
				$sMainText .= "<p>" . $sPar;
				$loop++;
			} else {
				$sMainText .= "<p>" . $sPar;
				$arPar = explode(".", str_replace("</p>", "", $sPar));
				$sTitle = $arPar[0];
				$sSubTitle = $arPar[1];
				$sFirstPage = "<p>" . $sPar;
				sx_UpdateRow($sTitle, $sSubTitle, $sFirstPage, $sMainText, $arTexts[$id][0]);
				$sMainText = "";
				$loop = 0;
				$id++;
			}

			if ($id >= $iTextRows) {
				break;
			}
			$copyMsg = ($r - 1) . " Paragraphs of (" . $iRows . ") have been updated successfully in " . ($id - 1) . " Records!";
		}
	}
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
			height: 620px
		}
	</style>
</head>

<body class="body">
	<header id="header">
		<h2>Updates Ipsum Lorem text</h2>
	</header>

	<h2>Update rows in Text table with Ipsum Lorem text in selected string fields</h2>
	<p>You must manuallly set upload to TRUE - it is FAULSE by default.</>
	<section>
		<?php if (@$copyMsg != "") { ?>
			<p class="textMsg"><?= @$copyMsg ?></p>
		<?php } ?>
		<h3>The content of existed Ipsum Lorem text</h3>
		<form action="lorem.php" method="post" name="createFiles">
			<textarea name="IpsumLorem">
<?php

$arParagraphs = scandir($strToFolder);
if (is_array($arParagraphs)) {
	$iFiles = count($arParagraphs);
	for ($f = 0; $f < $iFiles; $f++) {
		$f_Name = $arParagraphs[$f];
		$f_Ext = strtolower(pathinfo($f_Name)["extension"]);
		if ($f_Ext == "svg" || $f_Ext == "txt" || $f_Ext == "css" || $f_Ext == "js") {
			/*
				echo readfile($strToFolder."/".$f_Name);
				Reads and writes the file content and the Number of Characters ;
			*/

			$myfile = fopen($strToFolder . "/" . $f_Name, "r");
			//echo $f_Name . ": ";
			//echo "\n";
			echo fread($myfile, filesize($strToFolder . "/" . $f_Name));
			fclose($myfile);
			echo "\n";
		}
	}
} ?>
			</textarea>
			<p><input type="submit" name="Create Files"></p>
		</form>
	</section>
</body>

</html>