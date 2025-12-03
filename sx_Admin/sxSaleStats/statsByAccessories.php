<?php

// Replace WHERE clause with AND if necessary
if (!empty($strYearWhere)) {
    $strYearWhere = str_replace("WHERE ", "AND ", $strYearWhere);
}

// Set sorting order in session
if (!isset($_SESSION['Sorting'])) {
    $_SESSION['Sorting'] = 'ASC';
} else {
    $_SESSION['Sorting'] = ($_SESSION['Sorting'] === 'ASC') ? 'DESC' : 'ASC';
}
$strSorting = $_SESSION['Sorting'];

// Get orderBy parameter
$strOrderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : '';
if (!empty($strOrderBy)) {
    $str_OrderBy = " ORDER BY $strOrderBy $strSorting";
} else {
    $str_OrderBy = "ORDER BY e.Order_Year, e.ProductID, e.AccessoryID";
}

// Get accessories from ordered items
$sql = "WITH expanded AS (
    SELECT 
        soi.ProductID,
        CAST(accessory AS UNSIGNED) AS AccessoryID,
        YEAR(so.OrderDate) AS Order_Year
    FROM shop_order_items AS soi
    INNER JOIN shop_orders so ON soi.OrderID = so.OrderID
    CROSS JOIN JSON_TABLE(
        CASE 
            WHEN JSON_VALID(Accessories) THEN Accessories 
            ELSE '[]' 
        END, 
        '$[*]' COLUMNS (accessory JSON PATH '$')
    ) AS jt
    WHERE soi.Accessories <> '' AND soi.Accessories IS NOT NULL
    $strYearWhere
    )
    SELECT 
        e.Order_Year,
        e.ProductID,
        p.ProductName AS ProductName,
        e.AccessoryID,
        a.AccessoryName AS AccessoryName,
        COUNT(*) AS Count
    FROM expanded e
    LEFT JOIN products p ON e.ProductID = p.ProductID
    LEFT JOIN product_accessories a ON e.AccessoryID = a.AccessoryID
    GROUP BY e.Order_Year, e.ProductID, e.AccessoryID, p.ProductName, a.AccessoryName
    $str_OrderBy";

$stmt = $conn->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>By Product Accessories: <?php echo $str_StatisticsYear ?></h2>
<p>Click on column headers to change sorting</p>
<table>
    <?php

    $arrNoFormat = ['Order_Year', 'ProductID', 'ProductName', 'AccessoryID', 'AccessoryName', 'Count'];
    $arrNoSum = ['Order_Year', 'ProductID', 'ProductName', 'AccessoryID', 'AccessoryName'];
    $sum = [];
    $accName = [];
    $accCount = [];
    $header = true;
    foreach ($results as $row) {
        if ($header) {
            $sortColor = '';
            echo '<tr>';
            foreach ($row as $key => $value) {
                $strKey = str_replace('_', ' ', $key);
                echo "<th><a href=\"" . $_SERVER['PHP_SELF'] . "?by=Accessories&orderBy=$key\"><span{$sortColor}>{$strKey}</span></a></th>";
                $sum[$key] = 0;
            }
            echo '</tr>';
            $header = false;
        }
        echo '<tr>';
        foreach ($row as $key => $value) {
            $iValue = $value;
            if (is_numeric($value) && !in_array($key, $arrNoFormat)) {
                $iValue = number_format((float)$value, 2, '.', ' ');
            }
            $strClass = '';
            if (in_array($key, $arrNoSum)) {
                $strClass = ' class="text_field"';
            }
            echo "<td{$strClass}>{$iValue}</td>";

            if (is_numeric($value)) {
                $sum[$key] += $value;
            }

            // Count the total number for every unique accessory
            if ($key === 'AccessoryID') {
                if (!isset($accCount[$value])) {
                    $accName[$value] = $row['AccessoryName'];
                    $accCount[$value] = 0;
                }
                $accCount[$value] += $row['Count'];
            }
        }
        echo '</tr>';
    }

    echo '<tr>';
    //$arrNoFormat = ['Product_Orders', 'Quantity'];
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
            echo "<td>$value</td>";
        }
        $loop++;
    }
    echo '</tr>';
    ?>
</table>

<h2>By Unique Accessories: <?php echo $str_StatisticsYear ?></h2>
<table>
    <tr>
        <th>Accessory ID</th>
        <th>Accessory Name</th>
        <th>Count</th>
    </tr>

    <?php
    ksort($accName);
    ksort($accCount);
    $iSum = 0;
    foreach ($accCount as $key => $value) {
        echo '<tr>';
        echo "<td>$key</td>";
        echo "<td class=\"text_field\">$accName[$key]</td>";
        echo "<td>$value</td>";
        echo '</tr>';
        $iSum += $value;
    }
    echo '<tr>';
    echo "<td>Sum</td>";
    echo "<td></td>";
    echo "<td>$iSum</td>";
    echo '</tr>';
    ?>
</table>