<?php
//===========================================
//	THIS NAVIGATION MENU IS ORDERED BY GROYP AND CATEGORY
//===========================================
function sx_GetPDFMenuRows()
{
    $conn = dbconn();
    $sql = "SELECT 
		a.ArchiveID,  
		a.GroupID, 
		a.CategoryID, 
		a.ArchiveName, 
		a.HiddenFilesName, 
		a.InsertDate, 
		g.LoginToRead, 
		g.GroupName" . STR__LangNr . " AS GroupName, 
		c.CategoryName" . STR__LangNr . " AS CategoryName 
		FROM (pdf_archives AS a
		INNER JOIN pdf_groups AS g ON g.GroupID = a.GroupID) 
		LEFT JOIN pdf_categories AS c ON a.CategoryID = c.CategoryID 
		WHERE g.Publish = True 
		AND (c.Publish = True OR c.Publish IS NULL)
		AND a.Hidden = False " . STR__LoginAnd . STR__LanguageAnd . "
		ORDER BY g.Sorting DESC, g.GroupName" . STR__LangNr . " ASC, 
		c.Sorting DESC, c.CategoryName" . STR__LangNr . " ASC, 
		a.Sorting DESC, a.InsertDate DESC, a.ArchiveID ASC ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rs = $stmt->fetchAll(PDO::FETCH_NUM);
    if (is_array($rs)) {
        return $rs;
    } else {
        return "";
    }
}

$aResults = sx_GetPDFMenuRows();

if (is_array($aResults)) { ?>
    <h2><span><?= lngByGroup ?></span></h2>
    <nav class="sxAccordionNav jqAccordionNav">
        <ul>
            <?php
            $iRows = count($aResults);
            $loopGroup = 0;
            $loopCat = 0;
            $radioCat = false;
            for ($iRow = 0; $iRow < $iRows; $iRow++) {
                $intArchiveID = $aResults[$iRow][0];
                $intGroupID = $aResults[$iRow][1];
                $intCategoryID = $aResults[$iRow][2];
                $strArchiveName = $aResults[$iRow][3];
                $strHiddenFilesName = $aResults[$iRow][4];
                $dInsertDate = $aResults[$iRow][5];
                $radioLoginToRead = $aResults[$iRow][6];
                $strGroupName = $aResults[$iRow][7];
                $strCategoryName = $aResults[$iRow][8];
                if (return_Is_Date($dInsertDate) == false) {
                    $dInsertDate = "";
                }
                if (SX__radioHideInsertDate) {
                    $dInsertDate = "";
                }

                if (intval($intGroupID) != intval($loopGroup)) {
                    $strOpen = "";
                    $strDisplay = "none";
                    if (intval($loopCat) > 0 && $radioCat) {
                        echo "</ul></li>";
                    }
                    if (intval($loopGroup) > 0) {
                        echo "</ul></li>";
                    }
                    if (intval($intGroupID) == intval($iGroupID)) {
                        $strOpen = ' class="open"';
                        $strDisplay = "block";
                    }
                    $radioCat = false;
                    $loopCat = 0 ?>
                    <li>
                        <div<?= $strOpen ?>><?= $strGroupName ?></div>
                            <ul style="display: <?= $strDisplay ?>">
                            <?php
                        }

                        if (intval($intCategoryID) > 0 && intval($intCategoryID) != intval($loopCat)) {
                            $radioCat = true;
                            $strOpen = "";
                            $strDisplay = "none";
                            if (intval($loopCat) > 0) {
                                echo "</ul></li>";
                            }
                            if (intval($intCategoryID) == intval($iCategoryID)) {
                                $strOpen = 'class="open"';
                                $strDisplay = "block";
                            } ?>
                                <li>
                                    <div <?= $strOpen ?>><?= $strCategoryName ?></div>
                                    <ul style="display: <?= $strDisplay ?>">
                                        <?php
                                    }

                                    if (intval($intCategoryID) == 0 && intval($loopCat) > 0) {
                                        echo "</ul></li>";
                                    }

                                    $strOpen = "";
                                    if (intval($intArchID) == floor($intArchiveID)) {
                                        $strOpen = 'class="open"';
                                    }
                                    if (!empty($strHiddenFilesName) && $radioLoginToRead) {
                                        if ($radioSaveHiddenFile) { ?>
                                            <li><a <?= $strOpen ?> href="sx_PrintFile.php?id=<?= $intArchiveID ?>&type=pdf" target="_blank"><?= $strArchiveName ?> <span><?= $dInsertDate ?></span></a></li>
                                        <?php } else { ?>
                                            <li><a <?= $strOpen ?> href="ps_PDF.php?archID=<?= $intArchiveID ?>&type=pdf"><?= $strArchiveName ?> <span><?= $dInsertDate ?></span></a></li>
                                        <?php }
                                    } else { ?>
                                        <li><a <?= $strOpen ?> href="ps_PDF.php?archID=<?= $intArchiveID ?>"><?= $strArchiveName ?> <span><?= $dInsertDate ?></span></a></li>
                                <?php
                                    }
                                    $loopGroup = $intGroupID;
                                    $loopCat = $intCategoryID;
                                } ?>
                                    </ul>
                                </li>
                                <?php if (intval($loopCat) > 0 && $radioCat) {
                                    echo "</ul></li>";
                                } ?>
                            </ul>
    </nav>
<?php
}
$aResults = null;
?>