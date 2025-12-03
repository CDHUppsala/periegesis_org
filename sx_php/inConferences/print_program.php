<body>

    <?php
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
        p.PaperID AS PaperID,
        p.PaperTitle AS Paper_Title,
        p.PaperAuthors AS Paper_Authors,
        p.Speakers AS Speakers,
        p.StartTime AS Paper_StartTime,
        p.EndTime AS Paper_EndTime
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
    if (isset($int_ConferenceID) && intval($int_ConferenceID) > 0) {
        $arProgram = sx_getConferenceProgram($int_ConferenceID);
    } else {
        echo "The record is empty!";
        exit;
    }

    if (empty($strExport)) { ?>
        <div style="margin: 12px 20px;">
            <a href="default.php"><?= lngHomePage ?></a> |
            <a target="_top" href="sx_PrintPage.php?confid=<?= $int_ConferenceID ?>&export=print"><?= lngSavePrintInPDF ?></a> |
            <a target="_top" href="sx_PrintPage.php?confid=<?= $int_ConferenceID ?>&export=word"><?= lngSaveInWord ?></a> |
            <a target="_top" href="sx_PrintPage.php?confid=<?= $int_ConferenceID ?>&export=html"><?= lngSaveInHTML ?></a>
            <hr>
        <?php }

    if (is_array($arProgram)) {
        $iRows = count($arProgram);
        $sPlaceName = $arProgram[0]['PlaceName'];
        $sPlaceAddress = $arProgram[0]['PlaceAddress'];
        $sPlacePostalCode = $arProgram[0]['PlacePostalCode'];
        $sPlaceCity = $arProgram[0]['PlaceCity'];
        $sOrganizers = $arProgram[0]['Organizers'];
        $strCaptionSubTitle = "<h4>" . lngPlace . ": " . $sPlaceName . ", " .  $sPlaceAddress . ", " .  $sPlacePostalCode . " " .  $sPlaceCity . ". 
        <br>" . lngOrganizers . ": " . $sOrganizers . "</h4>";

        $strCaptionTitle = "<h4>" . $arProgram[0]['Conference_StartDate'] . " | " . $arProgram[0]['Conference_EndDate'] . "</h4>
        <h1>" . $arProgram[0]['Conference_Title'] . "</h1>";
        ?>
            <table class="no_bg table_print">
                <caption>
                    <!--h4><?= str_SiteTitle ?></h4-->
                    <?= $strCaptionTitle . $strCaptionSubTitle ?>
                </caption>
                <?php
                $loopID = -1;
                $loopSubID = -1;
                $bLoop1 = false;
                $bLoop2 = false;
                for ($r = 0; $r < $iRows; $r++) {
                    $dSessionDate = $arProgram[$r]["Session_Date"];
                    if (return_Is_Date($dSessionDate)) {
                        $sDateName = return_Week_Day_Name($dSessionDate) . " " . $dSessionDate;
                        $iDateID = intval(str_replace("-", "", $dSessionDate));
                    }

                    $iSessionID = $arProgram[$r]['SessionID'];
                    if (return_Filter_Integer($iSessionID) == 0) {
                        $iSessionID = 0;
                    }
                    $sStartTime = return_Time_Minutes($arProgram[$r]['Session_StartTime']);
                    $sEndTime = return_Time_Minutes($arProgram[$r]['Session_EndTime']);
                    $sTitle = $arProgram[$r]['Session_Title'];
                    $sModerator = $arProgram[$r]['Session_Moderator'];
                    if(!empty($sModerator)) {
                        $sTitle .= ', CHAIR: '. $sModerator;
                    }
    
                    $pStartTime = return_Time_Minutes($arProgram[$r]['Paper_StartTime']);
                    $pEndTime = return_Time_Minutes($arProgram[$r]['Paper_EndTime']);
                    $pTitle = $arProgram[$r]['Paper_Title'];
                    $pAuthors = $arProgram[$r]['Paper_Authors'];
                    $pSpeakers = $arProgram[$r]['Speakers'];
                    if (!empty($pAuthors) && !empty($pSpeakers)) {
                        $pAuthors .= ", ";
                    }
                    if (!empty($pAuthors) || !empty($pSpeakers)) {
                        $pTitle = $pAuthors . $pSpeakers . ", " . $pTitle;
                    }

                    if (intval($loopID) != intval($iDateID)) { ?>
                        <tr>
                            <th colspan="5"><p><?= $sDateName ?></p></th>
                        </tr>
                    <?php }
                    if (intval($iSessionID) > 0 && intval($loopSubID) != intval($iSessionID)) { ?>
                        <tr class="head">
                            <td class="bg"><span><?= $sStartTime ?></span></td>
                            <td class="bg"><span><?= $sEndTime ?></span></td>
                            <td colspan="3"><span><?= $arProgram[$r]['Session_PlaceName'] ?></span> <?= $sTitle ?></td>
                        </tr>
                    <?php
                    }
                    if (!empty($pTitle)) { ?>
                        <tr>
                            <td> </td>
                            <td> </td>
                            <td class="bg"><span><?= $pStartTime ?></span></td>
                            <td class="bg"><span><?= $pEndTime ?></span></td>
                            <td style="width: 100%"><?= $pTitle ?></td>
                        </tr>
                <?php
                    }
                    $loopID = $iDateID;
                    $loopSubID = $iSessionID;
                } ?>
            </table>
            <hr>
            <p style="text-align: center;">
                <?= lngPrintedDate ?>: <?= Date("Y-m-d") ?><br>
                <?= lngFromWebPage ?>: <b><?= str_SiteTitle ?></b><br>
                <?= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ?>
            </p>

        <?php
    }
    if (empty($strExport)) { ?>
        </div>
    <?php } ?>
</body>

</html>
<?php
if ($strExport == "print") { ?>
    <script>
        window.print();
    </script>
<?php } ?>