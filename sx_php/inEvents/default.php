<?php

/**
 * The array with event information is created at config_events.php
 * to populate Meta Tags
 */

if (!is_array($arr_EventByID)) {
    echo "<h3>" . lngRecordsNotFound . "</h3>";
} else {
    $iEventID = $arr_EventByID[0][0];
    $int_TextID = $arr_EventByID[0][1];
    $dEventStartDate = $arr_EventByID[0][2];
    $dEventEndDate = $arr_EventByID[0][3];
    $sStartTime = $arr_EventByID[0][4];
    $sEndTime = $arr_EventByID[0][5];
    $sPlaceName = $arr_EventByID[0][6];
    $sPlaceAddress = $arr_EventByID[0][7];
    $sPlacePostalCode = $arr_EventByID[0][8];
    $sPlaceCity = $arr_EventByID[0][9];
    $sContactPhone = $arr_EventByID[0][10];
    $sOrganizers = $arr_EventByID[0][11];
    $sEventTitle = $arr_EventByID[0][12];
    $sEventSubTitle = $arr_EventByID[0][13];
    $sMediaURL = $arr_EventByID[0][14];
    $sPDFAttachements = $arr_EventByID[0][15];
    $memoNotes = $arr_EventByID[0][16];

    $radioRegisterToParticipate = $arr_EventByID[0][17];
    $strParticipationMode = $arr_EventByID[0][18];
    $strOnlineParticipationLink = $arr_EventByID[0][19];

    if (defined('SX_UseEventRegistration') && SX_UseEventRegistration) {
        if (!empty($dEventStartDate) && return_Is_Date($dEventStartDate)) {
            if ($dEventStartDate < date('Y-m-d')) {
                $radioRegisterToParticipate = false;
            }
        }
    } else {
        $radioRegisterToParticipate = false;
    }
    if (!empty($memoNotes)) {
        $int_TextID = 0;
    }

    $dt = return_Date_From_Datetime($dEventStartDate);
    $str_DatePeriod = lng_DayNames[return_Week_Day_1_7($dt) - 1] . ", " . return_Month_Day($dt) . " " . lng_MonthNamesGen[return_Month($dt) - 1] . " " . return_Year($dt);
    if (!empty($dEventEndDate)) {
        $dt = return_Date_From_Datetime($dEventEndDate);
        $str_DatePeriod .= " - " . lng_DayNames[return_Week_Day_1_7($dt) - 1] . ", " . return_Month_Day($dt) . " " . lng_MonthNamesGen[return_Month($dt) - 1] . " " . return_Year($dt);
    }

    $strGoogleMap = "";
    $str_PlaceTime = "";
    $str_EmailPlaceTime = "";
    if ($strParticipationMode != 'Online') {
        $str_PlaceTime = "<b>" . lngPlace . ":</b> ";
        $str_EmailPlaceTime = $str_PlaceTime;
        if (!empty($sPlaceName)) {
            $strGoogleMap = $sPlaceName;
            $str_PlaceTime .= $sPlaceName;
            $str_EmailPlaceTime .= $sPlaceName;
        }
        if (!empty($sPlaceAddress)) {
            if (!empty($strGoogleMap)) {
                $strGoogleMap .= ", ";
            }
            $strGoogleMap .= $sPlaceAddress;

            $str_PlaceTime .= ', ' . $sPlaceAddress;
            $str_EmailPlaceTime .= ', ' . $sPlaceAddress;
        }
        if (!empty($sPlacePostalCode)) {
            if (!empty($strGoogleMap)) {
                $strGoogleMap .= ", ";
            }
            $strGoogleMap .= $sPlacePostalCode . " ";
            $str_PlaceTime .= ', ' . $sPlacePostalCode;
        }
        if (!empty($sPlaceCity)) {
            $strGoogleMap = $strGoogleMap . $sPlaceCity;

            $str_PlaceTime .= ', ' . $sPlaceCity;
            $str_EmailPlaceTime .= ', ' . $sPlaceCity;
        }
        if (!empty($strGoogleMap)) {
            $strGoogleMap = trim(rtrim($strGoogleMap, ","));
            $strGoogleMap = urlencode($strGoogleMap);
            $str_PlaceTime .= ' <a target="_blank" href="https://www.google.com/maps/search/?api=1&query=' . $strGoogleMap . '">Google Map</a>';
        }
    } else {
        $str_PlaceTime = "<b>" . lngPlace . ":</b> Online";
        $str_EmailPlaceTime = "<b>" . lngPlace . ":</b> Online";
    }
    if (!empty($sStartTime)) {
        $str_PlaceTime .= "<br><b>" . lngTime . ":</b> " . $sStartTime;
        $str_EmailPlaceTime .= "<br><b>" . lngTime . ":</b> " . $sStartTime;
        if (!empty($sEndTime)) {
            $str_PlaceTime .= " - " . $sEndTime;
            $str_EmailPlaceTime .= " - " . $sEndTime;
        }
    }

    if (!empty($sOrganizers)) {
        $str_PlaceTime .= "<br><b>" . lngOrganizers . ":</b> " . $sOrganizers;
    }
    if (!empty($sContactPhone)) {
        $str_PlaceTime .= ", <b>" . lngContact . ":</b> " . $sContactPhone;
    }

    $str_EmailMedia = "";
    if (!empty($sMediaURL)) {
        $str_EmailMedia = $sMediaURL;
        if (strpos($sMediaURL, ";") > 0) {
            $str_EmailMedia = trim(explode(';', $sMediaURL)[0]);
        }
    }
    /**
     * If the detail description of the event
     * is in main text table, open the Text ID
     */

    if (intval($int_TextID) > 0) {
        include PROJECT_PHP . "/inTexts/read.php";
    } else { ?>
        <article>

            <h1 class="head"><span><?= $sEventTitle ?></span></h1>
            <div class="print align_right">
                <?php
                getTextPrinter("sx_PrintPage.php?print=events&pg=event&eid=" . $iEventID, "read" . $iEventID);
                getLocalEmailSender("events.php?eid=" . $iEventID, $sEventTitle, $sEventSubTitle, "");
                ?>
            </div>
            <?php
            if (!empty($sEventSubTitle)) { ?>
                <h2><?= $sEventSubTitle ?></h2>
            <?php
            } ?>
            <h3><?= $str_DatePeriod ?></h3>
            <p><?= $str_PlaceTime ?></p>
            <?php
            if (!empty($sMediaURL)) {
                if (strpos($sMediaURL, ";") > 0) {
                    get_Manual_Image_Cycler($sMediaURL, "", "");
                } else {
                    get_Any_Media($sMediaURL, "Left", "");
                }
            } ?>
            <div class="text_resizeable">
                <div class="text_max_width">
                    <?php
                    echo $memoNotes;
                    if (!empty($sPDFAttachements)) {
                        if (strpos('<a ', $sPDFAttachements) > 0) {
                            echo $sPDFAttachements;
                        } else {
                            sx_getDownloadableFiles($sPDFAttachements);
                        }
                    } ?>
                </div>
            </div>
        </article>
<?php
        $arr_EventByID = null;
    }
}



if ($radioRegisterToParticipate) {
    include __DIR__ . "/participants/participation_form.php";
}
?>