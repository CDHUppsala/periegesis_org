<?php

function sx_getEventByID($id)
{
    $conn = dbconn();
    $sql = "SELECT EventID, TextID, 
		EventStartDate, EventEndDate, 
		StartTime, EndTime, 
		PlaceName, PlaceAddress, PlacePostalCode, PlaceCity, 
		ContactPhone, 
		Organizers, 
		EventTitle, 
		EventSubTitle, 
		MediaURL, PDFAttachements,
		Notes,
        RegisterToParticipate,
        ParticipationMode,
        OnlineParticipationLink
	FROM events 
	WHERE Hidden = False 
		AND EventID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $rs = $stmt->fetchAll(PDO::FETCH_BOTH);
    if ($rs) {
        return $rs;
    } else {
        return null;
    }
}

/*
Not Used - but can be it like that:
if(!empty($_GET['date']) && empty($_GET['eid']) && empty($_GET['load']) ) {
    arrDayEvents = sx_getEventByDay($day)
}
*/


function sx_getEventByDay_NU($day)
{
    $conn = dbconn();
    $sql = "SELECT EventID, TextID, 
		EventStartDate, EventEndDate, 
		StartTime, EndTime, 
		PlaceName, PlaceAddress, PlacePostalCode, PlaceCity, 
		ContactPhone, 
		Organizers, 
		EventTitle, 
		EventSubTitle, 
		MediaURL, PDFAttachements,
		Notes,
        RegisterToParticipate,
        ParticipationMode,
        OnlineParticipationLink
	FROM events 
	WHERE Hidden = False 
	" . str_LanguageAnd . "
	AND ((EventStartDate >= ?) OR (EventEndDate >= ?)) 
	ORDER BY EventStartDate ASC, StartTime ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$day, $day]);
    $rs = $stmt->fetchAll(PDO::FETCH_BOTH);
    if ($rs) {
        return $rs;
    } else {
        return null;
    }
}


/**
 * Get data to Opens a list with the comming events
 * The variabl $isEventPage Define if the event list opens in the First page of the site or in the very Event page
 * Used to show different number of events (less in First Page)
 * @param mixed $isEventPage: If in Event Page, show 12 else show 6
 * @return array|null
 */
function sx_getEventByList($isEventPage = false)
{
    $conn = dbconn();
    $iTemp = (int) int_NumberEventsInList > 0 ? (int) int_NumberEventsInList : 6;

    if ($isEventPage) {
        $iTemp = 8;
    }

    $slimit = " LIMIT $iTemp ";

    $sql = "SELECT EventID, 
        RegisterToParticipate,
        EventStartDate, EventEndDate, StartTime, EndTime, 
		PlaceName, PlaceAddress, PlacePostalCode, PlaceCity, 
		ContactPhone, Organizers, 
		EventTitle, 
		EventSubTitle, 
		MediaURL,
        ParticipationMode
	FROM events 
	WHERE Hidden = False 
	" . str_LanguageAnd . "
	AND ((EventStartDate >= '" . date('Y-m-d') . "') OR (EventEndDate >= '" . date('Y-m-d') . "')) 
	ORDER BY EventStartDate ASC, StartTime ASC " . $slimit;
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rs = $stmt->fetchAll(PDO::FETCH_BOTH);

    // Reorder the array: Move events with passed start dates to the end
    usort($rs, function ($a, $b) {
        $today = date('Y-m-d');
        $aPastStart = $a['EventStartDate'] < $today ? 1 : 0;
        $bPastStart = $b['EventStartDate'] < $today ? 1 : 0;

        // First, sort based on whether the start date is in the past
        if ($aPastStart !== $bPastStart) {
            return $aPastStart - $bPastStart; // Past events go last
        }

        // Otherwise, keep the original order (EventStartDate and StartTime)
        return strcmp($a['EventStartDate'], $b['EventStartDate']) ?: strcmp($a['StartTime'], $b['StartTime']);
    });

    return $rs ?? [];
}

/**
 * Get Month data to Opens Events in Month Calendar or Month Table
 * @param mixed $fDate : The first date of the month and year
 * @param mixed $lDate : The last date of the mont and year
 * @return array|null : array of events
 */
function sx_getEventByDatePeriod($fDate, $lDate)
{
    $conn = dbconn();
    if (return_Is_Date($fDate) && return_Is_Date($lDate)) {
        $sql = "SELECT EventID,
            RegisterToParticipate,
            EventStartDate, EventEndDate, StartTime, EndTime, 
			PlaceName, PlaceAddress, PlacePostalCode, PlaceCity, 
			ContactPhone, Organizers, 
			EventTitle, 
			EventSubTitle, 
			MediaURL,
            ParticipationMode
		FROM events 
		WHERE Hidden = False
        " . str_LanguageAnd . "
		AND (((EventStartDate >= ?) 
			AND (EventStartDate <= ?)) 
			OR ((EventEndDate >= ?) 
			AND (EventEndDate <= ?))) 
		ORDER BY EventStartDate ASC, StartTime ASC ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$fDate, $lDate, $fDate, $lDate]);
        $rs = $stmt->fetchAll(PDO::FETCH_NUM);
        if ($rs) {
            return $rs;
        } else {
            return null;
        }
    }
}
