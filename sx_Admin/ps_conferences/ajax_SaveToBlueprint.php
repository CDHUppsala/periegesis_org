<?php
include realpath(dirname(__DIR__) ."/functionsLanguage.php");
include PROJECT_ADMIN ."/login/lockPage.php";
include PROJECT_ADMIN ."/functionsTableName.php";
include PROJECT_ADMIN ."/functionsDBConn.php";

include "blueprint_functions.php";

$aResults = json_decode(stripslashes($_POST['data']));

if (is_array($aResults)) {
    $sql = "DROP TABLE IF EXISTS conf_blueprint";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $stmt = null;

    $sql = "CREATE TABLE `conf_blueprint` (
        `PaperID` int DEFAULT '0',
        `ConferenceID` int DEFAULT '0',
        `StartDate` date DEFAULT NULL ,
        `EndDate` date DEFAULT NULL ,
        `SessionID` int DEFAULT NULL,
        `SessionTitle` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL ,
        `SessionDate` date DEFAULT NULL ,
        `S_StartTime` time DEFAULT NULL ,
        `S_EndTime` time DEFAULT NULL ,
        `P_StartTime` time DEFAULT NULL ,
        `P_EndTime` time DEFAULT NULL ,
        `PaperTitle` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $stmt = null;

    $r_count = count($aResults);
    $c_count = count($aResults[0]);
    for ($r = 0; $r < $r_count; $r++) {
        $arrValues = null;
        for ($c = 0; $c < $c_count; $c++) {
            $arrValues[] = $aResults[$r][$c];
        }
        if (is_array($arrValues)) {
            $sql = "INSERT INTO conf_blueprint
            (PaperID,
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
            PaperTitle)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute($arrValues);
        }
    }
}
