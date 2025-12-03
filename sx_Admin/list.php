<?php
include __DIR__ . "/functionsLanguage.php";
include __DIR__ . "/login/lockPage.php";
include __DIR__ . "/functionsTableName.php";
include __DIR__ . "/functionsDBConn.php";
include __DIR__ . "/configFunctions.php";
include __DIR__ . "/functionsImages.php";

if (isset($_GET["updateMode"])) {
    $_SESSION["UpdateMode"] = true;
} elseif (isset($_GET["searchMode"])) {
    $_SESSION["UpdateMode"] = false;
} elseif (!isset($_SESSION["UpdateMode"])) {
    $_SESSION["UpdateMode"] = false;
}

if (isset($_GET["ShowImages"])) {
    if ($_GET["ShowImages"] == "Yes") {
        $_SESSION["ShowImages"] = true;
    } else {
        $_SESSION["ShowImages"] = false;
    }
} elseif (!isset($_SESSION["ShowImages"])) {
    $_SESSION["ShowImages"] = false;
}

$strUpdateTypeTitle = "";
if ($_SESSION["UpdateMode"]) {
    $strUpdateTypeTitle = lngUpdateGeneral;
}

/**
 * ========================================
 * The Use of the next include:
 * You can change initial setings from configuration tables regarding
 *      - Fields selected to be visible in the list
 *      - Updateabale fields
 * So, you can  enable different types of Views and Updates
 * addapted to specific tables (e.g. for Texts or Products)
 * ========================================
 */

include __DIR__ . "/list_configFields.php";

/**
 * ========================================
 * The Use of the next include:
 *      Pursue Multiple Updates from the List of records
 *      Reload the this page after update
 * ========================================
 */

include __DIR__ . '/list_multipleUpdates.php';

/**
 * ========================================
 * Get UPDATEABLE Fields from configuration tables
 * and Create a WHERE string hold in session
 * ========================================
 */

$strUpdateAbleWhere = "";
if ($_SESSION["UpdateMode"] && !empty($arrUpdateableFields)) {

    foreach ($arrUpdateableFields as $key => $value) {
        $intValue = "";
        if ($value == 6) {
            $intValue = true;
        }
        if ($value == 7) {
            $intValue = false;
        }
        if ($value == 8) {
            $intValue = "BothValues";
        }
        if ($intValue === true || $intValue === false) {
            if (empty($strUpdateAbleWhere)) {
                $strUpdateAbleWhere = " AND (" . $key . " = " . $intValue . " ";
            } else {
                $strUpdateAbleWhere .= " OR " . $key . " = " . $intValue . " ";
            }
        } elseif ($intValue == "BothValues") {
            if (empty($strUpdateAbleWhere)) {
                $strUpdateAbleWhere = " AND (" . $key . " IS NOT NULL ";
            } else {
                $strUpdateAbleWhere .= " OR " . $key . " IS NOT NULL";
            }
        }
    }
    if (!empty($strUpdateAbleWhere)) {
        $strUpdateAbleWhere .= ")";
    }
}



/**
 * GET THE TABLE FIELDS AND OTHER TABLE VARIABLES
 * Obs! Do that only once, when a new table is requested,
 * and hold information in sessions.
 * So, check if a new table is requested!
 */

if (!empty($_GET["RequestTable"])) {
    $maxcol = 0;
    $arrFieldNames = [];

    //$_SESSION["PrimaryKeyName"] = sx_getPrimaryKeyName_isc($request_Table);
    $_SESSION["PrimaryKeyName"] = sx_getPrimaryKeyName($request_Table);

    $sql = "SELECT " . $strTopRecords_1 . " " . $strSelectedFields . " FROM " . $request_Table . " " . $strLimitRecords_1;

    //echo $sql ."<br>";
    //exit();

    $stmt = $conn->query($sql);
    $rs = $stmt->fetch();
    $maxcol = $stmt->columnCount();
    $radioTemp = true;
    if (!$rs) {
        $radioTemp = false;
    }
    for ($i = 0; $i < $maxcol; $i++) {
        $meta = $stmt->getColumnMeta($i);
        $xName = $meta["name"];
        $xType = $meta["native_type"];
        $arrFieldNames[$i][0] = $xName;
        $arrFieldNames[$i][1] = $xType;

        if (($xType == "DATE" || $xType == "DATETIME") && $radioTemp) {
            $_SESSION["DateFieldName"] = $xName;
            $_SESSION["MinimalDate"] = $rs[$i];
            $radioTemp = false;
        }
    }
    $_SESSION["ArrFieldNames"] = $arrFieldNames;
    $stmt = null;
    $rs = null;
}

if (!empty($_SESSION["ArrFieldNames"])) {
    $arrFieldNames = $_SESSION["ArrFieldNames"];
}

/**
 * DEFINE SEARCH SESSIONS for Text and Date Fields
 */

$strSearchText = "";
$search_TextWhere = "";
if (!empty($_POST["SearchText"])) {
    $strSearchTextDisplay = sx_clean_input(sx_SQLSafe(trim($_POST["SearchText"])));
    $strSearchText = strtoupper($strSearchTextDisplay);

    $maxcol = 0;
    if (is_array($arrFieldNames)) {
        $maxcol = count($arrFieldNames);
    }
    if (strpos($strSearchText, '.') == 0 && intval($strSearchText) > 0) {
        $search_TextWhere = $_SESSION["PrimaryKeyName"] . " = " . intval($strSearchText);
        $_SESSION["SearchTextWhere"] = $search_TextWhere;
        $_SESSION["SearchTextDisplay"] = $strSearchTextDisplay;
    } elseif (!empty($strSearchText) && floor($maxcol) > 0) {
        $sx = 0; //To start searching without "or"
        for ($i = 0; $i < $maxcol; $i++) {
            $xName = $arrFieldNames[$i][0];
            $xType = $arrFieldNames[$i][1];
            if ($xType == "VAR_STRING" || $xType == "BLOB" || $xType == "TEXT") {
                if ($sx == 0) {
                    $search_TextWhere = " UPPER(" . $xName . ") like '%" . $strSearchText . "%'";
                    $sx = 1;
                } else {
                    $search_TextWhere = $search_TextWhere . " OR UPPER(" . $xName . ") like '%" . $strSearchText . "%'";
                }
            }
        }
        $_SESSION["SearchTextWhere"] = $search_TextWhere;
        $_SESSION["SearchTextDisplay"] = $strSearchTextDisplay;
    }
    //Clear previous date session, if any, when only text search is submited
    if (!empty($_SESSION["SearchDateWhere"])) {
        unset($_SESSION["SearchDateWhere"]);
        unset($_SESSION["SearchDateDisplay"]);
    }
}

/**
 * The $_GET is used to search dates directly fron links in content.php page
 * To avoid the use of $_REQUEST
 */
$intSearchDate = 0;
$search_DateWhere = "";
if (!empty($_POST["SearchDate"]) || !empty($_GET["SearchDate"])) {
    if (!empty($_POST["SearchDate"])) {
        $intSearchDate = intval($_POST["SearchDate"]);
    }
    if (!empty($_GET["SearchDate"])) {
        $intSearchDate = intval($_GET["SearchDate"]);
    }

    if ($intSearchDate > 0 && !empty($_SESSION["DateFieldName"])) {
        if ($intSearchDate <= 12) {
            //Recall function
            $countDate = sx_AddToDate(date('Y-m-d'), -$intSearchDate, "months");
            $search_DateWhere = $_SESSION["DateFieldName"] . " >= " . $sDateSymbol . $countDate . $sDateSymbol . " ";
        } elseif ($intSearchDate == (date('Y') + 10)) {
            $search_DateWhere = $_SESSION["DateFieldName"] . " >= " . $sDateSymbol . date('y-m-d') . $sDateSymbol . " ";
        } else {
            $search_DateWhere = $_SESSION["DateFieldName"] . " >= " . $sDateSymbol . $intSearchDate . "-01-01" . $sDateSymbol . "
				AND " . $_SESSION["DateFieldName"] . " <= " . $sDateSymbol . $intSearchDate . "-12-31" . $sDateSymbol . " ";
        }
        $_SESSION["SearchDateWhere"] = $search_DateWhere;
        $_SESSION["SearchDateDisplay"] = $intSearchDate;
        //Clear previous text session, if any, when only date serach is submited
        if (empty($_POST["SearchText"])) {
            unset($_SESSION["SearchTextWhere"]);
            unset($_SESSION["SearchTextDisplay"]);
        }
    } else {
        $search_DateWhere = "";
        unset($_SESSION["SearchDateWhere"]);
        unset($_SESSION["SearchDateDisplay"]);
    }
}
/**
 * If both above search inputs are empty
 */
if (empty($_POST["SearchText"]) && empty($_POST["SearchDate"]) && empty($_GET["SearchDate"])) {
    if (empty($_GET["RequestTable"])) {
        if (isset($_SESSION["SearchTextWhere"])) {
            $search_TextWhere = $_SESSION["SearchTextWhere"];
        }
        if (isset($_SESSION["SearchDateWhere"])) {
            $search_DateWhere = $_SESSION["SearchDateWhere"];
        }
    } else { //When the Table is new or has been reloaded - Clean sessions
        unset($_SESSION["SearchTextWhere"]);
        unset($_SESSION["SearchTextDisplay"]);
        // Dynamic restrictions of passed dates in text tables
        if (($request_Table == "texts" || $request_Table == "text_news" || $request_Table == "texts_blog") && sx_radioUseLastPublishedMonths) {
            $search_DateWhere = " PublishedDate >= " . $sDateSymbol . sx_AddToDate(date('Y-m-d'), -sx_LastPublishedMonths, "months") . $sDateSymbol;
            $_SESSION["SearchDateWhere"] = $search_DateWhere;
            $_SESSION["SearchDateDisplay"] = sx_LastPublishedMonths;
        } else {
            unset($_SESSION["SearchDateWhere"]);
            unset($_SESSION["SearchDateDisplay"]);
        }
    }
}

/**
 * Define sessions for search in subcategories
 */

if (isset($_GET["searchFieldName"]) && isset($_GET["searchFieldValue"])) {
    $tempName = sx_SQLSafe($_GET["searchFieldName"]);
    $tempValue = $_GET["searchFieldValue"];
    if (sx_IsDate($tempValue)) {
        $tempValue = "'" . $tempValue . "'";
    } elseif (intval($tempValue) > 0) {
        $tempValue = intval($tempValue);
    } elseif (sx_checkTableAndFieldNames($tempValue)) {
        $tempValue = "'" . $tempValue . "' ";
    } else {
        header("Location: main.php?msg=No+way+home");
        exit;
    }
    if (sx_checkTableAndFieldNames($tempName)) {
        $searchFieldNameWhere = " AND " . $tempName . " = " . $tempValue;
        $_SESSION["SearchFieldNameWhere"] = $searchFieldNameWhere;
    } else {
        /**
         * Hmmm, You are in real trouble!
         * Someone logged in as administrator and manipulates predefined queries
         */
        header("Location: main.php?msg=No+way+home!");
        exit;
    }
    /**
     * There is no meaning to sort the selected field, 
     * so, sorting it means to stops searching by that field
     */
} elseif (isset($_SESSION["SearchFieldNameWhere"]) && !isset($_GET["RequestTable"])) {
    $searchFieldNameWhere = $_SESSION["SearchFieldNameWhere"];
} else {
    $searchFieldNameWhere = null;
    unset($_SESSION["SearchFieldNameWhere"]);
}

/**
 * Define Ordering sessions from fields in the Table Headers
 * Check the name of fileds/columns
 */
if (isset($_GET["orderby"])) {
    $orderByFieldName = $_GET["orderby"];
    if (sx_checkTableAndFieldNames($orderByFieldName) == false) {
        header("Location: main.php?msg=No+way+home");
    }
    if (!isset($_SESSION["OrderByFieldName"]) || $orderByFieldName != $_SESSION["OrderByFieldName"]) {
        $_SESSION["Sort"] = "desc";
    } else { //Same field
        if ($_SESSION["Sort"] == "asc") {
            $_SESSION["Sort"] = "desc";
        } else {
            $_SESSION["Sort"] = "asc";
        }
    }
    $orderByStatement = $orderByFieldName . " " . $_SESSION["Sort"];
    /**
     * To add order by field from initial configuration tables
     */
    if (!empty($str_OrderByField) && strpos($str_OrderByField, $orderByFieldName) === false) {
        $orderByStatement .= ", " . $str_OrderByField;
    }
    $_SESSION["OrderByStatement"] = $orderByStatement;

    //To display the sorting field and returns to the 1st sorting page
    $_SESSION["OrderByFieldName"] = $orderByFieldName;
    $_SESSION["Page"] = 1;
} elseif (isset($_SESSION["OrderByStatement"]) && empty($_GET["RequestTable"])) {
    $orderByStatement = $_SESSION["OrderByStatement"];
    $orderByFieldName = $_SESSION["OrderByFieldName"];
} else {
    //From the sx_config_tables Table
    if (!empty($str_OrderByField)) {
        //Get the first ordering field in case more than one are defined
        if (strpos($str_OrderByField, ",") > 0) {
            $arrOrderByField = explode(",", $str_OrderByField);
            $strFirstField = trim($arrOrderByField[0]);
        } else {
            $strFirstField = $str_OrderByField;
        }
        $orderByStatement = $str_OrderByField;
        $_SESSION["OrderByStatement"] = $orderByStatement;
        if (strpos($strFirstField, " desc") > 0) {
            $_SESSION["Sort"] = "desc";
        } else {
            $_SESSION["Sort"] = "asc";
        }
        //Get the pure name of the sorting field for comparison
        $_SESSION["OrderByFieldName"] = trim(str_replace(" desc", "", $strFirstField));
        $_SESSION["OrderByFieldName"] = trim(str_replace(" asc", "", $_SESSION["OrderByFieldName"]));
        $orderByFieldName = $_SESSION["OrderByFieldName"];
    } else { //Clean sessions
        unset($_SESSION["OrderByStatement"]);
        unset($_SESSION["Sort"]);
        unset($_SESSION["OrderByFieldName"]);
        $orderByStatement = "";
        $orderByFieldName = "";
    }
}

/**
 * Include Search from the Content Page for any Field Value in any Table
 * Comment if not used as it can be dangerous!
 */

include __DIR__ . "/list_searchFromContent.php";


/**
 * ============================================
 * DEFINE PAGE SPECIFIC SESSIONS
 * ============================================
 */

if (isset($_POST["PageSize"])) {
    $intPageSize = intval($_POST["PageSize"]);
    $_SESSION["intPageSize"] = $intPageSize;
} else {
    if (isset($_SESSION["intPageSize"])) {
        $intPageSize = $_SESSION["intPageSize"];
    } else {
        $intPageSize = 50;
    }
}


/**
 * Define the current page for pagination, requested or default
 * It must always be > 0
 */

$radioRequestPage = false;
if (isset($_GET["page"])) {
    $iRequestPage = intval($_GET["page"]);
    $radioRequestPage = true;
} elseif (isset($_POST["page"])) {
    $iRequestPage = intval($_POST["page"]);
    $radioRequestPage = true;
}

if ($radioRequestPage) {
    $iCurrentPage = $iRequestPage;
    if ($iCurrentPage < 1) {
        $iCurrentPage = 1;
    }
    $_SESSION["Page"] = $iCurrentPage;
} elseif (isset($_SESSION["Page"]) && empty($_GET["RequestTable"]) && empty($_GET["searchForm"])) {
    $iCurrentPage = $_SESSION["Page"];
} else {
    $iCurrentPage = 1;
}


/**
 * Just in unpropable casen - or Views with no PK
 */
if (!isset($_SESSION["PrimaryKeyName"]) || empty($_SESSION["PrimaryKeyName"])) {
    header("Location: main.php?clear=yes&msg=No+PK");
    exit();
} else {
    $strPK = $_SESSION["PrimaryKeyName"];
}

/**
 * ========================================
 * ========================================
 * CREATES THE BASIC RECORDSET
 * ========================================
 * ========================================
 */

$strFirstWhere = "";
if ($radio_TablesWithLoginAdminID && intval($intLoginUserLevel) > 1) {
    $strFirstWhere = " (LoginAdminID = " . $intLoginAdminID . " OR LoginAdminID = 0) ";
    if (!empty($search_DateWhere)) {
        $search_DateWhere = " AND " . $search_DateWhere;
    }
} elseif (empty($search_DateWhere)) {
    $strFirstWhere = $strPK . " > 0 ";
}

$strSQL = "SELECT " . $strSelectedFields . " FROM " . $request_Table . "
	WHERE " . $strFirstWhere . $search_DateWhere . $strUpdateAbleWhere . $searchFieldNameWhere . $initialFieldNameWhere . $greaterFNameWhere;
if (!empty($search_TextWhere)) {
    $strSQL = $strSQL . " AND (" . $search_TextWhere . ")";
}
if (!empty($orderByStatement)) {
    $strSQL = $strSQL . " ORDER BY " . $orderByStatement;
}
/**
 * Count the total number of records
 */
$intRecordCount = sx_getRecordCount($strSQL);

$intPageCount = ceil($intRecordCount / $intPageSize);
if ($intPageCount < $intRecordCount / $intPageSize) {
    $intPageCount++;
}

if ($intPageCount > 1) {
    if ($iCurrentPage > $intPageCount) {
        $iCurrentPage = $intPageCount;
    }
    $iStartRecord = ($intPageSize * $iCurrentPage) - $intPageSize;
    $strSQL = $strSQL . " LIMIT " . $iStartRecord . "," . $intPageSize;
}

$_SESSION["Page"] = $iCurrentPage;

/*
echo $strSQL;
exit();
*/
$stmt = $conn->prepare($strSQL);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_NUM);
$aResults = null;
if ($rows) :
    $aResults = $rows;
endif;
$stmt = null;
$rows = null;

/**
 * Constant variables to be used in functions below
 */

define("int_SearchDate", $intSearchDate);
define("int_PageSize", $intPageSize);
define("int_PageCount", $intPageCount);
define("i_CurrentPage", $iCurrentPage);


/**
 * ==================================================
 * PAGE Navigation FUNCTIONS: use the above constants
 * ==================================================
 */

include_once "list_navFunctions.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SXCMS List of Records</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
    <script src="<?php echo sx_ADMIN_DEV ?>js/jsFunctions.js"></script>
    <script src="js/jq/jquery.min.js"></script>
    <script src="<?php echo sx_ADMIN_DEV ?>js/jqFunctions.js?v=4"></script>
    <script>
        jQuery(function($) {
            $("#Export").click(function() {
                $(this).closest("form")
                    .attr("action", "list_exports.php")
                    .attr("target", "_blank");
            });

        });
    </script>
</head>

<body class="body">

    <!--
===========================================================
	Main Header Links
===========================================================
-->
    <header id="header">
        <h2><?= lngTable . ": " . strtoupper($request_Table) . "<br>" . lngListOfRecords . ": <span>" . $intRecordCount . "</span>" ?>
            <?php
            if ($_SESSION["UpdateMode"]) {
                echo lngUpdateableMode;
            } else {
                echo lngSearchMode;
            } ?>
        </h2>
        <div>
            <?php
            if (!empty($js_UpdateableFields)) { ?>
                <?php
                if ($_SESSION["UpdateMode"]) { ?>
                    <a class="button" href="list.php?searchMode=yes"><?= lngSearchMode ?></a>
                <?php
                } else { ?>
                    <a class="button" href="list.php?updateMode=yes"><?= lngUpdateableMode ?></a>
                <?php
                } ?>
            <?php
            }
            if ($_SESSION["ShowImages"]) { ?>
                <a class="button" href="list.php?ShowImages=No"><?= lngHideImages ?></a>
            <?php
            } else { ?>
                <a class="button" id="ShowHideImages" href="list.php?ShowImages=Yes"><?= lngShowImages ?></a>
            <?php
            }
            $radioTextTable = false;
            if ($request_Table == "texts" || $request_Table == "text_news" || $request_Table == "texts_blog" || $request_Table == "news") {
                $radioTextTable = true;
            }
            include_once "list_links_to_sitemaps.php";
            ?>
        </div>
        <div>
            <button class="button jqHelpButton" data-id="helpSearch">HELP</button>
        </div>

    </header>

    <!--
===========================================================
	Page Navigation
===========================================================
-->

    <?php
    $intSearchDateDisplay = 0;
    if (isset($_SESSION["SearchDateDisplay"]) && intval($_SESSION["SearchDateDisplay"]) > 0) {
        $intSearchDateDisplay = $_SESSION["SearchDateDisplay"];
    }
    $strSearchTextDisplay = "";
    if (isset($_SESSION["SearchTextDisplay"])) {
        $strSearchTextDisplay = $_SESSION["SearchTextDisplay"];
    } ?>
    <div id="navBG">
        <?php
        /**
         * Make searchable all visible table fields that are related to other tables
         * The include file uses the following variables
         * @$strSelectedFields: visible fields for the current table
         * @$searchFieldNameWhere: Field names for current search, if any
         */

        include __DIR__ . "/list_searchRelatedFields.php";

        ?>
        <div class="row">
            <div>
                <?php sx_getPageAndSearchForm() ?>
            </div>
            <?php
            if (!empty($strSearchTextDisplay) || intval($intSearchDateDisplay) > 0) { ?>
                <div>
                    <b><?= lngResults ?>:</b>
                    <?php
                    if (!empty($strSearchTextDisplay)) { ?>
                        <?= lngSearchText . ": <i>" . $strSearchTextDisplay ?></i><br>
                    <?php
                    }
                    if (intval($intSearchDateDisplay) > 0) {
                        if ($intSearchDateDisplay <= 12) {
                            echo lngSearchPeriod . ": <i>" . lngLast . " " . $intSearchDateDisplay . " " . lngMonths . "</i>";
                        } elseif ($intSearchDateDisplay > date('Y')) {
                            echo lngSearchPeriod . ": <i>" . lngFrom . " " . lngComingDates . "</i>";
                        } else {
                            echo lngSearchPeriod . ": <i>" . lngFrom . " " . $intSearchDateDisplay . "</i>";
                        }
                    } ?>
                </div>
            <?php
            } ?>
        </div>
    </div>
    <div id="navPagingTop">
        <div class="row flex_align_center">
            <div class="row flex_justify_start">
                <?php
                if (!in_array($request_Table, $arr_NotAddableTables)) { ?>
                    <h3><a href="add.php"><?= lngAddANewRecord ?></a></h3>
                <?php
                } else { ?>
                    <h3>This Table List is only for View</h3>
                <?php
                } ?>
                <h3><?php sx_getArrowPageNav() ?></h3>
                <?php
                if (!empty($strUpdateTypeTitle)) { ?>
                    <h3><?= $strUpdateTypeTitle ?></h3>
                <?php
                } ?>
            </div>
            <?php
            if (!$_SESSION["UpdateMode"]) { ?>
                <div class="row flex_gap">
                    <div>
                        <div class="text_small">Copy/Print/Export Visible Table Rows</div>
                        <div class="row flex_align_center">
                            <input type="button" value="To Clipboard" class="button jq_CopyToClipboard" data-id="TableList">
                            <input type="button" value="To PDF" class="button jq_PrintDivElement" data-id="TableList">
                            <input type="button" value="To Excel" class="button jq_ExportTableIntoExcel" data-id="TableList">
                        </div>
                    </div>
                    <form method="POST" name="ExportTable" action="">
                        <input type="hidden" name="HiddenDBTable" value="<?= $request_Table ?>" />
                        <div class="text_small">Export All Table Rows as:</div>
                        <div class="row flex_align_center">
                            <div>
                                <span><input type="radio" name="ExportType" value="csv" checked>csv</span>
                                <span><input type="radio" name="ExportType" value="xml">xml</span>
                                <span><input type="radio" name="ExportType" value="json">json</span>
                            </div>
                            <input class="button" type="submit" value="Export" id="Export" name="Export">
                        </div>
                    </form>
                </div>
            <?php
            } ?>
        </div>
    </div>

    <section class="list_table">
        <form method="POST" name="multipleUpdate" action="list.php?strMultipleUpdate=yes">
            <input type="hidden" name="PKName" value="<?= $strPK ?>">
            <table id="TableList" class="jqTableList">
                <thead>
                    <tr>
                        <?php
                        /**
                         * TABLE HEADERS
                         */
                        if (is_array($arrFieldNames)) {
                            $maxcol = count($arrFieldNames);
                            $arrUsedUpdateableFields = array();
                            $arrUsedUpdateableFieldTypes = array();
                            for ($i = 0; $i < $maxcol; $i++) {
                                $xName = $arrFieldNames[$i][0];
                                if (sx_getUpdateableFieldType($xName) != 50) {
                                    $xType = $arrFieldNames[$i][1];
                                    $strAliasName = sx_checkAsName($xName);

                                    //Get field names
                                    if ($xType != "BLOB") {
                                        $strSortElement = '<div title="' . lngOrderResultsByThisField . '"><img class="sx_svg" src="images/sx_svg_blue/sx_up_down.svg"></div>';

                                        if ($_SESSION["UpdateMode"]) {
                                            if (!empty($arrUpdateableFields[$xName])) {
                                                $arrUsedUpdateableFields[] = $xName;
                                                $arrUsedUpdateableFieldTypes[] = $xType;
                                                $strSortElement = "";
                                            }
                                        }
                                        if (isset($_SESSION["Sort"]) && $_SESSION["Sort"] == "desc") {
                                            $strImg = "&#x25BC;";
                                        } else {
                                            $strImg = "&#x25B2;";
                                        }
                                        if ($i == 0) { ?>
                                            <th colspan="2"><?= $strSortElement ?><span><?= $strImg ?></span>
                                            <?php } else { ?>
                                            <th><?= $strSortElement ?>
                                            <?php }
                                        if ($orderByFieldName == $xName) { ?>
                                                <a title="<?= lngChangeDescendingAscending ?>" class="light" href="list.php?orderby=<?= $xName ?>"><?= $strAliasName ?></a>
                                            <?php
                                        } else { ?>
                                                <a title="<?= lngOrderByThisField ?>" href="list.php?orderby=<?= $xName ?>"><?= $strAliasName ?></a>
                                            <?php
                                        } ?>
                                            </th>
                                <?php
                                    }
                                }
                            } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        }

                        /**
                         * ===========================================================
                         * CREATES RECORD FIELDS
                         * ===========================================================
                         * BLOB,DATE,LONG,SHORT,TIMESTAMP,TINY,VAR_STRING
                         * ======= All
                         * BIT,BLOB,DATE,DATETIME,DOUBLE,FLOAT,GEOMETRY,
                         * INT24,LONG,LONGLONG,NEWDECIMAL,SHORT,
                         * STRING,TIME,TIMESTAMP,TINY,VAR_STRING,YEAR
                         * ======== Numeric
                         * DOUBLE,FLOAT,LONG,LONGLONG, SHORT
                         */
                        if (is_array($aResults)) {
                            $iRows = count($aResults);
                            $iCols = count($aResults[0]);
                            for ($r = 0; $r < $iRows; $r++) {
                                for ($i = 0; $i < $iCols; $i++) {
                                    $xName = $arrFieldNames[$i][0];
                                    if (sx_getUpdateableFieldType($xName) != 50) {
                                        $xType = $arrFieldNames[$i][1];
                                        $xValue = $aResults[$r][$i];
                                        if ($xName == $strPK) {
                                            $intPK = $xValue;
                    ?>
                                    <tr>
                                        <td>
                                            <?php if (!in_array($request_Table, $arr_NotDeleteableTables)) { ?>
                                                <a title="<?= lngDeleteRecord ?>" href="delete.php?strIDName=<?= $strPK ?>&strIDValue=<?= $intPK ?>">
                                                    <img class="sx_svg_bg reverse" src="images/sx_svg/sx_clear.svg" height="24"></a>
                                            <?php }
                                            if (in_array($request_Table, $arr_NewsLetterTables)) { ?>
                                                <a title="Newsletters" target="_blank" href="email/default.php?tbl=<?= $request_Table ?>&cid=<?= $intPK ?>">
                                                    <img class="sx_svg_bg" src="images/sx_svg/sx_mail_open.svg" height="24"></a>
                                            <?php }
                                            if (!in_array($request_Table, $arr_NotCopyableTables)) { ?>
                                                <a title="<?= lngCopyRecord ?>" href="copy.php?strIDName=<?= $strPK ?>&strIDValue=<?= $intPK ?>">
                                                    <img class="sx_svg_bg" src="images/sx_svg/sx_plus_bold.svg"></a>
                                            <?php } ?>
                                            <a title="<?= lngViewRecord ?>" href="view.php?strIDName=<?= $strPK ?>&strIDValue=<?= $intPK ?>">
                                                <img class="sx_svg_bg" src="images/sx_svg/sx_search.svg" height="24"></a>
                                            <?php if (!in_array($request_Table, $arr_NotEditableTables)) { ?>
                                                <a title="<?= lngEditRecord ?>" href="edit.php?strIDName=<?= $strPK ?>&strIDValue=<?= $intPK ?>">
                                                    <img class="sx_svg_bg" src="images/sx_svg/sx_pencil.svg" height="24"></a>
                                            <?php } ?>
                                            <input type="hidden" name="PKValue[]" value="<?= $intPK ?>">
                                        </td>
                                    <?php
                                        }
                                        if ($i == 0) { ?>
                                        <td><?= $xValue ?></td>
                                        <?php
                                        } else {
                                            if ($xType == "LONG") { // 3 Makes searchable the subcategories that are related to long intigers
                                                if (intval($xValue) == 0) {
                                                    $xValue = 0;
                                                } ?>
                                            <td><a title="<?= lngShowRecordsFromThisCategoryOnly ?>" href="list.php?searchFieldName=<?= $xName ?>&searchFieldValue=<?= $xValue ?>"><?= sx_getRelatedFieldNameForList($xName, $xValue) ?></td>
                                            <?php
                                            } elseif ($xType == "SHORT") { // 2 Can be updateable
                                                if (intval($xValue) == 0) {
                                                    $xValue = 0;
                                                }
                                                if (@$arrUpdateableFields[$xName] == 13 && $_SESSION["UpdateMode"]) {
                                                    $strRName = $xName . "[]"; ?>
                                                <td>
                                                    <input maxlength="4" title="<?= lngMaxNumber ?>: 9999" style="text-align: right" size="5" type="text" value="<?= $xValue ?>" name="<?= $strRName ?>" onChange="validateZeroNumber(this)">
                                                </td>
                                            <?php
                                                } else {
                                            ?>
                                                <td><?= sx_getRelatedFieldNameForList($xName, $xValue) ?></td>
                                            <?php
                                                }
                                            } elseif ($xType == "DOUBLE" || $xType == "FLOAT" || $xType == "INT24") { //Can be updateable
                                                if (intval($xValue) == 0) {
                                                    $xValue = 0;
                                                }
                                                if (@$arrUpdateableFields[$xName] == 13 && $_SESSION["UpdateMode"]) {
                                                    $strRName = $xName . "[]"; ?>
                                                <td>
                                                    <input style="text-align: right" size="6" type="text" value="<?= $xValue ?>" name="<?= $strRName ?>" onChange="IsAllNumeric(this)">
                                                </td>
                                            <?php
                                                } else {
                                            ?>
                                                <td><?= number_format($xValue, 2) ?></td>
                                            <?php
                                                }
                                            } elseif ($xType == "TIME") {  //Can be updateable
                                                if (intval($xValue) == 0) {
                                                    $xValue = 0;
                                                }
                                                if (@$arrUpdateableFields[$xName] == "TIME" && $_SESSION["UpdateMode"]) {
                                                    $strRName = $xName . "[]"; ?>
                                                <td>
                                                    <input size="18" type="time" value="<?= $xValue ?>" name="<?= $strRName ?>">
                                                </td>
                                            <?php
                                                } else {
                                            ?>
                                                <td><?= $xValue ?></td>
                                            <?php
                                                }
                                            } elseif ($xType == "DATE") {  //Can be updateable
                                                if (empty($xValue) || DateTime::createFromFormat('Y-m-d', $xValue) === false) {
                                                    $xValue = '';
                                                }
                                                if (@$arrUpdateableFields[$xName] == "DATE" && $_SESSION["UpdateMode"]) {
                                                    $strRName = $xName . "[]"; ?>
                                                <td>
                                                    <div class="row flex_align_center">
                                                        <a title="Show Records from this Date Only" href="list.php?searchFieldName=<?= $xName ?>&searchFieldValue=<?= $xValue ?>">[i]</a>
                                                        <input size="18" type="date" value="<?= $xValue ?>" name="<?= $strRName ?>">
                                                    </div>
                                                </td>
                                            <?php
                                                } else { ?>
                                                <td><a title="Show Records from this Date Only" href="list.php?searchFieldName=<?= $xName ?>&searchFieldValue=<?= $xValue ?>"><?= $xValue ?></a></td>
                                            <?php
                                                }
                                            } elseif ($xType != "BLOB") { //Excludes Memos Fields
                                                if ($xType == "STRING" || $xType == "VAR_STRING") {
                                                    $strLinks = "";
                                                    if (!empty($xValue)) {
                                                        $strLinks = sx_getLinksAndImages($orderByFieldName, $xName, $xValue);
                                                    } ?>
                                                <td><?= $strLinks ?></td>
                                            <?php
                                                    //Get updateable YES/NO fields
                                                } elseif (($xType == "TINY") && ($_SESSION["UpdateMode"] && @$arrUpdateableFields[$xName] > 0)) {
                                                    $changeColor = ' class="bgUpdateables"';
                                                    if ($xValue) {
                                                        $strCheckBox = "checked";
                                                        $strHiddenValue = "Yes";
                                                    } else {
                                                        $strCheckBox = "";
                                                        $strHiddenValue = "No";
                                                    }
                                                    //$strRName = $strPK."[]".$intPK."[]".$xName;
                                                    $strRName = $xName . "[]"; ?>
                                                <td <?= $changeColor ?>>
                                                    <input type="checkbox" <?= $strCheckBox ?> name="box<?= $intPK . "_" . $i ?>" value="Yes" onchange="sxChangeRadioValue(this,'radio<?= $intPK . "_" . $i ?>')">
                                                    <input type="hidden" value="<?= $strHiddenValue ?>" checked name="<?= $strRName ?>" id="radio<?= $intPK . "_" . $i ?>">
                                                </td>
                                            <?php
                                                } else {
                                            ?>
                                                <td><?= $xValue ?></td>
                            <?php
                                                }
                                            }
                                        }
                                    }
                                } ?>
                                    </tr>
                            <?php
                            }
                        } ?>
                </tbody>
            </table>
            <hr>
            <div class="row">
                <div id="navPagingBG"><?php sx_getArrowNav() ?></div>
                <?php if ($_SESSION["UpdateMode"]) { ?>
                    <div><input class="button" type="submit" value="Update" name="UpdateList"></div>
                    <div>
                        <input type="hidden" name="UsedUpdateableFields" value="<?= implode(",", $arrUsedUpdateableFields) ?>">
                        <input type="hidden" name="UsedUpdateableFieldTypes" value="<?= implode(",", $arrUsedUpdateableFieldTypes) ?>">
                    </div>
                <?php } ?>
            </div>
        </form>
    </section>
    <?php
    include __DIR__ . "/sxHelpFiles/helpSearch.php";
    include __DIR__ . "/errorMsgForClient.php";
    ?>
    <div id="imgPreview"><img src=""></div>
    <?php
    /**
     * The variable $radio_ShowHideImages is defined in functionsImages.php
     * and redifined as GLOBAL in a functions that tests the existens of images
     * in the List. If NOT, hide the Tab for showing/hiding images
     */
    if (isset($radio_ShowHideImages) && $radio_ShowHideImages === false) { ?>
        <script>
            $sx('#ShowHideImages').css('display', 'none');
        </script>
    <?php
    }

    ?>

</body>

</html>