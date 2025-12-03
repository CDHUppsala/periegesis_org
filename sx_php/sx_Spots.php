<?php

if (strpos(strtolower(sx_PATH), "/index.php") > 0 || strpos(strtolower(sx_PATH), "/default.php") > 0) {
	$strWhere = " AND (sg.ShowInFirstPage = True OR sg.ShowInAllPages = True) ";
} elseif (intval($int_GroupID) > 0) {
	$strWhere = " AND (sg.ShowInGroupID = " . $int_GroupID . " OR sg.ShowInAllPages = True) ";
} else {
	$strWhere = " AND sg.ShowInAllPages = True ";
}

$aResults = null;
$sql = "SELECT 
		sg.SpotGroupID,
		sg.SpotGroupName" . str_LangNr . " AS SpotGroupName, 
		s.TextID, 
		s.AboutID, 
		s.ThemeID, 
		s.PDFArchiveID, 
		s.MediaArchiveID, 
		s.GalleryID,
		s.FolderGalleryID,
		s.LinkURL, 
		s.SpotName, 
		s.MediaURL, 
		s.MediaPlace, 
		s.Notes 
		FROM spot_groups AS sg
		INNER JOIN spots AS s
		ON sg.SpotGroupID = s.SpotGroupID 
		WHERE sg.Active = True 
		AND s.Publish = True 
		AND (s.ShowDate <= '" . Date('Y-m-d') . "' OR s.ShowDate IS NULL) 
		AND (s.HideDate >= '" . Date('Y-m-d') . "' OR s.HideDate IS NULL) 
		" . $strWhere . str_LanguageAnd . "
		ORDER BY sg.Sorting DESC, sg.SpotGroupID ASC, s.Sorting DESC, s.HideDate ASC ";
$rs = $conn->query($sql)->fetchAll(PDO::FETCH_NUM);
if ($rs) {
	$aResults = $rs;
}
$rs = null;

if (is_array($aResults)) { ?>
	<section class="spots" aria-label="Spot Themes">
		<?php
		$iRows = count($aResults);
		$iLoopGroup = 0;
		for ($iRow = 0; $iRow < $iRows; $iRow++) {
			$iSpotGroupID = $aResults[$iRow][0];
			$sSpotGroupName = $aResults[$iRow][1];

			if (intval($iSpotGroupID) != $iLoopGroup) {
				if ($iRow > 0) {
					echo "</div></div>";
				} ?>
				<div class="spot_column">
					<h2><span><?= $sSpotGroupName ?></span></h2>
					<div class="grid_cards">
					<?php
				}
				$iLoopGroup = $iSpotGroupID;
				$intThisTextID = $aResults[$iRow][2];
				$intThisAboutID = $aResults[$iRow][3];
				$intCurThemeID = $aResults[$iRow][4];
				$intThisPDFArchiveID = $aResults[$iRow][5];
				$intThisMediaArchiveID = $aResults[$iRow][6];
				$intThisGalleryID = $aResults[$iRow][7];
				$intThisFolderGalleryID = $aResults[$iRow][8];
				$sLinkURL = $aResults[$iRow][9];
				$sSpotName = $aResults[$iRow][10];
				$sMediaURL = $aResults[$iRow][11];
				$sMediaPlace = $aResults[$iRow][12];
				$memoSpotNote = $aResults[$iRow][13];
				if (intval($intThisTextID == 0)) {
					$intThisTextID = 0;
				}
				if (intval($intThisAboutID == 0)) {
					$intThisAboutID = 0;
				}
				if (intval($intCurThemeID == 0)) {
					$intCurThemeID = 0;
				}

				$aTagOpen = "";
				$aTagClose = "";
				if (!empty($sLinkURL)) {
					$aTagClose = "</a>";
					$aTagOpen = return_Left_Link_Tag($sLinkURL);
				} elseif (intval($intCurThemeID) > 0) {
					$aTagClose = "</a>";
					$aTagOpen = '<a href="texts.php?themeid=' . $intCurThemeID . '">';
				} elseif (intval($intThisAboutID) > 0) {
					$aTagClose = "</a>";
					$aTagOpen = '<a href="about.php?aboutid=' . $intThisAboutID . '">';
				} elseif (intval($intThisTextID) > 0) {
					$aTagClose = "</a>";
					$aTagOpen = '<a href="texts.php?tid=' . $intThisTextID . '">';
				}
				$strAlt = $sMediaURL;
				if (!empty($sSpotName)) {
					$strAlt = sx_Remove_Quotes($sSpotName);
				} ?>
					<figure>
						<?php
						$sMediaPlace = "Center";
						if (!empty($sMediaURL)) {
							echo $aTagOpen . '<img alt="' . $strAlt . '" src="../images/' . $sMediaURL . '">' . $aTagClose;
						} ?>
						<figcaption>
							<?php
							if (!empty($sSpotName)) { ?>
								<h3><?= $aTagOpen . $sSpotName . $aTagClose ?></h3>
							<?php
							}
							if (!empty($memoSpotNote)) { ?>
								<div><?= $memoSpotNote ?></div>
							<?php
							} ?>
							<?php
							if (
								intval($intThisTextID) > 0
								|| intval($intThisPDFArchiveID) > 0
								|| intval($intThisGalleryID) > 0
								|| intval($intThisMediaArchiveID) > 0
							) { ?>
								<footer>
									<?php
									if (intval($intThisTextID) > 0) { ?>
										<a href="texts.php?tid=<?= $intThisTextID ?>"><?= lngOpenText ?></a><br>
									<?php
									}
									if (intval($intThisPDFArchiveID) > 0) { ?>
										<a target="_blank" title="<?= lngOpenInNewWindow ?>" href="ps_PDF.php?archID=<?= $intThisPDFArchiveID ?>"><?= lngOpenInPDFArchives ?></a><br>
									<?php
									}
									if (intval($intThisGalleryID) > 0) { ?>
										<a target="_blank" title="<?= lngOpenInNewWindow ?>" href="ps_gallery.php?galleryID=<?= $intThisGalleryID ?>"><?= lngViewGallery ?></a>
									<?php
									}
									if (intval($intThisFolderGalleryID) > 0) {
										$strGalleryPage = "photos.php";
										if ($radio_UseFolderGallery) {
											$strGalleryPage = "ps_gallery_byfolder.php";
										} ?>
										<a target="_blank" title="<?= lngOpenInNewWindow ?>" href=". $strGalleryPage ." ?int1=<?= $intThisFolderGalleryID ?>"><?= lngViewGallery ?></a>
									<?php
									}
									if (intval($intThisMediaArchiveID) > 0) { ?>
										<a target="_blank" title="<?= lngOpenInNewWindow ?>" href="ps_media.php?archID=<?= $intThisMediaArchiveID ?>"><?= lngViewVideoGallery ?></a>
									<?php
									} ?>
								</footer>
							<?php
							} ?>
						</figcaption>
					</figure>
				<?php
			}
				?>
					</div>
				</div>
	</section>
<?php
}
$aResults = null;
?>