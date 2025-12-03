<?php

if (!is_array($arProgram)) {
    echo "<h1>Coming soon!</h1>";
} else {
    $iRows = count($arProgram);
    $strNavPath = "conferences.php?";
    $dayStart = $arProgram[0]['Conference_StartDate'];
    $dayEnd = $arProgram[0]['Conference_EndDate'];
    $strTitleDates = "<span>" . $dayStart . "</span> <span>" . $dayEnd . "</span>";
    $iDays = return_Date_Difference($dayStart, $dayEnd)  + 1;
?>
    <section class="conference_program">
        <div class="print_sole"><?php getTextPrinter("sx_PrintPage.php?confid=" . $int_ConferenceID, $int_ConferenceID) ?></div>
        <h5><span class="text_xxsmall"><?php echo lngConferenceProgram ?></span></h5>
        <div class="program_dates"><?php echo $strTitleDates ?></div>
        <h2><?= $arProgram[0]['Conference_Title'] ?></h2>
        <nav class="nav_tabs_bg">
            <div class="nav_tabs jqNavTabs">
                <ul>
                    <li class="selected"><span><?= return_Week_Day_Name($dayStart) . "<br>" . $dayStart; ?></span></li>
                    <?php
                    for ($d = 1; $d <  $iDays; $d++) {
                        $tmpDate = return_Add_To_Date($dayStart, $d);
                    ?>
                        <li><span><?= return_Week_Day_Name($tmpDate) . "<br>" . $tmpDate; ?></span></li>
                    <?php
                    } ?>
                </ul>
            </div>
            <div class="nav_tab_layers sxAccordionNav jqAccordionNav">

                <?php
                $loopID = -1;
                $loopSubID = -1;
                $bLoop1 = false;
                $bLoop2 = false;
                for ($r = 0; $r < $iRows; $r++) {
                    $dSessionDate = $arProgram[$r]["Session_Date"];
                    if (return_Is_Date($dSessionDate)) {
                        $iDateID = intval(str_replace("-", "", $dSessionDate));
                    }

                    $iSessionID = $arProgram[$r]['SessionID'];
                    if (return_Filter_Integer($iSessionID) == 0) {
                        $iSessionID = 0;
                    }
                    $sTitle = $arProgram[$r]['Session_Title'];
                    $sModerator = $arProgram[$r]['Session_Moderator'];
                    if(!empty($sModerator)) {
                        $sTitle .= ', CHAIR: '. $sModerator;
                    }
                    $sPlace = $arProgram[$r]['Session_PlaceName'];

                    $sSessionName = "<b>" . return_Time_Minutes($arProgram[$r]['Session_StartTime']) . 
                        "-" . return_Time_Minutes($arProgram[$r]['Session_EndTime']) . "</b>, 
                        " . $sTitle;
                    if(!empty($sPlace)) {
                        $sSessionName .= ', <span>'. $sPlace .'</span>';
                    }
                    $iTextID = $arProgram[$r]['PaperID'];
                    $pTitle = $arProgram[$r]['Paper_Title'];
    
                    $timeStartEnd = return_Time_Minutes($arProgram[$r]['Paper_StartTime']) . "-" . return_Time_Minutes($arProgram[$r]['Paper_EndTime']);
                    $sName = $arProgram[$r]['Paper_Authors'];
                    $sBreak = $arProgram[$r]['Session_Break'];
                    $pBreak = $arProgram[$r]['Paper_Break'];

                    if (!empty($sName)) {
                        $sName = $sName . ", ";
                    }
                    if (!empty($arProgram[$r]['Speakers'])) {
                        $sName .= $arProgram[$r]['Speakers'] . ", ";
                    }

                    if (intval($loopID) != intval($iDateID)) {
                        if ($bLoop2) {
                            echo "</ul></li>";
                        }
                        if ($bLoop1) {
                            echo "</ul>";
                        }
                        $bLoop1 = false;
                        $bLoop2 = false;
                        $sDisplay = "none";
                        if ($r == 0) {
                            $sDisplay = "block";
                        } ?>
                        <ul style="display: <?= $sDisplay ?>;">
                            <?php
                            $bLoop1 = true;
                            $loopSubID = 0;
                        }
                        if (intval($iSessionID) > 0 && intval($loopSubID) != intval($iSessionID)) {
                            if ($bLoop2) {
                                echo "</ul></li>";
                            }
                            $bLoop2 = true;
                            echo '<li>';
                            if (!empty($iTextID) && (int) $iTextID > 0) {
                                echo '<div>' . $sSessionName . '</div>';
                            } else {
                                if ($sBreak) {
                                    echo '<a><span>' . $sSessionName . '</span></a>';
                                } else {
                                    echo '<a href="' . $strNavPath . "sesid=" . $iSessionID . '">' . $sSessionName . '</a>';
                                }
                            }
                            echo '<ul style="display: none;">';
                        }
                        if (!empty($iTextID) && (int) $iTextID > 0) {
                            $strClass = "";
                            if (intval($int_PaperID) == intval($iTextID)) {
                                $strClass = 'class="open" ';
                            }
                            if ($pBreak) { ?>
                                <li><a <?= $strClass ?>href="javascript:void(0)"><span><?= $timeStartEnd . " " . $sName ?> <?= $pTitle ?></a></span></li>
                            <?php
                            } else { ?>
                                <li><a <?= $strClass ?>href="<?= $strNavPath ?>paperid=<?= $iTextID ?>"><span><?= $timeStartEnd . " " . $sName ?></span> <?= $pTitle ?></a></li>
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
            </div>
        </nav>
        <?php
        echo '<p>';
        $sPlaceName = $arProgram[0]['PlaceName'];
        $sPlaceAddress = $arProgram[0]['PlaceAddress'];
        $sPlacePostalCode = $arProgram[0]['PlacePostalCode'];
        $sPlaceCity = $arProgram[0]['PlaceCity'];
        $sOrganizers = $arProgram[0]['Organizers'];
        echo "<b>" . lngPlace . ":</b> " . $sPlaceName . ", " .  $sPlaceAddress . ", " .  $sPlacePostalCode . " " .  $sPlaceCity . ". <b>" . lngOrganizers . ":</b> " . $sOrganizers;
        echo '</p>';

        if ($radio_LoggedParticipant) {
            if (!empty($str_WebinarURL)) {
                echo '<p class="text_small"><b>Live Connection URL:</b> <a href="' . $str_WebinarURL . '">' . lngClickHere . '</a></p>';
            }
        } ?>
    </section>
<?php
}
?>