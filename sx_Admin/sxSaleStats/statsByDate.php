<?php

if (!isset($_SESSION['Sorting'])) {
    $_SESSION['Sorting'] = "ASC";
} else {
    $_SESSION['Sorting'] = ($_SESSION['Sorting'] === "ASC") ? "DESC" : "ASC";
}

$strSorting = $_SESSION['Sorting'];

// Adjust WHERE clause if necessary
if (!empty($strYearWhere)) {
    $strYearWhere = str_replace("WHERE ", "AND ", $strYearWhere);
}

// Handle ORDER BY
$strOrderBy = isset($_GET["orderBy"]) ? " ORDER BY " . $_GET["orderBy"] . " " . $strSorting : "";

// Handle GroupByField selection
if (!empty($_POST["GroupByField"])) {
    $strGroupByField = $_POST["GroupByField"];
    $_SESSION["GroupByField"] = $strGroupByField;
} elseif (!empty($_SESSION["GroupByField"])) {
    $strGroupByField = $_SESSION["GroupByField"];
} else {
    $strGroupByField = "";
    $_SESSION["GroupByField"] = "";
}

switch ($strGroupByField) {
    case "Month":
        $GroupByField = "YEAR(o.OrderDate) AS By_Year, MONTH(o.OrderDate) AS By_Month";
        $strGroupBy = "By_Year, By_Month";
        break;
    case "Quarter":
        $GroupByField = "YEAR(o.OrderDate) AS By_Year, QUARTER(o.OrderDate) AS By_Quarter";
        $strGroupBy = "By_Year, By_Quarter";
        break;
    case "Week":
        $GroupByField = "YEAR(o.OrderDate) AS By_Year, WEEK(o.OrderDate, 1) AS By_Week";
        $strGroupBy = "By_Year, By_Week";
        break;
    default:
        $GroupByField = "YEAR(o.OrderDate) AS By_Year";
        $strGroupBy = "By_Year";
        break;
}

// Construct SQL query
$sql = "SELECT DISTINCTROW $GroupByField, 
        COUNT(orderID) AS Orders, 
        SUM(DiscountExtra) AS Extra_Discount, 
        SUM(DiscountShipping) AS Shipp_Discount, 
        SUM(DiscountPrices) AS Prices_Discount, 
        SUM(DiscountTotal) AS Sum_Discount, 
        SUM(PayExpenses) AS Pay_Expenses, 
        SUM(ShipCharge) AS Shipping, 
        SUM(TotalAccessories) AS Accessories,
        SUM(Total) AS Total, 
        SUM(TotalVAT) AS Total_VAT 
    FROM shop_orders AS o 
    WHERE Total > 0 $strYearWhere
    GROUP BY $strGroupBy $strOrderBy ";

$stmt = $conn->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($strExport)) { ?>
    <div class="coloredBG">
        <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"] . "?by=Date") ?>" name="GroupBy">
            <?= lngSelectGroupByField ?>:
            <select name="GroupByField" size="1">
                <option value="Year" <?= ($strGroupByField == "Year") ? " selected" : "" ?>><?= lngYear ?></option>
                <option value="Quarter" <?= ($strGroupByField == "Quarter") ? " selected" : "" ?>><?= lngQuarter ?></option>
                <option value="Month" <?= ($strGroupByField == "Month") ? " selected" : "" ?>><?= lngMonth ?></option>
                <option value="Week" <?= ($strGroupByField == "Week") ? " selected" : "" ?>><?= lngWeek ?></option>
            </select>
            <input type="submit" name="Select" value="Select">
        </form>
    </div>
<?php
} ?>

<h2><?= lngByDate . ": " . $str_StatisticsYear ?></h2>
<table>
    <?php
    $arrNoFormat = ['By_Year', 'By_Month', 'By_Quarter', 'By_Week', 'Orders'];
    $arrNoSum = ['By_Month', 'By_Quarter', 'By_Week'];
    $sum = [];
    $header = true;
    foreach ($results as $row) {
        if ($header) {
            $sortColor = '';
            echo '<tr>';
            foreach ($row as $key => $value) {
                $strKey = str_replace('_', ' ', $key);
                echo "<th><a href=\"" . $_SERVER['PHP_SELF'] . "?by=Product&orderBy=$key\"><span{$sortColor}>{$strKey}</span></a></th>";
                $sum[$key] = 0;
            }
            echo '</tr>';
            $header = false;
        }
        echo '<tr>';
        foreach ($row as $key => $value) {
            $iValue = $value;
            if (is_numeric($value) && !in_array($key,$arrNoFormat)) {
                $iValue = number_format((float)$value, 2, '.', ' ');
            }
            $strClass = '';
            if(in_array($key,$arrNoSum)) {
                $strClass = ' class="text_field"';
            }
            echo "<td{$strClass}>{$iValue}</td>";

            if (is_numeric($value)) {
                $sum[$key] += $value;
            }
        }
        echo '</tr>';
    }
        echo '<tr>';
    $loop = 0;
    foreach ($sum as $key => $value) {
        if ($loop === 0) {
            echo "<td>Sum</td>";
        } elseif (in_array($key, $arrNoSum)) {
            echo '<td></td>';
        } else {
            if (is_numeric($value) && $key !== 'Orders') {
                $value = number_format((float)$value, 2, '.', ' ');
            }
            echo "<td>$value</td>";
        }
        $loop++;
    }
    echo '</tr>';
    ?>
</table>