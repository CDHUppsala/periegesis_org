<?php

/**
 * To view images when their field is selected for sorting
 */
//## 
//=============================================================
function sx_getImgFolder($strImgURL)
{
	if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/images/" . $strImgURL)) {
		return "../images/" . $strImgURL;
	} elseif (file_exists($_SERVER["DOCUMENT_ROOT"] . "/imgProducts/" . $strImgURL)) {
		return "../imgProducts/" . $strImgURL;
	} elseif (file_exists($_SERVER["DOCUMENT_ROOT"] . "/imgGallery/" . $strImgURL)) {
		return "../imgGallery/" . $strImgURL;
	} else {
		return "images/NoImage.png";
	}
}
// Show Tab to hide/show images only if the table includes images
$radio_ShowHideImages = false;
function sx_getLinksAndImages($strSortField, $xName, $value, $soleImage = false)
{
	$strViewLink = "";
	$tValue = mb_strtolower($value);
	if (
		strpos($tValue, ".svg") > 0 ||
		strpos($tValue, ".gif") > 0 ||
		strpos($tValue, ".png") > 0 ||
		strpos($tValue, ".bmp") > 0 ||
		strpos($tValue, ".webp") > 0 ||
		strpos($tValue, ".jpg") > 0 ||
		strpos($tValue, ".jpeg") > 0
	) {
		global $radio_ShowHideImages;
		$radio_ShowHideImages = true;
		if (strpos($value, ";") == 0) {
			$value = $value . ";";
		}
		$arrValue = explode(";", $value);
		$iTemp = count($arrValue);
		for ($f = 0; $f < $iTemp; $f++) {
			$strTemp = trim($arrValue[$f]);
			if (!empty($strTemp)) {
				/**
				 * Show images even when sorting by the field name of images
				 * The variable $soleImage implies that the call is from an administration subfolder
				 */
				if ((!empty($strSortField) && $strSortField == $xName) || $_SESSION["ShowImages"]) {
					$strViewLink = $strViewLink . '<a title="Open the images in a new window" target="_blank" href="view_images.php?imgURL=' . $strTemp . '">';
					if($soleImage) {
					$strViewLink = $strViewLink . '<img class="imgPreview" src="../' . sx_getImgFolder($strTemp) . '"></a>';
					}else{
						$strViewLink = $strViewLink . '<img class="imgPreview" src="' . sx_getImgFolder($strTemp) . '"></a>';
					}
				} else {
					if ($strViewLink != "") {
						$strViewLink = $strViewLink . " | ";
					}
					$strViewLink = $strViewLink . '<a title="' . $strTemp . '" target="_blank" href="view_images.php?imgURL=' . $strTemp . '">';
					$strViewLink = $strViewLink . "Image</a>";
				}
			}
			if($soleImage) {
				break;
			}
		}
    }else	if (strpos($tValue, ".mp3") > 0 || strpos($tValue, ".mp4") > 0 ) {
        $strViewLink = $strViewLink . '<a title="' . $value . '" target="_blank" href="view_images.php?imgURL=' . $value . '">';
        $strViewLink = $strViewLink . "View Video</a>";

	} elseif (strpos($value, "@") > 0) {
		$strViewLink = '<a target="_blank" href="mailto:' . $value . '"><span title="' . $value . '">Mail</span></a>';
	} elseif (strpos($value, "http://") !== false || strpos($value, "https://") !== false) {
		$strViewLink = '<a target="_blank" title="' . $value . '" href="' . $value . '">Open Link</a>';
	} elseif (strpos($value, "www.") !== false) {
		$strViewLink = '<a target="_blank" title="' . $value . '" href="http://' . $value . '">Open Link</a>';
	} else {
		$strViewLink = $value;
	}
	return  $strViewLink;
}
