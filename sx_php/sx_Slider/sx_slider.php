<?php
/*
V 2002-03
Image Ratio:	0.5, 0.36, 0.4, 0.5625, 0.6, 0.75, 1, 1.5 
Image Size:     cover, contain
Efffect Mode:   fade_both, fade_active, move_left_right, move_right_left, move_top_bottom, start_top_left, end_top_left, end_top_right
Thumps Type:    box, number, image
Thumps Place:   bottom_margin, bottom_left, bottom_center, bottom_right 
Description Place:	desc_margin desc_bottom 
*/
if (is_array($aSliderRows) && !empty($aSliderRows)) {
	$iRows = count($aSliderRows);
	$iCol = count($aSliderRows[0]);
?>
	<div class="sx_slider" id="jq_sx_slider" data-source="<?= $str_SliderSource ?>" data-mode="<?= $str_SliderEfffectMode ?>" data-thamp_place="<?= $str_SliderThumpsPlace ?>" data-desc_place="<?= $str_SliderDescPlace ?>" data-ratio="<?= $str_SliderImageRatio ?>" data-type="<?= $str_SliderThumpsType ?>">
		<figure>
			<?php
			for ($i = 0; $i < $iRows; $i++) {
				$iLinkID = $aSliderRows[$i][0];
				if (intval($iLinkID) == 0) {
					$iLinkID = 0;
				}
				$strSliderTitle = $aSliderRows[$i][1];
				$strSliderTitle = sx_Replace_Quotes($strSliderTitle);
				$strSliderSubTitle = $aSliderRows[$i][2];
				if (!empty($strSliderSubTitle) && strlen($strSliderSubTitle) > 0) {
					$strSliderSubTitle = sx_Replace_Quotes($strSliderSubTitle);
				}
				$strSliderImage = $aSliderRows[$i][3];

				$strHREF = "#";
				$strSliderAuthor = "";
				$strSliderEventDate = "";
				$strEventTime = "";
				$strSliderEventDateTime = "";
				$strEventWeekDay = "";

				if ($str_SliderSource == "Slider") {
					if (intval($iLinkID) > 0) {
						$strLinkType = $aSliderRows[$i][4];
						if ($strLinkType == "Text" || $strLinkType == "News") {
							$strHREF = 'texts.php?tid=' . $iLinkID;
						} elseif ($strLinkType == "About") {
							$strHREF = 'about.php?aboutid=' . $iLinkID;
						} elseif ($strLinkType == "Product") {
							$strHREF = 'products.php?pid=' . $iLinkID;
						} elseif ($strLinkType == "Group") {
							$strHREF = 'products.php?int0=' . $iLinkID;
						} elseif ($strLinkType == "Category") {
							$strHREF = 'products.php?int1=' . $iLinkID;
						} elseif ($strLinkType == "Gallery") {
							$strHREF = 'ps_gallery.php?gid=' . $iLinkID;
						} elseif ($strLinkType == "Video") {
							$strHREF = 'ps_media.php?archID=' . $iLinkID;
						} elseif ($strLinkType == "PDF") {
							$strHREF = 'ps_PDF.php?archID=' . $iLinkID;
						} elseif ($strLinkType == "Articles") {
							$strHREF = 'articles.php?aid=' . $iLinkID;
						}
					}
				} elseif ($str_SliderSource == "Texts") {
					if (intval($iLinkID) > 0) {
						$strHREF = 'texts.php?tid=' . $iLinkID;
					}
					if ($aSliderRows[$i][4] != "") {
						$strSliderAuthor = $aSliderRows[$i][4] . " " . $aSliderRows[$i][5];
					}
					if ($aSliderRows[$i][6] != "") {
						if (!empty($strSliderAuthor)) {
							$strSliderAuthor = $strSliderAuthor . ", " . $aSliderRows[$i][6];
						} else {
							$strSliderAuthor = $aSliderRows[$i][6];
						}
					}
				} elseif ($str_SliderSource == "Events") {
					$strHREF = 'events.php?tid=' . $aSliderRows[$i][7] . "&eid=" . $iLinkID;
					$strSliderEventDate = $aSliderRows[$i][4];
					if (!empty($strSliderEventDate)) {
						$strEventWeekDay = substr(lng_DayNames[return_Week_Day_1_7($strSliderEventDate) - 1], 0, 3) . "<br>" . return_Month_Day_01($strSliderEventDate) . "/" . return_Month_01($strSliderEventDate);
						$strSliderEventDateTime = $strSliderEventDate;
						if (!empty($aSliderRows[$i][5]) && strlen($aSliderRows[$i][5]) > 0) {
							$strEventTime = $aSliderRows[$i][5];
							if (!empty($aSliderRows[$i][6]) && strlen($aSliderRows[$i][6]) > 0) {
								$strEventTime = $strEventTime . "-" . $aSliderRows[$i][6];
							}
							$strSliderEventDateTime .= ", " . $strEventTime;
						}
					}
				} elseif ($str_SliderSource == "Products") {
					if (intval($iLinkID) > 0) {
						$strHREF = 'products.php?pid=' . $iLinkID;
					}
					$strSliderSubTitle = number_format($aSliderRows[$i][4], 2) . " " . $usedCurrency;
				}

				if (!empty($strSliderAuthor) && strlen($strSliderAuthor) > 0) {
					$strSliderAuthor = sx_Replace_Quotes($strSliderAuthor);
				}

				if (empty($strSliderTitle)) {
                    $strAlt = get_Link_Title_From_File_Name($strSliderImage) .' - '. SX_imageAltName;
				}else{
                    $strAlt = $strSliderTitle;
                }
				$strNotes = "";
				if ($strSliderAuthor != "") {
					$strNotes = $strSliderAuthor;
				} elseif ($strSliderSubTitle != "") {
					$strNotes = $strSliderSubTitle;
				} ?>
				<img src="<?= $strSliderImgPath . $strSliderImage ?>" data-href="<?= $strHREF ?>" data-title="<?= $strSliderTitle ?>" data-notes="<?= $strNotes ?>" data-datetime="<?= $strSliderEventDateTime ?>" data-thumb="<?= $strEventWeekDay ?>" alt="<?= $strAlt ?>">
			<?php
			} ?>
			<div class="sx_container"></div>
		</figure>
	</div>
<?php
	$aSliderRows = null;
}
?>