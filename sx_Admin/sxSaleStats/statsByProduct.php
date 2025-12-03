<?php


// Set sorting order in session
if (!isset($_SESSION['Sorting'])) {
    $_SESSION['Sorting'] = 'ASC';
} else {
    $_SESSION['Sorting'] = ($_SESSION['Sorting'] === 'ASC') ? 'DESC' : 'ASC';
}
$strSorting = $_SESSION['Sorting'];

// Get orderBy parameter
$strOrderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : '';
$strOrderBy = $strOrderBy ? " ORDER BY $strOrderBy $strSorting" : '';


$sql = "SELECT 
        oi.ProductID AS Product_ID, 
        p.ProductName AS Product_Name, 
        COUNT(oi.OrderID) AS Product_Orders, 
        SUM(oi.Quantity) AS Quantity, 
        ((100 / (100 - oi.DiscountRate)) * oi.OrderedPrice) AS Initial_Price, 
        oi.OrderedPrice AS Ordered_Price, 
        oi.DiscountRate AS Discount_Rate, 
        (((100 / (100 - oi.DiscountRate)) * oi.OrderedPrice) - oi.OrderedPrice) AS Discount, 
        ((((100 / (100 - oi.DiscountRate)) * oi.OrderedPrice) - oi.OrderedPrice) * SUM(oi.Quantity)) AS Sum_Discount, 
        (oi.OrderedPrice * SUM(oi.Quantity)) AS Sum_Sales 
    FROM shop_order_items AS oi 
    INNER JOIN products AS p ON oi.ProductID = p.ProductID 
    INNER JOIN shop_orders AS o ON oi.OrderID = o.OrderID {$strYearWhere}
    GROUP BY oi.ProductID, p.ProductName, oi.OrderedPrice, oi.DiscountRate {$strOrderBy}";

$stmt = $conn->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2><?php echo lngByProduct . ': ' . $str_StatisticsYear ?></h2>
<p>Click on column headers to change sorting</p>
<table>
    <?php

    $arrNoFormat = ['Product_ID', 'Product_Name', 'Product_Orders', 'Quantity'];
    $arrNoSum = ['Product_Name', 'Initial_Price', 'Ordered_Price', 'Discount_Rate'];
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