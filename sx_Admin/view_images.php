<?php
include "functionsLanguage.php";
include PROJECT_ADMIN . "/login/lockPage.php";

$strImgURL = "";
if (isset($_GET["imgURL"])) {
	$strImgURL = $_GET["imgURL"];
}
?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Public Sphere Content Management System - View Images</title>
</head>
<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css" />
</head>

<body style="padding: 40px">
	<?php
	if (!empty($strImgURL)) {
		if (
			strpos($strImgURL, ".ogg") > 0 ||
			strpos($strImgURL, ".webm") > 0 ||
			strpos($strImgURL, ".mp4") > 0
		) {
			$strType = "video/mp4";
			if (strpos($strImgURL, ".ogg") > 0) {
				$strType = "video/ogg";
			} elseif (strpos($strImgURL, ".webm") > 0) {
				$strType = "video/webm";
			} ?>
			<h2>/imgMedia/<?= $strImgURL ?></h2>
			<video controls>
				<source src="<?php echo $strImgURL ?>" type="<?php echo $strType ?>" />
			</video>
		<?php
		} elseif (
			strpos($strImgURL, ".mp3") > 0 ||
			strpos($strImgURL, ".ogg") > 0 ||
			strpos($strImgURL, ".wav") > 0

		) {
			$strType = "audio/mp3";
			if (strpos($strImgURL, ".ogg") > 0) {
				$strType = "audio/ogg";
			} elseif (strpos($strImgURL, ".webm") > 0) {
				$strType = "audio/wav";
			} ?>
			<h2>/imgMedia/<?= $strImgURL ?></h2>
			<audio controls>
				<source src="<?php echo $strImgURL ?>" type="<?php echo $strType ?>" />
			</audio>
			<?php
			// C:\webs\ps_dev\htdocs_shop\imgProducts
		} else {
			if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/images/" . $strImgURL . "")) { ?>
				<h2>/images/<?= $strImgURL ?></h2>
				<div>
					<img style="width: 100%" src="../images/<?= $strImgURL ?>">
				</div>
			<?php
			} elseif (file_exists($_SERVER["DOCUMENT_ROOT"] . "/imgGallery/" . $strImgURL . "")) { ?>
				<h2>/imgGallery/<?= $strImgURL ?></h2>
				<div>
					<img style="width: 100%" src="../imgGallery/<?= $strImgURL ?>">
				</div>
			<?php
			} elseif (file_exists($_SERVER["DOCUMENT_ROOT"] . "/imgProducts/" . $strImgURL . "")) { ?>
				<h2>/imgGallery/<?= $strImgURL ?></h2>
				<div>
					<img style="width: 100%" src="../imgProducts/<?= $strImgURL ?>">
				</div>
			<?php
			} else {
				echo "<h2>Image Not Found</h2>";
			}
		}
	} else {
		echo "<h2>Image Not Found</h2>";
	}
	?>
</body>

</html>