<?php

if ($radioWeek) {
    $aResults = sx_getEventByDatePeriod($date_ThisMonday, $date_ThisSunday);
}
?>
<div class="events_by_week_block">
    <div class="print float_right">
        <?php
        getTextPrinter("sx_PrintPage.php?print=events&pg=week&monday=" . $date_ThisMonday, "week");
        ?>
    </div>
    <h2 class="head"><span><?= $str_EventsByWeekTitle ?></span></h2>
    <div class="pagination jqUniversalAjax">
        <ul>
            <li><a data-query="load=week&monday=<?= $date_PrevMonday ?>" data-id="jqLoadWeek" data-url="ajax_Events.php" title="<?= lngPreviousWeek ?>" href="ajax_Events.php?load=week&monday=<?= $date_PrevMonday ?>">&#10094;&#10094;</a></li>
            <li class="remove_styles">
                <ul class="jqWeekTabs">
                    <?php
                    for ($i = 0; $i < 7; $i++) {
                        $iWD = intval(return_Week_Day_1_7(date('Y-m-d')));
                        $strClass = "";
                        if ($i == ($iWD - 1)) {
                            $strClass = ' class="active"';
                        } ?>
                        <li<?= $strClass ?>><span title="<?= lng_DayNames[$i] ?>"><?= trim(mb_substr(lng_DayNames[$i], 0, 1)) ?></span>
            </li>
        <?php
                    } ?>
        <li title="<?= lngWeekEvent ?>"><span><?= mb_substr(lng_DayNames[0], 0, 1) . "-" . mb_substr(lng_DayNames[6], 0, 1) ?></span></li>
        </ul>
        </li>
        <li><a data-query="load=week&monday=<?= $date_NextMonday ?>" data-id="jqLoadWeek" data-url="ajax_Events.php" title="<?= lngNextWeek ?>" href="events.php?load=week&monday=<?= $date_NextMonday ?>">&#10095;&#10095;</a></li>
        </ul>
    </div>
    <?php
    if (!is_array($aResults)) { ?>
        <div class="align_center">
            <h4><?= $date_ThisMonday . " | " . $date_ThisSunday ?></h4>
            <div><?= lngNoEvents ?></div>
        </div>
    <?php
    } else { ?>
        <ul class="events_by_week">
            <?php
            $iRows = count($aResults);
            $dateCurrent = date('Y-m-d');
            $radioCloseTag = false;

            for ($w = 0; $w < 7; $w++) {
                $radioStartList = true;
                $radioEvent = false;
                $loopDate = return_Add_To_Date($date_ThisMonday, $w);
                $strDispaly = "none";
                if ($loopDate == $dateCurrent) {
                    $strDispaly = "block";
                }

                for ($r = 0; $r < $iRows; $r++) {
                    $iEventID = $aResults[$r][0];
                    $radioRegisterToParticipate = $aResults[$r][1];
                    $dEventStartDate = $aResults[$r][2];
                    $dEventEndDate = $aResults[$r][3];
                    $sStartTime = $aResults[$r][4];
                    $sEndTime = $aResults[$r][5];
                    $sPlaceName = $aResults[$r][6];
                    $sPlaceAddress = $aResults[$r][7];
                    $sPlacePostalCode = $aResults[$r][8];
                    $sPlaceCity = $aResults[$r][9];
                    $sContactPhone = $aResults[$r][10];
                    $sOrganizers = $aResults[$r][11];
                    $sEventTitle = $aResults[$r][12];
                    $sEventSubTitle = $aResults[$r][13];
                    //$sMediaURL = $aResults[$r][14];
                    $strParticipationMode = $aResults[$r][15];

                    if(empty($dEventStartDate) || !return_Is_Date($dEventStartDate)) {
                        continue;
                    }else{
                        if($dEventStartDate < date('Y-m-d')) {
                            $radioRegisterToParticipate = false;
                        }
                    }

                    $strGoogleMap = "";
                    $str_PlaceTime = "";
                    if ($strParticipationMode != 'Online') {
                        $str_PlaceTime = "<strong>" . lngPlace . ":</strong> ";
                        if (!empty($sPlaceName)) {
                            $strGoogleMap = $sPlaceName;
                            $str_PlaceTime .= $sPlaceName . ", ";
                        }
                        if (!empty($sPlaceAddress)) {
                            if (!empty($strGoogleMap)) {
                                $strGoogleMap .= ", ";
                            }
                            $strGoogleMap .= $sPlaceAddress;
                            $str_PlaceTime .= $sPlaceAddress . ", ";
                        }
                        if (!empty($sPlacePostalCode)) {
                            if (!empty($strGoogleMap)) {
                                $strGoogleMap .= ", ";
                            }
                            $strGoogleMap .= $sPlacePostalCode . " ";
                        }
                        if (!empty($sPlaceCity)) {
                            $strGoogleMap = $strGoogleMap . $sPlaceCity;

                            $str_PlaceTime .= $sPlaceCity . ", ";
                        }
                        if (!empty($strGoogleMap)) {
                            $strGoogleMap = trim(rtrim($strGoogleMap, ","));
                            $strGoogleMap = urlencode($strGoogleMap);
                            $str_PlaceTime .= '<a target="_blank" href="https://www.google.com/maps/search/?api=1&query=' . $strGoogleMap . '">Map</a>';
                        }
                    } else {
                        $str_PlaceTime = "<strong>" . lngPlace . ":</strong> Online";
                    }
                    if (!empty($sStartTime)) {
                        $str_PlaceTime .= "<br><strong>" . lngTime . ":</strong> " . $sStartTime;
                        if (!empty($sEndTime)) {
                            $str_PlaceTime .= " - " . $sEndTime;
                        }
                    }

                    if (!empty($sOrganizers)) {
                        //$str_PlaceTime .= "<br><strong>" . lngOrganizers . ":</strong> " . $sOrganizers;
                    }
                    if (!empty($sContactPhone)) {
                        //$str_PlaceTime .= ", <strong>" . lngContact . ":</strong> " . $sContactPhone;
                    }

                    $radioEndDate = false;
                    if (return_Is_Date($dEventEndDate)) {
                        if ($loopDate >= $dEventStartDate && $loopDate <= $dEventEndDate) {
                            $radioEndDate = true;
                        }
                    }

                    if ($loopDate == $dEventStartDate || $radioEndDate) {
                        if ($radioStartList) {
                            if ($radioCloseTag) {
                                echo "</li>";
                            } ?>
                            <li style="display: <?= $strDispaly ?>">
                                <h4><?= lng_DayNames[$w] . " " . $loopDate ?></h4>
                            <?php
                        } ?>
                            <div>
                                <div class="flex_between flex_nowrap flex_align_start">
                                    <a href="events.php?eid=<?= $iEventID ?>&monday=<?= $date_ThisMonday ?>"><?= $sEventTitle ?></a>
                                    <?php
                                    getLocalEmailSender("events.php?eid=" . $iEventID, $sEventTitle, $sEventSubTitle, "")
                                    ?>
                                </div>
                                <div><?= $str_PlaceTime ?></div>
                                <?php
                                if ($radioRegisterToParticipate) { ?>
                                    <div>
                                        <a href="events.php?eid=<?= $iEventID ?>#ParticipationForm">Sign up to Participate »»</a>
                                    </div>
                                <?php
                                } ?>
                            </div>
                        <?php

                        $radioStartList = false;
                        $radioEvent = true;
                        $radioCloseTag = true;
                    }
                }
                if ($radioEvent == false) {
                    // Loop in a New Week Day
                        ?>
                            <li style="display:<?= $strDispaly ?>;">
                                <h4><?= lng_DayNames[$w] . " " . $loopDate ?></h4>
                                <div><?= lngNoEvents ?></div>
                            </li>
                    <?php
                } else {
                    //= Loop in the Same Week Day;
                    if ($radioCloseTag) {
                        echo "</li>";
                    }
                    $radioCloseTag = false;
                }
            }
            if ($radioCloseTag) {
                echo "</li>";
            } ?>
        </ul>
        <?php
        if (date('Y-m-d') < $date_ThisMonday || date('Y-m-d') > $date_ThisSunday) { ?>
            <script>
                var sx_ClickLastWeekTab = true;
            </script>
    <?php
        }
    }
    $aResults = null;
    ?>
</div>