<?php
if ($radioWeek) {
    $aResults = sx_getEventByDatePeriod($date_ThisMonday, $date_ThisSunday);
}

if ($strExport == "") { ?>
    <p style="font-family: Verdana, Arial, helvetica; font-size: 9pt;">
        <a href="default.php"><?= lngHomePage ?></a> |
        <a target="_top" href="sx_PrintPage.php?print=events&pg=week&monday=<?= $date_ThisMonday ?>&export=print"><?= lngPrintText ?></a> |
        <a target="_top" href="sx_PrintPage.php?print=events&pg=week&monday=<?= $date_ThisMonday ?>&export=word"><?= lngSaveInWord ?></a> |
        <a target="_top" href="sx_PrintPage.php?print=events&pg=week&monday=<?= $date_ThisMonday ?>&export=html"><?= lngSaveInHTML ?></a>
    </p>
    <hr>
<?php
} ?>
<h1><?= str_SiteTitle . ": " . $str_EventsByWeekTitle ?></h1>
<?php
if ($strExport == "") { ?>
    <table style="width: 100%;">
        <tr>
            <th style="font-size: 1.5em; vertical-align: middle; text-align: center; padding: 10px;">
                <a title="<?= lngPreviousWeek ?>" href="sx_PrintPage.php?print=events&pg=week&load=week&monday=<?= $date_PrevMonday ?>">&#10094;&#10094;&#10094;</a>
            </th>
            <td style="vertical-align: middle; text-align: center;">
                <h4><?= lngWeek . " " . intval(return_Week_In_Year($date_ThisMonday)) . ": " . $date_ThisMonday . " | " . $date_ThisSunday ?></h4>
            </td>
            <th style="font-size: 1.5em; vertical-align: middle; text-align: center; padding: 10px;">
                <a title="<?= lngNextWeek ?>" href="sx_PrintPage.php?print=events&pg=week&load=week&monday=<?= $date_NextMonday ?>">&#10095;&#10095;&#10095;</a>
            </th>
        </tr>
    </table>
<?php
} else { ?>
    <h4><?= lngWeek . " " . intval(return_Week_In_Year($date_ThisMonday)) . ": " . $date_ThisMonday . " | " . $date_ThisSunday ?></h4>
<?php
}
if (!is_array($aResults)) { ?>
    <h4><?= $date_ThisMonday . " | " . $date_ThisSunday ?></h4>
    <p><?= lngNoEvents ?></p>
    <?php
} else {

    echo "<table>";
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
                    echo "<tr>";
                    echo "<th><h3>" . lng_DayNames[$w] . "</h3> $loopDate </th>";
                    echo "<td>";
                }
                echo '<h3><a href="' . sx_LANGUAGE_PATH . 'events.php?eid=' . $iEventID . '&monday=' . $date_ThisMonday . '">' . $sEventTitle . '</a></h3>';
                echo "<p>" . $str_PlaceTime . "</p>";
                if ($radioRegisterToParticipate) { ?>
                    <p>
                        <a href="<?= sx_LANGUAGE_PATH ?>events.php?eid=<?= $iEventID ?>#ParticipationForm">Sign up to Participate »»</a>
                    </p>
<?php
                }

                $radioStartList = false;
                $radioEvent = true;
                $radioCloseTag = true;
            }
        }
        if ($radioEvent == false) {
            // Loop in a New Week Day
            echo "<tr>";
            echo "<th>" . lng_DayNames[$w] . "<br>$loopDate </th>";
            echo "<td>" . lngNoEvents . "</td>";
        } else {
            //= Loop in the Same Week Day;
            if ($radioCloseTag) {
                echo "</td></tr>";
            }
            $radioCloseTag = false;
        }
    }
    if ($radioCloseTag) {
        echo "</td></tr>";
    }
    echo "</table>";
}
$aResults = null;
?>