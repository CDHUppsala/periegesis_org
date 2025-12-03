<?php

function sx_GetRecords($by)
{
    $conn = dbconn();
    $strOrderBy = "";
    if ($by == "year") {
        $strOrderBy = " ma.InsertDate DESC, ";
    }
    $sql = "SELECT 
			ma.CategoryID, 
			mc.CategoryName" . STR__LangNr . " AS CategoryName, 
			ma.ArchiveID, 
			ma.ArchiveName, 
			ma.InsertDate 
		FROM media_categories AS mc
			INNER JOIN media_archives AS ma
			ON mc.CategoryID = ma.CategoryID 
		WHERE mc.Hidden = False 
			AND ma.Hidden = False " . STR__LanguageAnd . "
		ORDER BY " . $strOrderBy . " 
			mc.Sorting DESC, 
			mc.CategoryID, 
			ma.Sorting DESC, 
			ma.ArchiveID ";
    $stmt = $conn->query($sql);
    $rs = $stmt->fetchAll(PDO::FETCH_NUM);
    if ($rs) {
        return  $rs;
    } else {
        return "";
    }
}

function sx_GetLists($aResult, $display, $by)
{
    $bByYear = false;
    if ($by == "year") {
        $bByYear = true;
    }
    $intYear = 0;
    $strDisplay = "none";
    if ($display) {
        $strDisplay = "block";
    } ?>
    <ul style="display: <?= $strDisplay ?>">
        <?php
        $iRows = count($aResult);
        $loopID = 0;
        for ($iRow = 0; $iRow < $iRows; $iRow++) {
            $intCategoryID = $aResult[$iRow][0];
            $strCategoryName = $aResult[$iRow][1];
            $intArchiveID = $aResult[$iRow][2];
            $strArchiveName = $aResult[$iRow][3];
            $dateInsertDate = $aResult[$iRow][4];
            if (return_Is_Date($dateInsertDate)) {
                $tempDate = DateTime::createFromFormat("Y-m-d", $dateInsertDate);
                $intYear = $tempDate->format("Y");
                if ($bByYear) {
                    $dateInsertDate = substr($dateInsertDate, 5);
                }
            }

            if ($bByYear && intval($intYear) > 0) {
                $sListTitle = $intYear;
                $iListID = intval($intYear);
                $iListOpen = INT__Year;
            } else {
                $sListTitle = $strCategoryName;
                $iListID = intval($intCategoryID);
                $iListOpen = INT__CategoryID;
            }

            if ($iListID != $loopID) {
                if (intval($loopID) > 0) {
                    echo "</ul></li>";
                }
                $strOpen = "";
                $strDisplay = "none";
                if (intval($iListOpen) == intval($iListID)) {
                    $strOpen = ' class="open"';
                    $strDisplay = "block";
                } ?>
                <li>
                    <div<?= $strOpen ?>><?= $sListTitle ?></div>
                        <ul style="display:<?= $strDisplay ?>">
                        <?php
                    }
                    $strOpen = "";
                    if (intval(INT__ArchID) == intval($intArchiveID)) {
                        $strOpen = 'class="open" ';
                    } ?>
                        <li><a <?= $strOpen ?>href="ps_media.php?archID=<?= $intArchiveID ?>"><span><?= $dateInsertDate ?></span> <?= $strArchiveName ?></a></li>
                    <?php
                    if ($bByYear) {
                        $loopID = intval($intYear);
                    } else {
                        $loopID = intval($intCategoryID);
                    }
                } ?>
                        </ul>
                </li>
    </ul>
<?php
}

?>
<h1><span><?= $strMediaMenuTitle ?></span></h1>
<nav class="sxAccordionNav jqAccordionNav">
    <ul>
        <?php
        $aResults = null;
        $index = -1;

        if ($radioMediaByCategory) {
        ?>
            <li>
                <div class="open"><?= $strByCategoryTitle ?></div>
                <?php
                $aResults = sx_GetRecords("");
                if (is_array($aResults)) {
                    sx_GetLists($aResults, true, "");
                }
                $aResults = null; ?>
            </li>
        <?php
        }
        if ($radioMediaByYear) {
        ?>
            <li>
                <div><?= $strByYearTitle ?></div>
                <?php
                $aResults = sx_GetRecords("year");
                if (is_array($aResults)) {
                    sx_GetLists($aResults, false, "year");
                }
                $aResults = null; ?>
            </li>
        <?php
        }
        ?>
    </ul>
</nav>