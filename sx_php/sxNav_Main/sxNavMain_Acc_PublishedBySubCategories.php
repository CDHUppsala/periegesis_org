<?php

/**
 * 3 levels accordion menu with 2 clickable levels
 * X Number of Rows by GROUP, CATEGORIES and SUBCATEGORIES
 * Optionally Filtered by Group Login (function parameter = true)
 * Only for MySQL
 */

 include_once __DIR__ . "/functions_PublishedTexts_Queries.php";

 $aResults = sx_GetRecentTextsBySubCategories(false);

if (is_array($aResults)) {
    $iRows = count($aResults);
    $strNavPath = "texts.php?";
    if (empty($str_PublishedTextsByClassTitle)) {
        $str_PublishedTextsByClassTitle = lngRecent;
    } ?>
    <section class="jqNavMainToBeCloned">
        <h2 class="head slide_up_NU jqToggleNextRight_NU"><span><?= $str_PublishedTextsByClassTitle ?></span></h2>
        <nav class="sxAccordionNav <?= sx_jqAccordionForm ?>">
            <ul>
                <?php
                $intExpandable = -1;
                $loopID = -1;
                $loopSubID = -1;
                $loopSubSubID = -1;
                $loopex = 0;
                $bLoop1 = false;
                $bLoop2 = false;
                $bLoop3 = false;
                for ($r = 0; $r < $iRows; $r++) {
                    $iGroupID = $aResults[$r][0];
                    if (return_Filter_Integer($iGroupID) == 0) {
                        $iGroupID = 0;
                    }
                    $sGroupName = $aResults[$r][1];
                    $iCategoryID = $aResults[$r][2];
                    if (return_Filter_Integer($iCategoryID) == 0) {
                        $iCategoryID = 0;
                    }
                    $sCategoryName = $aResults[$r][3];
                    $iSubCategoryID = $aResults[$r][4];
                    if (return_Filter_Integer($iSubCategoryID) == 0) {
                        $iSubCategoryID = 0;
                    }
                    $sSubCategoryName = $aResults[$r][5];
                    $iTextID = $aResults[$r][6];
                    $sTitle = $aResults[$r][7];
                    $dPublishedDate = $aResults[$r][8];
                    $sName = $aResults[$r][9] . " " . $aResults[$r][10];
                    if ($sName != "") {
                        $sName = ", " . $sName . ", ";
                    }
                    $sName = $sName . $dPublishedDate;

                    if (intval($loopID) != intval($iGroupID)) {
                        if ($bLoop3) {
                            echo "</ul></li>";
                        }
                        if ($bLoop2) {
                            echo "</ul></li>";
                        }
                        if ($bLoop1) {
                            echo "</ul>";
                        }
                        $bLoop1 = false;
                        $bLoop2 = false;
                        $bLoop3 = false;
                        $strClass = "";
                        $sDisplay = "none";
                        if (intval($int_GroupID) == intval($iGroupID)) {
                            $strClass = ' class="open"';
                            $sDisplay = "block";
                        }
                        $strArchiveLink = "";
                        if ($radio_ShowArchivesList && intval($iCategoryID) == 0) {
                            $strArchiveLink = '<a class="archive" title="' . lngViewArchives . '" href="' . $strNavPath . "gid=" . $iGroupID . '"></a>';
                        } ?>
                        <li><?= $strArchiveLink ?><div <?= $strClass ?>><?= $sGroupName ?></div>
                            <ul style="display: <?= $sDisplay ?>;">
                                <?php
                                $bLoop1 = true;
                                if (intval($intExpandable) < 0) {
                                    if (intval($int_GroupID) == intval($iGroupID)) {
                                        $intExpandable = $loopex;
                                    }
                                }
                                $loopex++;
                                $loopSubID = 0;
                            }
                            if (intval($iCategoryID) > 0 && intval($loopSubID) != intval($iCategoryID)) {
                                if ($bLoop3) {
                                    echo "</ul></li>";
                                }
                                if ($bLoop2) {
                                    echo "</ul></li>";
                                }
                                $bLoop3 = false;
                                $bLoop2 = true;
                                $strClass = "";
                                $sDisplay = "none";
                                if (intval($int_CatID) == intval($iCategoryID)) {
                                    $strClass = ' class="open"';
                                    $sDisplay = "block";
                                }
                                $strArchiveLink = "";
                                if ($radio_ShowArchivesList && intval($iSubCategoryID) == 0) {
                                    $strArchiveLink = '<a class="archive" title="' . lngViewArchives . '" href="' . $strNavPath . "cid=" . $iCategoryID . '"></a>';
                                } ?>
                                <li><?= $strArchiveLink ?><div <?= $strClass ?>><?= $sCategoryName ?></div>
                                    <ul style="display: <?= $sDisplay ?>;">
                                    <?php
                                }
                                if (intval($iSubCategoryID) > 0 && intval($loopSubSubID) != intval($iSubCategoryID)) {
                                    if ($bLoop3) {
                                        echo "</ul></li>";
                                    }
                                    $bLoop3 = true;
                                    $strArchiveLink = "";
                                    $strClass = "";
                                    $sDisplay = "none";
                                    if (intval($int_SubCatID) == intval($iSubCategoryID)) {
                                        $strClass = ' class="open"';
                                        $sDisplay = "block";
                                    }
                                    if ($radio_ShowArchivesList) {
                                        $strArchiveLink = '<a class="archive" title="' . lngViewArchives . '" href="' . $strNavPath . "scid=" . $iSubCategoryID . '"></a>';
                                    } ?>
                                        <li><?= $strArchiveLink ?><div <?= $strClass ?>><?= $sSubCategoryName ?></div>
                                            <ul style="display: <?= $sDisplay ?>;">
                                            <?php
                                        }
                                        $strClass = "";
                                        if (intval($int_TextID) == intval($iTextID)) {
                                            $strClass = 'class="open" ';
                                        } ?>
                                            <li><a <?= $strClass ?>href="<?= $strNavPath ?>tid=<?= $iTextID ?>"><?= $sTitle ?><span><?= $sName ?></span></a></li>
                                        <?php
                                        $loopID = $iGroupID;
                                        $loopSubID = $iCategoryID;
                                        $loopSubSubID = $iSubCategoryID;
                                    }
                                    if (intval($loopSubSubID) > 0) {
                                        echo "</ul></li>";
                                    }
                                    if (intval($loopSubID) > 0) {
                                        echo "</ul></li>";
                                    } ?>
                                            </ul>
                                        </li>
                                    </ul>
        </nav>
    </section>
    <?php
    if (intval($iGroupID) > 0) {
        //Not open and reorder groups when text is open from Most or Recent selections
        if (isset($_GET["nav"])) {
            $intExpandable = -1;
        }
    }
    if (intval($intExpandable) > 0) { ?>
        <script>
        //    sx_reorderPublishedByClass(<?= $intExpandable ?>);
        </script>
<?php
    }
}
$aResults = "";
?>