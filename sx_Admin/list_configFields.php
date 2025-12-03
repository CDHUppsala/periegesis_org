<?php

/**
 * Change initial seting from configuration tables to enable different views and updates
 * Change the content of Updateable fields ($js_UpdateableFields) and Selected Fields ($strSelectedFields)
 * UpdateType is defined manually in the left manu and can be used for all tables:
 *      e.g. list.php?RequestTable=texts&updateMode=yes&updateType=slider&sort=ShowInSlider&ShowImages=Yes 
 *      Here, you can manually redefine witch fields for eny table will be visible and/or updateable
 */

$strUpdateType = null;
$strSearchFieldName = null;

if (isset($_GET["updateType"])) {
    $strUpdateType = $_GET["updateType"];
    if(sx_checkTableAndFieldNames($strUpdateType) == false) {
        $strUpdateType = null;
    }
}
if (isset($_GET["searchFieldName"])) {
    $strSearchFieldName = $_GET["searchFieldName"];
    if(sx_checkTableAndFieldNames($strSearchFieldName) == false) {
        $strSearchFieldName = null;
    }
}

if (!empty($strUpdateType)) {
    if ($request_Table == "texts") {
        $sxSelected = "TextID, LanguageID, GroupID, CategoryID, Title, AuthorID, PublishedDate, ";
        if ($strUpdateType == "published") {
            if ($strSearchFieldName == 'PublishAside') {
                $js_UpdateableFields = '{"Publish":"6","PublishAside":"9","PublishOrder":"13"}';
                $strSelectedFields = $sxSelected . "Publish, PublishAside, PublishOrder";
            } else {
                $js_UpdateableFields = '{"Publish":"6","PublishInFirstPage":"9","PublishOrder":"13"}';
                $strSelectedFields = $sxSelected . "Publish, PublishInFirstPage, PublishOrder";
            }
            $strUpdateTypeTitle = lngUpdatePublishedTexts;
        } elseif ($strUpdateType == "slider") {
            $js_UpdateableFields = '{"Publish":"6","PublishInFirstPage":"9","ShowInSlider":"9"}';
            $strSelectedFields = $sxSelected . "Publish, PublishInFirstPage, ShowInSlider, SliderImage, TopMediaURL";
            $strUpdateTypeTitle = lngUpdateSliders;
        } elseif ($strUpdateType == "images") {
            $js_UpdateableFields = '{"UseAuthorPhoto":"9"}';
            $strSelectedFields = $sxSelected . "UseAuthorPhoto, FirstPageMediaURL, TopMediaURL, RightMediaURL";
            $strUpdateTypeTitle = lngRecordImagesClickToViews;
        }
    } elseif ($request_Table == "text_news" || $request_Table == "texts_blog") {
        $sxSelected = "TextID, GroupID, CategoryID, Title, AuthorID, PublishedDate, ";
        if ($strUpdateType == "published") {
            $js_UpdateableFields = '{"Publish":"8","PublishOrder":"13"}';
            $strSelectedFields = $sxSelected . "Publish, PublishOrder";
            $strUpdateTypeTitle = lngUpdatePublishedTexts;
        } elseif ($strUpdateType == "slider") {
            $js_UpdateableFields = '{"Publish":"6","ShowInSlider":"9"}';
            $strSelectedFields = $sxSelected . "Publish, ShowInSlider, SliderImage ";
            $strUpdateTypeTitle = lngUpdateSliders;
        } elseif ($strUpdateType == "images") {
            $js_UpdateableFields = '{"UseAuthorPhoto":"9"}';
            $strSelectedFields = $sxSelected . "UseAuthorPhoto, FirstPageMediaURL, TopMediaURL, RightMediaURL";
            $strUpdateTypeTitle = lngRecordImagesClickToViews;
        }
    } elseif ($request_Table == "products") {
        $sxSelected = "ProductID, GroupID, CategoryID, ProductCode, ProductName, ProductPrice, ProductGrossPrice, ";
        if ($strUpdateType == "basic") {
            $js_UpdateableFields = '{"ProductPrice":"13","ProductGrossPrice":"13","UnitsInStock":"13","UnitsOnOrder":"13","UseStockControl":"9","ProductWeight":"13","Sorting":"13"}';
            $strSelectedFields = $sxSelected . "UnitsInStock, UnitsOnOrder, UseStockControl, ProductWeight, Sorting";
            $strUpdateTypeTitle = lngUpdateBasic;
        } elseif ($strUpdateType == "new") {
            $js_UpdateableFields = '{"ProductPrice":"13","ProductGrossPrice":"13","ShowAsNew":"9","Sorting":"13"}';
            $strSelectedFields = $sxSelected . "ShowAsNew, Sorting";
            $strUpdateTypeTitle = lngUpdateNew;
        } elseif ($strUpdateType == "offers") {
            $js_UpdateableFields = '{"ProductPrice":"13","ProductGrossPrice":"13","OfferRate":"13","OfferPrice":"13":"ShowAsOffer":"9","Sorting":"13"}';
            $strSelectedFields = $sxSelected . "OfferRate, OfferPrice, ShowAsOffer, Sorting";
            $strUpdateTypeTitle = lngUpdateOffers;
        } elseif ($strUpdateType == "selected") {
            $js_UpdateableFields = '{"ProductPrice":"13","ProductGrossPrice":"13","ShowAsSelected":"9","Sorting":"13"}';
            $strSelectedFields = $sxSelected . "ShowAsSelected, Sorting";
            $strUpdateTypeTitle = lngUpdateSelected;
        } elseif ($strUpdateType == "images") {
            $js_UpdateableFields = '{"ProductPrice":"13","ProductGrossPrice":"13"}';
            $strSelectedFields = "ProductID, GroupID, CategoryID, ProductCode, ProductName, ProductPrice, ProductGrossPrice, ProductImages";
            //$strUpdateTypeTitle = lngProductImagesClickToViews;
        }
    }

    $_SESSION["UpdateableFieldsArray"] = $js_UpdateableFields;
    $_SESSION["SelectedFieldsArray"] = $strSelectedFields;
    $_SESSION["UpdateTypeTitle"] = $strUpdateTypeTitle;
}

if (isset($_SESSION["UpdateableFieldsArray"])) {
    $js_UpdateableFields = $_SESSION["UpdateableFieldsArray"];
    $arrUpdateableFields = json_decode($js_UpdateableFields, true);
    $strSelectedFields = $_SESSION["SelectedFieldsArray"];
    $strUpdateTypeTitle = $_SESSION["UpdateTypeTitle"];
}
