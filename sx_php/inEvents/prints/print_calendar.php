<style>
    table {
        font-size: 11pt;
        line-height: 110%;
    }

    table h1,
    table h2,
    table h3,
    table h4 {
        line-height: 100%;
        padding: 0;
        margin: 5pt 0
    }

    table h1 {
        font-size: 14pt;
    }

    table h2 {
        font-size: 13pt;
    }

    table h3 {
        font-size: 12pt;
    }

    table h4 {
        font-size: 11pt;
    }
</style>
<?php
if (!isset($aResults) && $radioCalendar) {
    $aResults = sx_getEventByDatePeriod($date_FirstMonthDate, $date_LastMonthDate);
}

if ($strExport == "") { ?>
    <p>
        <a href="default.php"><?= lngHomePage ?></a> |
        <a target="_top" href="sx_PrintPage.php?print=events&pg=calendar&date=<?= $date_FirstMonthDate ?>&export=print"><?= lngPrintText ?></a> |
        <a target="_top" href="sx_PrintPage.php?print=events&pg=calendar&date=<?= $date_FirstMonthDate ?>&export=word"><?= lngSaveInWord ?></a> |
        <a target="_top" href="sx_PrintPage.php?print=events&pg=calendar&date=<?= $date_FirstMonthDate ?>&export=html"><?= lngSaveInHTML ?></a>
    </p>
    <hr>
<?php
} ?>

<h1><?= str_SiteTitle . ": " . $str_EventsByCalendarTitle ?></h1>
<?php
if ($strExport == "") { ?>
    <table style="width: 100%;">
        <tr>
            <th style="font-size: 1.5em; vertical-align: middle; text-align: center; padding: 10px;">
                <a title="<?= lngPreviousMonth ?>" href="sx_PrintPage.php?print=events&pg=calendar&load=calendar&date=<?= return_Add_To_Date($dDate, -1, 'month') ?>">&#10094;&#10094;&#10094;</a>
            </th>
            <td style="vertical-align: middle; text-align: center;">
                <h4><?= lngMonth . ": " . lng_MonthNames[return_Month($dDate) - 1] . " " . return_Year($dDate) ?></h4>
            </td>
            <th style="font-size: 1.5em; vertical-align: middle; text-align: center; padding: 10px;">
                <a title="<?= lngNextMonth ?>" href="sx_PrintPage.php?print=events&pg=calendar&load=calendar&date=<?= return_Add_To_Date($dDate, 1, 'month') ?>">&#10095;&#10095;&#10095;</a>
            </th>
        </tr>
    </table>
<?php
} else { ?>
    <h4><?= lngMonth . ": " . lng_MonthNames[return_Month($dDate) - 1] . " " . return_Year($dDate) ?></h4>
    <?php
}
if (!is_array($aResults)) {
    echo "<h3>" . lngRecordsNotFound . "</h3>";
} else {
    echo '<table style="width: 100%;">';
    $iRows = count($aResults);
    for ($i = 0; $i < 7; $i++) {
        $sTemp = lng_DayNames[$i] ?>
        <th><?= mb_substr($sTemp, 0, 2) ?></th>
    <?php
    } ?>
    </tr>
    <tr>
        <?php
        /**
         * To start new week efter iLoopWeekDay = 7 
         * and to add the last week days after the end of month
         */
        $iLoopWeekDay = $date_MonthsFirstWeekDay;
        $iTemp = 0;
        for ($iCurrent = 1; $iCurrent < $date_MonthsFirstWeekDay; $iCurrent++) {
            $iTemp = return_Month_Day(return_Add_To_Date($date_FirstMonthDate, - ($date_MonthsFirstWeekDay - $iCurrent))) ?>
            <td style="color: #aaaaaa"><?= $iTemp ?></td>
        <?php
        }

        for ($iCurrent = 1; $iCurrent < ($int_MonthDays + 1); $iCurrent++) {
            $sCurrent = $iCurrent;
            if (strlen($sCurrent) == 1) {
                $sCurrent = "0" . $iCurrent;
            }
            $loopDate = date(return_Year($dDate) . "-" . return_Month_01($dDate) . "-" . $sCurrent) ?>
            <td>
                <?php
                echo '<div>' . $iCurrent . '</div>';

                for ($r = 0; $r < $iRows; $r++) {
                    $dEventStartDate = $aResults[$r][2];
                    if ($loopDate < ($dEventStartDate)) {
                        break;
                    }
                    $dEventEndDate = $aResults[$r][3];
                    $radioEndDate = return_Is_Date($dEventEndDate);
                    $strFirstDay = "";
                    if ($radioEndDate == false) {
                        $dEventEndDate = $dEventStartDate;
                    }
                    if ($loopDate == ($dEventStartDate) || ($loopDate > ($dEventStartDate) && $loopDate <= ($dEventEndDate))) {
                        $iEventID = $aResults[$r][0];
                        $radioRegisterToParticipate = $aResults[$r][1];
                        $sStartTime = $aResults[$r][4];
                        $sEndTime = $aResults[$r][5];
                        $sPlaceName = $aResults[$r][6];
                        $sPlaceAddress = $aResults[$r][7];
                        $sPlacePostalCode = $aResults[$r][8];
                        $sPlaceCity = $aResults[$r][9];
                        //$sContactPhone = $aResults[$r][10];
                        //$sOrganizers = $aResults[$r][11];
                        $sEventTitle = $aResults[$r][12];
                        //$sEventSubTitle = $aResults[$r][13];
                        //$sMediaURL = $aResults[$r][14];
                        $strParticipationMode = $aResults[$r][15];

                        if ($dEventStartDate < date('Y-m-d')) {
                            $radioRegisterToParticipate = false;
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

                        echo "<p>";
                        echo '<strong><a href="' . sx_LANGUAGE_PATH . "events.php?eid=" . $iEventID . "&date=" . $dEventStartDate . '">' . $sEventTitle . "</a></strong>";
                        echo "<br>";
                        echo $str_PlaceTime;
                        if ($radioRegisterToParticipate) { ?>
                            <br>
                            <a href="<?= sx_LANGUAGE_PATH ?>events.php?eid=<?= $iEventID ?>#ParticipationForm">Sign up to Participate »»</a>
                <?php
                        }
                        echo "</p>";
                    }
                } ?>
            </td>
            <?php
            if ($iLoopWeekDay == 7) { ?>
    </tr>
    <tr>
    <?php
                $iLoopWeekDay = 0;
            }
            $iLoopWeekDay = $iLoopWeekDay + 1;
        }
        if ($iLoopWeekDay != 1) {
            $z = 1;
            while ($iLoopWeekDay <= 7) { ?>
        <td style="color: #aaaaaa"><?= $z ?></td>
    <?php
                $iLoopWeekDay = $iLoopWeekDay + 1;
                $z = $z + 1;
            } ?>
    </tr>
<?php
        } ?>
</table>
<?php
}
$aResults = null;
?>