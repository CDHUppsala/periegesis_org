<?php
$intCustomerID = $_GET['cid'] ?? 0;
if ($intCustomerID === 0) {
    header('Location: ../main.php?msg=Select_a_Customer');
    exit;
}

// Retrieve information from form
$strChoice = $_POST['choice'] ?? '';
$strWhere = '';

if ($strChoice === '' || $strChoice === 'New') {
    $strWhere = " AND (Completed = 0 AND InProcess = 0)";
} elseif ($strChoice === 'InProcess') {
    $strWhere = " AND (InProcess = 1 AND Completed = 0)";
} elseif ($strChoice === 'Completed') {
    $strWhere = " AND Completed = 1";
} else {
    $strWhere = '';
}

$sql = "
    SELECT 
        o.OrderID, o.Completed, o.InProcess, 
        o.OrderDate, o.ShipDate, 
        o.Total, 
        s.ShippingMethod, 
        s.ShippingTrackURL 
    FROM shop_orders as o
    LEFT JOIN shop_shipping as s ON o.ShipMethod = s.ShippingID 
    WHERE CustomerID = :intCustomerID 
    $strWhere 
    ORDER BY OrderID DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([':intCustomerID' => $intCustomerID]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SXCMS List of Records</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2025-02-28">
    <script src="<?php echo sx_ADMIN_DEV ?>js/jsFunctions.js?v=2025-02-28"></script>
    <script src="../js/jq/jquery.min.js"></script>
    <script src="<?php echo sx_ADMIN_DEV ?>js/jqFunctions.js?v=2025-02-28"></script>
    <script>
        jQuery(function($) {
            $("#Export").click(function() {
                $(this).closest("form")
                    .attr("action", "list_exports.php")
                    .attr("target", "_blank");
            });

        });
    </script>
    <style>
        tbody td span {
            display: block;
            padding-right: 12px;
            text-align: right;
        }

        td[data-name="OrderDate"] {
            white-space: nowrap;
        }

        td .sx_svg {
            width: 1.5rem;
            height: 1.5rem;
            fill: var(--basic-color);
        }
    </style>
</head>

<body class="body">
    <header id="header">
        <h2><?= LNG_OrderArchive ?></h2>
    </header>
    <form method="POST" action="index.php?cid=<?php echo $intCustomerID ?>&pg=archive" id="form1" name="form1">
        <div class="flex_end">
            <span><?= LNG_SortBy ?>: </span>
            <select size="1" name="choice">
                <option value="New" <?= ($strChoice === '' || $strChoice === 'New') ? 'selected' : '' ?>><?= LNG_NewOrders ?></option>
                <option value="InProcess" <?= ($strChoice === 'InProcess') ? 'selected' : '' ?>><?= LNG_InProcess ?></option>
                <option value="Completed" <?= ($strChoice === 'Completed') ? 'selected' : '' ?>><?= LNG_Completed ?></option>
                <option value="All" <?= ($strChoice === 'All') ? 'selected' : '' ?>><?= LNG_All ?></option>
            </select><input type="submit" value="&#10095&#10095" name="Go">
        </div>
    </form>

    <section>
        <?php
        if (empty($orders)) {
            echo "<h2>" . LNG_NoRecords . "</h2>";
        } else { ?>
            <div>
                <table border="0" cellspacing="0" cellpadding="2">
                    <tr>
                        <th colspan="2"><?= LNG_ID ?></th>
                        <th><?= LNG_Product ?></th>
                        <th class="align_right"><?= LNG_Total ?></th>
                    </tr>

                    <?php
                    $intSumTotal = 0;
                    foreach ($orders as $order) {
                        $intOrderID = $order['OrderID'];
                        $intOrderDate = $order['OrderDate'];
                        $radioInProcess = $order['InProcess'];
                        $radioCompleted = $order['Completed'];
                        $dateShipDate = $order['ShipDate'];
                        $strShippingMethod = $order['ShippingMethod'];
                        $strShippingTrackURL = $order['ShippingTrackURL'];
                        $intTotal = $order['Total'];
                        $intSumTotal += $intTotal;

                        if ($radioCompleted) {
                            $strStatus = LNG_Completed;
                        } elseif ($radioInProcess) {
                            $strStatus = LNG_InProcess;
                        } else {
                            $strStatus = LNG_NewOrder;
                        }
                    ?>
                        <tr>
                            <td><?= $intOrderID ?></td>
                            <td class="no_wrap">
                                <a title="<?= LNG_VieOrder ?>" href="index.php?oid=<?= $intOrderID ?>">
                                    <svg class="sx_svg">
                                        <use xlink:href="../../imgPG/sx_svg/sx_symbols.svg#sx_book_open"></use>
                                    </svg>
                                </a>
                                <a title="<?= LNG_InvoiceForm ?>" target="_blank" href="index.php?oid=<?= $intOrderID ?>">
                                    <svg class="sx_svg">
                                        <use xlink:href="../../imgPG/sx_svg/sx_symbols.svg#sx_print"></use>
                                    </svg>
                                </a>
                            </td>
                            <td width="100%">
                                <div><b><?= LNG_Date ?>:</b> <?= $intOrderDate ?></div>
                                <div><b><?= LNG_OrderStatus ?>:</b> <?= $strStatus ?></div>
                                <div><b><?= LNG_ShipDate ?>:</b> <?= $dateShipDate ?></div>
                                <div><b><?= LNG_TrackDelivery ?>:</b> <?= $strShippingMethod ?></div>
                            </td>
                            <td class="align_right"><?= number_format($intTotal, 2) ?></td>
                        </tr>
                    <?php } ?>

                    <tr>
                        <td colspan="3" class="align_right no_wrap"><?= LNG_Total ?>:</td>
                        <td class="align_right no_wrap"><?= SX_usedCurrency . " " . number_format($intSumTotal, 2) ?></td>
                    </tr>
                </table>
            </div>
        <?php
        }

        $conn = null; // Close connection
        ?>
    </section>
</body>

</html>