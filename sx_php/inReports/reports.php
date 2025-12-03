<?php

$radioTemp = false;

if (is_array($rsReport)) {
	$radioTemp = true;
	$int_ProjectID = $rsReport["ProjectID"];
	$strProjectName = $rsReport["ProjectName"];
	$strProjectSubName = $rsReport["ProjectSubName"];
	$strChapterName = $rsReport["ChapterName"];
	$strSubChapterName = $rsReport["SubChapterName"];
	$strTitle = $rsReport["Title"];
	$strSubTitle = $rsReport["SubTitle"];
	$dateInsertDate = $rsReport["InsertDate"];
	$strMediaTopURL = $rsReport["MediaTopURL"];
	$strImagesFromFolder = $rsReport["ImagesFromFolder"];
	$strMediaTopNotes = $rsReport["MediaTopNotes"];
	$strMediaRightURL = $rsReport["MediaRightURL"];
	$strMediaRightNotes = $rsReport["MediaRightNotes"];
	$intPDFArchiveID = $rsReport["PDFArchiveID"];
	if (intval($intPDFArchiveID) == 0) {
		$intPDFArchiveID = 0;
	}
	$strFilesForDownload = $rsReport["FilesForDownload"];
	$memoReportNotes = $rsReport["ReportNotes"];
}
$rsReport = null;

$temp_SubChapterName = "";
if (!empty($strSubChapterName)) {
	$temp_SubChapterName = "/" . $strSubChapterName;
}

$radioGreek = false;
if (sx_CurrentLanguage != 'el') {
	$radioGreek = sx_Check_Greek_Language(substr($memoReportNotes, 200));
}
$strClassFontExtended = "";
if ($radioGreek) {
	$strClassFontExtended = ' polytonic_font';
}

if ($radioTemp) { ?>
	<section>
		<h1 class="head align_center"><span><?= $strProjectName ?></span></h1>
		<?php
		if (!empty($strProjectSubName)) { ?>
			<h3 class="head align_center"><span><?= $strProjectSubName ?></span></h3>
		<?php
		} ?>
		<h5 class="head align_center"><span><?= lngChapter . ": " . $strChapterName . $temp_SubChapterName ?></span></h5>
	</section>
	<article class="text_wraper">
		<h2 class="head"><span><?= $strTitle ?></span></h2>
		<?php
		if (!empty($strSubTitle)) { ?>
			<h5><?= $strSubTitle ?></h5>
		<?php }

		$radioMediaLinks = false;
		if ($radio_ShowSocialMediaInText) {
			$radioMediaLinks = true;
		}
		include PROJECT_PHP . "/basic_PrintIncludes.php";

		$strFolderPhotos = "";
		if (!empty($strImagesFromFolder)) {
			$strFolderPhotos = return_Folder_Images($strImagesFromFolder);
		}
		if (!empty($strFolderPhotos) && strpos($strFolderPhotos, ";") > 0) {
			get_Manual_Image_Cycler($strFolderPhotos, $strImagesFromFolder, $strMediaTopNotes);
		} elseif (!empty($strMediaTopURL)) {
			if (strpos($strMediaTopURL, ";") > 0) {
				get_Manual_Image_Cycler($strMediaTopURL, "", $strMediaTopNotes);
			} else {
				get_Any_Media($strMediaTopURL, "Center", $strMediaTopNotes);
			}
		}
		if (!empty($strMediaRightURL)) {
			if (strpos($strMediaRightURL, ";") > 0) {
				get_Right_Images($strMediaRightURL, $strMediaRightNotes);
			} else {
				get_Any_Media($strMediaRightURL, "Right", $strMediaRightNotes);
			}
		} ?>
		<div class="text text_resizeable<?php echo $strClassFontExtended ?>">
			<div class="text_max_width">
				<?php
				echo $memoReportNotes;

				if (!empty($strFilesForDownload) && !empty($str_LinksToFiles)) {
					//echo $str_LinksToFiles;
					sx_getDownloadableFiles($strFilesForDownload);
				} ?>
			</div>
		</div>
	</article>
<?php
} ?>