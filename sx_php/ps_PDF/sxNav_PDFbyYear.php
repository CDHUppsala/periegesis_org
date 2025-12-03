<?php

/**
 * THIS NAVIGATION MENU IS ORDERED BY PUBLICATION YEAR
 * Does Not include HiddenFilesName and Groups with LoginToRead
 */

function sx_GetPDFMenuRowsByYear()
{
    $conn = dbconn();
    $sql = "SELECT 
		a.ArchiveID,  
		a.ArchiveName, 
		a.PublicationYear, 
		a.InsertDate
		FROM (pdf_groups AS g 
		INNER JOIN pdf_archives AS a ON a.GroupID = g.GroupID) 
		WHERE g.Publish = True " . STR__LoginAnd . "
			AND a.Hidden = False " . STR__LanguageAnd . "
		ORDER BY a.PublicationYear DESC, a.InsertDate DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rs = $stmt->fetchAll(PDO::FETCH_NUM);
    if (is_array($rs)) {
        return $rs;
    } else {
        return null;
    }
}

$aResults = sx_GetPDFMenuRowsByYear();

if (is_array($aResults)) { ?>
    <h2><span><?= lngByYear ?></span></h2>
    <nav class="sxAccordionNav jqAccordionNav">
        <ul>
            <?php
            $iRows = count($aResults);
            $loopYear = 0;
            $radioYear = false;
            $radioFirstLoop = False;
            for ($iRow = 0; $iRow < $iRows; $iRow++) {
                $intArchiveID = $aResults[$iRow][0];
                $strArchiveName = $aResults[$iRow][1];
                $intPublicationYear = $aResults[$iRow][2];
                $dInsertDate = $aResults[$iRow][3];

                $intYear = $intPublicationYear;
                if (SX__radioHideInsertDate) {
                    $dInsertDate = "";
                } elseif (return_Is_Date($dInsertDate) == false) {
                    $dInsertDate = date("Y-m-d");
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
                    <li>
                        <div <?= $strOpen ?>><?= $intYear ?></div>
                        <ul style="display: <?= $strDisplay ?>">
                        <?php
                    }

                    $strOpen = "";
                    if (intval($intArchID) == floor($intArchiveID)) {
                        $strOpen = 'class="open" ';
                    } ?>
                        <li><a <?= $strOpen ?>href="ps_PDF.php?archID=<?= $intArchiveID ?>"><?= $strArchiveName ?> <span><?= $dInsertDate ?></span></a></li>
                    <?php
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