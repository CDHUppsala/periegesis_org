<?php
/**
 * Called by an ajax file from a language folder
 * Used in Text Calendar by Month
 * Get only the months from a selected year that contain texts.
 * To avoid empty months in selection list.
 */
$iYear = 0;
if (isset($_GET["year"])) {
    $iYear = $_GET["year"];
}
if (return_Filter_Integer($iYear) == 0) {
    $iYear = date("Y");
} else {
    $iYear = (int) ($iYear);
}
if (strlen($iYear) == 4) {
    $sql = "SELECT DISTINCT MONTH(PublishedDate) AS AsMonth
    FROM ". sx_TextTableVersion ."
    WHERE PublishedDate >= ? AND PublishedDate <= ? 
    ORDER BY MONTH(PublishedDate) ASC ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$iYear."-01-01",$iYear."-12-31"]);
    $aResults = $stmt->fetchAll(PDO::FETCH_NUM);
    $stmt = null;

    if (is_array($aResults)) {
        $iRows = count($aResults);
        for ($r=0; $r < $iRows; $r++) {
            $i = $aResults[$r][0];
            $strName = lng_MonthNames[$i-1];
            $strSelected = "";
            if ($i == Date("n")) {
                $strSelected = " selected";
            } ?>
    		<option value="<?=$i?>" <?=$strSelected?>><?=$strName?></option>
    	<?php
        }
    }
    $aResults = null;
}
?>
