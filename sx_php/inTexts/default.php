<?php
/*
' GLOBAL Navigation to Requested Archived TEXTS with Paging
' Is used to open:
'    IN default.php
'    - Texts Published in First Page
'    - The Recent N Texts From the First Classification Level (Groups) of Texts (depending on the desig)
'    IN texts.php
'    - Requested Texts from the Lowest Existing level of the 3 levels of clasification
'    - Requested Texts from Themes and Authors
' All Relevant VARIABLES and FUNCTIONS are defined in inText_Archives/archives_TextsPagingQuery.php
 */
$radioFirstPage = false;
if (strpos(strtolower(sx_PATH), "/index.php") > 0) {
    $radioFirstPage = true;
}

/**
 * Use $radioArchivesNavigation together with $radioFirstPage
 *   to deal with ajax loading...
 * Consider removing $radioFirstPage, which cannot be recognosed 
 *   by ajax and is rather superfluous
 */

$strFirstPageWhere = "";
if (
    $radio_ShowByPublishInFirstPage
    && ($radioFirstPage || $radioArchivesNavigation == false)
) {
    $strFirstPageWhere = " AND t.PublishInFirstPage = True ";
}

$radioUseTwoColumns = sx_UseTwoColumns;
if (
    sx_UseTwoColumnsInFirstPage === false
    && ($radioFirstPage || $radioArchivesNavigation == false)
) {
    $radioUseTwoColumns = false;
}

$sx_HighlightFirstText = sx_HighlightFirstText;
/**
 * To Highlight first article only in first page which is not an archive page
 * If 2 columns and text is hilighted in first page (with 1 column)
 * - get udd records for the first pagination and even for the rest
 * - but NOT with Archive Navigation (when $radioArchivesNavigation === true)
 */
$iLimitLeft = 0;
$iLimitRight = 0;
if ($sx_HighlightFirstText) {
    if (intval($iCurrentPage) > 1 || $radioArchivesNavigation) {
        $sx_HighlightFirstText = false;
        $iLimitLeft = 1;
    } else {
        $iLimitRight = 1;
    }
}

/**
 * To deal with aside text
 * - they will not appear in the main (left) column in First Page
 *   and the will not appear in the pagination of the First Page
 * - But they must appear in various archives and pagination of archives   
 */

$strWherePublishedAside = "AND t.PublishAside = False";
if ($radioArchivesNavigation) {
    $iLimitLeft = 0;
    $iLimitRight = 0;
    $strWherePublishedAside = "";
}
/**
 * Query variables comes from inText_Archives/archives_TextsPagingQuery.php
 */
$aResults = "";
$sql = "SELECT
    t.TextID,
    t.IncludeInTextID,
    t.Title,
    t.SubTitle,
    t.AuthorID,
    a.FirstName AS AuthorFirstName,
    a.LastName AS AuthorLastName,
    a.Photo,
    t.Coauthors,
    t.Source,
    t.PublishedMedia,
    t.PublishedMediaLink,
    t.PublishedDate,
    t.HideDate,
    t.UseAuthorPhoto,
    t.FirstPageMediaURL,
    t.FirstPageMediaNotes,
    t.FirstPageMediaPlace,
    t.TopMediaExternalLink,
	t.AllowTextComments,
    t.FirstPageText,
    t.MainText
    FROM (texts AS t
	INNER JOIN text_groups AS g ON t.GroupID = g.GroupID
    LEFT JOIN text_categories AS c ON t.CategoryID = c.CategoryID
    LEFT JOIN text_subcategories AS sc ON t.SubCategoryID = sc.SubCategoryID
    )
    LEFT JOIN text_authors AS a ON t.AuthorID = a.AuthorID
	WHERE " . $strPublishWhere . "
    (t.PublishedDate <= '" . date('Y-m-d') . "' OR t.PublishedDate IS NULL)
	AND t.Publish = True
    " .  $strWherePublishedAside . "
	AND g.Hidden = False AND (c.Hidden = False OR c.Hidden IS NULL) AND (sc.Hidden = False OR sc.Hidden IS NULL)
    " . str_LoginToReadAnd_Grupp . $strFirstPageWhere . str_LanguageAnd_Text . "
    ORDER BY " . $strByPublishOrder . " t.PublishedDate DESC, t.TextID DESC ";
//echo $sql . '<hr>';
$aResults = sx_get_text_pagination($sql, $iLimitLeft, $iLimitRight);

if (!empty($strSecondHeaderTitle) && sx_showTopPagination) { ?>
    <h4 class="archives">
        <span><?= $strFirstHeaderTitle . ": " . $strSecondHeaderTitle . " - " . lngPage . ": " . $iCurrentPage . "/" . $iPageCount ?></span>
    </h4>
    <?php
}

if (!is_array($aResults)) {
    echo "<h2>" . lngTheCategoryIsEmpty . "</h2>";
} else {
    $strPageQuery = "";
    if ($radioArchivesNavigation) {
        $strPageQuery = "&page=" . $iCurrentPage;
    }
    if (intval($iPageCount) > 1 && sx_Nobody == true) { ?>
        <div class="page_navigation jqUniversalAjax" data-url="ajax_ArchivesPaging.php" data-id="jqLoadPageNav">
            <?php
            sx_getPageNavigation_ByArrows($strPageNavigationURL, $iCurrentPage, $iPageCount)
            ?>
        </div>
    <?php
    }

    $iRows = count($aResults);

    $radioApplyRelatedTexts = true;
    if (sx_ShowRelatedOnlyInFirstPage) {
        if ($radioFirstPage == false) {
            $radioApplyRelatedTexts = false;
        }
    }

    $arrRelated = null;
    if ($radio_UseRelatedTexts && $radioApplyRelatedTexts) {
        $arrIDs = array();
        $arrIncs = array();
        for ($z = 0; $z < $iRows; $z++) {
            $arrIDs[] = $aResults[$z][0];
            $x = $aResults[$z][1];
            if ($x > 0) {
                $arrIncs[] = $x;
            }
        }
        $arrRelated = sx_getRelatedTextsFirstPageAll($arrIDs, $arrIncs);
    }

    $strTemp = " text_max_width";
    $strClass = "";
    if ($sx_HighlightFirstText) {
        $strClass = ' class="heighlight"';
    }
    $iLoopRows = $iRows;
    for ($r = 0; $r < $iRows; $r++) {
        if ($radioUseTwoColumns && $r < 2) {
            if ($sx_HighlightFirstText) {
                if ($r == 1 && $iRows > 2) {
                    echo '<div class="grid_equal">';
                    $strTemp = "";
                    $iLoopRows = $iRows - 1;
                }
            } else {
                if ($r == 0 && $iRows > 1) {
                    echo '<div class="grid_equal">';
                    $strTemp = "";
                }
            }
        }
        /**
         * If two columns and udd number of rows or highlighten first text
         * Close the two columns grid and publish the last row along
         */
        if ($radioUseTwoColumns && $iRows > 2 && $r == $iRows - 1) {
            $remainder = $iRows % 2;
            $radioTemp = false;
            if ($sx_HighlightFirstText) {
                if ($remainder == 0) {
                    $radioTemp = true;
                }
            } elseif ($remainder > 0) {
                $radioTemp = true;
            }
            if ($radioTemp) {
                echo '</div>';
                $strTemp = " text_max_width";
                $iLoopRows = 0;
            }
        } ?>
        <article <?php echo $strClass ?>>
            <?php
            $strClass = "";
            $iTextID = $aResults[$r][0];
            $iIncludeInTextID = $aResults[$r][1];
            if (return_Filter_Integer($iIncludeInTextID) == 0) {
                $iIncludeInTextID = 0;
            };
            $strTitle = $aResults[$r][2];
            $strSubTitle = $aResults[$r][3];
            $iAuthorID = $aResults[$r][4];
            if (return_Filter_Integer($iAuthorID) == 0) {
                $iAuthorID = 0;
            }
            $strAuthorFirstName = $aResults[$r][5];
            $strAuthorLastName = $aResults[$r][6];
            $strPhoto = $aResults[$r][7];
            $strCoauthors = $aResults[$r][8];
            $strSource = $aResults[$r][9];
            $strPublishedMedia = $aResults[$r][10];
            $strPublishedMediaLink = $aResults[$r][11];
            $datePublishedDate = $aResults[$r][12];
            $radioHideDate = $aResults[$r][13];
            $radioUseAuthorPhoto = $aResults[$r][14];
            $strFirstPageMediaURL = $aResults[$r][15];
            $strFirstPageMediaNotes = $aResults[$r][16];
            $strFirstPageMediaPlace = $aResults[$r][17];
            $strTopMediaExternalLink = $aResults[$r][18];
            $radioAllowTextComments = $aResults[$r][19];
            $memoText = $aResults[$r][20];
            $radioReadMore = true;
            if (empty($memoText)) {
                $memoText = $aResults[$r][21];
                $radioReadMore = false;
            }
            /**
             * Use first page image (and top image) as links to external source 
             */
            if (sx_includeExternalLinkInFirstPageImage && !empty($strTopMediaExternalLink)) {
                $strTopMediaExternalLink = '';
            }

            $strAuthor = "";
            if (intval($iAuthorID) > 0) {
                $strAuthor = '<a class="opacity_link" title="' . lngAuthorAllTexts . '" href="texts.php?authorID=' . $iAuthorID . '">' . $strAuthorFirstName . " " . $strAuthorLastName . "</a>";
            }
            if (!empty($strCoauthors)) {
                if (!empty($strAuthor)) {
                    $strAuthor .= ", ";
                }
                $strAuthor .= $strCoauthors;
            }
            if (!empty($strSource)) {
                if (!empty($strAuthor)) {
                    $strAuthor .= ", ";
                }
                $strAuthor .= $strSource;
            }
            if (!empty($strPublishedMedia)) {
                $leftTag = "";
                $rightTag = "";
                if (!empty($strPublishedMediaLink)) {
                    $leftTag = return_Left_Link_Tag($strPublishedMediaLink);
                    $rightTag = "</a>";
                }
                if (!empty($strAuthor)) {
                    $strAuthor = $strAuthor . ", ";
                }
                $strAuthor .= $leftTag . $strPublishedMedia . $rightTag;
            }
            if (return_Is_Date($datePublishedDate) && $radioHideDate == false) {
                if (!empty($strAuthor)) {
                    $strAuthor = $strAuthor . ", ";
                }
                $strAuthor = $strAuthor . '<span>' . lngPublished . ":</span> " . $datePublishedDate;
            } ?>

            <header>
                <?php
                if (!empty($strTitle)) { ?>
                    <h1><a href="<?= $strPageURL ?>tid=<?= $iTextID . $strPageQuery ?>"><?= $strTitle ?></a></h1>
                <?php
                }
                if (!empty($strSubTitle)) { ?>
                    <h2><?= $strSubTitle ?></h2>
                <?php
                }
                if (!empty($strAuthor)) { ?>
                    <h4><?= $strAuthor ?></h4>
                <?php
                } ?>
            </header>

            <?php
            if (!empty($strFirstPageMediaURL)) {
                if (strpos($strFirstPageMediaURL, ";") > 0) {
                    get_Manual_Image_Cycler($strFirstPageMediaURL, "", $strFirstPageMediaNotes);
                } else {
                    if (!empty($strPhoto) && $radioUseAuthorPhoto) {
                        $strFirstPageMediaPlace = "Center";
                    }
                    get_Any_Media($strFirstPageMediaURL, $strFirstPageMediaPlace, $strFirstPageMediaNotes, $strTopMediaExternalLink, $iTextID);
                }
            }
            if (!empty($strPhoto) && $radioUseAuthorPhoto) {
                get_Any_Media($strPhoto, "Left", $strAuthorFirstName . " " . $strAuthorLastName, "", $iTextID);
            } ?>

            <div class="text<?= $strTemp ?>" lang="<?= sx_CurrentLanguage ?>">
                <?php echo $memoText ?>
            </div>

            <?php
            if ($radioReadMore) { ?>
                <div class="align_right">
                    <a href="<?= $strPageURL ?>tid=<?= $iTextID . $strPageQuery ?>"><?= lngReadMore ?></a>
                </div>
            <?php
            }
            if ($radio_UseRelatedTexts && !empty($arrRelated)) {
                sx_getRelatedTextsFirstPageByID($iTextID, $iIncludeInTextID, $arrRelated);
            } ?>
        </article>
    <?php
    }

    $aResults = null;
    $arrRelated = null;

    /**
     * If the list contains more than two texts 
     *   AND Include Texts In Two Columns is True,
     *   close the DIV for the grid display of articles
     */
    if ($radioUseTwoColumns && $iLoopRows > 2) {
        echo "</div>";
    }
}

/**
 * ==================================================================
 * PAGINATION: 
 * - Use ajax only for pagination of texts published in the first page
 * - Not with Archive Navigation 
 * ==================================================================
 */
if (intval($iPageCount) > 1 && (sx_showPaginationInFirstPage || $radioFirstPage == false)) { ?>
    <section>
        <?php
        if ($radioArchivesNavigation) {
            echo '<div class="page_navigation">';
        } else {
            echo '<div class="page_navigation jqUniversalAjax" data-url="ajax_ArchivesPaging.php" data-id="jqLoadPageNav">';
        }
        if (sx_includeFormPaginationInFirstPage) {
            sx_getPageNavigation_ByForm($strPageNavigationURL, $iCurrentPage, $iPageCount);
        }
        sx_getPageNavigation_ByArrows($strPageNavigationURL, $iCurrentPage, $iPageCount);
        echo '</div>';
        ?>
        <div class="text_xxsmall align_center"><?php echo LNG__TotalRecords . ': ' . $iRecordCount ?>mm</div>
    </section>
<?php
} ?>