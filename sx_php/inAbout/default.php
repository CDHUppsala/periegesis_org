<?php

if (intval($int_AboutID) == 0) {
	$int_AboutID = 0;
}
/**
 * Different menus for different About Groups
 */
if (intval($int_AboutGroupID) == 0) {
	$int_AboutGroupID = 0;
}


$strWhere = str_LanguageAnd;
$strOrderBy = " ORDER BY Sorting DESC ";

if (intval($int_AboutGroupID) > 0) {
	$strWhere = " AND AboutGroupID = " . $int_AboutGroupID . str_LanguageAnd;
}
if (intval($int_AboutID) > 0) {
	$strWhere = " AND AboutID = " . $int_AboutID;
	$strOrderBy = "";
}

$radioTemp = false;

$sql = "SELECT AboutID, Title, SubTitle,
    MediaTopURL, ImagesFromFolder, MediaTopDisplayMode, MediaTopNotes, 
	MediaRightURL, MediaRightNotes, 
    PDFArchiveID, FilesForDownload, WideScreen, AboutNotes 
    FROM about 
	WHERE Hidden = False $strWhere $strOrderBy LIMIT 1";
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt = null;
if (is_array($rs)) {
	$radioTemp = true;
	$iAboutID = $rs["AboutID"];
	$strTitle = $rs["Title"];
	$strSubTitle = $rs["SubTitle"];
	$strMediaTopURL = $rs["MediaTopURL"];
	$strImagesFromFolder = $rs["ImagesFromFolder"];
	$strMediaTopDisplayMode = $rs["MediaTopDisplayMode"];
	$strMediaTopNotes = $rs["MediaTopNotes"];
	$strMediaRightURL = $rs["MediaRightURL"];
	$strMediaRightNotes = $rs["MediaRightNotes"];
	$intPDFArchiveID = $rs["PDFArchiveID"];
	if (intval($intPDFArchiveID) == 0) {
		$intPDFArchiveID = 0;
	}
	$strFilesForDownload = $rs["FilesForDownload"];
	$radioWideScreen = $rs["WideScreen"];
	$memoAboutNotes = $rs["AboutNotes"];
}
$rs = null;

if (empty($str_AboutGroupName)) {
	$str_AboutGroupName = $str_TextsAboutTitle;
} ?>
<section id="immersive_reading_about_<?php echo $int_AboutID ?>" class="immersive-reader-button">
	<h1 class="head"><span><?= $str_AboutGroupName ?></span></h1>
	<article>
		<?php

		$strMediaPaths = "";
		$radioBottomGallery = false;
		if ($radioTemp == false) { ?>
			<h2 class="head"><span><?= lngRecordsNotFound ?></span></h2>
		<?php
		} else { ?>
			<h2 class="head"><span><?= $strTitle ?></span></h2>
			<?php

			if (!empty($strSubTitle)) { ?>
				<h3><?php echo $strSubTitle ?></h3>
		<?php }

			$radioMediaLinks = false;
			if ($radio_ShowSocialMediaInText) {
				$radioMediaLinks = true;
			}
			include PROJECT_PHP . "/basic_PrintIncludes.php";

			$radioSorting = true;
			if (!empty($strImagesFromFolder) && strlen($strImagesFromFolder) > 2) {
				$strMediaPaths = return_Folder_Images($strImagesFromFolder);
			} elseif (!empty($strMediaTopURL)) {
				$strMediaPaths = $strMediaTopURL;
				$radioSorting = false;
			}

			if (!empty($strMediaPaths)) {
				if (strpos($strMediaPaths, ";") > 0) {
					$arrMediaPaths = explode(';', $strMediaPaths);
					$sFirstFile = $arrMediaPaths[0];
					if (return_file_extension($sFirstFile) == 'pdf') {
						// Expected multiple PDF files
						get_multiple_PDF_Files($arrMediaPaths);
					} elseif (empty($strMediaTopDisplayMode) || $strMediaTopDisplayMode == "Slider" || $strMediaTopDisplayMode == "Top_Slider" || $strMediaTopDisplayMode == "None") {
						get_Manual_Image_Cycler($strMediaPaths, "", $strMediaTopNotes);
					} elseif ($strMediaTopDisplayMode == "Bottom_Gallery") {
						$radioBottomGallery = true;
					}
				} else {
					get_Any_Media($strMediaPaths, "Center", $strMediaTopNotes, "", $int_AboutID);
				}
			}

			if (!empty($strMediaRightURL)) {
				if (strpos($strMediaRightURL, ";") > 0) {
					get_Right_Images($strMediaRightURL, $strMediaRightNotes);
				} else {
					get_Any_Media($strMediaRightURL, "Right", $strMediaRightNotes);
				}
			}
			echo '<div class"text_resizeable">';
			if (!$radioWideScreen) {
				echo '<div class="text_max_width">';
			}
			echo $memoAboutNotes;
			if (!empty($strFilesForDownload)) {
				sx_getDownloadableFiles($strFilesForDownload);
			}
			if (!$radioWideScreen) {
				echo '</div>';
			}
			echo '</div>';
		}
		if ($radioBottomGallery) {
			get_Inline_Gallery_Images($strMediaPaths, '../images/', $radioSorting);
		} ?>
	</article>
</section>