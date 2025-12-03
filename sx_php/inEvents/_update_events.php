<?php

/**
 * Just for Test
 * Updates the event callendar to current date interval for test
 * Is included in include_first_page.php
 *      Remove the include in real projects
 *      Coment the include after every use to avoid changing active calendars in your local system
 */

if (str_contains(sx_ROOT_HOST, '//localhost:') && $radioRemotMySQL === false) {
    $sxAddonsDate = date('Y-m-d');
    if (isset($_GET["monday"])) {
        $sxAddonsDate = $_GET["monday"];
        if (return_Is_Date($sxAddonsDate) == false) {
            $sxAddonsDate = date('Y-m-d');
        }
    }

    $sxAddonsFirstMonth = return_Year($sxAddonsDate) . "-" . return_Month_01($sxAddonsDate) . "-01";

    $d = return_Month($sxAddonsDate);
    $radioExit = false;
    $i = 0;
    $z = 0;
    for ($x = 1; $x <= 4; $x++) {
        for ($z = $x; $z <= 12; $z += 4) {
            $i = $i + 1;
            if ($i == $d) {
                $radioExit = true;
                break;
            }
        }
        if ($radioExit) {
            break;
        }
    }
    $i = 0;
    if ($z > 0) {
        $sql = "SELECT EventID, EventStartDate
        FROM events
        ORDER BY EventID ASC ";
        $rs = $conn->query($sql)->fetchAll(pdo::FETCH_ASSOC);
        if ($rs) {
            if ($rs[0]["EventStartDate"] < return_Add_To_Date(date('Y-m-d'), -31)) {
                $iRows = count($rs);
                for ($r = 0; $r < $iRows; $r++) {
                    $dLoop = return_Add_To_Date($sxAddonsFirstMonth, (-$z + $r));
                    $sql = "UPDATE EVENTS SET EventStartDate = ? WHERE EventID = ? ";
                    $sm = $conn->prepare($sql);
                    $sm->execute([$dLoop, $rs[$r]["EventID"]]);
                }
            }
        }
        $rs = null;
    }
}
