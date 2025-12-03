<?php
$dateNowDate = date("Y-m-d");

//## CHECK IF IP IS VALID

$radioValidIP = false;
$currentVisitorIP = return_User_IP();
if (strlen($currentVisitorIP) > 0) {
    $radioValidIP = check_User_IP($currentVisitorIP);
}

//## CHECK IF SESSIONS ARE ANABLED

$_SESSION["TempVisitorIP"] = $currentVisitorIP;
if (strlen($_SESSION["TempVisitorIP"]) < 6) {
    $radioValidIP = false;
}
unset($_SESSION["TempVisitorIP"]);

//## UNCOMMENT ON ATTACK
/*
if ($radioValidIP === false) {
    $conn = null;
    header("Location: sx/default.php");
}
*/

//#### CHECK TO ADD VISITS

$radioAddToVisits = false;
if ($radioValidIP) {
    $sxPartIP = substr($currentVisitorIP, 0, 6);
    if ($sxPartIP != "157.55" && $sxPartIP != "157.56" && $sxPartIP != "66.249" && $sxPartIP != "65.55.") {
        $radioAddToVisits = true;
    }
}
if (isset($_SESSION["VisitorIP"])) {
    if ($currentVisitorIP == $_SESSION["VisitorIP"]) {
        $radioAddToVisits = false;
    }
}

//#### COMMENT ON REAL SITE

$radioAddToVisits = true;
$radioValidIP = true;

//#### Adds a new visit in current date

if ($radioAddToVisits) {
    $radioEmptyTable = true;
    $dateVisitDate = $dateNowDate;
    $intVisits = 0;
    $intTotalVisits = 0;
    $sql = "SELECT Datum, Visits, TotalVisits FROM visits ORDER BY VisitID DESC LIMIT 1";
    $rs = $conn->query($sql);
    $row = $rs->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $radioEmptyTable = false;
        $dateVisitDate = $row["Datum"];
        $intVisits = $row["Visits"];
        $intTotalVisits = $row["TotalVisits"];
    }
    $rs = null;
    $row = null;
    if ($dateVisitDate != $dateNowDate || $radioEmptyTable) {
        $intTotalVisits++;
        $sql = "INSERT INTO visits  (Datum, Visits, TotalVisits)
        VALUES (:dateNowDate, 1, :iTotalVisits)";
        $rs = $conn->prepare($sql);
        $rs->bindParam(':dateNowDate', $dateNowDate, PDO::PARAM_STR);
        $rs->bindParam(':iTotalVisits', $intTotalVisits, PDO::PARAM_INT);
        $rs->execute();
    } else {
        $intVisits++;
        $intTotalVisits++;
        $sql = "UPDATE visits SET
            Visits = :iVisits,
            TotalVisits = :iTotalVisits
            WHERE Datum = :dateNowDate ";
        $rs = $conn->prepare($sql);
        $rs->bindParam(':iVisits', $intVisits, PDO::PARAM_INT);
        $rs->bindParam(':iTotalVisits', $intTotalVisits, PDO::PARAM_INT);
        $rs->bindParam(':dateNowDate', $dateVisitDate, PDO::PARAM_STR);
        $rs->execute();
    }
}

/**
 * Get the ID ($int_StatisticsTextID) of any Text Table (texts, articles or items) AND Published Date 
 * from either sx_Config or corresponding page configuration files
 * REMOVE STATS FROM CONFERENCES
 */
$intVisitedTextID = 0;

if (isset($int_StatisticsTextID) && intval($int_StatisticsTextID) > 0) {
    $intVisitedTextID = (int) $int_StatisticsTextID;
}

if ($radioValidIP && intval($intVisitedTextID) > 0) {
    $radioAddToText = true;
    $sql = "SELECT TextID, PublishedDate, VisitDate, TotalVisits FROM visits_texts WHERE TextID = :VisitedTextID ";
    $rs = $conn->prepare($sql);
    $rs->bindParam(':VisitedTextID', $intVisitedTextID, PDO::PARAM_INT);
    $rs->execute();
    $obj = $rs->fetchObject();
    if ($obj) {
        $radioAddToText = false;
        $dPublishedDate = $obj->PublishedDate;
        $dVisitDate = $obj->VisitDate;
        $iTotalVisits = $obj->TotalVisits;
    }
    $obj = null;
    $rs = null;

    if ($radioAddToText) {
        if (isset($dateStatsFirstDate)) {
            if (!sx_IsDate($dateStatsFirstDate)) {
                $dateStatsFirstDate = $dateNowDate;
            }
        } else {
            $dateStatsFirstDate = $dateNowDate;
        }
        $sql = "INSERT INTO visits_texts (TextID, PublishedDate, VisitDate, TotalVisits )
            VALUES (:VisitedTextID, :dateStatsFirstDate, :dateNowDate, 1)";
        $rs = $conn->prepare($sql);
        $rs->bindParam(':VisitedTextID', $intVisitedTextID, PDO::PARAM_INT);
        $rs->bindParam(':dateStatsFirstDate', $dateStatsFirstDate, PDO::PARAM_STR);
        $rs->bindParam(':dateNowDate', $dateNowDate, PDO::PARAM_STR);
        $rs->execute();
    } else {
        if ($dVisitDate != $dateNowDate) {
            $dVisitDate = $dateNowDate;
        }
        $iTotalVisits++;
        $sql = "UPDATE visits_texts SET  
            VisitDate = :VisitDate, 
            TotalVisits = :TotalVisits 
            WHERE TextID = :VisitedTextID ";
        $rs = $conn->prepare($sql);
        $rs->bindParam(':VisitDate', $dVisitDate, PDO::PARAM_STR);
        $rs->bindParam(':TotalVisits', $iTotalVisits, PDO::PARAM_INT);
        $rs->bindParam(':VisitedTextID', $intVisitedTextID, PDO::PARAM_INT);
        $rs->execute();
    }
}

$_SESSION["VisitorIP"] = $currentVisitorIP;
