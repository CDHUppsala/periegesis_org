<?php

if (!isset($intEventID) || intval($intEventID) == 0) {
    echo "<h3>" . lngRecordsNotFound . "</h3>";
    exit();
}
$aResults = null;
if (intval($intEventID) > 0) {
    $sql = "SELECT EventID, RegisterToParticipate,
        EventStartDate, EventEndDate, StartTime, EndTime, 
		PlaceName, PlaceAddress, PlacePostalCode, PlaceCity, 
		ContactPhone, Organizers,
        EventTitle,
        EventSubTitle, 
		MediaURL,
        Notes,
        ParticipationMode
	FROM events 
	WHERE Hidden = False 
	AND EventID = " . $intEventID;

    $stmt = $conn->query($sql);
    $rs = $stmt->fetch(PDO::FETCH_NUM);
    if ($rs) {
        $aResults = $rs;
    }
    $rs = null;
    $stmt = null;
}

if (!is_array($aResults)) {
    echo "<h3>" . lngRecordsNotFound . "</h3>";
} else {
    $iEventID = $aResults[0];
    $radioRegisterToParticipate = $aResults[1];
    $dEventStartDate = $aResults[2];
    $dEventEndDate = $aResults[3];
    $sStartTime = $aResults[4];
    $sEndTime = $aResults[5];
    $sPlaceName = $aResults[6];
    $sPlaceAddress = $aResults[7];
    $sPlacePostalCode = $aResults[8];
    $sPlaceCity = $aResults[9];
    $sContactPhone = $aResults[10];
    $sOrganizers = $aResults[11];
    $sEventTitle = $aResults[12];
    $sEventSubTitle = $aResults[13];
    $sMediaURL = $aResults[14];
    $memoNotes = $aResults[15];
    $strParticipationMode = $aResults[16];

    $dt = return_Date_From_Datetime($dEventStartDate);
    $str_DatePeriod = lng_DayNames[return_Week_Day_1_7($dt) - 1] . ", " . return_Month_Day($dt) . " " . lng_MonthNamesGen[return_Month($dt) - 1] . " " . return_Year($dt) . " (" . $dEventStartDate . ")";
    if (!empty($dEventEndDate)) {
        $dt = return_Date_From_Datetime($dEventEndDate);
        $str_DatePeriod .= " - " . lng_DayNames[return_Week_Day_1_7($dt) - 1] . ", " . return_Month_Day($dt) . " " . lng_MonthNamesGen[return_Month($dt) - 1] . " " . return_Year($dt);
    }

    $strGoogleMap = "";
    $str_PlaceTime = "";
    if ($strParticipationMode != 'Online') {
        $str_PlaceTime = "<strong>" . lngPlace . ":</strong> ";
        if ($sPlaceName != "") {
            $strGoogleMap = $sPlaceName;
            $str_PlaceTime .= $sPlaceName . ", ";
        }
        if ($sPlaceAddress != "") {
            if ($strGoogleMap != "") {
                $strGoogleMap .= ", ";
            }
            $strGoogleMap .= $sPlaceAddress;
            $str_PlaceTime .= $sPlaceAddress . ", ";
        }
        if ($sPlacePostalCode != "") {
            if ($strGoogleMap != "") {
                $strGoogleMap .= ", ";
            }
            $strGoogleMap .= $sPlacePostalCode . " ";
            $str_PlaceTime .= $sPlacePostalCode . " ";
        }
        if ($sPlaceCity != "") {
            $strGoogleMap = $strGoogleMap . $sPlaceCity;

            $str_PlaceTime .= $sPlaceCity . ", ";
        }
        if (!empty($strGoogleMap)) {
            $strGoogleMap = trim(rtrim($strGoogleMap, ","));
            $strGoogleMap = urlencode($strGoogleMap);
            $str_PlaceTime .= '<a target="_blank" href="https://www.google.com/maps/search/?api=1&query=' . $strGoogleMap . '">Google Map</a>';
        }
    } else {
        $str_PlaceTime = "<strong>" . lngPlace . ":</strong> Online";
    }
    if ($sStartTime != "") {
        $str_PlaceTime .= "<br><strong>" . lngTime . ":</strong> " . $sStartTime;
        if ($sEndTime != "") {
            $str_PlaceTime .= " - " . $sEndTime;
        }
    }

    if (!empty($sOrganizers)) {
        $str_PlaceTime .= "<br><strong>" . lngOrganizers . ":</strong> " . $sOrganizers;
    }
    if (!empty($sContactPhone)) {
        $str_PlaceTime .= ", <strong>" . lngContact . ":</strong> " . $sContactPhone;
    }

    if ($strExport == "") { ?>
        <p>
            <a href="default.php"><?= lngHomePage ?></a> |
            <a target="_top" href="sx_PrintPage.php?print=events&pg=event&eid=<?= $intEventID ?>&export=print"><?= lngPrintText ?></a> |
            <a target="_top" href="sx_PrintPage.php?print=events&pg=event&eid=<?= $intEventID ?>&export=word"><?= lngSaveInWord ?></a> |
            <a target="_top" href="sx_PrintPage.php?print=events&pg=event&eid=<?= $intEventID ?>&export=html"><?= lngSaveInHTML ?></a>
        </p>
        <hr>
    <?php
    } ?>
    <h1><?= $sEventTitle ?></h1>
    <?php
    if ($sEventSubTitle != "") { ?>
        <h2><?= $sEventSubTitle ?></h2>
    <?php
    } ?>

    <h3><?= $str_DatePeriod ?></h3>
    <p><?= $str_PlaceTime ?></p>
    <?php
    if ($radioRegisterToParticipate) { ?>
        <p>
            <a href="events.php?eid=<?= $iEventID ?>#ParticipationForm">Sign up to Participate »»</a>
        </p>
<?php
    }
    echo $memoNotes;
    if (!empty($sMediaURL)) {
        echo "<hr>";
        get_Images_To_Print($sMediaURL, "");
    }
}
$aResults = null;
?>