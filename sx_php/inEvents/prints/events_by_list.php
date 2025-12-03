<?php
if (strpos(sx_HOST_PATH, "/events.php") > 0) {
    $aEvents = sx_getEventByList(true);
} else {
    $aEvents = sx_getEventByList(false);
}
if (is_array($aEvents)) { ?>
    <section class="jqNavMainToBeCloned">
        <div class="events_by_list_block">
            <div class="print float_right">
                <?php
                getTextPrinter("sx_PrintPage.php?print=events&pg=list", "List");
                ?>
            </div>
            <h2 class="head"><span><?= $str_EventsListTitle ?></span></h2>
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
                //$sContactPhone = $aEvents[$r][10];
                //$sOrganizers = $aEvents[$r][11];
                $sEventTitle = $aEvents[$r][12];
                $sEventSubTitle = $aEvents[$r][13];
                //$sMediaURL = $aEvents[$r][14];
                $strParticipationMode = $aEvents[$r][15];

                if (empty($dEventStartDate) || return_Is_Date($dEventStartDate) === false) {
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
                        $sDay = $sDay . "<br>" . 
                            sx_getCapitals(mb_substr(lng_MonthNames[return_Month($dEventStartDate) - 1], 0, 3));
                        $sMonth = return_Month_Day($dEventEndDate) . "<br>" .
                            sx_getCapitals(mb_substr(lng_MonthNames[return_Month($dEventEndDate) - 1], 0, 3));
                    } else {
                        $sDay = $sDay . "-" . return_Month_Day($dEventEndDate);
                        $sMonth = sx_getCapitals(mb_substr(lng_MonthNames[return_Month($dEventStartDate) - 1], 0, 3));
                    }
                } else {
                    $sMonth = sx_getCapitals(mb_substr(lng_MonthNames[return_Month($dEventStartDate) - 1], 0, 3));
                } ?>
                <div class="events_by_list">
                    <ul class="events_by_list_date">
                        <li><?= $sDay ?></li>
                        <li><?= $sMonth ?></li>
                    </ul>
                    <ul class="events_by_list_content">
                        <li class="flex_between flex_nowrap flex_align_start">
                            <a href="events.php?eid=<?= $iEventID ?>&date=<?= $dEventStartDate ?>"><?= $sEventTitle ?></a>
                            <?php
                            getLocalEmailSender("events.php?eid=" . $iEventID, $sEventTitle, $sEventSubTitle, "");
                            ?>
                        </li>
                        <li><?= $str_PlaceTime ?></li>
                        <?php
                        if ($radioRegisterToParticipate) { ?>
                            <li>
                                <a href="events.php?eid=<?= $iEventID ?>#ParticipationForm">Sign up to participate »»</a>
                            </li>
                        <?php
                        } ?>
                    </ul>
                </div>
            <?php
            }
            ?>
        </div>
    </section>
<?php
}
$aEvents = null;
?>