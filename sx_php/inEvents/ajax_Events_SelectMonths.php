<?php
include dirname(__DIR__) . "/sx_config.php";

$intYear = 0;
if (isset($_GET["year"])) {
    $intYear = $_GET["year"];
}

if (intval($intYear) > 0 && strlen($intYear) == 4) {


    $openConn;
    $minDte = $intYear . "-01-01";
    $maxDte = $intYear . "-12-31";
    $aResults = null;

    $sql = "SELECT DISTINCT MONTH(EventStartDate) AS AsMonth 
        FROM events 
        WHERE EventStartDate >= ? AND EventStartDate <= ?
        " . str_LanguageAnd . "
        ORDER BY MONTH(EventStartDate) ASC ";

    $stmt = $conn->prepare($sql);

    $stmt->execute([$minDte, $maxDte]);
    $rs = $stmt->fetchAll(PDO::FETCH_NUM);
    if ($rs) {
        $aResults = $rs;
    }
    $stmt = null;
    $rs = null;
    $closeConn;

    if (!empty($aResults) && is_array($aResults)) {
        $iRows = count($aResults);
        $i = -1;
        for ($r = 0; $r < $iRows; $r++) {
            $i = $aResults[$r][0];
            $strName = lng_MonthNames[$i - 1];
            $strSelected = "";
            if ($i == Date("n")) {
                $strSelected = " selected";
            } ?>
            <option value="<?= $i ?>" <?= $strSelected ?>><?= $strName ?></option>
<?php
        }
    } else {
        echo '<option value="0">No Events</option>';
    }
}
?>