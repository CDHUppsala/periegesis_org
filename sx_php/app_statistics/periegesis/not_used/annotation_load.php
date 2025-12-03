<?php

/**
 * NOT USED
 */

$docids = array(
    "vg4xbkht7tblkt",
    "q25vx8179944yk",
    "qzqqtb52ut59u7",
    "iteuia4mpqdfa9",
    "o12fg62uwe4a3r",
    "dab3yh8cvjciwz",
    "2ytcj885cuie6w",
    "n1dny8qzasy2ni",
    "fh0zinvr8oqmjl",
    "bx6x9ozcqcn0d4"
);
$annotation_dir = PROJECT_PRIVATE . "/annotations";

if (!is_dir($annotation_dir)) mkdir($annotation_dir, 0777, true);

$str_ColumnName = "Periegesis";
$dateCurrent = date('Y-m-d');
$dateInterval = return_Add_To_Date(date('Y-m-d'), -30);
$radio_Upload = false;
$jsonStats = "";


$sql = "SELECT CachingDate, CachedData FROM data_caching WHERE CachingName = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$str_ColumnName]);
$rs = $stmt->fetch();

$dateCachingDate = $rs["CachingDate"];
$strCachedData = $rs["CachedData"];

/*
echo $dateCurrent;
echo '<br>';
echo $dateInterval;
echo '<br>';
echo $dateCachingDate;
echo '<br>';
echo $strCachedData;
echo '<br>';
*/

if (empty($dateCachingDate)) {
    $sql = "INSERT INTO data_caching (CachingName, CachingDate) VALUES (?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$str_ColumnName, $dateCurrent]);
    $radio_Upload = true;
} else {
    if ($dateCachingDate < $dateInterval || empty($strCachedData)) {
        $sql = "UPDATE data_caching SET CachingDate = ? WHERE CachingName = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$dateCurrent, $str_ColumnName]);
        $radio_Upload = true;
    }
}

if ($radio_Upload) {
    foreach ($docids as $docid) {
        //$url = "http://recogito.humlab.umu.se/document/$docid/downloads/annotations/csv";
        $url = "http://recogito.abm.uu.se/document/$docid/downloads/annotations/csv";
        $filepath = "$annotation_dir/$docid.csv";
        if (!file_put_contents($filepath, file_get_contents($url))) {
            exit("Downloading $url to $filepath failed.");
        }
    }
} else {
    echo $strCachedData;
    exit();
}

if ($radio_Upload) {
    $stats_persons = 0;
    $stats_places = 0;
    $stats_events = 0;
    $lastUpdate = 0;
    $arrStats = array();
    foreach ($docids as $docid) {
        $filepath = "$annotation_dir/$docid.csv";
        $mtime = filemtime($filepath);
        if ($lastUpdate === 0 || $mtime < $lastUpdate) $lastUpdate = $mtime;

        $file = fopen($filepath, "r");
        $x = 0;
        while (!feof($file)) {
            if ($x > 0) {
                $row_array = fgetcsv($file);
                if (!empty($row_array)) {
                    $sType = $row_array[4];
                    if ($sType == 'PERSON') {
                        $stats_persons++;
                    } elseif ($sType == 'PLACE') {
                        $stats_places++;
                    } elseif ($sType == 'EVENT') {
                        $stats_events++;
                    }
                }
            }
            $x = 1;
        }
        fclose($file);
    }
    $arrStats["Persons"] = $stats_persons;
    $arrStats["Places"] = $stats_places;
    $arrStats["Events"] = $stats_events;
    $arrStats["LastUpdated"] = $lastUpdate;

    $jsonStats = json_encode($arrStats);

    $sql = "UPDATE data_caching SET CachedData = ? WHERE CachingName = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$jsonStats, $str_ColumnName]);
    echo $jsonStats;
    exit();
}
