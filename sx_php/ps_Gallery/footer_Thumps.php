<?php

$aResults = null;
if (intval($int1) > 0) {
	$urlProductImgs = "../imgGallery/";
	$sql = "SELECT 
		PhotoID, 
		PhotoTitle, 
		PhotoSubTitle, 
		Photographer, 
		PhotoURL, 
		PhotoNote 
	FROM gallery_photos 
	WHERE Hidden = False 
	AND GalleryID = $int1  $strLanguageAnd
	ORDER BY GalleryID, Sorting DESC, PhotoID ";
	$stmt = $conn->query($sql);
	$rs = $stmt->fetchAll(PDO::FETCH_NUM);
	if ($rs) {
		$aResults =  $rs;
	}
	$stmt = null;
	$rs = null;
}

if (is_array($aResults)) { ?>
	<ul id="jqThumpsBG">
		<?php
		$iRows = count($aResults);
		for ($r = 0; $r < $iRows; $r++) {
			$intPhotoID = $aResults[$r][0];
			$strPhotoTitle =  $aResults[$r][1];
			$strPhotoSubTitle = $aResults[$r][2];
			if (!empty($strPhotoSubTitle)) {
				$strPhotoTitle = $strPhotoTitle . " - " . $strPhotoSubTitle;
			}
			$strPhotographer = $aResults[$r][3];
			$strPhotoURL = $aResults[$r][4];
			$memoPhotoDesc =  $aResults[$r][5];

			$strHTML = '';
			$strHTML = "<h3>" . $strPhotoTitle . "</h3><div class=text>" . $memoPhotoDesc . "</div>";
			$strHTML = sx_getCleanedJavaText($strHTML) ?>
			<li id="img_<?= $intPhotoID ?>" photo_src="<?= $urlProductImgs . $strPhotoURL ?>" photo_info="<?= $strHTML ?>">
				<img alt="<?= $strPhotoTitle ?>" title="<?= $strPhotoTitle . " [" . $strPhotoURL . "] ID: " . $intPhotoID ?>" src="<?= $urlProductImgs . $strPhotoURL ?>" height="70">
			</li>
		<?php
		} ?>
	</ul>
	<?php
	if (intval($iPhotoID) > 0) { ?>
		<script>
			var int_PhotoID = <?= $iPhotoID ?>;
		</script>
<?php
	}
}
$aResults = null;
?>