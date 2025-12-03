<?php
include PROJECT_PHP . '/sx_config.php';
include PROJECT_PHP_SHOP . '/sx_config_shop.php';
include PROJECT_PHP_SHOP . '/All_Get_Functions.php';

/**
 * Access to this page is allowed:
 * 1.
 *  -   to Signed in Customer ID, hold in the session name CustomerID
 *  -   and to the Last Customer, hold in the session name LastCutosmerID
 *  -   The Order ID in both cases is sent as $_GET 
 * 2.
 *  - to anyone that clicks on a link sent by email and contain the 3 query variables:
 *      - crid=CustomerID, oid=OrderID and vc=ViewCode
 */

$radioPrintOrder = false;
$radioUseViewCode = false;

// Check if customer is Signed In 
$intOrderID = $_GET['orderID'] ?? 0;
$intCustomerID = $_SESSION['CustomerID'] ?? 0;
if ((int)$intCustomerID === 0) {
    // Check session from the Last Customer ID that sent an order 
    $intCustomerID = $_SESSION['LastCustomerID'] ?? 0;
}


if ((int)$intCustomerID > 0 && (int)$intOrderID > 0) {
    $radioPrintOrder = true;
} else {
    // For customers who print the order from codes sent by email
    $intCustomerID = $_GET['crid'] ?? 0;
    $intOrderID = $_GET['oid'] ?? 0;
    $strViewCode = $_GET['vc'] ?? '';

    if (!empty($strViewCode)) {
        $strViewCode = sx_get_sanitized_random_code($strViewCode);
    }

    if ((int)$intCustomerID > 0 && (int)$intOrderID > 0 && !empty($strViewCode)) {
        $radioUseViewCode = true;
        $radioPrintOrder = true;
    }
}

if ($radioPrintOrder) {
    include dirname(__DIR__) . '/customer_OrderPrint.php';
} else {
    write_To_Log("Order Print: Empty CustomerID, OrderID or ViewCode Hack-Attempt!");
    echo "<h2>No Records Found!</h2>";
    exit;
}
