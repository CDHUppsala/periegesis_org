<?php

/**
 * STUDIO X - 2 CLASSIFICATION LEVELS UNIVERSAL ACCORDION MENU
 * Last Links to the SECOND Classification (Gallery OR Category)
 */

$strGoToPageName = "ps_gallery_byfolder.php";
function sx_GetFolderGroups()
{
	$conn = dbconn();
	$sql = "SELECT 
			fg.GroupID, 
			fgg.GroupName" . STR__LangNr . " AS GroupName,
			fg.GalleryID, 
			fg.GalleryName" . STR__LangNr . " AS GalleryName 
		FROM folder_galleries AS fg 
			LEFT JOIN folder_gallery_groups AS fgg 
			ON fg.GroupID = fgg.GroupID 
		WHERE fg.Hidden = False 
			AND (fgg.Hidden = False OR fgg.Hidden IS NULL) " . STR__LoginAnd . " 
		ORDER BY fgg.Sorting DESC, GroupName, fg.Sorting DESC, GalleryName";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$rs = $stmt->fetchAll(PDO::FETCH_NUM);
	if (is_array($rs)) {
		return $rs;
	} else {
		return "";
	}
}

$aResults = sx_GetFolderGroups();

if (is_array($aResults)) { ?>
	<section class="jqNavMainToBeCloned">
		<h2><span><?= $strFolderGalleryMenuTitle ?></span></h2>
		<nav class="sxAccordionNav jqAccordionNav">
			<ul>
				<?php
				$levelUL1 = False;
				$loop1 = 0;
				$iRows = count($aResults);
				for ($iRow = 0; $iRow < $iRows; $iRow++) {
					$intListGroupID = $aResults[$iRow][0];
					if (intval($intListGroupID) == 0) {
						$intListGroupID = 0;
					}
					$strListGroupName = $aResults[$iRow][1];
					$intListCatID = $aResults[$iRow][2];
					if (intval($intListCatID) == 0) {
						$intListCatID = 0;
					}
					$strListCatName = $aResults[$iRow][3];

					if (intval($intListGroupID) != intval($loop1)) {
						if ($levelUL1) {
							echo "</ul></li><!--1-->";
						}
						$levelUL1 = True;
						$strBoxView = "none";
						$strClassView = "";
						if (intval($int0) == intval($intListGroupID)) {
							$strBoxView = "block";
							$strClassView = ' class="open"';
						}
						echo "<li>";
						echo "<div{$strClassView}>{$strListGroupName}</div>";
						echo "<ul style=\"display: {$strBoxView}\">";
					}
					if (intval($intListCatID) > 0) {
						$strClassView = "";
						if (intval($int1) == intval($intListCatID)) {
							$strClassView = ' class="open"';
						}
						echo '<li>';
						echo "<a{$strClassView} href=\"{$strGoToPageName}?int1={$intListCatID}\">{$strListCatName}</a>";
						echo '</li>';
					}
					$loop1 = $intListGroupID;
				}
				if ($levelUL1) {
					echo "</ul></li>";
				}
				?>
			</ul>
		</nav>
	</section>
<?php
}
$aResults = null;
?>