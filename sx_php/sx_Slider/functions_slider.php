<?php
function getSliderRows($int)
{
    $conn = dbconn();
    $sql = "SELECT LinkID, Title, SubTitle, SliderImage, LinkType 
        FROM slider 
        WHERE Publish = True " . str_LanguageAnd . "
        ORDER BY Sorting DESC, SliderID ASC LIMIT " . $int;
    $stmt = $conn->query($sql);
    $rs = $stmt->fetchAll(PDO::FETCH_NUM);
    if (!empty($rs)) {
        return $rs;
    } else {
        return null;
    }
}

function getTextSliderRows($int)
{
    $conn = dbconn();
    $sql = "SELECT t.TextID, t.Title, t.SubTitle, t.SliderImage, 
        a.FirstName, a.LastName, t.Coauthors
        FROM " . sx_TextTableVersion . " AS t 
        LEFT JOIN text_authors AS a ON t.AuthorID = a.AuthorID 
        WHERE t.ShowInSlider = True 
        ORDER BY t.PublishOrder DESC, t.PublishedDate DESC, t.TextID DESC LIMIT " . $int;
    $stmt = $conn->query($sql);
    $rs = $stmt->fetchAll(PDO::FETCH_NUM);
    if (!empty($rs)) {
        return $rs;
    } else {
        return  Null;
    }
}

function getEventsSliderRows($int)
{
    $conn = dbconn();
    $sql = "SELECT EventID, 
        EventTitle" . str_LangNr . " AS EventTitle, 
        EventSubTitle" . str_LangNr . " AS EventSubTitle,
        SliderImage, 
        EventStartDate, StartTime, EndTime, TextID 
        FROM events 
        WHERE Hidden = False AND ShowInSlider = True 
        AND (EventStartDate >= ?)
        AND (EventStartDate <= ?) 
        ORDER BY EventStartDate ASC, StartTime ASC LIMIT " . $int;
    $stmt = $conn->prepare($sql);
    $stmt->execute([date('Y-m-d'), return_Add_To_Date(date('Y-m-d'), 6)]);
    $rs = $stmt->fetchAll(PDO::FETCH_NUM);
    if (!empty($rs)) {
        return $rs;
    } else {
        return  Null;
    }
}

function getProductSliderRows($int)
{
    $conn = dbconn();
    $sql = "SELECT ProductID, 
        ProductName" . str_LangNr . " AS ProductName, 
        ProductSubName" . str_LangNr . " AS ProductSubName, SliderImage, ProductPrice 
        FROM Products 
        WHERE ShowInSlider = True LIMIT " . $int;
    $stmt = $conn->query($sql);
    $rs = $stmt->fetchAll(PDO::FETCH_NUM);
    if (!empty($rs)) {
        return $rs;
    } else {
        return  Null;
    }
}

// Variables are defined in sxConfig.php

if (empty($str_SliderImageFolder)) {
    $str_SliderImageFolder = "images";
}
$strSliderImgPath = "../" . $str_SliderImageFolder . "/";
$aSliderRows = Null;
if ($str_SliderSource == "Texts") {
    $aSliderRows = getTextSliderRows($int_MaxSliders);
} elseif ($str_SliderSource == "Events") {
    $aSliderRows = getEventsSliderRows($int_MaxSliders);
} elseif ($str_SliderSource == "Products") {
    $aSliderRows = getProductSliderRows($int_MaxSliders);
} else {
    $str_SliderSource = "Slider";
    $aSliderRows = getSliderRows($int_MaxSliders);
}

if (!is_array($aSliderRows)) {
    $radio_DefaultSliderPage = False;
} else {
    $iRows = count($aSliderRows);
    $arrSliderImage = "";
    for ($i = 0; $i < $iRows; $i++) {
        $loopImg = $aSliderRows[$i][3];
        if (!empty($loopImg)) {
            if (!empty($arrSliderImage)) {
                $arrSliderImage = $arrSliderImage . ",";
            }
            $arrSliderImage .= "'" . $strSliderImgPath . $loopImg . "'";
        }
    }
    if (!empty($arrSliderImage)) { ?>
        <script>
            sxPreloadSliderImages([<?= $arrSliderImage ?>]);
        </script>
<?php
        /*
        <link rel="stylesheet" href="../sxCss/ps/sx_slider.css?v=2024-02-10">
        <script src="../sxScripts/ps/sx_slider.js?v=2024-02-10"></script>
        */
    } else {
        $radio_DefaultSliderPage = False;
    }
} ?>