<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsDBConn.php";
include "functions.php";
include "sxVisits.php";

/**
 * Get the last visit date and the number of visits
 */
$dateFirstVisitDate = null;
$dateLastVisitDate = null;

$intVisits = 0;
$intTotalVisits = 0;

$sql = "SELECT MIN(Datum) AS AsDatum FROM visits WHERE Datum is NOT Null ";
$rs = $conn->query($sql)->fetch();
if ($rs) {
    $dateFirstVisitDate = $rs["AsDatum"];
    if (!sx_IsDate($dateFirstVisitDate)) {
        $dateFirstVisitDate = date('Y-m-d');
    }
} else {
    $dateFirstVisitDate = date('Y-m-d');
}
$rs = null;

$intMinVisitYear = sx_getYear($dateFirstVisitDate);
$sql = "SELECT Datum, Visits, TotalVisits FROM visits ORDER BY VisitID DESC LIMIT 1";
$rs = $conn->query($sql)->fetch();
if ($rs) {
    $dateLastVisitDate = $rs["Datum"];
    $intVisits = $rs["Visits"];
    $intTotalVisits = $rs["TotalVisits"];
}
$rs = null;

$intRatio = 200;
define("sx_intRatio", $intRatio);

?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>SX Statistics</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
    <script src="<?php echo sx_ADMIN_DEV ?>js/jsFunctions.js"></script>
</head>

<body id="bodyStats" class="body">
    <header id="header">
        <h2><?= lngVisitsStatistics ?></h2>
        <div>
            <a class="button" href="default.php"><?= lngAggregatedStatistics ?></a>
            <a class="button" href="default.php?pg=v"><?= lngVisitsByDate ?></a>
            <?php
            if (sx_IncludeProductStats) { ?>
                <a class="button" href="default.php?pg=p"><?= lngProductStatistics ?></a>
            <?php
            }
            if (STR_TextTableVersion == "articles") { ?>
                <a class="button" href="default.php?pg=a"><?= lngTextStatistics ?></a>
            <?php
            } elseif (STR_TextTableVersion == "items") { ?>
                <a class="button" href="default.php?pg=i"><?= lngTextStatistics ?></a>
            <?php
            } elseif (STR_TextTableVersion == "texts") { ?>
                <a class="button" href="default.php?pg=t"><?= lngTextStatistics ?></a>
            <?php
            } ?>
        </div>
    </header>
    <?php

    $strPG = $_GET["pg"] ?? '';
    if ($strPG == "v") {
        /**
         * Request (v)
         * Visits by date
         */
        if (isset($_POST["datePeriod"])) {
            $strDatePeriod = $_POST["datePeriod"];
        } elseif (isset($_GET["datePeriod"])) {
            $strDatePeriod = @$_GET["datePeriod"];
        } else {
            $strDatePeriod = 0;
        }

        if (isset($_POST["dateInterval"])) {
            $strDateInterval = $_POST["dateInterval"];
        } elseif (isset($_GET["dateInterval"])) {
            $strDateInterval = @$_GET["dateInterval"];
        } else {
            $strDateInterval = "q";
        }

        if ($strDatePeriod > 0 && $strDatePeriod <= 12) {
            $countDatePeriod = sx_AddToDate(Date('Y-m-d'), -$strDatePeriod, 'months');
            $strDatePeriodWhere = "WHERE (Datum >= '" . $countDatePeriod . "')";
            $writePeriod = lngLast . " " . $strDatePeriod . " " . lngMonths;
        } elseif ($strDatePeriod > 12) {
            $strDatePeriodWhere = "WHERE YEAR(Datum) = " . $strDatePeriod;
            $writePeriod = lngYear . " " . $strDatePeriod;
        } else {
            $strDatePeriodWhere = "";
            $writePeriod = lngAllDates;
        }


        $aResults = sx_getVisits($strDatePeriodWhere, $strDateInterval);
        $maxVisits = 0;
        $intVisitsTotal = 0;

        if (!empty($aResults)) {
            foreach ($aResults as $row) {
                $currentCount = (int)$row['countResult'];

                if ($currentCount > $maxVisits) {
                    $maxVisits = $currentCount;
                }
                $intVisitsTotal += $currentCount;
            }
        }

        /**
         * Get the ratio for the highest line
         */
        if ($maxVisits == 0) {
            echo "<h2>No Page Views to Display</h2>";
        } else {
            sx_showVisits($maxVisits, $strDateInterval, $intRatio, $aResults);
        }
        $aResults = null;
        sx_showVisitsForm($strDateInterval, $strDatePeriod, $intMinVisitYear);
    } elseif ($strPG == "p" && sx_IncludeProductStats) {
        /**
         * Request (p): Visits by product
         */
        include_once "sxProducts.php";
        sx_getProductVisits(500);
    } elseif ($strPG == "t") {
        /**
         * Request (t): Visits Texts by Date
         */
        include_once "sxTexts.php";
        sx_getTextByDateLinks();
        sx_getTextVisits(200);
    } elseif ($strPG == "a") {
        /**
         * Request (a): Visits Articles by Date
         */
        include_once "sxArticles.php";
        sx_getArticlesByDateLinks();
        sx_getArticleVisits(200);
    } elseif ($strPG == "i") {
        /**
         * Request (t): Visits Items - not by Date
         */
        include_once "sxItems.php";

        sx_getItemsByDateLinks();
        $data = sx_getItemVisitsData(200);
        sx_renderItemVisits($data);
    } else {
        /**
         * First, Default page: Aggregate statistics
         */
    ?>
        <section>
            <h2><?= lngAggregatedStatistics ?></h2>
            <table class="tableBorders">
                <tr>
                    <td><?= lngVisitsToday ?> <b><?= $dateLastVisitDate ?></b></td>
                    <td><?= $intVisits ?></td>
                </tr>
                <tr>
                    <td><?= lngTotalVisitsSince ?> <b><?= $dateFirstVisitDate ?></b></td>
                    <td><?= number_format(($intTotalVisits), 0, ",", " ") ?></td>
                </tr>
            </table>
        </section>
        <section>
            <?php
            $strDatePeriod = 1;
            $strDateInterval = "y";
            $countDatePeriod = sx_AddToDate(Date('Y-m-d'), -$strDatePeriod, 'months');
            $strDatePeriodWhere = "WHERE (Datum >= '" . $countDatePeriod . "')";
            $writePeriod = lngLast . " " . $strDatePeriod . " " . lngMonths;

            $aResults = sx_getVisits($strDatePeriodWhere, $strDateInterval);
            $maxVisits = 0;
            $intVisitsTotal = 0;

            if (!empty($aResults)) {
                foreach ($aResults as $row) {
                    $currentCount = (int)$row['countResult'];

                    if ($currentCount > $maxVisits) {
                        $maxVisits = $currentCount;
                    }
                    $intVisitsTotal += $currentCount;
                }
            }

            //Get the ratio for the highest line
            if ($maxVisits == 0) {
                echo "<h2>No Page Views to Display</h2>";
            } else {
                sx_showVisits($maxVisits, $strDateInterval, 200, $aResults);
            }
            $aResults = null;
            ?>
        </section>

    <?php
        if (sx_IncludeProductStats) {
            echo '<hr>';
            echo '<section>';
            include_once "sxProducts.php";
            sx_getProductVisits(20);
            echo '</section>';
        }
        echo '<hr>';
        echo '<section>';
        if (STR_TextTableVersion == "articles") {
            include_once "sxArticles.php";
            sx_getArticleVisits(20);
        } elseif (STR_TextTableVersion == "items") {
            include_once "sxItems.php";
            $data = sx_getItemVisitsData(20);
            sx_renderItemVisits($data);
        } else {
            include_once "sxTexts.php";
            sx_getTextVisits(20);
        }
        echo '</section>';
    } ?>
</body>

</html>