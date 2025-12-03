<?php
//=================================================
// 3 levels accordion menu with 1 + 1 clicking level
// The 3rd level is shown within the 2nd one by clicking on the 1st Level
// The 2nd level openes be default but is clickable and can be closed
//=================================================

include_once __DIR__ . "/functions_PublishedTexts_Queries.php";

$aResults = sx_GetRecentTextsByCategories(false);

if (is_array($aResults)) {
    $iRows = count($aResults);
    $strNavPath = "texts.php?";
    if (empty($str_PublishedTextsByClassTitle)) {
        $str_PublishedTextsByClassTitle = lngRecent;
    } ?>
    <section class="jqNavMainToBeCloned">
        <h2 class="head slide_up jqToggleNextRight"><span><?= $str_PublishedTextsByClassTitle ?></span></h2>
        <nav class="sxAccordionNav <?= sx_jqAccordionForm ?>">
            <ul>
                <?php
                $intExpandable = -1;
                $loopID = -1;
                $loopSubID = -1;
                $loopex = 0;
                $bLoop1 = false;
                $bLoop2 = false;
                for ($r = 0; $r < $iRows; $r++) {
                    $iGroupID = $aResults[$r][0];
                    $sGroupName = $aResults[$r][1];
                    $iCategoryID = $aResults[$r][2];
                    $sCategoryName = $aResults[$r][3];
                    $iTextID = $aResults[$r][4];
                    $sTitle = $aResults[$r][5];
                    $dPublishingDate = $aResults[$r][6];
                    $sName = $aResults[$r][7];
                    if (!empty($sName)) {
                        $sName = $sName . " " . $aResults[$r][8];
                    }
                    if ($sName != "") {
                        $sName = ", " . $sName . ", ";
                    }
                    $sName = $sName . $dPublishingDate;

                    if (intval($loopID) != intval($iGroupID)) {
                        if ($bLoop2) {
                            echo "</ul></li>";
                        }
                        if ($bLoop1) {
                            echo "</ul>";
                        }
                        $bLoop1 = false;
                        $bLoop2 = false;
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
                                if ($bLoop2) {
                                    echo "</ul></li>";
                                }
                                $bLoop2 = true;
                                $strClass = "";
                                $sDisplay = "none";
                                if (intval($int_CatID) == intval($iCategoryID)) {
                                    $strClass = ' class="open"';
                                    $sDisplay = "block";
                                }
                                $strArchiveLink = "";
                                if ($radio_ShowArchivesList) {
                                    $strArchiveLink = '<a class="archive" title="' . lngViewArchives . '" href="' . $strNavPath . "cid=" . $iCategoryID . '"></a>';
                                } ?>
                                <li><?= $strArchiveLink ?><div <?= $strClass ?>><?= $sCategoryName ?></div>
                                    <ul style="display: <?= $sDisplay ?>;">
                                    <?php
                                }
                                $strClass = "";
                                if (intval($int_TextID) == intval($iTextID)) {
                                    $strClass = 'class="open" ';
                                } ?>
                                    <li><a <?= $strClass ?>href="<?= $strNavPath ?>tid=<?= $iTextID ?>"><?= $sTitle ?>
                                            <span><?= $sName ?></span></a></li>
                                <?php
                                $loopID = $iGroupID;
                                $loopSubID = $iCategoryID;
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
        //Not open categories and subcategories when text is open from Most and Recent selections
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