<?php
 
//=================================================
// 3 levels accordion menu with 2 clickable level
// The 3rd level (including links) is shown by clicking the 2nd one 
// OBS! Uses the CSS sxNav_Accordion.css
//=================================================

$strNavPath = "default.php?pg=members&";
if ($str_MembersListTitle == "") {$str_MembersListTitle = "Poltical Map of Greece";}
 
$intGID = $NzInt[@$_GET["gid"]];
$intCID = $NzInt[@$_GET["cid"]];
$intSCID = $NzInt[@$_GET["scid"]];
 
$aResults= null;
if ($radio_UseMembersList) {
	$sql = "SELECT CountyID, CountyName, DistrictID, DistrictName, MunicipalityID, MunicipalityName
		FROM query_greece 
		ORDER BY CountyName, DistrictName, MunicipalityName ";
	$rs = $conn->query($sql);
	$aResults=$rs->fetch(PDO::FETCH_NUM);
	$rs = null;
}
 
if (is_array($aResults)) {
	$iRows = count($aResults);
	if (intval($intSCID) > 0) {
		for ($iRow=0; $iRow < $iRows; $iRow++) {
			$intMenuSubCatID = $aResults[$iRow][4];
			if (intval($intSCID) == intval($intMenuSubCatID)) {
				$currentSCName = $aResults[$iRow][5];
				$intCID = $aResults[$iRow][2];
				$currentCName = $aResults[$iRow][3];
				$intGID = $aResults[$iRow][0];
				$currentGName = $aResults[$iRow][1];
				break;
			}
		}
	} else {
		if (intval($intCID) > 0) {
		for ($iRow=0; $iRow < $iRows; $iRow++) {
			$intMenuCatID = $aResults[$iRow][2];
			if (intval($intCID) == intval($intMenuCatID)) {
				$currentCName = $aResults[$iRow][3];
				$intGID = $aResults[$iRow][0];
				$currentGName = $aResults[$iRow][1];
				break;
			}
		}
	}
	}
?>
<section class="jqNavMainToBeCloned">
	<h2 class="head slide_up jqToggleNextRight"><span><?=$str_MembersListTitle ?></span></h2>
	<div class="sxAccordionNav jqMenuGreece">
		<ul>
<?php
	$levelUL1=False;
	$levelUL2=False;
	$loop1=-1;
	$loop2=-1;
	$loop3=-1;
 
	for ($iRow=0; $iRow < $iRows; $iRow++) {
		$intMenuGroupID = $aResults[$iRow][0];
			$intMenuGroupID = $NzInt[$intMenuGroupID];
			$strMenuGroupName = $aResults[$iRow][1];
		$intMenuCatID = $aResults[$iRow][2];
			$intMenuCatID = $NzInt[$intMenuCatID];
			$strMenuCatName = $aResults[$iRow][3];
		$intMenuSubCatID = $aResults[$iRow][4];
			$intMenuSubCatID = $NzInt[$intMenuSubCatID];
			$strMenuSubCatName = $aResults[$iRow][5];
		
		if (intval($intMenuGroupID) != intval($loop1)) {
			if ($levelUL2) { 
				echo "</ul></li></ul></li>";
			} elseif ($levelUL1) { 
				echo "</ul></li>";
			}
			$levelUL1 = False;
			$levelUL2 = False;
			if (intval($intMenuCatID) == 0) { ?>
			<li><a href="<?=$strNavPath?>gid=<?=$intMenuGroupID?>"><span><?=$strMenuGroupName?></span></a></li>
<?php
			} else {
				$levelUL1 = True;
				if (intval($intGID) == intval($intMenuGroupID)) {
					$strBoxView = "block";
					$strClassView = "open";
				} else { 
					$strBoxView = "none";
					$strClassView = "";
				}
?>
			<li><div class="<?= $strClassView?>"><span><?=$strMenuGroupName?></span></div>
				<ul style="display: <?=$strBoxView?>;">
<?php
			}
		}
		if ($levelUL1 && intval($intMenuCatID) != intval($loop2)) {
			if ($levelUL2) { 
				echo "</ul></li>";
			}
			$levelUL2 = False;
			if (intval($intMenuSubCatID) == 0) { //The category has no subcategories
?>
					<li><a href="<?=$strNavPath?>cid=<?=$intMenuCatID?>"><span><?=$strMenuCatName ?></span></a></li>
<?php
			} else {
				$levelUL2=True;
				if (intval($intCID) == intval($intMenuCatID)) {
					$strBoxView = "block";
					$strClassView = "open";
				} else { 
					$strBoxView = "none";
					$strClassView = "";
				} 
?>
					<li><div class="<?=$strClassView?>"><span><?=$strMenuCatName?></span></div>
						<ul style="display: <?=$strBoxView?>;">
<?php
			}
		}
		if ($levelUL2 && intval($intMenuSubCatID) != intval($loop3)) {
?>
							<li><a href="<?=$strNavPath?>scid=<?=$intMenuSubCatID?>"><span><?=$strMenuSubCatName?></span></a></li>
<?php
		}
		$loop1 = $intMenuGroupID;
		$loop2 = $intMenuCatID;
		$loop3 = $intMenuSubCatID;
	}
	if ($levelUL2) { 
		echo "</ul></li></ul></li>";
	} elseif ($levelUL1) {
        echo "</ul></li>";
    }
?>
		</ul>
	</div>
<script>
$sx(".jqMenuGreece div").click(function(){
	$sx(this)
		.toggleClass("open")
		.next("ul").slideToggle(400)
	.end()
		.parent()
			.siblings()
				.find("div").removeClass("open")
			.end()
				.find("ul").hide(400)
});
</script>
</section>
<?php
}
$aResults = "";
?>
