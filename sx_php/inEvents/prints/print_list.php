<?php

if ($radioList) {
    $aEvents = sx_getEventByList(true);
}

if ($strExport == "") { ?>
    <p>
        <a href="default.php"><?= lngHomePage ?></a> |
        <a target="_top" href="sx_PrintPage.php?print=events&pg=list&export=print"><?= lngPrintText ?></a> |
        <a target="_top" href="sx_PrintPage.php?print=events&pg=list&export=word"><?= lngSaveInWord ?></a> |
        <a target="_top" href="sx_PrintPage.php?print=events&pg=list&export=html"><?= lngSaveInHTML ?></a>
    </p>
    <hr>
<?php
} ?>
<h1><?= str_SiteTitle . ": " . $str_EventsListTitle ?></h1>
<?php
if (is_array($aEvents)) { ?>
    <table>
        <?php
        $iRows = count($aEvents);
        $loopID = 0;
        for ($r = 0; $r < $iRows; $r++) {
            $iEventID = $aEvents[$r][0];
            $radioRegisterToParticipate = $aEvents[$r][1];
            $dEventStartDate = $aEvents[$r][2];
            $dEventEndDate = $aEvents[$r][3];
            $sStartTime = $aEvents[$r][4];
            $sEndTime = $aEvents[$r][5];
            $sPlaceName = $aEvents[$r][6];
            $sPlaceAddress = $aEvents[$r][7];
            $sPlacePostalCode = $aEvents[$r][8];
            $sPlaceCity = $aEvents[$r][9];
            $sContactPhone = $aEvents[$r][10];
            $sOrganizers = $aEvents[$r][11];
            $sEventTitle = $aEvents[$r][12];
            $sEventSubTitle = $aEvents[$r][13];
            //$sMediaURL = $aEvents[$r][14];
            $strParticipationMode = $aEvents[$r][15];


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


            $sDay = return_Month_Day($dEventStartDate);
            $sMonth = "";
            if (return_Is_Date($dEventEndDate)) {
                if (return_Month($dEventEndDate) != return_Month($dEventStartDate)) {
                    $sDay = $sDay . " " .
                        sx_getCapitals(mb_substr(lng_MonthNames[return_Month($dEventStartDate) - 1], 0, 3)) .
                        " " . return_Year($dEventStartDate) . "<br>";
                    $sMonth = return_Month_Day($dEventEndDate) . " " .
                        sx_getCapitals(mb_substr(lng_MonthNames[return_Month($dEventEndDate) - 1], 0, 3)) .
                        " " . return_Year($dEventEndDate);
                } else {
                    $sDay = $sDay . "-" . return_Month_Day($dEventEndDate);
                    $sMonth = sx_getCapitals(mb_substr(lng_MonthNames[return_Month($dEventStartDate) - 1], 0, 3)).
                    " " . return_Year($dEventStartDate);
                }
            } else {
                $sMonth = sx_getCapitals(mb_substr(lng_MonthNames[return_Month($dEventStartDate) - 1], 0, 3)) .
                    " " . return_Year($dEventStartDate);
            } ?>
            <tr>
                <th>
                    <?= $sDay . " " . $sMonth ?>
                </th>
                <td>
                    <a href="<?= sx_LANGUAGE_PATH ?>events.php?eid=<?= $iEventID ?>&date=<?= $dEventStartDate ?>"><?= $sEventTitle ?></a>
                    <p><?= $str_PlaceTime ?></p>
                    <?php
                    if ($radioRegisterToParticipate) { ?>
                        <p>
                            <a href="<?= sx_LANGUAGE_PATH ?>events.php?eid=<?= $iEventID ?>#ParticipationForm">Sign up to Participate »»</a>
                        </p>
                    <?php
                    } ?>
                </td>
            </tr>
        <?php
        }
        ?>
    </table>
<?php
}
$aEvents = null;
?>