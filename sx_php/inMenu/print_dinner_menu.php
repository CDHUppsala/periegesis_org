<?php

$strCatWhere = "";
$intMenuGroupID = 0;
if (isset($_GET["mcid"]) && !empty($_GET["mcid"])) {
	$intMenuGroupID = (int) $_GET["mcid"];
}

if (intval($intMenuGroupID) > 0) {
	$strCatWhere = " AND m.GroupID = " . $intMenuGroupID;
}

$rsMenu = null;
$sql = "SELECT m.MenuID, 
    m.GroupID, 
    m.CategoryID, 
    m.Volume, 
    m.MenuTitle" . str_LangNr . " AS MenuTitle, 
    m.MenuImage, 
    m.Price, 
    m.MenuContent, 
    mg.GroupName" . str_LangNr . " AS GroupName, 
    mg.GroupNotes" . str_LangNr . " AS GroupNotes,
    mc.CategoryName" . str_LangNr . " AS CategoryName, 
    mc.CategoryNotes" . str_LangNr . "  AS CategoryNotes
FROM (menu_dishes AS m
    INNER JOIN menu_dish_groups AS mg 
        ON m.GroupID = mg.GroupID) 
    LEFT JOIN menu_dish_categories AS mc ON m.CategoryID = mc.CategoryID
WHERE m.Available = True 
    AND mg.Hidden = False 
    AND (mc.Hidden = False OR mc.Hidden IS NULL) " . $strCatWhere . "
ORDER BY mg.Sorting DESC , 
    mg.GroupName, 
    mc.Sorting DESC , 
    mc.CategoryName";
$rs = $conn->query($sql)->fetchAll(PDO::FETCH_NUM);
if ($rs) {
	$rsMenu = $rs;
}
$rs = null;
$conn = null;


if (empty($strExport)) { ?>
	<a target="_top" href="sx_PrintPage.php?print=dinnermenu&mcid=<?= $intMenuGroupID ?>&export=print"><?= lngSavePrintInPDF ?></a> |
	<a target="_top" href="sx_PrintPage.php?print=dinnermenu&mcid=<?= $intMenuGroupID ?>&export=word"><?= lngSaveInWord ?></a> |
	<a target="_top" href="sx_PrintPage.php?print=dinnermenu&mcid=<?= $intMenuGroupID ?>&export=html"><?= lngSaveInHTML ?></a>
	<hr>
<?php
}

if (!is_array($rsMenu)) { ?>
	<h1><?= $str_MenuListTitle ?></h1>
	<p><?= lngRecordsNotFound ?></p>
<?php
} else { ?>
	<h1><?= $str_MenuListTitle ?></h1>
	<?php
	if (!empty($memo_MenuTopNote)) {
		echo $memo_MenuTopNote;
	}

	$iRows = count($rsMenu);
	$iLoopGroup = -1;
	$iLoopCat = -1;

	for ($r = 0; $r < $iRows; $r++) {
		$iMenuID = $rsMenu[$r][0];
		$iGroupID = $rsMenu[$r][1];
		$iCategoryID = $rsMenu[$r][2];
		$sVolume = $rsMenu[$r][3];
		$sMenuTitle = $rsMenu[$r][4];
		$sMenuImage = $rsMenu[$r][5];
		$sPrice = $rsMenu[$r][6];
		$sMenuContent = $rsMenu[$r][7];
		$sGroupName = $rsMenu[$r][8];
		$sGroupNotes = $rsMenu[$r][9];
		$sCategoryName = $rsMenu[$r][10];
		$sCategoryNotes = $rsMenu[$r][11];
			if (empty($sVolume)) {
				$sVolume = "&nbsp;";
			} else {
				$sVolume = rtrim($sVolume, ";");
				if (strpos($sVolume, ";") > 0) {
					$sVolume = str_replace(";", "<br>", $sVolume);
				}
			}
			$sPrice = str_replace(" ", "", $sPrice);
			$sPrice = rtrim($sPrice, ";");
			if (strpos($sPrice, ";") > 0) {
				$sPrice = str_replace(";", "€<br>", $sPrice);
			}
		if ($iLoopGroup != $iGroupID) {
			if ($iLoopGroup > 0) {
				echo "</table>";
			} ?>
			<h2><?= $sGroupName ?></h2>
			<?php
			if (!empty($sGroupNotes)) {
				echo $sGroupNotes;
			}
			echo "<table>";
		}
		if (intval($iCategoryID) == 0) { ?>
			<tr>
				<?php
				if ($radio_UseDishImages) { ?>
					<td style="width: 25%"><img alt="<?= $sMenuTitle ?>" src="<?= sx_ROOT_HOST ?>/images/<?= $sMenuImage ?>"></td>
				<?php
				} ?>
				<td style="width: 100%">
					<h3><?= $sMenuTitle ?></h3>
					<?= $sMenuContent ?>
				</td>
				<td><?= $sVolume ?></td>
				<td><?= $sPrice . SX_usedCurrency?></td>
			</tr>
			<?php
		} else {
			if ($iLoopCat != $iCategoryID) {
				if ($iLoopCat > 0) {
					echo "</table>";
				} ?>
				<h3><?= $sCategoryName ?></h3>
			<?php
				if ($sCategoryNotes != "") {
					echo $sCategoryNotes;
				}
				echo "<table>";
			} ?>
			<tr>
				<?php
				if ($radio_UseDishImages) { ?>
					<td style="width: 25%"><img alt="<?= $sMenuTitle ?>" src="<?= sx_ROOT_HOST ?>/images/<?= $sMenuImage ?>"></td>
				<?php
				} ?>
				<td style="width: 100%">
					<h3><?= $sMenuTitle ?></h3>
					<?= $sMenuContent ?>
				</td>
				<td><?= $sVolume ?></td>
				<td><?= $sPrice . SX_usedCurrency ?></td>
			</tr>
		<?php
		}
		$iLoopGroup = $iGroupID;
		$iLoopCat = $iCategoryID;
	}
	echo "</table>";
	if (!empty($memo_MenuBottomNote)) { ?>
		<?= $memo_MenuBottomNote ?>
<?php
	}
}
$rsMenu = null;
?>