<?php
//===========================================
//	THIS NAVIGATION MENU IS ORDERED BY GROYP AND PUBLICATION YEAR
//===========================================
function sx_GetPDFMenuRowsByGroupYear()
{
    $conn = dbconn();
    $sql = "SELECT 
		a.ArchiveID,  
		a.GroupID, 
		a.ArchiveName, 
		a.HiddenFilesName, 
		a.PublicationYear, 
		a.InsertDate, 
		g.LoginToRead, 
		g.GroupName".STR__LangNr." AS GroupName 
		FROM (pdf_groups AS g 
		INNER JOIN pdf_archives AS a ON g.GroupID = a.GroupID) 
		WHERE g.Publish = True 
			AND a.Hidden = False ". STR__LoginAnd . STR__LanguageAnd ."
		ORDER BY g.Sorting DESC, g.GroupID, a.Sorting DESC, a.PublicationYear DESC, InsertDate DESC ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rs = $stmt->fetchAll(PDO::FETCH_NUM);
    if (is_array($rs)) {
        return $rs;
    } else {
        return "";
    }
}

$aResults = sx_GetPDFMenuRowsByGroupYear();

if (is_array($aResults)) {?>
<h2><span><?=lngByGroupAndYear ?></span></h2>
<nav class="sxAccordionNav jqAccordionNav">
	<ul>
	<?php
    $iRows = count($aResults);
    $loopGroup = 0;
    $loopYear = 0;
    $radioYear = false;
    $radioFirstLoop = False;
    for ($iRow=0; $iRow < $iRows; $iRow++) {
        $intArchiveID = $aResults[$iRow][0];
        $intGroupID = $aResults[$iRow][1];
        $strArchiveName = $aResults[$iRow][2];
        $strHiddenFilesName = $aResults[$iRow][3];
        $intPublicationYear = $aResults[$iRow][4];
        $dInsertDate = $aResults[$iRow][5];
        $radioLoginToRead = $aResults[$iRow][6];
        $strGroupName = $aResults[$iRow][7];
        if (return_Is_Date($dInsertDate) == false) {
            $dInsertDate = date("Y-m-d");
        }
        if (intval($intPublicationYear) == 0) {
            $intPublicationYear = date("Y");
        }
        $intYear = $intPublicationYear;
            
        if (SX__radioHideInsertDate) {
            $dInsertDate = "";
        }
 
        if (intval($intGroupID) != intval($loopGroup)) {
            $strOpen = "";
            $strDisplay = "none";
            if ($radioFirstLoop && $radioYear) {
                echo "</ul></li>";
            }
            if (intval($loopGroup) > 0) {
                echo "</ul></li>";
            }
            if (intval($intGroupID) == intval($iGroupID)) {
                $strOpen = 'class="open"';
                $strDisplay = "block";
            }
            $radioYear = false;
            $radioFirstLoop = False;
            $loopYear = 0; ?>
		<li><div <?=$strOpen?>><?=$strGroupName?></div>
			<ul style="display: <?=$strDisplay?>">
			<?php
        }
 
        if (intval($intYear) != intval($loopYear)) {
            $radioYear = true;
            $strOpen = "";
            $strDisplay = "none";
            if ($radioFirstLoop) {
                echo "</ul></li>";
            }
            if (intval($intYear) == intval($iYear)) {
                $strOpen = 'class="open"';
                $strDisplay = "block";
            } ?>
		<li><div <?=$strOpen?>><?=$intYear?></div>
			<ul style="display: <?=$strDisplay?>">
			<?php
        }
 
        $strOpen = "";
        if (intval($intArchID) == floor($intArchiveID)) {
            $strOpen = 'class="open" ';
        }
        $radioSaveHiddenFile = false;
        if (!empty($strHiddenFilesName) && $radioLoginToRead) {
            if ($radioSaveHiddenFile) {?>
				<li><a <?=$strOpen?>href="sx_PrintFile.php?id=<?=$intArchiveID?>&type=pdf" target="_blank"><?= $strArchiveName ?> <span><?=$dInsertDate ?></span></a></li>
			<?php } else {?>
				<li><a <?=$strOpen?>href="ps_PDF.php?archID=<?=$intArchiveID?>&type=pdf"><?= $strArchiveName ?> <span><?=$dInsertDate ?></span></a></li>
			<?php ;}
        } else {?>
				<li><a <?=$strOpen?>href="ps_PDF.php?archID=<?=$intArchiveID?>"><?= $strArchiveName ?> <span><?=$dInsertDate ?></span></a></li>
		<?php ;}
        $loopGroup = $intGroupID;
        $loopYear = $intYear;
        $radioFirstLoop = True;
    }
    ?>
			</ul>
		</li>
		<?php if ($radioFirstLoop && $radioYear) {
        echo "</ul></li>";
    } ?>
	</ul>
</nav>
<?php
}
$aResults = null;
?>
