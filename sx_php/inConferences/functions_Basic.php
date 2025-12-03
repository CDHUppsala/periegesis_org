<?php

/**
 * To be used for cart presentation of past and comming conferences
 * Get all Past or all Comming conferences or just an idnividual conference
 */
function sx_getConferences($id = 0, $past = false)
{
    $conn = dbconn();
    $sql = "SELECT ConferenceID,
        Title,
        SubTitle,
        ParticipationMode,
        StartDate,
        EndDate,
        PlaceName,
        PlaceAddress,
        PlacePostalCode,
        PlaceCity,
        Organizers,
        Sponsors,
        ContactPhone,
        ImageLinks,
        MediaURL,
        PDFAttachements,
        HideSessionsInFirstPage,
        ShowSessionsInConference,
        Abstract,
        Notes
    FROM conferences
    WHERE Hidden = 0 ";
    if (intval($id) > 0) {
        $sql .= " AND ConferenceID = ? ";
    } elseif ($past) {
        $sql .= " AND EndDate < ? ORDER BY EndDate DESC ";
    } else {
        $sql .= " AND EndDate >= ? ORDER BY EndDate ASC ";
    }

    $stmt = $conn->prepare($sql);
    if (intval($id) > 0) {
        $stmt->execute([$id]);
        $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $stmt->execute([date('Y-m-d')]);
        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    if ($rs) {
        return $rs;
    } else {
        return null;
    }
}

/**
 * Get all session of a conference or just an idnividual session
 */
function sx_getSessions($cid = 0, $sid = 0)
{
    $conn = dbconn();
    $sql = "SELECT
        SessionID,
        ConferenceID,
        SessionTitle,
        SessionSubTitle,
        Break,
        Moderator,
        SessionDate,
        StartTime,
        PlaceName,
        EndTime,
        MediaURL,
        ImageLinks,
        PDFAttachements,
        Notes
    FROM conf_sessions
    WHERE Hidden = 0 ";
    if (intval($cid) > 0) {
        $sql .= " AND ConferenceID = ? 
        ORDER BY SessionDate ASC, StartTime ASC ";
    } else {
        $sql .= " AND SessionID = ? ";
    }
    $stmt = $conn->prepare($sql);
    if (intval($cid) > 0) {
        $stmt->execute([intval($cid)]);
        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt->execute([intval($sid)]);
        $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    if ($rs) {
        return $rs;
    }
    $rs = null;
    $stmt = null;
}

/**
 * Get all Papers of a Session or just an idnividual Paper
 */

function sx_getPapers($sid = 0, $pid = 0)
{
    $conn = dbconn();
    $sql = "SELECT 
        PaperID,
        ConferenceID,
        SessionID,
        PresentationDate,
        StartTime,
        EndTime,
        PaperTitle,
        PaperSubTitle,
        PaperAuthors,
        Speakers,
        AuthorPortraits,
        ImageLinks,
        MediaURL,
        PDFAttachements,
        AboutAuthors,
        Abstract,
        MainText
    FROM conf_papers
    WHERE Hidden = 0";
    if (intval($sid) > 0) {
        $sql .= " AND SessionID = ? ORDER BY StartTime ASC";
    } else {
        $sql .= " AND PaperID = ? ";
    }
    $stmt = $conn->prepare($sql);
    if (intval($sid) > 0) {
        $stmt->execute([intval($sid)]);
        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt->execute([intval($pid)]);
        $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    if ($rs) {
        return $rs;
    } else {
        return null;
    }
}



/**
 * Get all Past or all Coming Conferences
 */

function sx_getPastComingConferences($past = false, $limit = false)
{
    $conn = dbconn();
    $sql = "SELECT 
        ConferenceID,
        Title,
        CONCAT(StartDate, ' ', EndDate) AS Conference_Date
    FROM  conferences
    WHERE Hidden = 0 ";
    if ($past) {
        $sql .= " AND EndDate < ? ";
        $sql .= " ORDER BY StartDate DESC, ConferenceID";
    } else {
        $sql .= " AND EndDate >= ? ";
        $sql .= " ORDER BY StartDate ASC, ConferenceID ";
    }

    if ($limit) {
        $sql .= " LIMIT 1";
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute([date('Y-m-d')]);
    $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($rs) {
        return $rs;
    } else {
        return null;
    }
}


/**
 * To be used for a complete program of a conferencs
 */

function sx_getConferenceProgram($id)
{
    $conn = dbconn();
    $i_ConferenceID = $id;
    if (isset($_POST['ConferenceID'])) {
        $i_ConferenceID = intval($_POST['ConferenceID']);
        if (intval($i_ConferenceID) == 0) {
            $i_ConferenceID = 0;
        }
    }
    $aResults = null;
    if ($i_ConferenceID > 0) {
        $sql = "SELECT 
		c.ConferenceID AS ConferenceID,
		c.Title AS Conference_Title,
		c.StartDate AS Conference_StartDate,
        c.EndDate AS Conference_EndDate,
        c.PlaceName AS PlaceName,
        c.PlaceAddress AS PlaceAddress,
        c.PlacePostalCode AS PlacePostalCode,
        c.PlaceCity AS PlaceCity,
        c.Organizers AS Organizers,
		s.SessionID AS SessionID,
		s.SessionTitle AS Session_Title,
		s.Moderator AS Session_Moderator,
		s.SessionDate AS Session_Date,
		s.StartTime AS Session_StartTime,
        s.EndTime AS Session_EndTime,
        s.PlaceName AS Session_PlaceName,
        s.Break AS Session_Break,
        p.PaperID AS PaperID,
        p.PaperTitle AS Paper_Title,
        p.PaperAuthors AS Paper_Authors,
        p.Speakers AS Speakers,
        p.StartTime AS Paper_StartTime,
        p.EndTime AS Paper_EndTime,
        p.Break AS Paper_Break
	    FROM
    		conferences c
	    	LEFT JOIN conf_sessions s ON c.ConferenceID = s.ConferenceID
		    LEFT JOIN conf_papers p ON s.SessionID = p.SessionID AND p.Hidden = 0
    	WHERE c.ConferenceID = ?
	    	AND c.Hidden = 0
            AND s.Hidden = 0
	    ORDER BY s.SessionDate , s.StartTime , s.SessionID ,  p.StartTime , p.PaperID";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$i_ConferenceID]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $aResults = null;
        if ($rows) :
            $aResults = $rows;
        endif;
        $stmt = null;
        $rows = null;
    }
    return $aResults;
}

/**
 * Use a commmon query for 
 *      both middle page programs 
 *      and aside conference navigation
 */

$arProgram = null;
if (isset($int_ConferenceID) && intval($int_ConferenceID) > 0) :
    $arProgram = sx_getConferenceProgram($int_ConferenceID);
endif;

function sx_getMultiArrayKey($arr, $field, $value)
{
    foreach ($arr as $key => $row) {
        if ($row[$field] === $value)
            return $key;
    }
    return false;
}

/**
 * Gets an array that includes all (dublicated) session IDs of a date
 *      from the papers presented during that date
 */
function sx_getMultiArrayKeysArray($arr, $field, $value)
{
    $arrTemp = array();
    foreach ($arr as $key => $row) {
        if ($row[$field] === $value)
            $arrTemp[] = $key;
    }
    if (!empty($arrTemp)) {
        return $arrTemp;
    } else {
        return false;
    }
}

/**
 * For the conference program tha uses 2 sets of tabs
 * The second sett af tabs is created by this functon
 * Gets the Uniquew session periods (time periods) of a conference date (day)
 *      from an array that includes all papers presented in the conference
 */
function sx_getSessionsByDate($arr, $sessionDate)
{
    $arrKeys = sx_getMultiArrayKeysArray($arr, "Session_Date", $sessionDate);
    if (is_array($arrKeys)) {
        $curID = 0;
        $sClass = ' class="selected"';
        foreach ($arrKeys as $row => $key) {
            $iLoopID = $arr[$key]['SessionID'];
            if ($iLoopID != $curID) {
                $sTitle = $arr[$key]["Session_Title"];
                $sModerator = $arr[$key]['Session_Moderator'];
                if (!empty($sModerator)) {
                    $sTitle .= ', CHAIR: ' . $sModerator;
                }
                echo '<li' . $sClass . '>';
                echo return_Time_Minutes($arr[$key]["Session_StartTime"]) . '-' .
                    return_Time_Minutes($arr[$key]["Session_EndTime"]) . '<br>' .
                    $sTitle . "<br>" .
                    $arr[$key]["Session_PlaceName"];
                echo '</li>';
            }
            $curID = $iLoopID;
            $sClass = "";
        }
    }
}
