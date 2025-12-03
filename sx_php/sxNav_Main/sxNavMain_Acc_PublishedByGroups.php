<?php
//=================================================
// 1 levels accordion menu
//=================================================

include_once __DIR__ . "/functions_PublishedTexts_Queries.php";

$aResults = sx_GetRecentTextsByGroups();
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
                $loopex = 0;
                $bLoop1 = false;
                for ($r = 0; $r < $iRows; $r++) {
                    $iGroupID = $aResults[$r][0];
                    $sGroupName = $aResults[$r][1];
                    $iTextID = $aResults[$r][2];
                    $sTitle = $aResults[$r][3];
                    $dPublishedDate = $aResults[$r][4];
                    $strName = $aResults[$r][5];
                    if ($strName != "") {
                        $strName = $strName . " " . $aResults[$r][6];
                    }
                    if ($strName != "") {
                        $strName = ", " . $strName ;
                    }
                    if(sx_showDatesInMainMenu && !empty($dPublishedDate)) {
                    $strName .= ", " . $dPublishedDate;
                    }

                    if (intval($loopID) != intval($iGroupID)) {
                        if ($bLoop1) {
                            echo "</ul></li>";
                        }
                        $bLoop1 = false;
                        $strClass = "";
                        $sDisplay = "none";
                        if (intval($int_GroupID) == intval($iGroupID)) {
                            $strClass = ' class="open"';
                            $sDisplay = "block";
                        }
                        $strArchiveLink = "";
                        if ($radio_ShowArchivesList) {
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
                        }
                        $strClass = "";
                        if (intval($int_TextID) == intval($iTextID)) {
                            $strClass = 'class="open" ';
                        } ?>
                            <li>
                                <a <?= $strClass ?>href="<?= $strNavPath ?>tid=<?= $iTextID ?>"><?= $sTitle ?> <span><?= $strName ?></span></a>
                            </li>
                        <?php
                        $loopID = $iGroupID;
                    } ?>
                            </ul>
                        </li>
            </ul>
        </nav>
    </section>
    <?php
    if (intval($iGroupID) > 0) {
        if (isset($_GET["nav"])) {
            $intExpandable = -1;
        }
    }
    if (intval($intExpandable) > 0) { ?>
        <script>
            sx_reorderPublishedByClass(<?= $intExpandable ?>);
        </script>
<?php
    }
}
$aResults = "";
?>