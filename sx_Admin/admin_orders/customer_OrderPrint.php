<?php

// Export option
$strExport = $_GET['export'] ?? '';
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title><?= lngSiteTitle ?></title>
    <?php if (empty($strExport)): ?>
        <style type="text/css" media="print">
            div#print {
                display: none;
            }
        </style>
    <?php endif; ?>
    <style>
        body,
        table {
            font-family: arial;
            font-size: 16px;
            color: #000
        }

        h1 {
            font-size: 1.4em;
        }

        h2 {
            font-size: 1.2em;
        }

        table {
            width: 100%
        }

        th {
            background-color: #ccc;
            padding: 4px;
        }

        td {
            padding: 4px;
            vertical-align: top;
        }

        table#products td {
            border: 1px solid #eee;
        }
    </style>
</head>

<body>
    <?php

    if ($strExport === "" || $strExport === "html"): ?>
        <div style="max-width: 940px; margin: auto">
        <?php
    endif; ?>
        <?php if ($strExport === ""): ?>
            <div id="print" title="<?= LNG_ThisLineWillNotPrint ?>">
                <a href="javascript:print();"><?= LNG_PrintText ?></a> |
                <a target="_top" href="sx_PrintOrder.php?orderID=<?= $intOrderID ?>&export=word"><?= lngSaveInWord ?></a> |
                <a target="_top" href="sx_PrintOrder.php?orderID=<?= $intOrderID ?>&export=html"><?= lngSaveInHTML ?></a>
            </div>
        <?php endif; ?>

        <?php
        $langID = sx_GetCurrentLanguageID(sx_DefaultAdminLang);
        $arrSite = sx_GetSiteInformation($langID);
        $strSiteTitle = $arrSite['SiteTitle'];
        $strLogoImage = $arrSite['LogoImage'];
        $strLogoImageEmail = $arrSite['LogoImageEmail'];
        $strLogoImagePrint = $arrSite['LogoImagePrint'];
        $strSiteAddress = $arrSite['SiteAddress'];
        $strSitePostalCode = $arrSite['SitePostalCode'];
        $strSiteCity = $arrSite['SiteCity'];
        $strSiteCountry = $arrSite['SiteCountry'];
        $strSitePhone = $arrSite['SitePhone'];
        $strSiteMobile = $arrSite['SiteMobile'];
        $strSiteEmail = $arrSite['SiteEmail'];

        $strSiteInfo = "
            $strSiteTitle <br> 
            $strSiteAddress <br>
            $strSitePostalCode $strSiteCity <br>
            $strSiteCountry <br>
            $strSitePhone $strSiteMobile <br>
            $strSiteEmail
            ";

        ?>
        <table>
            <tr>
                <td><a target="_blank" href="<?= sx_ROOT_HOST . '/' . sx_DefaultAdminLang ?>/index.php">
                        <img src="<?= sx_ROOT_HOST ?>/images/<?php echo $strLogoImagePrint ?>"></a></td>
                <td>
                    <p><?php echo $strSiteInfo ?></p>
                </td>
            </tr>
        </table>

        <?php
        $sql = "SELECT OrderDate, CustomerID, DeliveryAddressID, CompanyName, FirstName, LastName, CompanyName, 
                    Email, Address, City, PostalCode, District, State, Country, Phone, Mobile, 
                    PayMethod, PayExpenses, SendInvoice, ShipMethod, ShipCharge, TotalVAT, Total, 
                    SuccessfulPayment, PayAgent, AdvancePayment, PaidAmount, 
                    DiscountShipping, DiscountPrices, DiscountExtra, DiscountTotal, 
                    InProcess, Completed, MessageToSeller 
                FROM shop_orders WHERE OrderID = :orderID";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['orderID' => $intOrderID]);

        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            echo "<h1>No Records Found!</h1>";
            exit();
        } else {
            $intOrderDate = $order['OrderDate'];
            $intCustomerID = $order['CustomerID'];
            $intDeliveryAddressID = $order['DeliveryAddressID'];
            $strCompName = $order['CompanyName'];
            $strFirst = $order['FirstName'];
            $strLast = $order['LastName'];
            $strCompany = $order['CompanyName'];
            $strEmail = $order['Email'];
            $strAddress = $order['Address'];
            $strCity = $order['City'];
            $strPostalCode = $order['PostalCode'];
            $intDistrictID = $order['District'];
            $strState = $order['State'];
            $intCountry = $order['Country'];
            $strPhone = $order['Phone'];
            $strMobile = $order['Mobile'];
            $strPayMethod = $order['PayMethod'];
            $intPayExpenses = (int)($order['PayExpenses'] ?? 0);
            $radioSendInvoice = $order['SendInvoice'];
            $intShipMethod = $order['ShipMethod'];
            $intShippCharge = $order['ShipCharge'];
            $intTax = $order['TotalVAT'];
            $intTotal = $order['Total'];
            $radioSuccessfulPayment = $order['SuccessfulPayment'];
            $intPayAgent = $order['PayAgent'];
            $radioAdvancePayment = $order['AdvancePayment'];
            $intPaidAmount = $order['PaidAmount'];
            $intDiscountShipping = $order['DiscountShipping'];
            $intDiscountPrices = $order['DiscountPrices'];
            $intDiscountExtra = $order['DiscountExtra'];
            $intDiscountTotal = $order['DiscountTotal'];
            $radioInProcess = $order['InProcess'];
            $radioCompleted = $order['Completed'];
            $strMessageToSeller = $order['MessageToSeller'];

            if ($radioInProcess) {
                $strStatus = "In Process";
            } elseif ($radioCompleted) {
                $strStatus = "Completed";
            } else {
                $strStatus = LNG_NewOrder;
            }
        }
        $strLang = sx_DefaultAdminLang;
        $str_Lang = '';

        $str_SendInvoice = $radioSendInvoice ? LNG_Invoice : LNG_Receipt;

        $strCountryWhere = (sx_DefaultAdminLang == "el") ? "CountryGreekName" : "CountryName";
        ?>

        <h1><?= LNG_OrderDetails ?></h1>
        <table>
            <tr>
                <td>

                    <table>
                        <tr>
                            <td><b><?= LNG_OrderID ?>:</b></td>
                            <td><?= $intOrderID ?></td>
                        </tr>
                        <tr>
                            <td><b><?= LNG_CustomerID ?>:</b></td>
                            <td><?= $intCustomerID ?></td>
                        </tr>
                        <tr>
                            <td><b><?= LNG_OrderDate ?>:</b></td>
                            <td><?= $intOrderDate ?></td>
                        </tr>
                        <tr>
                            <td><b><?= LNG_PayMethod ?>:</b></td>
                            <td><?= $strPayMethod ?></td>
                        </tr>
                        <tr>
                            <td><b><?= LNG_PayAgent ?>:</b></td>
                            <td><?= sx_GetFieldFromAnyTable('pay_methods', 'MethodName', 'PayMethodID', $intPayAgent) ?></td>
                        </tr>
                        <tr>
                            <td><b><?= LNG_ShippingMethod ?>:</b></td>
                            <td><?= sx_GetFieldFromAnyTable('shop_shipping', "ShippingMethod{$strLang}", 'ShippingID', $intShipMethod) ?></td>
                        </tr>
                        <tr>
                            <td><b><?= LNG_Status ?>:</b></td>
                            <td><?= $strStatus ?></td>
                        </tr>
                        <?php if ($radioAdvancePayment): ?>
                            <tr>
                                <td class="alignRight"><b><?= LNG_AdvancePayment ?>:</b></td>
                                <td><?= $radioAdvancePayment ?></td>
                            </tr>
                        <?php endif; ?>

                        <?php if ($radioAdvancePayment || $strPayMethod == 'Cash'): ?>
                            <tr>
                                <td class="alignRight"><b><?= LNG_Paid ?>:</b></td>
                                <td><?= number_format($intPaidAmount, 2) ?></td>
                            </tr>
                        <?php endif; ?>

                        <?php if ($strPayMethod == 'Online'): ?>
                            <tr>
                                <td class="alignRight"><b><?= LNG_SuccessfulPayment ?>:</b></td>
                                <td><?= $radioSuccessfulPayment ?></td>
                            </tr>
                        <?php endif; ?>

                        <tr>
                            <td><b><?= LNG_InvoiceForm ?>:</b></td>
                            <td><?= $str_SendInvoice ?></td>
                        </tr>
                    </table>
                </td>
                <?php
                $strAddressType = 'Billing & Delivery Address';
                if ($intDeliveryAddressID > 0) {
                    $strAddressType = 'Delivery Address';
                } ?>
                <td>

                    <table>
                        <tr>
                            <td colspan="2"><b><?= $strAddressType ?>:</b></td>
                        </tr>

                        <?php if (!empty($strCompName)): ?>
                            <tr>
                                <td><b><?= LNG_CompanyName ?>:</b></td>
                                <td><?= $strCompName ?></td>
                            </tr>
                        <?php endif; ?>

                        <tr>
                            <td><b><?= LNG_Name ?>:</b></td>
                            <td><?= $strLast . " " . $strFirst ?></td>
                        </tr>

                        <?php if (!empty($strPhone)): ?>
                            <tr>
                                <td><b><?= LNG_Phone ?>:</b></td>
                                <td><?= $strPhone ?></td>
                            </tr>
                        <?php endif; ?>

                        <?php if (!empty($strMobile)): ?>
                            <tr>
                                <td><b><?= LNG_Mobile ?>:</b></td>
                                <td><?= $strMobile ?></td>
                            </tr>
                        <?php endif; ?>

                        <tr>
                            <td><b><?= LNG_Email ?>:</b></td>
                            <td><?= $strEmail ?></td>
                        </tr>
                        <tr>
                            <td><b><?= LNG_Address ?>:</b></td>
                            <td><?= $strAddress ?></td>
                        </tr>
                        <tr>
                            <td><b><?= LNG_City ?>:</b></td>
                            <td><?= $strCity ?></td>
                        </tr>
                        <tr>
                            <td><b><?= LNG_PostalCode ?>:</b></td>
                            <td><?= $strPostalCode ?></td>
                        </tr>
                        <?php if (!empty($intDistrictID) && (int) $intDistrictID != 0) { ?>
                            <tr>
                                <td><b><?= LNG_District ?>:</b></td>
                                <td><?= getDistrictName($intDistrictID) ?></td>
                            </tr>
                        <?php } ?>
                        <?php if (!empty($strState) && $strState != 'NONE') { ?>
                            <tr>
                                <td><b><?= LNG_State ?>:</b></td>
                                <td><?= $strState ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td><b><?= LNG_Country ?>:</b></td>
                            <td><?= getCountryName($intCountry, $strCountryWhere) ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <?php if (!empty($strMessageToSeller)) { ?>
            <table>
                <tr>
                    <td><b><?= LNG_EmailMsgToSeller ?>:</b></td>
                    <td><?= htmlspecialchars($strMessageToSeller) ?></td>
                </tr>
            </table>
        <?php
        } ?>


        <h1><?= LNG_Products ?></h1>
        <?php

        $aResults = [];
        $sqlText = "SELECT 
                i.ProductID, 
                i.OrderedColor, 
                i.OrderedSize,  
                i.Accessories,  
                i.AccessoryPrices,  
                i.OrderedPrice, 
                i.DiscountRate,  
                i.Quantity, 
                p.ProductName{$str_Lang} AS ProductName, 
                p.ProductImages 
            FROM shop_order_items AS i 
            INNER JOIN products AS p ON i.ProductID = p.ProductID 
            WHERE i.OrderID = :orderID AND i.CustomerID = :customerID 
            ORDER BY i.ProductID";

        $stmt = $conn->prepare($sqlText);
        $stmt->execute(['orderID' => $intOrderID, 'customerID' => $intCustomerID]);

        $aResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($aResults) {
            $iRows = count($aResults);
            $includeSize = false;
            $includeColor = false;
            $intColSpan = 3;

            foreach ($aResults as $row) {
                if (!empty($row['OrderedSize']) && !$includeSize) {
                    $includeSize = true;
                    $intColSpan++;
                }
                if (!empty($row['OrderedColor']) && !$includeColor) {
                    $includeColor = true;
                    $intColSpan++;
                }
                if ($includeSize && $includeColor) break;
            }
        ?>
            <table id="products">
                <tr>
                    <th><?= LNG_ID ?></th>
                    <th align="left"><?= LNG_ProductName ?></th>
                    <?php if ($includeSize): ?>
                        <th><?= LNG_Size ?></th>
                    <?php endif; ?>
                    <?php if ($includeColor): ?>
                        <th><?= LNG_Color ?></th>
                    <?php endif; ?>
                    <th><?= LNG_Quantity ?></th>
                    <th><?= LNG_Price ?></th>
                    <th><?= LNG_Total ?></th>
                </tr>

                <?php
                $intSubTotal = 0;
                $intDiscountPerItem = 0;

                foreach ($aResults as $row) {
                    $intProductID = $row['ProductID'];
                    $strColor = $row['OrderedColor'];
                    $strSize = $row['OrderedSize'];
                    $strAccessories = $row['Accessories'];
                    $intAccessoryPrices = (float) $row['AccessoryPrices'] ?: 0;
                    $intPrice = (float) $row['OrderedPrice'];
                    $intDiscountRate = (float) $row['DiscountRate'];
                    $intQuant = (int) $row['Quantity'];
                    $strProdName = $row['ProductName'];
                    $strProductImages = $row['ProductImages'];
                    $strFirstImage = strtok($strProductImages, ';'); // Get the first image
                    $initialPrice = $intPrice / (1 - ($intDiscountRate / 100));
                    $intItemPrice = $intPrice + $intAccessoryPrices;
                    $intProdTotal = $intQuant * $intItemPrice;
                    $intDiscountPerItem = ($initialPrice - $intPrice) * $intQuant;
                    $intSubTotal += $intProdTotal;
                ?>
                    <tr>
                        <td><?= $intProductID ?></td>
                        <td>
                            <b><?= htmlspecialchars($strProdName) ?></b><br>
                            <?php
                            // Display Accessories
                            if (!empty($strAccessories)) {
                                $aAcc = json_decode($strAccessories);
                                foreach ($aAcc as $iAcc) {
                                    $iAcc = trim($iAcc);
                                    if (!empty($iAcc)) {
                                        $sTemp = get_Product_Accessory_Name($iAcc);
                                        $iTemp = get_Product_Accessory_Price($iAcc);
                                        echo htmlspecialchars($sTemp) . " (" . SX_usedCurrency . number_format($iTemp, 2) . ")<br>";
                                    }
                                }
                            } ?>
                        </td>
                        <?php if ($includeSize): ?>
                            <td><?= htmlspecialchars($strSize) ?></td>
                        <?php endif; ?>
                        <?php if ($includeColor): ?>
                            <td><?= htmlspecialchars($strColor) ?></td>
                        <?php endif; ?>
                        <td align="right"><?= $intQuant ?></td>
                        <td align="right">
                            <?= number_format($initialPrice, 2) ?><br>
                            <?php if ($intDiscountRate > 0): ?>
                                -<?= number_format($intDiscountRate, 2) ?>%<br>
                            <?php endif; ?>
                            <?php if ($intAccessoryPrices > 0): ?>
                                +<?= number_format($intAccessoryPrices, 2) ?><br>
                            <?php endif; ?>
                            <?php if ($intItemPrice !== $initialPrice): ?>
                                <?= number_format($intItemPrice, 2) ?>
                            <?php endif; ?>
                        </td>
                        <td align="right"><?= number_format($intProdTotal, 2) ?></td>
                    </tr>
            <?php }
            }
            ?>

            <tr>
                <td align="right" colspan="<?= $intColSpan ?>"><b><?= LNG_SubTotal ?>:</b></td>
                <td></td>
                <td align="right"><?= number_format($intSubTotal, 2) ?></td>
            </tr>

            <?php if ((int)$intDiscountPrices > 0): ?>
                <tr>
                    <td align="right" colspan="<?= $intColSpan ?>">
                        <b><?= LNG_TotalPriceDiscount ?>:</b>
                    </td>
                    <td align="right">-<?= number_format($intDiscountPrices, 2) ?></td>
                    <td></td>
                </tr>
            <?php endif; ?>

            <?php if ((int)$intDiscountExtra > 0): ?>
                <tr>
                    <td align="right" colspan="<?= $intColSpan ?>">
                        <b><?= LNG_TotalExtraDiscount ?>:</b>
                    </td>
                    <td align="right">-<?= number_format($intDiscountExtra, 2) ?></td>
                    <td align="right">-<?= number_format($intDiscountExtra, 2) ?></td>
                </tr>
            <?php endif; ?>

            <tr>
                <td align="right" colspan="<?= $intColSpan ?>"><b><?= LNG_ShippingCharge ?>:</b></td>
                <?php if ((int)$intDiscountShipping > 0): ?>
                    <td align="right">-<?= number_format($intDiscountShipping, 2) ?></td>
                <?php else: ?>
                    <td></td>
                <?php endif; ?>
                <td align="right"><?= number_format($intShippCharge, 2) ?></td>
            </tr>

            <tr>
                <td align="right" colspan="<?= $intColSpan ?>"><b><?= LNG_PayMethodExpenses ?>:</b></td>
                <td></td>
                <td align="right"><?= number_format($intPayExpenses, 2) ?></td>
            </tr>

            <?php if ((int)$intDiscountTotal > 0): ?>
                <tr>
                    <td colspan="<?= $intColSpan ?>" align="right"><b><?= LNG_TotalDiscount ?>:</b></td>
                    <td align="right">-<?= number_format($intDiscountTotal, 2) ?></td>
                    <td></td>
                </tr>
            <?php endif; ?>

            <tr>
                <td align="right" colspan="<?= $intColSpan ?>"><b><?= LNG_Total ?>:</b></td>
                <td align="right" colspan="2"><?= SX_usedCurrency . number_format($intTotal, 2) ?></td>
            </tr>

            <tr>
                <td align="right" colspan="<?= $intColSpan ?>"><b><?= LNG_VAT ?>:</b></td>
                <td align="right" colspan="2"><?= SX_usedCurrency . number_format($intTax, 2) ?></td>
            </tr>
            <tr>
                <td align="right" colspan="<?= $intColSpan ?>"><b><?= LNG_TotalExceptVAT ?>:</b></td>
                <td align="right" colspan="2"><?= SX_usedCurrency . number_format((float)($intTotal - $intTax), 2) ?></td>
            </tr>

            </table>


            <?php
            if ($intDeliveryAddressID > 0) {
                $sql = "SELECT AllowCashPayment, AllowCustomerAdvancedPayment, CustomerAdvancePaymentRate, 
                        UseGrossPrices, CompanyName, FirstName, LastName, Address, City, PostalCode, District, 
                        State, Country, Email, Phone, Mobile, TaxNumber, TaxAuthorityID
                    FROM shop_customers
                    WHERE CustomerID = ? ";

                $stmt = $conn->prepare($sql);
                $stmt->execute([$intCustomerID]);

                $arrBilling = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$arrBilling) {
                    echo "<h1>No Billing Information is Found!</h1>";
                    exit();
                } else {

                    $strAllowCashPayment = $arrBilling['AllowCashPayment'];
                    $strAllowCustomerAdvancedPayment = $arrBilling['AllowCustomerAdvancedPayment'];
                    $strCustomerAdvancePaymentRate = $arrBilling['CustomerAdvancePaymentRate'];
                    $strUseGrossPrices = $arrBilling['UseGrossPrices'];
                    $strCompanyName = $arrBilling['CompanyName'];
                    $strFirstName = $arrBilling['FirstName'];
                    $strLastName = $arrBilling['LastName'];
                    $strAddress = $arrBilling['Address'];
                    $strCity = $arrBilling['City'];
                    $strPostalCode = $arrBilling['PostalCode'];
                    $intDistrictID = $arrBilling['District'];
                    $strState = $arrBilling['State'];
                    $intCountry = $arrBilling['Country'];
                    $strEmail = $arrBilling['Email'];
                    $strPhone = $arrBilling['Phone'];
                    $strMobile = $arrBilling['Mobile'];
                    $strTaxNumber = $arrBilling['TaxNumber'];
                    $strTaxAuthorityID = $arrBilling['TaxAuthorityID'];
                } ?>
                <h1>Billing Address</h1>

                <table>
                    <tr>
                        <td><b><?= LNG_CustomerID ?>:</b></td>
                        <td><?= $intCustomerID ?></td>
                    </tr>

                    <?php if (!empty($strCompanyName)): ?>
                        <tr>
                            <td><b><?= LNG_CompanyName ?>:</b></td>
                            <td><?= $strCompanyName ?></td>
                        </tr>
                    <?php endif; ?>

                    <tr>
                        <td><b><?= LNG_Name ?>:</b></td>
                        <td><?= $strLastName . " " . $strFirstName ?></td>
                    </tr>

                    <?php if (!empty($strPhone)): ?>
                        <tr>
                            <td><b><?= LNG_Phone ?>:</b></td>
                            <td><?= $strPhone ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if (!empty($strMobile)): ?>
                        <tr>
                            <td><b><?= LNG_Mobile ?>:</b></td>
                            <td><?= $strMobile ?></td>
                        </tr>
                    <?php endif; ?>

                    <tr>
                        <td><b><?= LNG_Email ?>:</b></td>
                        <td><?= $strEmail ?></td>
                    </tr>
                    <tr>
                        <td><b><?= LNG_Address ?>:</b></td>
                        <td><?= $strAddress ?></td>
                    </tr>
                    <tr>
                        <td><b><?= LNG_City ?>:</b></td>
                        <td><?= $strCity ?></td>
                    </tr>
                    <tr>
                        <td><b><?= LNG_PostalCode ?>:</b></td>
                        <td><?= $strPostalCode ?></td>
                    </tr>
                    <?php if (!empty($intDistrictID) && (int) $intDistrictID != 0) { ?>
                        <tr>
                            <td><b><?= LNG_District ?>:</b></td>
                            <td><?= getDistrictName($intDistrictID) ?></td>
                        </tr>
                    <?php } ?>
                    <?php if (!empty($strState) && $strState != 'NONE') { ?>
                        <tr>
                            <td><b><?= LNG_State ?>:</b></td>
                            <td><?= $strState ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td><b><?= LNG_Country ?>:</b></td>
                        <td><?= getCountryName($intCountry, $strCountryWhere) ?></td>
                    </tr>
                </table>

            <?php
            }
            if (empty($strExport) || $strExport === "html") { ?>
        </div>
    <?php
            } ?>





    <p style="text-align: center;">
        <?= LNG_PrintedDate . ": " . date('Y-m-d') ?><br>
        <?= LNG_FromWebPage . ": <b>" . lngSiteTitle . "</b>" ?><br>
    </p>

</body>

</html>
<?php
// Handle export options
if ($strExport === "word") {
    header("Content-Type: application/vnd.ms-word");
    header("Content-Disposition: attachment; filename=" . sx_HOST . "_OrderID_" . $intCustomerID . "_" . $intOrderID . "_" . date('Y-m-d') . ".doc");
}
if ($strExport === "html") {
    header("Content-Type: text/html");
    header("Content-Disposition: attachment; filename=" . sx_HOST . "_OrderID_" . $intCustomerID . "_" . $intOrderID . "_" . date('Y-m-d') . ".html");
}
?>