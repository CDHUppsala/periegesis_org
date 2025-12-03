<?php
include __DIR__ . "/functionsLanguage.php";
include __DIR__ . "/login/lockPage.php";
include __DIR__ . "/functionsTableName.php";
include __DIR__ . "/functionsDBConn.php";
include __DIR__ . "/configFunctions.php";
include __DIR__ . "/functionsImages.php";

$radioIncludeBlobs = true;

if (isset($_GET["ShowImages"])) {
    if ($_GET["ShowImages"] == "Yes") {
        $_SESSION["ShowImages"] = true;
    } else {
        $_SESSION["ShowImages"] = false;
    }
} elseif (!isset($_SESSION["ShowImages"])) {
    $_SESSION["ShowImages"] = false;
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

    $sql = "SELECT " . $strTopRecords_1 . " " . $strSelectedFields . " FROM " . $request_Table . " " . $strLimitRecords_1;

    //echo $sql . "<br>";

    $stmt = $conn->query($sql);
    $rs = $stmt->fetch();
    $maxcol = $stmt->columnCount();
    $radioTemp = true;
    if (!$rs) {
        $radioTemp = false;
    }
    // For the export of the entire table
    $arrColumnNames = array();
    for ($i = 0; $i < $maxcol; $i++) {
        $meta = $stmt->getColumnMeta($i);
        $xName = $meta["name"];
        $xType = $meta["native_type"];
        $arrColumnNames[] = $xName;
        $arrFieldNames[$i][0] = $xName;
        $arrFieldNames[$i][1] = $xType;

        if (($xType == "DATE" || $xType == "DATETIME") && $radioTemp) {
            $_SESSION["DateFieldName"] = $xName;
            $_SESSION["MinimalDate"] = $rs[$i];
            $radioTemp = false;
        }
    }
    $_SESSION["ArrFieldNames"] = $arrFieldNames;
    $_SESSION["ArrColumnNames"] = $arrColumnNames;

    $stmt = null;
    $rs = null;
}

if (!empty($_SESSION["ArrFieldNames"])) {
    $arrFieldNames = $_SESSION["ArrFieldNames"];
}
if (!empty($_SESSION["ArrColumnNames"])) {
    $arrColumnNames = $_SESSION["ArrColumnNames"];
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
 * If both above search inputs are empty check for sessions
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
        unset($_SESSION["SearchDateWhere"]);
        unset($_SESSION["SearchDateDisplay"]);
    }
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
        $intPageSize = 100;
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
 * ========================================
 * ========================================
 * CREATES THE BASIC RECORDSET
 * ========================================
 * ========================================
 */

$strSQL = "SELECT " . $strSelectedFields . " FROM " . $request_Table;
if (!empty($search_TextWhere)) {
    $strSQL = $strSQL . " WHERE (" . $search_TextWhere . ")";
    if (!empty($search_DateWhere)) {
        $strSQL = $strSQL . " AND (" . $search_DateWhere . ")";
    }
} elseif (!empty($search_DateWhere)) {
    $strSQL = $strSQL . " WHERE (" . $search_DateWhere . ")";
}

if (!empty($orderByStatement)) {
    $strSQL = $strSQL . " ORDER BY " . $orderByStatement;
}

//echo $strSQL;

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

//define("int_SearchDate", $intSearchDate);
define("int_PageSize", $intPageSize);
define("int_PageCount", $intPageCount);
define("i_CurrentPage", $iCurrentPage);


/**
 * ==================================================
 * PAGE Navigation FUNCTIONS: use the above constants
 * ==================================================
 */

include_once "list_views_functions.php";

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
            echo lngSearchMode;
            ?>
        </h2>
        <div>
            <?php
            if ($_SESSION["ShowImages"]) { ?>
                <a class="button" href="list_views.php?ShowImages=No"><?= lngHideImages ?></a>
            <?php
            } else { ?>
                <a class="button" id="ShowHideImages" href="list_views.php?ShowImages=Yes"><?= lngShowImages ?></a>
            <?php
            }
            $radioTextTable = false;
            if ($request_Table == "texts" || $request_Table == "text_news" || $request_Table == "texts_blog" || $request_Table == "news") {
                $radioTextTable = true;
            }
            ?>
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
    <div class="row">
        <div class="row flex_gap">
            <h3>List only for View</h3>
            <h3><?php sx_getArrowPageNav() ?></h3>
        </div>
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
    </div>

    <section class="list_table">
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
                        for ($i = 0; $i < $maxcol; $i++) {
                            $xName = $arrFieldNames[$i][0];
                            $xType = $arrFieldNames[$i][1];
                            $strAliasName = sx_checkAsName($xName);

                            //Get field names
                            if ($xType != "BLOB" || ($xType == "BLOB" && $radioIncludeBlobs)) {
                                $strSortElement = '<div title="' . lngOrderResultsByThisField . '"><img class="sx_svg" src="images/sx_svg_blue/sx_up_down.svg"></div>';

                                if (isset($_SESSION["Sort"]) && $_SESSION["Sort"] == "desc") {
                                    $strImg = "&#x25BC;";
                                } else {
                                    $strImg = "&#x25B2;";
                                }
                                if ($i == 0) { ?>
                                    <th><?= $strSortElement ?><span><?= $strImg ?></span></th>
                                    <th><?= $strSortElement ?>
                                    <?php
                                } else { ?>
                                    <th><?= $strSortElement ?>
                                    <?php }
                                if ($orderByFieldName == $xName) { ?>
                                        <a title="<?= lngChangeDescendingAscending ?>" class="light" href="list_views.php?orderby=<?= $xName ?>"><?= $strAliasName ?></a>
                                    <?php
                                } else { ?>
                                        <a title="<?= lngOrderByThisField ?>" href="list_views.php?orderby=<?= $xName ?>"><?= $strAliasName ?></a>
                                    <?php
                                } ?>
                                    </th>
                            <?php
                            }
                        } ?>
                </tr>
                <thead>
                <tbody>
                    <?php
                    }
                    if (is_array($aResults)) {
                        $iRows = count($aResults);
                        $iCols = count($aResults[0]);
                        for ($r = 0; $r < $iRows; $r++) {
                            for ($i = 0; $i < $iCols; $i++) {
                                $xName = $arrFieldNames[$i][0];
                                $xType = $arrFieldNames[$i][1];
                                $xValue = $aResults[$r][$i];

                                if ($i == 0) { ?>
                                <td><?= $r ?></td>
                                <td><?= $xValue ?></td>
                                <?php
                                } else {
                                    if ($xType == "LONG") { // 3 Makes searchable the subcategories that are related to long intigers
                                        if (intval($xValue) == 0) {
                                            $xValue = 0;
                                        } ?>
                                    <td><a title="<?= lngShowRecordsFromThisCategoryOnly ?>" href="list_views.php?searchFieldName=<?= $xName ?>&searchFieldValue=<?= $xValue ?>"><?= sx_getRelatedFieldNameForList($xName, $xValue) ?></td>
                                <?php
                                    } elseif ($xType == "SHORT") {
                                        if (intval($xValue) == 0) {
                                            $xValue = 0;
                                        } ?>
                                    <td><?= sx_getRelatedFieldNameForList($xName, $xValue) ?></td>
                                <?php
                                    } elseif ($xType == "DOUBLE" || $xType == "FLOAT" || $xType == "INT24") {
                                        if (intval($xValue) == 0) {
                                            $xValue = 0;
                                        } ?>
                                    <td><?= number_format($xValue, 2) ?></td>
                                <?php
                                    } elseif ($xType == "TIME") {
                                        if (intval($xValue) == 0) {
                                            $xValue = 0;
                                        } ?>
                                    <td><?= $xValue ?></td>
                                <?php
                                    } elseif ($xType == "DATE") {
                                        if (empty($xValue) || DateTime::createFromFormat('Y-m-d', $xValue) === false) {
                                            $xValue = '';
                                        } ?>
                                    <td><a title="Show Records from this Date Only" href="list_views.php?searchFieldName=<?= $xName ?>&searchFieldValue=<?= $xValue ?>"><?= $xValue ?></a></td>
                                    <?php
                                    } elseif ($xType != "BLOB" || ($xType == "BLOB" && $radioIncludeBlobs)) {
                                        if ($xType == "STRING" || $xType == "VAR_STRING") {
                                            $strLinks = "";
                                            if (!empty($xValue)) {
                                                $strLinks = sx_getLinksAndImages($orderByFieldName, $xName, $xValue);
                                            } ?>
                                        <td><?= $strLinks ?></td>
                                    <?php
                                        } else { ?>
                                        <td><?= $xValue ?></td>
                        <?php
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
        <p id="navPagingBG"><?php sx_getArrowNav() ?></p>
    </section>
    <?php
    include __DIR__ . "/errorMsgForClient.php";
    $aResults = NULL;

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
    } ?>
</body>

</html>