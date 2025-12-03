<?php

/**
 * The variable (array) $aResults might have been set by the events_by_calendar
 *   if this application is used, so reuse it for events by month
 * If events by month is not used, set $aResults = null, just in case, to free resources
 */
if ($radioMonth) {
    //if (!isset($aResults)) {
        $aResults = sx_getEventByDatePeriod($date_FirstMonthDate, $date_LastMonthDate);
    //} ?>
    <div class="events_by_month_block">
        <div class="print float_right">
            <?php
            getTextPrinter("sx_PrintPage.php?print=events&pg=month&date=" . $date_FirstMonthDate, "month");
            ?>
        </div>
        <h2 class="head"><span><?= $str_EventsByMonthTitle ?></span></h2>
        <div class="pagination jqUniversalAjax">
            <ul>
                <li><a data-query="load=month&date=<?= return_Add_To_Date($dDate, -1, 'month') ?>" data-id="jqLoadMonth" data-url="ajax_Events.php" title="<?= lngPreviousMonth ?>" href="events.php?load=month&date=<?= return_Add_To_Date($dDate, -1, 'month') ?>">&#10094;&#10094;&#10094;&#10094;</a></li>
                <li><span><?= lng_MonthNames[return_Month($dDate) - 1] . " " . return_Year($dDate) ?></span></li>
                <li><a data-query="load=month&date=<?= return_Add_To_Date($dDate, 1, 'month') ?>" data-id="jqLoadMonth" data-url="ajax_Events.php" title="<?= lngNextMonth ?>" href="events.php?load=month&date=<?= return_Add_To_Date($dDate, 1, 'month') ?>">&#10095;&#10095;&#10095;&#10095;</a></li>
            </ul>
        </div>
        <?php
        if (!is_array($aResults)) {
            echo '<h3 class="align_center">' . lngRecordsNotFound . "</h3>";
        } else {
            $iRows = count($aResults); ?>
            <table class="events_by_month">
                <?php
                $loopID = 0;
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
                    $sMediaURL = $aResults[$r][14];
                    $strParticipationMode = $aResults[$r][15];

                    if (empty($dEventStartDate) || !return_Is_Date($dEventStartDate)) {
                        continue;
                    } else {
                        if ($dEventStartDate < date('Y-m-d')) {
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
                        $str_PlaceTime .= "<br><strong>" . lngOrganizers . ":</strong> " . $sOrganizers;
                    }
                    if (!empty($sContactPhone)) {
                        $str_PlaceTime .= ", <strong>" . lngContact . ":</strong> " . $sContactPhone;
                    }


                    if (!empty($sMediaURL) && str_contains($sMediaURL, ';')) {
                        $sMediaURL = trim(substr($sMediaURL, (strpos($sMediaURL, ";"))));
                    }
                    if (empty($sMediaURL)) {
                        $sMediaURL = STR_ReplaceListImage;
                    }

                    $radioEndDate = return_Is_Date($dEventEndDate);
                    $iStartMonth = return_Month($dEventStartDate);
                    $iWeekDay = return_Week_Day_1_7($dEventStartDate);
                    $iDay = return_Month_Day($dEventStartDate);
                    $strDay = strval($iDay);
                    /*
                    if ($radioEndDate) {
                        $iEndMonth = return_Month($dEventEndDate);
                        if ($iStartMonth != $iEndMonth && return_Month($dDate) != $iStartMonth) {
                            $strDay = "1-" . return_Month_Day($dEventEndDate);
                        } else {
                            $strDay = $iDay . "-" . return_Month_Day($dEventEndDate);
                        }
                    }
                        */
                    if (intval($loopID) != intval($iDay)) { ?>
                        <tr>
                            <th>
                                <div class="flex_between flex_nowrap">
                                    <div><?= $strDay ?></div>
                                    <div>
                                        <?php
                                        echo lng_DayNames[$iWeekDay - 1] . " " . $dEventStartDate;
                                        if ($radioEndDate) {
                                            echo " " . lngTo . " " . lng_DayNames[return_Week_Day_1_7($dEventEndDate) - 1] . " " . $dEventEndDate;
                                        } ?>
                                    </div>
                                </div>
                            </th>
                        </tr>
                    <?php
                        $loopID = $iDay;
                    } ?>
                    <tr>
                        <td>
                            <div class="flex_between flex_align_start">
                                <div class="flex_items">
                                    <?php
                                    if (!empty($sMediaURL)) {
                                        get_Any_Media($sMediaURL, "Center", "");
                                    } ?>
                                </div>
                                <div class="flex_items">
                                    <div class="flex_between flex_nowrap flex_align_basiline">
                                        <h4><a href="events.php?eid=<?= $iEventID ?>&date=<?= $dEventStartDate ?>"><?= $sEventTitle ?></a></h4>
                                        <?php
                                        getLocalEmailSender("events.php?eid=" . $iEventID, $sEventTitle, $sEventSubTitle, "");
                                        ?>
                                    </div>
                                    <?php
                                    if (!empty($sEventSubTitle)) {
                                        echo "<p>" . $sEventSubTitle . "</p>";
                                    } ?>
                                    <p><?= $str_PlaceTime ?></p>
                                    <?php
                                    if ($radioRegisterToParticipate) { ?>
                                        <p>
                                            <a href="events.php?eid=<?= $iEventID ?>#ParticipationForm">Sign up to participate »»</a>
                                        </p>
                                    <?php
                                    } ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php
                } ?>
            </table>
        <?php
        } ?>
    </div>
<?php
} ?>