<?php
$strGoToPageName = "ps_gallery.php";
$aResults = null;

$sql = "SELECT 
		gg.GroupID, 
		gg.GroupName{$strLangNr} AS GroupName, 
		g.GalleryID, 
		g.GalleryName{$strLangNr} AS GalleryName 
	FROM galleries AS g 
		LEFT JOIN gallery_groups AS gg 
		ON g.GroupID = gg.GroupID 
	WHERE g.Hidden = False 
		AND gg.Hidden = False {$strLogin_AliasAnd}
	ORDER BY gg.Sorting DESC, gg.GroupID, g.Sorting DESC, g.GalleryID ";
$stmt = $conn->query($sql);
$rs = $stmt->fetchAll(PDO::FETCH_NUM);
if ($rs) {
	$aResults =  $rs;
}
$stmt = null;
$rs = null;

if (is_array($aResults)) {
	$strCurrentMenuTitle = lngProductGroups;
	if (!empty($strGalleryMenuTitle)) {
		$strCurrentMenuTitle = $strGalleryMenuTitle;
	}
?>
	<h1><span><?= $strCurrentMenuTitle ?></span></h1>
	<nav class="sxAccordionNav" id="jqAccordionNav">
		<ul>
			<?php
			$levelUL1 = False;
			$loop1 = -1;

			$iRows = count($aResults);
			for ($iRow = 0; $iRow < $iRows; $iRow++) {
				$intGroupID = $aResults[$iRow][0];
				if (intval($intGroupID) == 0) {
					$intGroupID = 0;
				}
				$strGroupName = $aResults[$iRow][1];
				$intLinkedCatID = $aResults[$iRow][2];
				if (intval($intLinkedCatID) == 0) {
					$intLinkedCatID = 0;
				}
				$strLinkedCatName = $aResults[$iRow][3];

				if (intval($intGroupID) != intval($loop1)) {
					if ($levelUL1) {
						echo "</ul></li>";
					}
					$levelUL1 = False;
					if (intval($intLinkedCatID) == 0) {
						echo '<li><a href="' . $strGoToPageName . '?int0=' . $intGroupID . '">' . $strGroupName . '</a></li>';
					} else {
						$levelUL1 = True;
						$strBoxView = "none";
						$strClassView = "";
						if (intval($int0) == intval($intGroupID)) {
							$strBoxView = "block";
							$strClassView = ' class="open"';
						}
						echo "<li>";
						echo "<div{$strClassView}>{$strGroupName}</div>";
						echo "<ul style=\"display: {$strBoxView} \">";
					}
				}
				if ($levelUL1) {
					$strClassView = "";
					if (intval($int1) == intval($intLinkedCatID)) {
						$strClassView = ' class="open"';
					}
					echo '<li>';
					echo "<a{$strClassView} href=\"{$strGoToPageName}?int1={$intLinkedCatID}\">{$strLinkedCatName}</a>";
					echo '</li>';
				}
				$loop1 = $intGroupID;
			}
			if ($levelUL1) {
				echo "</ul></li>";
			} ?>
		</ul>
	</nav>
<?php
}
$aResults = null;
?>