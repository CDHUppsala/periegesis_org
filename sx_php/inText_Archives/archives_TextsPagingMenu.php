<?php
/*
Navigation List with links to Requested Archived TEXTS with Pagination
Is included IN includes_ArticlesAside.php:
-    List of Requested Texts by page from the Lowest Existing level of the 3 levels of clasification
-    List of Requested Texts by page from Themes, Authors and Dates or date periods
All Relevant VARIABLES and FUNCTIONS are defined in inText_Archives/archives_TextsPagingQuery.php

*/

$aResults = null;
if ($radio_ShowArchivesList && $radioArchivesNavigation) {
    $sql = "SELECT
		t.TextID,
        t.Title,
        t.PublishedDate,
		t.AuthorID,
		t.Coauthors,
        a.FirstName AS AuthorsFirstName,
        a.LastName AS AuthorsLastName
	FROM (" . sx_TextTableVersion . " AS t
		INNER JOIN text_groups AS g ON t.GroupID = g.GroupID)
	    LEFT JOIN text_authors AS a ON t.AuthorID = a.AuthorID
	WHERE " . $strPublishWhere . "
		(t.PublishedDate <= '" . date('Y-m-d') . "' OR (t.PublishedDate) IS NULL)
		AND t.Publish = True
		AND g.Hidden = False
	    " . str_LanguageAnd_Text . "
	ORDER BY t.PublishOrder DESC, t.PublishedDate DESC, t.TextID DESC ";
    //echo $sql;
    $aResults = sx_get_text_pagination($sql);
}

if (is_array($aResults)) { ?>
    <section class="jqNavMainToBeCloned">
        <h2 class="head"><span><?= $strFirstHeaderTitle ?></span></h2>
        <h5 class="archives"><span><?= $strSecondHeaderTitle ?></span></h5>
        <?php if (intval($iPageCount) > 1) { ?>
            <div class="text_xxsmall align_right marginTopMinus"><?= LNG__TotalRecords . ": " . $iRecordCount ?></div>
            <div class="page_navigation">
                <?php sx_getPageNavigation_ByForm($strPageNavigationURL, $iCurrentPage, $iPageCount); ?>
                <?php sx_getPageNavigation_ByArrows($strPageNavigationURL, $iCurrentPage, $iPageCount); ?>
            </div>
        <?php } ?>
        <nav class="nav_aside">
            <ul class="local max_height">
                <?php
                $iRows = count($aResults);
                $loopID = -1;
                for ($r = 0; $r < $iRows; $r++) {
                    $iTextID = $aResults[$r][0];
                    $sTitle = $aResults[$r][1];
                    $dPublishingDate = $aResults[$r][2];
                    $iAuthorID = $aResults[$r][3];
                    if (return_Filter_Integer($iAuthorID) == 0) {
                        $iAuthorID = 0;
                    }
                    $strCoauthors = $aResults[$r][4];

                    $sName = "";
                    if (intval($iAuthorID) > 0) {
                        $sName = $aResults[$r][5] . " " . $aResults[$r][6];
                    }
                    if (!empty($strCoauthors)) {
                        $sName = $sName . ", " . $strCoauthors;
                    }
                    if (!empty($sName)) {
                        $sName = $sName . ", ";
                    }
                    $strClassView = "";
                    if (intval($int_TextID) == intval($iTextID)) {
                        $strClassView = 'class="open" ';
                    } ?>
                    <li><a <?= $strClassView ?>href="<?= $strPageURL ?>tid=<?= $iTextID ?>&page=<?= $iCurrentPage ?>">
                            <?= $sTitle ?> <span><?= $sName . $dPublishingDate ?></span></a></li>
                <?php
                } ?>
            </ul>
        </nav>
    </section>
<?php
}
$aResults = null;
?>