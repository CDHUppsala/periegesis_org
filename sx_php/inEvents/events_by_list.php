<?php
/**
 * Define if the event list opens in the first page of the site
 * or in the very event page
 * Used to show different number of events (less in First Page)
 */
if (strpos(sx_HOST_PATH, "/events.php") > 0) {
    $aEvents = sx_getEventByList(true);
} else {
    $aEvents = sx_getEventByList(false);
}
if (!empty($aEvents)) { ?>
    <section class="jqNavMainToBeCloned">
        <div class="print float_right">
            <?php
            getTextPrinter("sx_PrintPage.php?print=events&pg=list", "List");
            ?>
        </div>
        <h2 class="head"><span><?= $str_EventsListTitle ?></span></h2>
        <div class="events_by_list_block">
            <?php

            foreach ($aEvents as $aEvent) {
                // Access each event's data using associative keys
                $iEventID = $aEvent['EventID'];
                $radioRegisterToParticipate = $aEvent['RegisterToParticipate'];
                $dEventStartDate = $aEvent['EventStartDate'];
                $dEventEndDate = $aEvent['EventEndDate'];
                $sStartTime = $aEvent['StartTime'];
                $sEndTime = $aEvent['EndTime'];
                $sPlaceName = $aEvent['PlaceName'];
                $sPlaceAddress = $aEvent['PlaceAddress'];
                $sPlacePostalCode = $aEvent['PlacePostalCode'];
                $sPlaceCity = $aEvent['PlaceCity'];
                //$sContactPhone = $aEvent['ContactPhone'];
                //$sOrganizers = $aEvent['Organizers'];
                $sEventTitle = $aEvent['EventTitle'];
                $sEventSubTitle = $aEvent['EventSubTitle'];
                //$sMediaURL = $aEvent['MediaURL'];
                $strParticipationMode = $aEvent['ParticipationMode'];


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
                        <?php
                        if (!empty($sEventSubTitle)) {
                            echo '<strong><li>' . $sEventSubTitle . '</strong></li>';
                        } ?>
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