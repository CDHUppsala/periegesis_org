<?php
 
$aResults = sx_getBooksNav();
if (is_array($aResults)) {
	$strNavPath = "books.php?";
	if (intval($iBookSubCatID) > 0) {
		$iBookGroupID = $intBookGroupID;
		$iBookCatID = $intBookCatID;
	}  elseif (intval($iBookCatID) > 0) {
		$iBookGroupID = $intBookGroupID;
	}  ?>
<section class="jqNavMainToBeCloned">
	<h2 class="head slide_up jqToggleNextRight"><span><?=$str_BooksNavTitle ?></span></h2>
	<nav class="sxAccordionNav jqAccordionNav">
		<ul>
	<?php 
	$iRows = count($aResults);
	//dim $levelUL1, $levelUL2;
	//dim $loop1, $loop2;
	$levelUL1=False;
	$levelUL2=False;
	$loop1 = -1;
	$loop2 = -1;
 
	for ($iRow=0; $iRow < $iRows; $iRow++) {
		$intMenuGroupID = $aResults[$iRow][0];
			$intMenuGroupID = intval($intMenuGroupID);
			$strMenuGroupName = $aResults[$iRow][1];
		$intMenuCatID = $aResults[$iRow][2];
			$intMenuCatID = intval($intMenuCatID);
			$strMenuCatName = $aResults[$iRow][3];
		$intMenuSubCatID = $aResults[$iRow][4];
			$intMenuSubCatID = intval($intMenuSubCatID);
			$strMenuSubCatName = $aResults[$iRow][5];
 
		if (intval($intMenuGroupID) != intval($loop1)) {
			if ($levelUL2) { 
				echo "</ul></li></ul></li>";
			}  elseif ($levelUL1) { 
				echo "</ul></li>";
			}
			$levelUL1 = False;
			$levelUL2 = False;
			if (intval($intMenuCatID) == 0) {?>
			<li><a href="<?=$strNavPath?>bookGroupID=<?=$intMenuGroupID?>"><?=$strMenuGroupName?></a></li> <?php 
			} else {
				$levelUL1 = True;
				$strBoxView = "none";
				$strClassView = "";
				if (intval($iBookGroupID) == intval($intMenuGroupID)) {
					$strBoxView = "block";
					$strClassView = ' class="open"';
				} ?>
			<li><div<?= $strClassView?>><?=$strMenuGroupName?></div>
				<ul style="display: <?=$strBoxView?>;"> <?php 
			}
		}
		if ($levelUL1 && intval($intMenuCatID) != intval($loop2)) {
			if ($levelUL2) { 
				echo "</ul></li>";
			}
			$levelUL2 = False;
				$strBoxView = "none";
				$strClassView = "";
				if (intval($iBookCatID) == intval($intMenuCatID)) {
					$strBoxView = "block";
					$strClassView = ' class="open"';
				} 
			if (intval($intMenuSubCatID) == 0) { //The category has no subcategories ?>
					<li><a<?=$strClassView?> href="<?=$strNavPath?>bookCatID=<?=$intMenuCatID?>"><?=$strMenuCatName ?></a></li> <?php 
			} else {
				$levelUL2 = True ?>
					<li><div<?=$strClassView?>><?=$strMenuCatName?></div>
						<ul style="display: <?=$strBoxView?>;"> <?php 
			}
		}
		if ($levelUL2) { 
			$strClassView = "";
			if (intval($iBookSubCatID) == intval($intMenuSubCatID)) {
				$strClassView = ' class="open"';
			} ?>
							<li><a<?=$strClassView?> href="<?=$strNavPath?>bookSubCatID=<?=$intMenuSubCatID?>"><?=$strMenuSubCatName?></a></li> <?php 
		}
		$loop1=$intMenuGroupID;
		$loop2=$intMenuCatID;
	}
	if ($levelUL2) { 
		echo "</ul></li></ul></li>";
	}  elseif ($levelUL1) { 
		echo "</ul></li>";
	} ?>
		</ul>
	</nav>
</section>
<?php 
}
$aResults = null;
?>
