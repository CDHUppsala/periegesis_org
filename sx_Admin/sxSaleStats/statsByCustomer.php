<?php

// Determine sorting order
if (!isset($_SESSION['Sorting'])) {
    $_SESSION['Sorting'] = 'ASC';
}

if (isset($_GET['orderBy'])) {
    $_SESSION['Sorting'] = ($_SESSION['Sorting'] === 'ASC') ? 'DESC' : 'ASC';
    $strSorting = $_SESSION['Sorting'];
    $strOrderBy = " ORDER BY " . $_GET['orderBy'] . " " . $strSorting;
} else {
    $strOrderBy = "";
}

// Define SQL Query
$sql = "SELECT 
        o.CustomerID AS Customer_ID, 
        c.CompanyName AS Company, 
        c.LastName AS Last_Name, 
        c.Country AS Country, 
        c.City AS City, 
        COUNT(o.OrderID) AS Orders, 
        SUM(o.DiscountPrices) AS Price_Discount, 
        SUM(o.DiscountExtra) AS Extra_Discount, 
        SUM(o.DiscountShipping) AS Shipp_Discount, 
        SUM(o.DiscountTotal) AS Sum_Discount, 
        SUM(o.PayExpenses) AS Pay_Expenses, 
        SUM(o.ShipCharge) AS Shipp_Charge,
        SUM(o.TotalAccessories) AS Accessories,
        SUM(o.Total) AS Total, 
        SUM(o.TotalVAT) AS Total_VAT 
    FROM shop_orders AS o 
    INNER JOIN shop_customers AS c ON o.CustomerID = c.CustomerID $strYearWhere 
    GROUP BY o.CustomerID, c.CompanyName, c.LastName, c.Country, c.City 
        $strOrderBy";

$stmt = $conn->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<h2><?php echo lngByCustomer . ": " . $str_StatisticsYear; ?></h2>
<p>Click on column headers to change sorting</p>
    <table>
    <?php
    			
    $arrNoFormat = ['Customer_ID', 'Company', 'Last_Name', 'Country', 'City', 'Orders'];
    $arrNoSum = ['Customer_ID', 'Company', 'Last_Name', 'Country', 'City'];
    $sum = [];
    $header = true;
    foreach ($results as $row) {
        if ($header) {
            $sortColor = '';
            echo '<tr>';
            foreach ($row as $key => $value) {
                $strKey = str_replace('_',' ',$key);
                echo "<th><a href=\"" . $_SERVER['PHP_SELF'] . "?by=Customer&orderBy=$key\"><span{$sortColor}>{$strKey}</span></a></th>";
                $sum[$key] = 0;
            }
            echo '</tr>';
            $header = false;
        }
        echo '<tr>';
        foreach ($row as $key => $value) {
            $iValue = $value;
            if (is_numeric($value) && !in_array($key,$arrNoFormat)) {
                $iValue = number_format((float)$value, 2,'.',' ');
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
        } elseif (in_array($key,$arrNoSum)) {
            echo '<td></td>';
        } else {
            if (is_numeric($value) && !in_Array($key,$arrNoFormat)) {
                $value = number_format((float)$value, 2,'.',' ');
            }
            echo "<td>$value</td>";
        }
        $loop++;
    }
    echo '</tr>';
    ?>
</table>
