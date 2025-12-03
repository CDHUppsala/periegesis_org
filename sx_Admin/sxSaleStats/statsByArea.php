<?php

// Sorting logic
if (!isset($_SESSION['Sorting'])) {
    $_SESSION['Sorting'] = "ASC";
} else {
    $_SESSION['Sorting'] = ($_SESSION['Sorting'] == "ASC") ? "DESC" : "ASC";
}
$strSorting = $_SESSION['Sorting'];

// Ordering
$strOrderBy = isset($_GET["orderBy"]) ? $_GET["orderBy"] : "";
if (!empty($strOrderBy)) {
    $strOrderBy = " ORDER BY $strOrderBy $strSorting";
}

// Change WHERE to AND, if any
if (!empty($strYearWhere)) {
    $strYearWhere = str_replace("WHERE ", "AND ", $strYearWhere);
}


// Grouping
if (!empty($_POST["GroupByField"])) {
    $_SESSION["GroupByField"] = $_POST["GroupByField"];
}

if (!empty($_SESSION["GroupByField"])) {
    $strGroupByField = $_SESSION["GroupByField"];
} else {
    $strGroupByField = "City";
}


$sql = "SELECT DISTINCT $strGroupByField,
        COUNT(OrderID) AS Orders,
        SUM(DiscountExtra) AS Extra_Discount,
        SUM(DiscountShipping) AS Shipp_Discount,
        SUM(DiscountPrices) AS Prices_Discount,
        SUM(DiscountTotal) AS Sum_Discount,
        SUM(PayExpenses) AS Pay_Expenses,
        SUM(ShipCharge) AS Shipping,
        SUM(TotalAccessories) AS Accessories,
        SUM(Total) AS Sum_Total,
        AVG(Total) AS Avg_Total
    FROM shop_orders
    WHERE total > 0 $strYearWhere
    GROUP BY $strGroupByField $strOrderBy";

$stmt = $conn->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Extract field names for sorting
$fieldNames = array_keys($results[0] ?? []);

$sum = [];
?>

<div class="coloredBG">
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>?by=Area" method="post">
        <label><?php echo lngSelectGroupByField; ?>:
            <select name="GroupByField">
                <option value="City" <?php echo ($strGroupByField == "City") ? "selected" : ""; ?>><?php echo lngCity; ?></option>
                <option value="Country" <?php echo ($strGroupByField == "Country") ? "selected" : ""; ?>><?php echo lngCountry; ?></option>
                <option value="State" <?php echo ($strGroupByField == "State") ? "selected" : ""; ?>><?php echo lngState; ?></option>
                <option value="PostalCode" <?php echo ($strGroupByField == "PostalCode") ? "selected" : ""; ?>><?php echo lngPostalCode; ?></option>
            </select>
        </label>
        <input type="submit" value="Select">
    </form>
</div>

<h2><?php echo lngByArea . ": " . $str_StatisticsYear; ?></h2>
<p>Click on column headers to change sorting</p>
<table>
    <tr>
        <?php foreach ($fieldNames as $fieldName) { ?>
            <th>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>?by=Area&orderBy=<?php echo urlencode($fieldName); ?>">
                    <span><?php echo str_replace("_", " ", $fieldName); ?></span>
                </a>
            </th>
        <?php
            $sum[$fieldName] = 0;
        } ?>
    </tr>

    <?php
    $arrNoFormat = ['City', 'Country', 'State', 'PostalCode', 'Orders'];
    $arrNoSum = ['City', 'Country', 'State', 'Postal_Code'];
    foreach ($results as $row) {
        echo '<tr>';
        foreach ($fieldNames as $fieldName) {
            $value = $row[$fieldName];
            $iValue = $value;
            if (is_numeric($value) && !in_array($fieldName, $arrNoFormat)) {
                $iValue = number_format($value, 2, '.', ' ');
            }
            $strClass = '';
            if(in_array($fieldName,$arrNoSum)) {
                $strClass = ' class="text_field"';
            }
            echo "<td{$strClass}>{$iValue}</td>";
            if (is_numeric($value)) {
                $sum[$fieldName] += $value;
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
            if (is_numeric($value) && !in_Array($key, $arrNoFormat)) {
                $value = number_format((float)$value, 2, '.', ' ');
            }
            echo "<td>{$value}</td>";
        }
        $loop++;
    }
    echo '</tr>';
    ?>
</table>