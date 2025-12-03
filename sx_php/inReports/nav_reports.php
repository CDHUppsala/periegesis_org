<?php
/**
 * If sortings are not defined, entries must be well sorted be ReportID
 */
$aResults = "";
$sql = "SELECT ReportID, ChapterName, SubChapterName, Title
	FROM reports
	WHERE ProjectID = " . $int_ProjectID . " AND Hidden = False " . str_LanguageAnd . "
	ORDER BY SortingChapters DESC, SortingSubChapters DESC, SortingTexts DESC, ReportID";
$stmt = $conn->prepare($sql);
$stmt->execute();
$aResults = $stmt->fetchAll(PDO::FETCH_NUM);
$stmt = null;

/*
echo "<pre>";
print_r($aResults);
echo "</pre>";
*/
//=================================================
// 3 levels accordion menu with 1 + 1 clicking level
// The 3rd level is shown within the 2nd one by clicking on the 1st Level
// The 2nd level openes be default but is clickable and can be closed
//=================================================

if (is_array($aResults)) {
    $iRows = count($aResults);
    $strNavPath = "reports.php?";
    ?>
	<section class="jqNavSideToBeCloned">
		<h2 class="head_nav slide_up jqToggleNextRight"><span><?= $strProjectName ?></span></h2>
		<div class="sxAccordionNav jqAccordionNav">
			<ul>
	<?php
    $loopChapter = -1;
    $loopSubChapter = "";
    $bLoop1 = false;
    $bLoop2 = false;
    for ($r = 0; $r < $iRows; $r++) {
        $iReportID = $aResults[$r][0];
        $currChapterName = $aResults[$r][1];
        $currSubChapterName = $aResults[$r][2];
        $strTitle = $aResults[$r][3];
        $nextChapterName = "";
        $nextSubChapterName = "";
        $nr = $r + 1;
        if ($nr < $iRows) {
            $nextChapterName = $aResults[$nr][1];
            $nextSubChapterName = $aResults[$nr][2];
        }

        if ($loopChapter != $currChapterName) {
            if ($bLoop2) {
                echo "</ul></li><!--0-->";
            }
            $bLoop2 = false;
            if ($bLoop1) {
                echo "</ul></li><!--1-->\n";
            }
            $bLoop1 = false;
            $loopSubChapter = "";
            if($currChapterName == $nextChapterName) {
                $strDisplay = "none";
                if ($currChapterName == @$strChapterName) {
                    $strDisplay = "block";
                } ?>
				<li><div><?= $currChapterName ?></div>
					<ul style="display: <?=$strDisplay?>;">
				<?php
                $bLoop1 = true;
            }
        }

        if (!empty($currSubChapterName) > 0 && $loopSubChapter != $currSubChapterName) {
            if(!empty($nextSubChapterName) && $currSubChapterName == $nextSubChapterName) {
                if ($bLoop2) {
                    echo "</ul></li><!--2-->";
                }
    			$bLoop2 = true; 
                $strDisplay = "none";
                if ($currSubChapterName == @$strSubChapterName) {
                    $strDisplay = "block";
                } ?>
						<li><div><?= $currSubChapterName ?></div>
							<ul style="display: <?=$strDisplay?>;">
                <?php
            }
        } 
        /**
         * For all list levels create a link to a Text
         */
        $strSelected = '';
        if($iReportID == $int_ReportID) {
            $strSelected = 'class="open" ';
        } ?>
				<li><a <?=$strSelected?>href="<?= $strNavPath ?>reportid=<?= $iReportID ?>"><?= $strTitle ?></a></li>
		<?php
        $loopChapter = $currChapterName;
        $loopSubChapter = $currSubChapterName;
    }
    if (!empty($loopSubChapter)) {
        echo "</ul></li><!--3-->";
    } 
    if ($bLoop1) {
        echo "</ul></li><!--4-->";
    } ?>
			</ul><!--c-->
		</div>
	</section>
<?php
}
$aResults = "";
?>