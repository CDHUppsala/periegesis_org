<?php

/**
 * @param $cid int Conference ID
 * @param $type string Media or PHP
 * return array of a singele conference entries
 */
function sx_getConferenceAttachments($cid, $type = 'Media')
{
    $strWhere = " AND AdvertiseMedia = 1 AND (MediaURL IS NOT NULL OR MediaURL <> '')  ";
    $strField = "MediaURL ";
    if ($type == "PDF") {
        $strWhere = " AND AdvertisePDF = 1 AND (PDFAttachements IS NOT NULL OR PDFAttachements <> '')  ";
        $strField = "PDFAttachements ";
    }
    $sql = "SELECT 
        ConferenceID,
        Title,
        StartDate,
        EndDate, " . $strField . "
    FROM conferences
    WHERE ConferenceID = ? " . $strWhere . "AND Hidden = 0";
    $conn = dbconn();
    $stmt = $conn->prepare($sql);
    $stmt->execute([intval($cid)]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * @param $cid int Conference ID
 * @param $sid int Session ID
 * @param $type string Media or PHP
 * returns array of conference sessions or session 
 */
function sx_getSessionAttachments($cid, $sid = 0, $type = 'Media')
{
    $strWhere = " AND s.AdvertiseMedia = 1 AND (s.MediaURL IS NOT NULL OR s.MediaURL <> '') ";
    $strField = "s.MediaURL ";
    if ($type == "PDF") {
        $strWhere = " AND s.AdvertisePDF = 1 AND (s.PDFAttachements IS NOT NULL OR s.PDFAttachements <> '') ";
        $strField = "s.PDFAttachements ";
    }
    if (intval($sid) > 0) {
        $strWhere = " AND s.SessionID = ? " . $strWhere;
    }
    $sql = "SELECT 
        s.SessionID,
        s.SessionTitle,
        s.SessionDate,
        s.StartTime,
        s.EndTime, " . $strField . "
    FROM conferences AS c
    INNER JOIN conf_sessions AS s ON c.ConferenceID = s.ConferenceID
    WHERE c.ConferenceID = ? AND c.Hidden = 0 AND s.Hidden = 0 " . $strWhere . "  
    ORDER BY s.SessionDate, s.StartTime ";

    $conn = dbconn();
    $stmt = $conn->prepare($sql);
    if (intval($sid) > 0) {
        $stmt->execute([intval($cid), intval($sid)]);
    } else {
        $stmt->execute([intval($cid)]);
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * @param $cid int Conference ID
 * @param $sid int Session ID
 * @param $type string Media or PHP
 * returns array of papers by conference sessions or session 
 */
function sx_getPaperAttachments($cid, $sid = 0, $type = 'Media')
{
    $strWhere = " AND p.AdvertiseMedia = 1 AND (p.MediaURL IS NOT NULL OR p.MediaURL <> '') ";
    $strField = "p.MediaURL ";
    if ($type == "PDF") {
        $strWhere = " AND p.AdvertisePDF = 1 AND (p.PDFAttachements IS NOT NULL OR p.PDFAttachements <> '') ";
        $strField = "p.PDFAttachements ";
    }
    if (intval($sid) > 0) {
        $strWhere = " AND s.SessionID = ? " . $strWhere;
    }
    $sql = "SELECT 
    p.PaperID,
    p.PresentationDate,
    p.StartTime,
    p.EndTime,
    p.PaperTitle,
    p.PaperAuthors,
    p.Speakers, " . $strField . "
    FROM (conf_papers AS p
    INNER JOIN conf_sessions AS s ON p.SessionID = s.SessionID)
    INNER JOIN conferences AS c ON s.ConferenceID = c.ConferenceID
    WHERE c.ConferenceID = ? AND c.Hidden = 0 AND s.Hidden = 0 AND p.Hidden = 0 " . $strWhere . "  
    ORDER BY PresentationDate, StartTime ";
    //echo $sql ."<br>";
    //echo $strWhere ."<br>";
    $conn = dbconn();
    $stmt = $conn->prepare($sql);
    if (intval($sid) > 0) {
        $stmt->execute([intval($cid), intval($sid)]);
    } else {
        $stmt->execute([intval($cid)]);
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


/**
 * @param $strDownloads string File name to download (PDF, DOC, EXCEL)
 * return STRING A readable file name by cleaning prefixes and replacing _ and - with white space 
 */
function sx_getLinksToPDF($strDownloads, $radioFormLink = true)
{
    $str_LinksToFiles = "";
    if (!empty($strDownloads)) {
        if (strpos($strDownloads, ";") == 0) {
            $strDownloads .= ";";
        }
        $arrTemp = explode(";", $strDownloads);
        for ($f = 0; $f < count($arrTemp); $f++) {
            $sLink = trim($arrTemp[$f]);
            $sTitle = "";
            if (!empty($sLink)) {
                $iPos = strpos($sLink, "/") + 1;
                $sTitle = substr($sLink, $iPos, strpos($sLink, ".") - $iPos);
                if (strpos($sTitle, 'pid_') !== false) {
                    $sTitle = substr($sTitle, strpos($sTitle, "_", 4));
                }
                $sTitle = str_replace("_", " ", str_replace("-", " ", $sTitle));
                if ($radioFormLink) {
                    $str_LinksToFiles .= '<a class="read_more" href="/imgPDF/' . $sLink . '" target="_blank">' . ucwords($sTitle) . "</a>";
                } else {
                    $str_LinksToFiles .= '<p><a href="/imgPDF/' . $sLink . '" target="_blank">' . ucwords($sTitle) . "</a></p>";
                }
            }
        }
        return $str_LinksToFiles;
    }
}

/**
 * @param $arrC array of a single Conference Record
 * @param $arrS array of Session or Sessions by Conference
 * @param $arrC array of Papers by Conference session or sessions
 * @param $type string Media or PHP
 * @param $session boolean I results are from session or conference
 * returns HTML Carts displaying links to Attachments from conference, sessions and eventually papers 
 */
function sx_showAttachments($arrC, $arrS, $arrP = null, $type = 'Media', $session = false)
{
    if ((is_array($arrC) && !empty($arrC)) ||
        (is_array($arrS)  && !empty($arrS)) ||
        (is_array($arrP) && !empty($arrP))
    ) { ?>
        <section class="grid_cards_wrapper" aria-label="Media and PDF Conferance attachments">
            <?php
            $countArrs = 3;
            if (!is_array($arrP)) {
                $countArrs = 2;
            }
            $tempTitle = 'Media Attachments from Current Conference';
            $strAttachName = 'MediaURL';
            if ($type == 'PDF') {
                $tempTitle = 'PDF File Attachments from Current Conference';
                $strAttachName = 'PDFAttachements';
            }
            if ($session) {
                $tempTitle .= ' Session';
            } ?>
            <h1><span><?= $tempTitle ?></span></h1>
            <div class="grid_cards">
                <?php
                for ($loop = 0; $loop < $countArrs; $loop++) {
                    $source = 'Conference Attachment';
                    if ($loop == 0) {
                        $arr = $arrC;
                        $arrC = null;
                    } elseif ($loop == 1) {
                        $arr = $arrS;
                        $arrS = null;
                        $source = 'Session Attachment';
                    } elseif ($loop == 2) {
                        $arr = $arrP;
                        $arrP = null;
                        $source = 'Paper Attachment';
                    }
                    $iRows = count($arr);
                    for ($r = 0; $r < $iRows; $r++) {
                        $strAuthors = "";
                        if ($loop == 0) {
                            $strTitle = '<a href="conferences.php?confid=' . $arr[$r]['ConferenceID'] . '">' . $arr[$r]['Title'] . '</a>';
                            $strDates = lngPeriod . ': ' . $arr[$r]['StartDate'] . ' | ' . $arr[$r]['EndDate'];
                            $strAttachValue = $arr[$r][$strAttachName];
                        } elseif ($loop == 1) {
                            $strTitle = '<a href="conferences.php?sesid=' . $arr[$r]['SessionID'] . '">' . $arr[$r]['SessionTitle'] . '</a>';
                            $strDates = lngDate . ': ' . $arr[$r]['SessionDate'] . ', ' . lngTime . ': ' . return_Time_Minutes($arr[$r]['StartTime']) . ' - ' . return_Time_Minutes($arr[$r]['EndTime']);
                            $strAttachValue = $arr[$r][$strAttachName];
                        } elseif ($loop == 2) {
                            $strTitle = '<a href="conferences.php?paperid=' . $arr[$r]['PaperID'] . '">' . $arr[$r]['PaperTitle'] . '</a>';
                            $strDates = lngDate . ': ' . $arr[$r]['PresentationDate'] . ', ' . lngTime . ': ' . return_Time_Minutes($arr[$r]['StartTime']) . ' - ' . return_Time_Minutes($arr[$r]['EndTime']);
                            $strAuthors = $arr[$r]['PaperAuthors'];
                            if (!empty($arr[$r]['Speakers'])) {
                                $strAuthors .= ' Speakers: ' . $arr[$r]['Speakers'];
                            }
                            $strAttachValue = $arr[$r][$strAttachName];
                        } ?>
                        <figure>
                            <?php
                            $radioMsg = false;
                            if ($type == 'Media') {
                                if (strpos($strAttachValue, ";") > 0) {
                                    $strAttachValue = explode(";", $strAttachValue)[0];
                                    $radioMsg = true;
                                }
                                $strObjectValue = return_Media_Type_URL($strAttachValue);
                                if (!empty($strObjectValue)) {
                                    get_Media_Type_Player($strAttachValue, $strObjectValue);
                                }
                            } ?>
                            <figcaption>
                                <?php
                                echo '<p><address>' . $source . "</address></p>";
                                if (!empty($strTitle)) {
                                    echo "<h4>" . $strTitle . "</h4>";
                                }
                                if (!empty($strAuthors)) {
                                    echo '<p><address>By ' . $strAuthors . "</address></p>";
                                }
                                if (!empty($strDates)) {
                                    echo '<p><time>' . $strDates . '</time></p>';
                                }
                                if ($radioMsg) {
                                    echo "<p><strong>Obs!</strong> More media files are available.</p>";
                                }
                                ?>
                            </figcaption>
                            <?php
                            if ($type == "PDF") {
                                echo sx_getLinksToPDF($strAttachValue);
                            } ?>
                        </figure>
                <?php
                    }
                } ?>
            </div>
        </section>
<?php
        $arr = null;
    }
}

function sx_RegisteredForThisConference($cid, $pid)
{
    $conn = dbconn();
    $sql = "SELECT ConferenceID
        FROM conf_to_participants
        WHERE ConferenceID = ? AND ParticipantID = ?
        	AND Cancelled = 0 LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$cid, $pid]);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        return true;
    } else {
        return false;
    }
}

/**
 * Transform a file name to a link name
 * @param $strFileName string File name to transform
 * returns STRING A readable file name by cleaning prefixes and replacing _ and - with white space 
 */
function sx_getLinkNameFromFileName($strFileName)
{
    $sTitle = "";
    if (!empty($strFileName)) {
        $iPos = strpos($strFileName, "/") + 1;
        $sTitle = substr($strFileName, $iPos, strpos($strFileName, ".") - $iPos);
        if (strpos($sTitle, 'pid_') !== false) {
            $sTitle = substr($sTitle, strpos($sTitle, "_", 4));
        }
        $sTitle = str_replace("_", " ", str_replace("-", " ", $sTitle));
        $sTitle = ucwords($sTitle);
    }
    return $sTitle;
}
?>