<?php
if (!isset($aResults) && $radioMonth) {
    $aResults = sx_getEventByDatePeriod($date_FirstMonthDate, $date_LastMonthDate);
}

if ($strExport == "") { ?>
    <p>
        <a href="default.php"><?= lngHomePage ?></a> |
        <a target="_top" href="sx_PrintPage.php?print=events&pg=month&date=<?= $date_FirstMonthDate ?>&export=print"><?= lngPrintText ?></a> |
        <a target="_top" href="sx_PrintPage.php?print=events&pg=month&date=<?= $date_FirstMonthDate ?>&export=word"><?= lngSaveInWord ?></a> |
        <a target="_top" href="sx_PrintPage.php?print=events&pg=month&date=<?= $date_FirstMonthDate ?>&export=html"><?= lngSaveInHTML ?></a>
    </p>
    <hr>
<?php
}
?>

<h1><?= str_SiteTitle . ": " . $str_EventsByMonthTitle ?></h1>
<?php if ($strExport == "") { ?>
    <table style="width: 100%;">
        <tr>
            <th style="font-size: 1.5em; vertical-align: middle; text-align: center; padding: 20px;">
                <a title="<?= lngPreviousMonth ?>" href="sx_PrintPage.php?print=events&pg=month&load=month&date=<?= return_Add_To_Date($dDate, -1, 'month') ?>">&#10094;&#10094;&#10094;&#10094;</a>
            </th>
            <td style="vertical-align: middle; text-align: center;">
                <h3><?= lngMonth . ": " . lng_MonthNames[return_Month($dDate) - 1] . " " . return_Year($dDate) ?></h3>
            </td>
            <th style="font-size: 1.5em; vertical-align: middle; text-align: center; padding: 10px;">
                <a title="<?= lngNextMonth ?>" href="sx_PrintPage.php?print=events&pg=month&load=month&date=<?= return_Add_To_Date($dDate, 1, 'month') ?>">&#10095;&#10095;&#10095;&#10095;</a>
            </th>
        </tr>
    </table>
<?php
} else { ?>
    <h3><?= lngMonth . ": " . lng_MonthNames[return_Month($dDate) - 1] . " " . return_Year($dDate) ?></h3>
<?php
}
if (!is_array($aResults)) {
    echo "<h3>" . lngRecordsNotFound . "</h3>";
} else {
    $iRows = count($aResults); ?>
    <table style="width: 100%">
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

            if (!empty($sMediaURL)) {
                if (str_contains($sMediaURL, ";")) {
                    $sMediaURL = explode(";", $sMediaURL)[0];
                }
                $sMediaURL = sx_ROOT_HOST . "/images/" . $sMediaURL;
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


            $iDay = return_Month_Day($dEventStartDate);
            $strDay = strval($iDay);

            $radioEndDate = return_Is_Date($dEventEndDate);
            if ($radioEndDate) {
                $iStartMonth = return_Month($dEventStartDate);
                $iEndMonth = return_Month($dEventEndDate);
                if ($iStartMonth != return_Month($dEventEndDate) && $iStartMonth != return_Month($dDate)) {
                    $strDay = "1-" . return_Month_Day($dEventEndDate);
                } else {
                    $strDay = $iDay . "-" . return_Month_Day($dEventEndDate);
                }
            }

            if (intval($loopID) != intval($iDay)) { ?>
                <tr>
                    <th><?= $strDay ?></th>
                    <th style="width:60%;">
                        <?= lng_DayNames[return_Week_Day_1_7($dEventStartDate) - 1] . " " . $dEventStartDate ?>
                        <?php if ($radioEndDate) {
                            echo " " . lngTo . " " . lng_DayNames[return_Week_Day_1_7($dEventEndDate) - 1] . " " . $dEventEndDate;
                        } ?>
                    </th>
                </tr>
            <?php
                $loopID = $iDay;
            } ?>
            <tr>
                <td>
                    <?php
                    if ($sMediaURL != "") { ?>
                        <img src="<?= $sMediaURL ?>" alt="<?= $sEventTitle ?>" />
                    <?php
                    } ?>
                </td>
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
        } ?>
    </table>
<?php
} ?>