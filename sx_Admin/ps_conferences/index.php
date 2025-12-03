<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsTableName.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

include __DIR__ . "/blueprint_functions.php";

$aResults = null;

$last_ConferenceID = sx_getLastConferenceID();
$last_SessionID = sx_getLastSessionID();
$last_PaperID = sx_getLastPaperID();

$radio_CreateBlueprint = false;

$data_Start = "";
$date_End = "";
$i_SessionsByDay = 0;
$i_PapersBySession = 3;
$i_MinutesByPaper = 0;
$i_Start_1 = '00:00:00';
$i_Start_2 = '00:00:00';
$i_Start_3 = '00:00:00';
$i_Start_4 = '00:00:00';

if (isset($_POST['Start']) && isset($_POST['End'])) {
    $data_Start = $_POST['Start'];
    $date_End = $_POST['End'];
    $_SESSION['Start'] = $data_Start;
    $_SESSION['End'] = $date_End;
} elseif (isset($_SESSION['Start']) && isset($_SESSION['End'])) {
    $data_Start = $_SESSION['Start'];
    $date_End = $_SESSION['End'];
}

if (isset($_POST['SessionsByDay'])) {
    $i_SessionsByDay = $_POST['SessionsByDay'];
    $_SESSION['SessionsByDay'] = $i_SessionsByDay;
    $radio_CreateBlueprint = true;
} elseif (isset($_SESSION['SessionsByDay'])) {
    $i_SessionsByDay = $_SESSION['SessionsByDay'];
}
if (isset($_POST['PapersBySession'])) {
    $i_PapersBySession = $_POST['PapersBySession'];
    $_SESSION['PapersBySession'] = $i_PapersBySession;
    $radio_CreateBlueprint = true;
} elseif (isset($_SESSION['PapersBySession'])) {
    $i_PapersBySession = $_SESSION['PapersBySession'];
}
if (isset($_POST['MinutesByPaper'])) {
    $i_MinutesByPaper = $_POST['MinutesByPaper'];
    $_SESSION['MinutesByPaper'] = $i_MinutesByPaper;
    $radio_CreateBlueprint = true;
} elseif (isset($_SESSION['MinutesByPaper'])) {
    $i_MinutesByPaper = $_SESSION['MinutesByPaper'];
}

if (isset($_POST['Start_1'])) {
    $i_Start_1 = $_POST['Start_1'];
    if (strlen($i_Start_1) < 8) {
        $i_Start_1 .= ':00';
    }
    $_SESSION['Start_1'] = $i_Start_1;
} elseif (isset($_SESSION['Start_1'])) {
    $i_Start_1 = $_SESSION['Start_1'];
}

if (isset($_POST['Start_2'])) {
    $i_Start_2 = $_POST['Start_2'];
    if (strlen($i_Start_2) < 8) {
        $i_Start_2 .= ':00';
    }
    $_SESSION['Start_2'] = $i_Start_2;
} elseif (isset($_SESSION['Start_2'])) {
    $i_Start_2 = $_SESSION['Start_2'];
}
if (isset($_POST['Start_3'])) {
    $i_Start_3 = $_POST['Start_3'];
    if (strlen($i_Start_3) < 8) {
        $i_Start_3 .= ':00';
    }
    $_SESSION['Start_3'] = $i_Start_3;
} elseif (isset($_SESSION['Start_3'])) {
    $i_Start_3 = $_SESSION['Start_3'];
}
if (isset($_POST['Start_4'])) {
    $i_Start_4 = $_POST['Start_4'];
    if (strlen($i_Start_4) < 8) {
        $i_Start_4 .= ':00';
    }
    $_SESSION['Start_4'] = $i_Start_4;
} elseif (isset($_SESSION['Start_4'])) {
    $i_Start_4 = $_SESSION['Start_4'];
}

$arrStarts = array($i_Start_1, $i_Start_2, $i_Start_3, $i_Start_4);

/**
 * If creates blueprint is true
 * Create an array equivalent to Blueprint Table
 */

if ($radio_CreateBlueprint) {
    $i_days = sx_dateDifference($data_Start, $date_End) + 1;
    $i_sessions = $i_days * $i_SessionsByDay;
    $i_papers = $i_sessions * $i_PapersBySession;
    $s_minutes = $i_MinutesByPaper * $i_PapersBySession * 60;
    $p_minutes = $i_MinutesByPaper * 60;
    $cid = $last_ConferenceID;
    $sid = $last_SessionID;
    $pid = 0;
    $date = $data_Start;
    for ($d = 0; $d < $i_days; $d++) {
        for ($s = 0; $s < $i_SessionsByDay; $s++) {
            $sid++;
            $s_start = $arrStarts[$s];
            $s_end = date("H:i:s", strtotime($s_start) + $s_minutes);
            $p_start = $s_start;
            for ($p = 0; $p < $i_PapersBySession; $p++) {
                $p_end = date("H:i:s", strtotime($p_start) + $p_minutes);
                $aResults[$pid] = [
                    $pid,
                    $cid,
                    $data_Start,
                    $date_End,
                    $sid,
                    'Day ' . $d + 1 . ' Session ' . $s + 1,
                    $date,
                    $s_start,
                    $s_end,
                    $p_start,
                    $p_end,
                    'Day ' . $d + 1 . ' Session ' . $s + 1 . ' Paper ' . $p + 1
                ];
                $pid++;
                $p_start = date("H:i:s", strtotime($p_start) + $p_minutes);
            }
        }
        $date = sx_AddToDate($date, 1);
    }
}


/**
 * Get from Blueprint Table
 */
$radio_Blueprint = true;
if ($radio_CreateBlueprint) {
    $radio_Blueprint = false;
}

/**
 * Check if Temporal Blueprint Table conf_blueprint exists in DB
 */
if ($radio_Blueprint) {
    $sql = "SELECT * 
		FROM information_schema.tables
		WHERE table_schema = ?
    	AND table_name = ?
		LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([sx_TABLE_SCHEMA, 'conf_blueprint']);
    $rs = $stmt->fetchAll(PDO::FETCH_NUM);
    if (!$rs) {
        $radio_Blueprint = false;
    }
    $stmt = null;
    $rs = null;
}

if ($radio_Blueprint) {
    $sql = "SELECT
		PaperID,
        ConferenceID,
        StartDate,
        EndDate,
        SessionID,
        SessionTitle,
        SessionDate,
        S_StartTime,
        S_EndTime,
        P_StartTime,
        P_EndTime,
        PaperTitle
	FROM conf_blueprint
	ORDER BY ConferenceID, SessionDate, SessionID, S_StartTime, P_StartTime";
    $stmt = $conn->query($sql);
    $rs = $stmt->fetchAll(PDO::FETCH_NUM);
    if ($rs) {
        $aResults = $rs;
    }
    $stmt = null;
    $rs = null;
}

?>
<!DOCTYPE html>
<html lang="<?= sx_DefaultAdminLang ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Public Sphere Content Management System</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css">
    <style>
        <?php
        include __DIR__ . "/blueprint.css";
        ?>
    </style>

    <script src="../js/jq/jquery.min.js"></script>
    <script>
        <?php
        include __DIR__ . "/blueprint.js";
        ?>
    </script>

</head>

<body>
    <header id="header" class="flex_between">
        <h2>Create a Blueprint Program for<br>Conference, Sessions and Papers</h2>
        <form name="UploadBluePrint" action="index.php" method="post">
            <input title="Save your changes before Reload or Export" class="button" type="submit" id="Replace_Blueprint_Table" name="Replace_Blueprint_Table" value="Save Changes To Blueprint Table">
            <a title="Obs! Save changes before Reload" class="button" href="index.php">Reload From Blueprint Table</a>
            <a title="Obs! Save changes before Export" class="button" href="default.php">Go to Export Blueprint</a>
        </form>
        <div>Last ConferenceID: <?= $last_ConferenceID ?>
            <br>Last SessionID: <?= $last_SessionID ?>
            <br>Last PaperID: <?= $last_PaperID ?>
        </div>
    </header>
    <?php
    if (!is_array($aResults)) { ?>
        <div class="alignCenter">
            <h2>Please fill the Form bellow to Create a New Blueprint</h2>
        </div>
    <?php
    } ?>
    <h4 class="alignCenter">Fill the form bellow to create a New Conference Blueprint or Reload the last one saved in the Blueprint Table
        <button class="help js_info" id-data="info_general">General Help</button>
    </h4>
    <form class="flex_center_stretch schedule" name="NewConferenceDates" method="post" action="index.php">
        <label>Conference<br>Start Date<br>
            <input name="Start" type="date" value="<?= $data_Start ?>" /></label>
        <label>Conference<br>End Date<br>
            <input name="End" type="date" value="<?= $date_End ?>" /></label>
        <label>Sessions per<br>Day <b class="help js_info" id-data="info_sessions">?</b><br>
            <input name="SessionsByDay" type="number" value="<?= $i_SessionsByDay ?>" max="4" /></label>
        <label>Papers per<br>Session <b class="help js_info" id-data="info_papers">?</b><br>
            <input name="PapersBySession" type="number" value="<?= $i_PapersBySession ?>" max="6" /></label>
        <label>Minutes per<br>Paper <b class="help js_info" id-data="info_minutes">?</b><br>
            <input name="MinutesByPaper" type="number" value="<?= $i_MinutesByPaper ?>" /></label>
        <label>Start Time of sessions<br>Insert Time only for the defined number of Sessions per Day <b class="help js_info" id-data="info_time">?</b><br>
            S1: <input name="Start_1" type="time" value="<?= $i_Start_1 ?>" title="Start Time for Session 1" />
            S2: <input name="Start_2" type="time" value="<?= $i_Start_2 ?>" title="Start Time for Session 2" />
            S3: <input name="Start_3" type="time" value="<?= $i_Start_3 ?>" title="Start Time for Session 3" />
            S4: <input name="Start_4" type="time" value="<?= $i_Start_4 ?>" title="Start Time for Session 4" />
        </label>
        <input class="button" name="ChangeDates" type="submit" value="Create Blueprint" />
    </form>
    <form id="CreateConference" name="CreateConference" action="index.php" method="post">
        <table id="design" class="no_bg">
            <?php
            if (is_array($aResults)) {
                /**
                 * NEW DATES Check if they are available
                 */
                $rCount = count($aResults);
                $cCount = count($aResults[0]);
                $radio_ChangeDates = false;
                if (isset($data_Start) && isset($data_Start)) {
                    if ($data_Start > $aResults[0][2]) {
                        $radio_ChangeDates = true;
                    }
                } ?>
                <thead>
                    <tr>
                        <th></th>
                        <th class="reorder">Paper ID</th>
                        <th title="The Title of Conference Will be saved as: Conference <?= $last_ConferenceID + 1 ?>">Conference ID</th>
                        <th title="Conference Start Date">C Start Date</th>
                        <th title="Conference Start Date">C End Date</th>
                        <th class="jq_sessions button" id-data="<?= $i_PapersBySession ?>" title="Reorder by <?= $i_PapersBySession ?> Papers per Session. Obs! Use it when you have add a New or Paralell Sessions for a Day. Not when you add Papers in a Session.">Session ID</th>
                        <th>Session Title</th>
                        <th>Session Date</th>
                        <th title="Session Start Time">S Start Time</th>
                        <th title="Session End Time">S End Time</th>
                        <th title="Paper Start Time - Click to Change">P Start Time</th>
                        <th title="Paper End Time - Click to Change">P End Time</th>
                        <th>Paper Title</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $iLast = 0;
                    $sClass = "";
                    $dLast = "";
                    $loopClass = "";
                    $temp_SID = 0;
                    $loop_SID = 0;
                    $loop_Last_SID = $last_SessionID + 1;
                    $loop_Last_PID = $last_PaperID + 1;
                    $basic_SessionDate = null;
                    $curr_SessionDate = null;
                    $last_SessionDate = null;
                    for ($z = 0; $z < $rCount; $z++) {
                        $iLoop = $aResults[$z][4]; //SessionID
                        $dLoop = $aResults[$z][6]; //SessionDate
                        if ($iLoop != $iLast) {
                            if ($sClass == "gray") {
                                $sClass = "white";
                            } else {
                                $sClass = "gray";
                            }
                        }

                        if ($radio_ChangeDates && isset($basic_SessionDate)) {
                            if ($aResults[$z][6] > $basic_SessionDate) {
                                /**
                                 * To short the Blueprint when Conference days are less then the Blueprint
                                 */
                                if (isset($last_SessionDate)) {
                                    if (sx_AddToDate($last_SessionDate, 1) == $date_End) {
                                        break;
                                    }
                                }
                                $last_SessionDate = $curr_SessionDate;
                            }
                            /**
                             * To short the Blueprint for one day Conference
                             */
                            if (!isset($last_SessionDate) && isset($curr_SessionDate)) {
                                $last_SessionDate = sx_AddToDate($curr_SessionDate, -1);
                            }
                        } ?>

                        <tr class="<?= $sClass ?>"">
							<td class=" flex_between">
                            <a href="javascript:void(0)" class="remove" title="Delete this Row">
                                <img class="sx_svg_bg reverse" src="../images/sx_svg/sx_clear.svg" height="24"></a>
                            <a href="javascript:void(0)" class="up" title="Move this Row Up">
                                <img class="sx_svg_bg" src="../images/sx_svg/sx_arrow_up.svg" height="24"></a>
                            <a href="javascript:void(0)" class="down" title="Move this Row Down">
                                <img class="sx_svg_bg" src="../images/sx_svg/sx_arrow_down.svg" height="24"></a>
                            <a href="javascript:void(0)" class="clone" title="Clone this Row and Insert it Bellow">
                                <img class="sx_svg_bg" src="../images/sx_svg/sx_plus.svg"></a>
                            </td>
                    <?php
                        for ($c = 0; $c < $cCount; $c++) {
                            $val = $aResults[$z][$c];
                            if ($c == 0) { //PaperID
                                $val = $loop_Last_PID;
                                $loop_Last_PID++;
                            } elseif ($c == 1) { //ConferenceID
                                $val = $last_ConferenceID + 1;
                            } elseif ($c == 4) { //SessionID
                                if ($val != $loop_SID) {
                                    $temp_SID =  $loop_Last_SID;
                                    $loop_Last_SID++;
                                }
                                $loop_SID = $val;
                                $val = $temp_SID;
                            }

                            $dClass = "";
                            if ($c == 6) { // SessionDate
                                if ($val != $dLast) {
                                    if (empty($loopClass)) {
                                        $loopClass = ' class="yellow"';
                                    } else {
                                        $loopClass = "";
                                    }
                                }
                                if (empty($loopClass)) {
                                    $dClass = ' class="red"';
                                } else {
                                    $dClass = $loopClass;
                                }
                            }

                            if ($radio_ChangeDates) {
                                if ($c == 2 && !empty($data_Start)) { // StartDate
                                    if ($val != $data_Start) {
                                        $val = $data_Start;
                                    }
                                }
                                if ($c == 3 && !empty($date_End)) { // EndDate
                                    if ($val != $date_End) {
                                        $val = $date_End;
                                    }
                                }
                                if ($c == 6) { // SessionDate
                                    if (isset($basic_SessionDate)) {
                                        if ($val == $basic_SessionDate) {
                                            $basic_SessionDate = $val;
                                            $val = $curr_SessionDate;
                                        } else {
                                            $basic_SessionDate = $val;
                                            $val = sx_AddToDate($curr_SessionDate, 1);
                                            $curr_SessionDate = $val;
                                        }
                                    } else {
                                        $basic_SessionDate = $val;
                                        $val = $data_Start;
                                        $curr_SessionDate = $val;
                                    }
                                }
                            }
                            if ($c == 7 || $c == 8) { //S StartTime and EndTime
                                echo '<td class="td_edit jq_time">' . $val . '</td>';
                            } elseif ($c == 9 || $c == 10) { //P StartTime and EndTime
                                echo '<td class="td_edit_time jq_time">' . $val . '</td>';
                            } elseif ($c == 5 || $c == 11) { //Sesion and Paper Titles
                                echo '<td class="td_edit jq_text">' . $val . '</td>';
                            } else {
                                echo "<td$dClass>$val</td>";
                            }
                        }
                        echo "</tr>";
                        $iLast = $iLoop;
                        $dLast = $dLoop;
                    }
                } ?>
                </tbody>
        </table>
        <?php
        $aResults = null;
        ?>
    </form>
    <hr>
    <h3 class="alignCenter"><a class="button" href="default.php">Go to Export Blueprint</a></h3>

    <div id="Console"></div>
    <div id="change_time" class="dialog_box flex_left">
        <a class="button jq_HideDialog" href="javascript:void(0)">Change Data</a>
    </div>
    <div id="change_content" class="dialog_box flex_left">
        <button class="button jq_change_content">Change or Close</button>
    </div>

    <?php
    include __DIR__ . "/help.html";
    ?>
</body>

</html>