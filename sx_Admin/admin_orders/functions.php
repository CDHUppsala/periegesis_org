<?php

function sx_GetFieldFromAnyTable($tbl, $field, $idName, $idValue) {
    $conn = dbconn();
    try {
        $sql = "SELECT $field FROM $tbl WHERE $idName = :idValue";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idValue', $idValue, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() ?: $idValue;
    } catch (PDOException $e) {
        return $idValue;
    }
}

function sx_GetSiteInformation($lid) {
    $conn = dbconn();
    $sql = "SELECT SiteTitle, LogoImage, LogoImageEmail, LogoImagePrint, SiteAddress, 
            SitePostalCode, SiteCity, SiteCountry, SitePhone, SiteMobile, SiteEmail 
            FROM site_setup WHERE SubOffice = 0 AND (LanguageID = ? OR LanguageID = 0) LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$lid]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function sx_GetCurrentLanguageCode($lid) {
    $conn = dbconn();
    $sql = "SELECT LanguageCode FROM languages WHERE LanguageID = :lid";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':lid', $lid, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn() ?: 0;
}

function sx_GetCurrentLanguageID($code) {
    $conn = dbconn();
    $sql = "SELECT LanguageID FROM languages WHERE LanguageCode = :code";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':code', $code, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn() ?: "";
}

function sx_GetActiveLanguages() {
    $conn = dbconn();
    $sql = "SELECT LanguageID, LanguageName FROM languages WHERE Hidden = FALSE";
    $stmt = $conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_NUM);
}

function sx_GetCustomerDistricts() {
    $conn = dbconn();
    $sql = "SELECT DISTINCT c.District, d.DistrictName FROM shop_customers AS c 
            INNER JOIN shop_greek_districts AS d ON c.District = d.ConstantDistrictID";
    $stmt = $conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_NUM);
}

function sx_GetCustomerCountries() {
    $conn = dbconn();
    $sql = "SELECT DISTINCT c.Country, w.CountryGreekName, w.CountryName FROM shop_customers AS c 
            INNER JOIN countries AS w ON c.Country = w.ConstantCountryID";
    $stmt = $conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_NUM);
}

function sx_RemoveDublicatesAndKey($str, $sep, $move) {
    $arrStr = explode($sep, $str);
    $arrStr = array_unique(array_filter($arrStr));
    if (($key = array_search($move, $arrStr)) !== false) {
        unset($arrStr[$key]);
    }
    return implode($sep, $arrStr);
}

function getDistrictName($id) {
    $name = $id;
    $conn = dbConn();
    $sql = "SELECT DistrictName FROM shop_greek_districts WHERE ConstantDistrictID = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $name = $row["DistrictName"];
    }
    return $name;
}


function getCountryName($id, $name) {
    $countryName = $id;
    $conn = dbConn();
    $sql = "SELECT $name FROM countries WHERE ConstantCountryID = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $countryName = $row[$name];
    }
    return $countryName;
}

function getShippingMethod($id) {
    $shippingMethod = "";
    if ((int)$id > 0) {
        $conn = dbConn();
        $sql = "SELECT ShippingMethod FROM shop_shipping WHERE InUse = True AND ShippingID = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $shippingMethod = $row["ShippingMethod"];
        }
    }
    return $shippingMethod;
}

function getPayMethodName($id) {
    $methodName = "Not Defined";
    if ((int)$id > 0) {
        $conn = dbConn();
        $sql = "SELECT MethodName FROM pay_methods WHERE InUse = True AND PayMethodID = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $methodName = $row["MethodName"];
        }
    }
    return $methodName;
}

function get_Product_Accessory_Name($aid) {
    if ((int)$aid > 0) {
        $conn = dbConn();
        $sql = "SELECT AccessoryName FROM product_accessories WHERE AccessoryID = :aid";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':aid', $aid, PDO::PARAM_INT);
        $stmt->execute();
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $row["AccessoryName"];
        }
    }else{
        return '';
    }
}

function get_Product_Accessory_Price($aid) {
    $accessoryPrice = NULL;
    if ((int)$aid > 0) {
        $conn = dbConn();
        $sql = "SELECT AccessoryPrice FROM product_accessories WHERE AccessoryID = :aid";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':aid', $aid, PDO::PARAM_INT);
        $stmt->execute();
        $accessoryPrice = $stmt->fetch(PDO::FETCH_COLUMN);
    }
    if ($accessoryPrice !== false && is_numeric($accessoryPrice)) {
        return (float) $accessoryPrice; 
    } else {
        return 0;
    }
}
