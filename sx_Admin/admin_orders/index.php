<?php
include dirname(__DIR__) . "/functionsLanguage.php";
include dirname(__DIR__) . "/login/lockPage.php";
include dirname(__DIR__) . "/functionsTableName.php";
include dirname(__DIR__) . "/functionsDBConn.php";
include dirname(__DIR__) . "/configFunctions.php";
include dirname(__DIR__) . "/functionsImages.php";
include __DIR__ . "/functions.php";

const SX_usedCurrency = '€';


$intCustomerID = $_GET['cid'] ?? 0;
$intOrderID = $_GET['oid'] ?? 0;
$intViewOrderID = $_GET['viewID'] ?? 0;
if ($intCustomerID > 0) {
    include __DIR__ . "/customer_OrderArchive.php";
} elseif ($intOrderID > 0) {
    include __DIR__ . "/customer_OrderPrint.php";
} elseif ($intViewOrderID > 0) {
    include __DIR__ . "/view.php";
} else {
    echo 'No records found';
}
