<?php

/**
 * 2 levels accordion menu with 2 clickable levels
 * X Number of Rows by GROUP and CATEGORIES
 * Optionally Filtered by Group Login (function parameter = true)
 * Only for MySQL
 * jqAccordionNav/jqAccordionNavNext
 */

if (is_array($arProgram)) {
    $iRows = count($arProgram);
    $strNavPath = "conferences.php?";
    $strTitleDates = '<span>' . $arProgram[0]['Conference_StartDate'] . '</span> <span>'
        . $arProgram[0]['Conference_EndDate'] . '</span>';
?>
    <section class="conference_program jqNavMainToBeCloned">
        <h5><span class="text_xxsmall"><?php echo lngConferenceProgram ?></span></h5>
        <div class="program_dates"><?php echo $strTitleDates ?></div>
        <h2 class="head_nav"><span><?php echo $arProgram[0]['Conference_Title'] ?></span></h2>
        <nav class="sxAccordionNav jqAccordionNav">
            <ul>
                <?php
                $loopID = -1;
                $loopSubID = -1;
                $bLoop1 = false;
                $bLoop2 = false;
                for ($r = 0; $r < $iRows; $r++) {
                    $dSessionDate = $arProgram[$r]["Session_Date"];
                    if (return_Is_Date($dSessionDate)) {
                        $sDateName = return_Week_Day_Name($dSessionDate) . " " . $dSessionDate;
                        $iDateID = intval(str_replace("-", "", $dSessionDate));
                    }

                    $iSessionID = $arProgram[$r]['SessionID'];
                    if (return_Filter_Integer($iSessionID) == 0) {
                        $iSessionID = 0;
                    }
                    $sSessionName = return_Time_Minutes($arProgram[$r]['Session_StartTime']) . "-" . return_Time_Minutes($arProgram[$r]['Session_EndTime'])  . " " . $arProgram[$r]['Session_Title'];
                    $iTextID = $arProgram[$r]['PaperID'];
                    $sTitle = $arProgram[$r]['Paper_Title'];
                    $dPublishingDate = return_Time_Minutes($arProgram[$r]['Paper_StartTime']) . "-" . return_Time_Minutes($arProgram[$r]['Paper_EndTime']);
                    $sName = $arProgram[$r]['Paper_Authors'];
                    $sBreak = $arProgram[$r]['Session_Break'];
                    $pBreak = $arProgram[$r]['Paper_Break'];
                    if (!empty($sName)) {
                        $sName = $sName . ", " . $arProgram[$r]['Speakers'];
                    } else {
                        $sName = $arProgram[$r]['Speakers'];
                    }
                    if (!empty($sName)) {
                        //$sName = ", " . $sName . ", ";
                    }
                    $sTitle = $dPublishingDate . ": " . $sTitle;

                    if (intval($loopID) != intval($iDateID)) {
                        if ($bLoop2) {
                            echo "</ul></li>";
                        }
                        if ($bLoop1) {
                            echo "</ul></li>";
                        }
                        $bLoop1 = false;
                        $bLoop2 = false;
                        $strClass = "";
                        $sDisplay = "none";
                        if ($date_SessionDate == $dSessionDate) {
                            $strClass = ' class="open"';
                            $sDisplay = "block";
                        }
                        echo '<li>';
                        echo '<div' . $strClass . '>' . $sDateName . '</div>';
                        echo '<ul style="display: ' . $sDisplay . '">';
                        $bLoop1 = true;
                        $loopSubID = 0;
                    }
                    if (intval($iSessionID) > 0 && intval($loopSubID) != intval($iSessionID)) {
                        if ($bLoop2) {
                            echo "</ul></li>";
                        }
                        $strClass = "";
                        $sDisplay = "none";
                        if (intval($int_SessionID) == intval($iSessionID)) {
                            $strClass = ' class="open"';
                            $sDisplay = "block";
                        }
                        if (!empty($iTextID) && (int) $iTextID > 0) {
                            $bLoop2 = true;
                            $strArchiveLink = "";
                            $radio_ShowArchivesList = true;
                            if ($radio_ShowArchivesList && $sBreak == false) {
                                $strArchiveLink = '<a class="archive" title="' . lngConferenceSession . '" href="' . $strNavPath . "sesid=" . $iSessionID . '"></a>';
                            }
                            echo '<li>' . $strArchiveLink;
                            echo '<div' . $strClass . '>' . $sSessionName . '</div>';
                            echo '<ul style="display: ' . $sDisplay . '">';
                        } else {
                            $bLoop2 = false;
                            if ($sBreak) {
                                echo '<li><a href="javascrip:void(0)"><span>' . $sSessionName . '</span></a></li>';
                            } else {
                                echo '<li><a' . $strClass . ' href="' . $strNavPath . "sesid=" . $iSessionID . '">' . $sSessionName . '</a></li>';
                            }
                        }
                    }
                    if (!empty($iTextID) && (int) $iTextID > 0) {
                        $strClass = "";
                        if (intval($int_PaperID) == intval($iTextID)) {
                            $strClass = 'class="open" ';
                        }
                        if ($pBreak) { ?>
                            <li><a <?= $strClass ?>href="javascrip:void(0)"><span><?= $sTitle ?></span></a></li>
                        <?php
                        } else { ?>
                            <li><a <?= $strClass ?>href="<?= $strNavPath ?>paperid=<?= $iTextID ?>"><?= $sTitle ?>
                                    <span><?= $sName ?></span></a></li>
                <?php
                        }
                    }
                    $loopID = $iDateID;
                    $loopSubID = $iSessionID;
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
}
$arProgram = null;
?>