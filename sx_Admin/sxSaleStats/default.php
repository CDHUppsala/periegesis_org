<?php
include dirname(__DIR__) . "/functionsLanguage.php";
include dirname(__DIR__) . "/login/lockPage.php";
include dirname(__DIR__) . "/functionsDBConn.php";

if (isset($_GET['clear'])) {
    unset($_SESSION['GroupByField']);
    unset($_SESSION['Sorting']);
    unset($_SESSION['StatYear']);
}

// Keep the Year session between different statistic tables 
if (isset($_GET['new'])) {
    unset($_SESSION['GroupByField']);
    unset($_SESSION['Sorting']);
}

$str_Path = $_SERVER['QUERY_STRING'];
$strExport = $_GET['export'] ?? '';
$strStatsBy = $_GET['by'] ?? 'Product';

//$conn = dbConn();

$arrYears = [];
$sql = "SELECT DISTINCT YEAR(OrderDate) FROM shop_orders ORDER BY YEAR(OrderDate) DESC";
$stmt = $conn->query($sql);
if ($stmt) {
    $arrYears = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
$stmt = null;

$intStatYear = (int)($_POST['StatYear'] ?? 0);
if ($intStatYear > 0) {
    $_SESSION['StatYear'] = $intStatYear;
} elseif (isset($_SESSION['StatYear']) && $_SESSION['StatYear'] > 0) {
    $intStatYear = $_SESSION['StatYear'];
}

$int_DefaultYear = $arrYears[0] ?? 0;
if ($intStatYear === 0 && $int_DefaultYear > 0) {
    $intStatYear = $int_DefaultYear;
} ?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>STUDIO X CMS - Statistics</title>
    <?php if (!$strExport) : ?>
        <link rel="stylesheet" type="text/css" href="../css/sxCMS.css">
    <?php endif; ?>
    <style>
        th {
            padding-right: 0.5rem;
            text-align: center;
        }

        td {
            padding: 0.25rem 0.5rem;
        }

        td:nth-child(n+2) {
            text-align: right;
        }
        td.text_field {
            text-align: left;
        }

        tr:last-child td {
            border-top: 1px solid #999 !important;
            white-space: nowrap;
        }
    </style>
</head>

<body class="body">
    <?php if (!$strExport) : ?>
        <div id="header" class="row">
            <h2><?= getCapitals(lngSaleStatistics) ?></h2>
            <div>
                <a class="button" href="default.php?by=Product&new=yes"> <?= lngByProduct ?></a>
                <a class="button" href="default.php?by=Accessories&new=yes">By Accessories</a>
                <a class="button" href="default.php?by=Customer&new=yes"> <?= lngByCustomer ?></a>
                <a class="button" href="default.php?by=Area&new=yes"> <?= lngByArea ?></a>
                <a class="button" href="default.php?by=Date&new=yes"> <?= lngByDate ?></a>
            </div>
            <div>
                <a class="button" href="default.php?by=<?= $strStatsBy ?>&export=print"><?= lngPrintText ?></a>
                <a class="button" target="_top" href="default.php?by=<?= $strStatsBy ?>&export=excel"><?= lngSaveInExcel ?></a>
                <a class="button" target="_top" href="default.php?by=<?= $strStatsBy ?>&export=word"><?= lngSaveInWord ?></a>
                <a class="button" target="_top" href="default.php?by=<?= $strStatsBy ?>&export=html"><?= lngSaveInHTML ?></a>
            </div>
        </div>

        <div class="floatRight" style="margin: 0.5rem 0.5rem 0 0">
            <?php if (!empty($arrYears)) : ?>
                <form method="post" name="GetByYear" action="default.php?<?= $str_Path ?>">
                    <select name="StatYear">
                        <option value="1900">All Years</option>
                        <?php foreach ($arrYears as $year) : ?>
                            <option value="<?= $year ?>" <?= ($intStatYear == $year) ? 'selected' : '' ?>><?= $year ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="submit" name="SubmitYear" value="Select Year">
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php
    $strYearWhere = "";
    $str_StatisticsYear = "All Years";
    if ($intStatYear != 1900) {
        $strYearWhere = " WHERE YEAR(OrderDate) = $intStatYear";
        $str_StatisticsYear = "From Year $intStatYear";
    }

    switch ($strStatsBy) {
        case "Product":
            include "statsByProduct.php";
            break;
        case "Customer":
            include "statsByCustomer.php";
            break;
        case "Area":
            include "statsByArea.php";
            break;
        case "Date":
            include "statsByDate.php";
            break;
        case 'Accessories':
            include "statsByAccessories.php";
            break;
    }
    $conn = null;
    ?>
</body>

</html>

<?php
if ($strExport) {
    $filename = $_SERVER['HTTP_HOST'] . "_statistics_" . $strStatsBy . "_" . date("Y-m-d") . ".";
    switch ($strExport) {
        case "excel":
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=" . $filename . "xls");
            break;
        case "word":
            header("Content-Type: application/vnd.ms-word");
            header("Content-Disposition: attachment; filename=" . $filename . "doc");
            break;
        case "html":
            header("Content-Type: text/html");
            header("Content-Disposition: attachment; filename=" . $filename . "html");
            break;
        case "print":
            echo '<script>window.print();</script>';
            break;
    }
}
?>