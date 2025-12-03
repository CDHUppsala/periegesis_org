<?php
include "functionsLanguage.php";
include "functionsTableName.php";
include "functionsDBConn.php";

$targetValue = "";
if (isset($_POST["target_Value"])) {
    $targetValue = $_POST["target_Value"];
}

$grandValue = "";
if (isset($_POST["grand_Value"])) {
    $grandValue = $_POST["grand_Value"];
}

$parentValue = "";
if (isset($_POST["parent_Value"]) && !empty(($_POST["parent_Value"]))) {
    $parentValue = $_POST["parent_Value"];
}

$dataSQL = "";
if (isset($_POST["relation_SQL"])) {
    $dataSQL = $_POST["relation_SQL"];
}

$arrResults = null;
if (!empty($grandValue) && !empty($dataSQL)) {
    $stmt = $conn->prepare($dataSQL);
    if (empty($parentValue)) {
        $stmt->execute([$grandValue]);
    } else {
        $stmt->execute([$grandValue, $parentValue]);
    }
    $rs = $stmt->fetchAll(PDO::FETCH_NUM);
    if ($rs) {
        $arrResults = $rs;
    }
    $rs = null;
    $stmt = null;
}

if (is_array($arrResults)) {
    $strSelected = "";
    if (!empty($targetValue)) {
        $strSelected = " selected";
    }
    echo '<option value=""' . $strSelected . '>Choose</option>';
    $iCount = count($arrResults);
    for ($r = 0; $r < $iCount; $r++) {
        $sFieldValue = $arrResults[$r][0];
        $s_FieldValue = $sFieldValue;
        if (!empty($sFieldValue) & strlen($sFieldValue) > 50) {
            $s_FieldValue = substr($sFieldValue, 0, 50) . '...';
        }
        $iFieldSorting = $arrResults[$r][1];
        $strSelected = "";
        if (!empty($targetValue) && $targetValue == $sFieldValue) {
            $strSelected = " selected";
        }
        if (!empty($sFieldValue)) { ?>
            <option title="<?= $sFieldValue ?>" data-id="<?= $iFieldSorting ?>" value="<?= $sFieldValue ?>" <?= $strSelected ?>><?= $s_FieldValue . " [" . $iFieldSorting . "]" ?></option>
<?php
        }
    }
}
$arrResults = null;
?>