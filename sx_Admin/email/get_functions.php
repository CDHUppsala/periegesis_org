<?php
function sx_getConferenceInformation()
{
    $conn = dbconn();
    $sql = "SELECT ConferenceID, Title, StartDate, EndDate 
		FROM conferences 
		WHERE Hidden = 0 
		ORDER BY EndDate DESC ";
    $rs = $conn->query($sql)->fetchAll();
    if ($rs) {
        return  $rs;
    } else {
        return null;
    }
}

function sx_getCoursesInformation()
{
    $conn = dbconn();
    $sql = "SELECT CourseID, CourseTitle, TeacherNames, CourseEndDate 
		FROM courses
		WHERE ShowInSite = True 
		ORDER BY CourseEndDate DESC ";
    $rs = $conn->query($sql)->fetchAll();
    if ($rs) {
        return  $rs;
    } else {
        return null;
    }
}


/**
 * Get the Language code from Language ID
 */
function sx_getLanguageCode($lid)
{
    $conn = dbconn();
    $sql = "SELECT LanguageCode 
		FROM languages  
		WHERE LanguageID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$lid]);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        return  $rs["LanguageCode"];
    } else {
        return null;
    }
}

/**
 * Get the Language ID from Language code
 */
function sx_getLanguageID($code)
{
    $conn = dbconn();
    $sql = "SELECT LanguageID 
		FROM languages  
		WHERE LanguageCode = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$code]);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        return  $rs["LanguageID"];
    } else {
        return null;
    }
}

function getActiveLanguages()
{
    $conn = dbconn();
    $sql = "SELECT LanguageID, LanguageName 
		FROM languages 
		WHERE Hidden = False ";
    $rs = $conn->query($sql)->fetchAll();
    if ($rs) {
        return  $rs;
    } else {
        return null;
    }
}

function sx_getAllLanguages()
{
    $conn = dbconn();
    $sql = "SELECT LanguageID, LanguageName 
			FROM languages ";
    $rs = $conn->query($sql)->fetchAll();
    if ($rs) {
        return  $rs;
    } else {
        return null;
    }
}

function sx_getNewslttersByGroup()
{
    $conn = dbconn();
    $sql = "SELECT GroupID, GroupName 
		FROM newsletter_groups 
		ORDER BY Sorting DESC ";
    $rs = $conn->query($sql)->fetchAll();
    if ($rs) {
        return  $rs;
    } else {
        return null;
    }
}

function sx_getSiteInformation($lid)
{
    $conn = dbconn();
    $sql = "SELECT SiteTitle,
			LogoTitle, LogoSubTitle,
			LogoImage, LogoImageEmail,
			SiteAddress, SitePostalCode, SiteCity, 
			SitePhone, SiteMobile, SiteEmail 
		FROM site_setup 
		WHERE SubOffice = 0 AND (LanguageID = " . $lid . " OR LanguageID = 0) LIMIT 1";
    $rs = $conn->query($sql)->fetch(PDO::FETCH_NUM);
    if ($rs) {
        return  $rs;
    } else {
        return null;
    }
}

function sx_getConference($cid)
{
    $conn = dbconn();
    $radioTemp = False;
    $sql = "SELECT Title, SubTitle, StartDate, EndDate,
    		ImageLinks, PlaceName, PlaceAddress, PlaceCity
		FROM conferences 
		WHERE ConferenceID = ? ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$cid]);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($rs) { //4,5
        $radioTemp = True;
        $strTitle = $rs["Title"];
        $strSubTitle = $rs["SubTitle"];
        $dateStartDate = $rs["StartDate"];
        $dateEndDate = $rs["EndDate"];
        if (!empty($dateEndDate)) {
            $dateStartDate = $dateStartDate . " | " . $dateEndDate;
        }
        $strImgURL = $rs["ImageLinks"];
        if (strpos($strImgURL, "jpg") == 0 && strpos($strImgURL, "svg") == 0 && strpos($strImgURL, "gif") == 0 && strpos($strImgURL, "png") == 0 && strpos($strImgURL, "bmp") == 0) {
            $strImgURL = "";
        }
        if (strpos($strImgURL, ";") > 0) {
            $strImgURL = substr($strImgURL, 0, strpos($strImgURL, ";"));
        }
        $strPlaceName = $rs["PlaceName"];
        $strPlaceAddress = $rs["PlaceAddress"];
        if (!empty($strPlaceAddress)) {
            $strPlaceName = $strPlaceName . ", " . $strPlaceAddress;
        }
        $strPlaceCity = $rs["PlaceCity"];
        if (!empty($strPlaceCity)) {
            $strPlaceName = $strPlaceName . ", " . $strPlaceCity;
        }
    }
    $stmt = null;
    $rs = null;

    $strTextArea = "";
    if ($radioTemp) {
        if (!empty($strImgURL) && !empty($strTitle)) {
            $strTextArea = '<p><img style="width: 400px; height: auto" width="400" alt="' . htmlspecialchars($strTitle) . '" src="' . sx_ROOT_HOST . "/images/" . $strImgURL . '" /></p>';
        }
        $str_CurrentLanguage = sx_DefaultSiteLang;

        $strTextArea .= '<h3><a target="_blank" href="' . sx_ROOT_HOST . "/" . $str_CurrentLanguage . "/conferences.php?confid=" . $cid . '">';
        $strTextArea .= $strTitle . "</a></h3>";
        if (!empty($strSubTitle)) {
            $strTextArea .= "<h4>" . $strSubTitle . "</h4>";
        }
        $strTextArea .= "<p><b>" . $dateStartDate . "</b></p>";
        if (!empty($strPlaceName)) {
            $strTextArea .= '<p>' . $strPlaceName . '</p>';
        }
        return  $strTextArea;
    }
}


function sx_getEvent($eid)
{
    $conn = dbconn();
    $radioTemp = False;
    $sql = "SELECT LanguageID, EventTitle, EventSubTitle, EventStartDate, EventEndDate, StartTime, EndTime, MediaURL, PlaceName, PlaceAddress, PlaceCity 
		FROM events 
		WHERE EventID = ? ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$eid]);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($rs) {
        $radioTemp = True;
        $intLanguageID = $rs["LanguageID"];
        $strEventTitle = $rs["EventTitle"];
        $strSubTitle = $rs["EventSubTitle"];
        $dateEventStartDate = $rs["EventStartDate"];
        $dateEventEndDate = $rs["EventEndDate"];
        if (!empty($dateEventEndDate)) {
            $dateEventStartDate = $dateEventStartDate . " - " . $dateEventEndDate;
        }
        $strEventTime = $rs["StartTime"];
        $strEndTime = $rs["EndTime"];
        if (!empty($strEndTime)) {
            $strEventTime = $strEventTime . " - " . $strEndTime;
        }
        $strImgURL = $rs["MediaURL"];
        if (strpos($strImgURL, "jpg") == 0 && strpos($strImgURL, "svg") == 0 && strpos($strImgURL, "gif") == 0 && strpos($strImgURL, "png") == 0 && strpos($strImgURL, "bmp") == 0) {
            $strImgURL = "";
        }
        if (strpos($strImgURL, ";") > 0) {
            $strImgURL = substr($strImgURL, 0, strpos($strImgURL, ";"));
        }

        $strPlaceName = $rs["PlaceName"];
        $strPlaceAddress = $rs["PlaceAddress"];
        if (!empty($strPlaceAddress)) {
            $strPlaceName = $strPlaceName . ", " . $strPlaceAddress;
        }
        $strPlaceCity = $rs["PlaceCity"];
        if (!empty($strPlaceCity)) {
            $strPlaceName = $strPlaceName . ", " . $strPlaceCity;
        }
    }
    $rs = null;

    $strTextArea = "";
    if ($radioTemp) {
        if (!empty($strImgURL) && !empty($strEventTitle)) {
            $strTextArea .= '<p><img style="width: 100%; height: auto;" alt="' . htmlspecialchars($strEventTitle) . '" src="' . sx_ROOT_HOST . "/images/" . $strImgURL . '" /></p>';
        }

        if (intval($intLanguageID) == 0) {
            $str_LanguageCode = sx_DefaultSiteLang;
        } else {
            $str_LanguageCode = sx_getLanguageCode($intLanguageID);
        }

        $strTextArea .= "<h4>" . $dateEventStartDate . ", " . $strEventTime . "</h4>";
        $strTextArea .= '<h3><a target="_blank" href="' . sx_ROOT_HOST . "/" . $str_LanguageCode . "/events.php?eid=" . $eid . '">';
        $strTextArea .= $strEventTitle . "</a></h3>";
        if ($strSubTitle != "") {
            $strTextArea .= "<h4>" . $strSubTitle . "</h4>";
        }
        if ($strPlaceName != "") {
            $strTextArea .= $strPlaceName;
        }
        return  $strTextArea;
    }
}

function sx_getText($sTbl, $tid)
{
    $conn = dbconn();
    $aResults = null;
    $strBasename = '/texts.php?';
    $strLeftQuery = "tid=";
    if ($sTbl == "news") {
        $sql = " SELECT LanguageID, Title, SubTitle, MediaURL, 
			CONCAT(a.FirstName, ', ', a.LastName) AS AuthorNames
		FROM news
		WHERE TextID = ?
		AND Publish = True ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$tid]);
        $rs = $stmt->fetch(PDO::FETCH_NUM);

        if ($rs) {
            $aResults = $rs;
        } else {
            return null;
        }
        $rs = null;
    } elseif ($sTbl == "texts") {
        $sql = " SELECT t.LanguageID, t.Title, t.SubTitle, 
			t.FirstPageMediaURL,
		    CONCAT(a.FirstName, ', ', a.LastName) AS AuthorNames,
		t.Coauthors
		FROM texts AS t LEFT JOIN text_authors AS a ON t.AuthorID = a.AuthorID
		WHERE t.TextID = ?
		AND t.Publish = True ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$tid]);
        $rs = $stmt->fetch(PDO::FETCH_NUM);

        if ($rs) {
            $aResults = $rs;
        }
        $rs = null;
    } elseif ($sTbl == "articles") {
        $strBasename = '/articles.php?';
        $strLeftQuery = "aid=";
        $sql = " SELECT LanguageID, Title, SubTitle, TopMediaPaths, AuthorName
		FROM articles 
		WHERE ArticleID = ?
		AND Hidden = 0 ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$tid]);
        $rs = $stmt->fetch(PDO::FETCH_NUM);
        if ($rs) {
            $aResults = $rs;
        }
        $rs = null;
    }

    $strTextArea = '';
    $sCoauthors = "";
    if (is_array($aResults)) {
        $iLanguageID = $aResults[0];
        $sTitle = $aResults[1];
        $sSubTitle = $aResults[2];
        $sMediaURL = $aResults[3];
        $sAuthorNames = $aResults[4];
        if ($sTbl == "texts") {
            $sCoauthors = $aResults[5];
        }
        $aResults = null;


        if (!empty($sMediaURL)) {
            if (strpos($sMediaURL, ";") > 0) {
                $sMediaURL = substr($sMediaURL, 0, strpos($sMediaURL, ";"));
            }
            if (sx_check_image_suffix($sMediaURL)) {
                $strTextArea = '<p><img width="100%" height="auto" alt="' . htmlspecialchars($sTitle) . '" src="' . sx_ROOT_HOST . "/images/" . $sMediaURL . '"></p>';
            }
        }
        if (intval($iLanguageID) == 0) {
            $str_LanguageCode = sx_DefaultSiteLang;
        } else {
            $str_LanguageCode = sx_getLanguageCode($iLanguageID);
        }

        $strLeftLink = '<a target="_blank" href="' . sx_ROOT_HOST . "/" . $str_LanguageCode . $strBasename . $strLeftQuery . $tid . '">';

        $strTextArea .= '<h3>' . $strLeftLink . $sTitle . '</a></h3>';

        $sAuthors = $sAuthorNames;
        if (!empty($sAuthors) && !empty($sCoauthors)) {
            $sAuthors .= ", $sCoauthors";
        }
        if (!empty($sAuthors)) {
            $sAuthors = "<i>$sAuthors</i>";
        }
        if (!empty($sSubTitle)) {
            $strTextArea .= "<h4> $sSubTitle </h4>";
        }

        if (!empty($sAuthors)) {
            $strTextArea .= "<p> by $sAuthors </p>";
        }
    }
    return  $strTextArea;
}


function sx_getProduct($pid)
{
    $conn = dbconn();
    $aResults = null;
    $sql = "SELECT ProductID, ProductName, ProductName_2, ProductName_3, 
			ProductSubName, ProductSubName_2, ProductSubName_3, 
			ProductImages, ProductPrice, OfferRate, OfferPrice, PriceNote, 
			ProductShortDesc, ProductShortDesc_2, ProductShortDesc_3
		FROM products 
		WHERE ProductID = " . $pid;
    $rs = $conn->query($sql)->fetch();
    if ($rs) {
        $aResults = $rs;
    }
    $rs = null;
    $strTextArea = "";
    if (is_array($aResults)) {
        $i_ProductID = $aResults[0];
        $strProductName = $aResults[1];
        $strProductName_2 = $aResults[2];
        $strProductName_3 = $aResults[3];
        $strProductSubName = $aResults[4];
        $strProductSubName_2 = $aResults[5];
        $strProductSubName_3 = $aResults[6];
        $strProductImages = $aResults[7];
        $i_ProductPrice = $aResults[8];
        $i_OfferRate = $aResults[9];
        $i_OfferPrice = $aResults[10];
        $strPriceNote = $aResults[11];
        $strProductShortDesc = $aResults[12];
        $strProductShortDesc_2 = $aResults[13];
        $strProductShortDesc_3 = $aResults[14];

        $aResults = null;

        $strProductPrice = "";
        if (intval($i_OfferPrice) > 0) {
            $iTemp = 100 * (1 - ($i_OfferPrice / $i_ProductPrice));
            $strProductPrice = "Intial Price: <strike>" . $i_ProductPrice . "</strike><br>";
            $strProductPrice = $strProductPrice . 'New Price: <span style="font-weight: bold; color:#ff3300">' . $i_OfferPrice . " €</span><br>";
            $strProductPrice = $strProductPrice . "Discount: " . number_format($iTemp, 2, ",", " ") . "%";
        } elseif (intval($i_OfferRate) > 0) {
            $iTemp = ((100 - intval($i_OfferRate)) / 1) * $i_ProductPrice;
            $strProductPrice = "Intial Price: <strike>" . $i_ProductPrice . "</strike><br>";
            $strProductPrice = $strProductPrice . 'New Price: <span style="color:#ff3300">' . number_format($iTemp, 2, ",", " ") . " €</span><br>";
            $strProductPrice = $strProductPrice . "Discount: " . $i_OfferRate . "%";
        } else {
            $strProductPrice = "Price: " . $i_ProductPrice . " €";
        }

        $str_CurrentLanguage = sx_getLanguageCode(1);
        $strProductName = '<a target="_blank" href="' . sx_ROOT_HOST . "/" . $str_CurrentLanguage . "/products.php?pid=" . $pid . '">' . $strProductName . "</a>";
        if (!empty($strProductName_2)) {
            $str_CurrentLanguage = sx_getLanguageCode(2);
            $strProductName_2 = '<a target="_blank" href="' . sx_ROOT_HOST . "/" . $str_CurrentLanguage . "/products.php?pid=" . $pid . '">' . $strProductName_2 . "</a>";
            $strProductName = $strProductName . "<br>" . $strProductName_2;
        }
        if (!empty($strProductName_3)) {
            $str_CurrentLanguage = sx_getLanguageCode(3);
            $strProductName_3 = '<a target="_blank" href="' . sx_ROOT_HOST . "/" . $str_CurrentLanguage . "/products.php?pid=" . $pid . '">' . $strProductName_3 . "</a>";
            $strProductName = $strProductName . "<br>" . $strProductName_3;
        }

        if (!empty($strProductSubName_2)) {
            $strProductSubName = $strProductSubName . "<br>" . $strProductSubName_2;
        }
        if (!empty($strProductSubName_3)) {
            $strProductSubName = $strProductSubName . "<br>" . $strProductSubName_3;
        }

        if (!empty($strProductShortDesc)) {
            $strProductDesc = $strProductShortDesc;
        }
        if (!empty($strProductShortDesc_2)) {
            $strProductDesc = $strProductShortDesc . $strProductShortDesc_2;
        }
        if (!empty($strProductShortDesc_3)) {
            $strProductDesc = $strProductShortDesc . $strProductShortDesc_3;
        }

        if (!empty($strProductImages)) {
            $str_ProductName = "";
            if (!empty($strProductName)) {
                $str_ProductName = htmlspecialchars(str_replace('"', "", $strProductName));
            }
            if (strpos($strProductImages, ";") > 0) {
                $strProductImages = substr($strProductImages, strpos($strProductImages, ";"));
            }
            $strTextArea = '<p><img style="width: 100%; height: auto;" alt="' . $str_ProductName . '" src="' . sx_ROOT_HOST . "/imgProducts/" . $strProductImages . '"></p>';
        }

        $strTextArea .= "<h2>" . $strProductName . "</h2>";
        if (!empty($strProductSubName)) {
            $strTextArea .= "<h3>" . $strProductSubName . "</h3>";
        }
        $strTextArea .= "<p>" . $strProductPrice . "</p>";
        $strTextArea .= $strProductShortDesc;

        return  $strTextArea;
    }
}

function getCustomerDistricts()
{
    $conn = dbconn();
    $sql = " SELECT DISTINCT c.District, d.DistrictName 
		FROM shop_customers AS c 
		INNER JOIN shop_greek_districts AS d 
		ON c.District = d.ConstantDistrictID 
		WHERE c.DenyAccess = False 
		ORDER BY d.DistrictName DESC";
    $rs = $conn->query($sql)->fetchAll();
    if ($rs) {
        return  $rs;
    } else {
        return null;
    }
}

function getCustomerCountries()
{
    $conn = dbconn();
    $sql = " SELECT DISTINCT c.Country, w.CountryGreekName 
		FROM shop_customers AS c 
		INNER JOIN countries AS w 
		ON c.Country = w.ConstantCountryID 
		WHERE c.DenyAccess = False 
		ORDER BY w.CountryGreekName ASC";
    $rs = $conn->query($sql)->fetchAll();
    if ($rs) {
        return  $rs;
    } else {
        return null;
    }
}
