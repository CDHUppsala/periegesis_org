<?php
/*
    ========================================================
    1. Functions FOR PAGINATION OF TEXTS
Is used to open
IN texts.php to create a Navigation list with Links to Requested Archived TEXTS with Paging
-    Requested Texts from the Lowest Existing level of the 3 levels of clasification of texts
-    Requested Texts from Themes, Authors and Dates or deta periods
IN default.php to create Introductions to to Requested Archived TEXTS with Paging:
-    Texts Published in First Page or (depending on the design and Text Table used)
-    The Recent N Texts From the First Classification Level (Groups) of Texts
    ========================================================
*/

/**
 * Page navigation by form (selct the page number)
 * @param mixed $path
 * @param mixed $intCurrentPage
 * @param mixed $intPageCount
 * @return void
 */
function sx_getPageNavigation_ByForm($path, $intCurrentPage, $intPageCount)
{ ?>
    <form action="<?= $path ?>" name="goToWhateverPage" method="post">
        <label><?= lngPage . ": " ?></label>
        <select aria-label="Select Page" size="1" name="page">
            <?php for ($i = 1; $i <= $intPageCount; $i++) {
                $strSelected = "";
                if (intval($i) == intval($intCurrentPage)) {
                    $strSelected = "selected ";
                } ?>
                <option <?= $strSelected ?>value="<?= $i ?>"><?= $i ?></option>
            <?php
            } ?>
        </select>
        <input type="submit" name="goTopage" value="&#10095;&#10095;">
    </form>
<?php
}

/**
 * Page navigation by arrows (next-previous page)
 * @param mixed $path
 * @param mixed $intCurrentPage
 * @param mixed $intPageCount
 * @return void : a HTNL list with links to pages.
 */
function sx_getPageNavigation_ByArrows($path, $intCurrentPage, $intPageCount): void
{ ?>
    <ul>
        <li><a title="<?= lngFirstPage ?>" href="<?= $path . "page=1" ?>">&#10094;&#10094;&#10094;&#10094;</a></li>
        <?php if ($intCurrentPage > 1) { ?>
            <li><a title="<?= lngPreviousPage ?>" href="<?= $path . "page=" . ($intCurrentPage - 1) ?>">&#10094;&#10094;</a></li>
        <?php } else { ?>
            <li><span>&#10094;&#10094;</span></li>
        <?php } ?>
        <li><?= $intCurrentPage ?> / <?= $intPageCount ?></li>
        <?php if ($intCurrentPage < $intPageCount) { ?>
            <li><a title="<?= lngNextPage ?>" href="<?= $path . "page=" . ($intCurrentPage + 1) ?>">&#10095;&#10095;</a></li>
        <?php } else { ?>
            <li><span>&#10095;&#10095;</span></li>
        <?php } ?>
        <li><a title="<?= lngLastPage ?>" href="<?= $path . "page=" . $intPageCount ?>">&#10095;&#10095;&#10095;&#10095;</a></li>
    </ul>
<?php
}

/*
    =====================================================================================
    GLOBAL Navigation to Requested Archived TEXTS with Paging
    Is used to open:
    IN index.php
    - Texts Published in First Page
    IN texts.php
    - Requested Texts from the Lowest Existing level of the 3 levels of clasification
    - Requested Texts from Themes, Authors and Calendar
    =====================================================================================
*/

/**
 * Global Pagination variables
 * - shold be an even number, mainly when texts are presented in 2 columns
 * - the variable $i_MaxArticlesPerPage is for Archive Navigations
 * - the variable $i_MaxFirstPageArticles (see bellow) is for pagination from the First Page
 */

$iPageSize = 8;
if (intval($i_MaxArticlesPerPage) > 0) {
    $iPageSize = $i_MaxArticlesPerPage;
}


//## Determins, with $radio_FirstArchiveRequest from sx_config.php, if archive pages will open;
$radioArchivesNavigation = false;
$strFirstHeaderTitle = $str_ArchivesListTitle;
$strSecondHeaderTitle = "";
$strPublishWhere = "";
$strByPublishOrder = "";

$strArchiveQuery = "";

/**
 * The variable "page" can be requested 
 * both as _GET and _POST variable
 */
$i_RequestPage = 0;
if (!empty(return_Get_or_Post_Request("page"))) {
    $i_RequestPage = (int) return_Get_or_Post_Request("page");
}

/*
    SQL String for Searching and Pagination in Text Archives
    ====================================================
    The variable $radio_FirstArchiveRequest is defined in sx_config.php
      and checks if ther is a First Request for Archive Texts
    - A First Request (by Group ID, Theme ID, Author ID or Date) gives $radio_FirstArchiveRequest = True in sx_config.php
    The code bellow creates the SQL String according to Variables defined in sx_config.php.
    - The SQL Sring and related Variables are hold in sessions, wich are used as long as 
        1 the Request includes the parameter "page" (i_RequestPage > 0), which
            sets the variable $radioArchivesNavigation = true
        2 A New First Request is made
    ====================================================
*/

if ($radio_FirstArchiveRequest) {
    if ((int)($int_Year) > 0 && (int)($int_Month) > 0) {
        $radioArchivesNavigation = true;
        if ($str_TextsByCalenderTitle == "") {
            $str_TextsByCalenderTitle = lngTextCalendar;
        }
        $str_Month = $int_Month;
        $str_NextMonth = intval($int_Month) + 1;
        $int_NextYear = $int_Year;

        if (strlen($int_Month) == 1) {
            $str_Month = "0" . $int_Month;
            if (strlen($str_NextMonth) == 1) {
                $str_NextMonth = "0" . $str_NextMonth;
            }
        } elseif (intval($int_Month) == 12) {
            $int_NextYear = intval($int_Year) + 1;
            $str_NextMonth = "01";
        }
        $strPublishWhere = " t.PublishedDate >= '" . $int_Year . "-" . $str_Month . "-01' AND t.PublishedDate < '" . $int_NextYear . "-" . $str_NextMonth . "-01'";
        $strSecondHeaderTitle = lngMonthsArticles . " " . lng_MonthNamesGen[$int_Month - 1] . " " . $int_Year;
        $strArchiveQuery = "month=" . $int_Month . "&year=" . $int_Year;
        if (intval($int_Week) > 0) {
            $aReturn = return_Week_Start_End_Dates($int_Week, $int_Year);
            $dateStart = $aReturn[0];
            $dateEnd = $aReturn[1];
            $strPublishWhere = " t.PublishedDate >= '" . ($dateStart) . "' AND t.PublishedDate <= '" . ($dateEnd) . "'";
            $strSecondHeaderTitle = lngWeeksArticles . " " . $int_Week . ", " . lng_MonthNames[$int_Month - 1] . " " . $int_Year;
            $strArchiveQuery = "week=" . $int_Week . "&" . $strArchiveQuery;
        }

        if (isset($int_Day) && intval($int_Day) > 0) {
            $str_Day = strval($int_Day);
            if (strlen($str_Day) == 1) {
                $str_Day = "0" . $str_Day;
            }
            $strPublishWhere = " t.PublishedDate = '" . $int_Year . "-" . $str_Month . "-" . $str_Day . "'";
            $strSecondHeaderTitle = lngDaysAritcles . " " . $int_Day . " " . lng_MonthNamesGen[$int_Month - 1] . " " . $int_Year;
            $strArchiveQuery = "day=" . $int_Day . "&" . $strArchiveQuery;
        }
    } elseif (intval($int_ThemeID) > 0) {
        $radioArchivesNavigation = true;
        if (empty($str_TextsByThemesTitle)) {
            $str_TextsByThemesTitle = lngThemes;
        }
        $strFirstHeaderTitle = $str_TextsByThemesTitle;
        $strSecondHeaderTitle = $strThemeName;
        $strPublishWhere = " t.ThemeID = " . $int_ThemeID;
        $strArchiveQuery = "themeID=" . $int_ThemeID;
    } elseif (intval($int_AuthorID) > 0) {
        $radioArchivesNavigation = true;
        $strFirstHeaderTitle = lngAuthor;
        $strSecondHeaderTitle = $strAuthorName;
        $strPublishWhere = " t.AuthorID = " . $int_AuthorID;
        $strArchiveQuery = "authorID=" . $int_AuthorID;
    } elseif (intval($int_SubCatID) > 0) {
        $radioArchivesNavigation = true;
        $strSecondHeaderTitle = $str_GroupName . "/ " . $str_CategoryName . "/ " . $str_SubCategoryName;
        $strPublishWhere = " t.SubCategoryID = " . $int_SubCatID;
        $strArchiveQuery = "scid = " . $int_SubCatID;
    } elseif (intval($int_CatID) > 0) {
        $radioArchivesNavigation = true;
        $strSecondHeaderTitle = $str_GroupName . "/ " . $str_CategoryName;
        $strPublishWhere = " t.CategoryID = " . $int_CatID;
        $strArchiveQuery = "cid=" . $int_CatID;
    } elseif (intval($int_GroupID) > 0) {
        $radioArchivesNavigation = true;
        $strSecondHeaderTitle = $str_GroupName;
        $strPublishWhere = " t.GroupID = " . $int_GroupID;
        $strArchiveQuery = "gid=" . $int_GroupID;
    }
} elseif (intval($i_RequestPage) == 0 || !isset($_SESSION["ArchivesNavigation"]) || $_SESSION["ArchivesNavigation"] == false) {
    /**
     * So, there is No First Archive Request, and we are not in Archive Navigation state
     * In this case, set the default values for the first page of the site
     * - Publish in first page only articles that are checked for that (if this is activated by adminstrators)
     * - Recet the number of articles per page (in case it is different from that for archives).
     */
    if (sx_TextTableVersion == "texts" && $radio_ShowByPublishInFirstPage) {
        $strPublishWhere = " t.PublishInFirstPage = True ";
    }
    if (intval($i_MaxFirstPageArticles) > 0) {
        $iPageSize = $i_MaxFirstPageArticles;
    }
}

/**
 * $iPageSize shold be an even number, mainly when texts are presented in 2 columns
 */
if ($iPageSize % 2 > 0) {
    $iPageSize += 1;
}
/**
 * Use Publish Ordering in all requests.
 * This gives the administrators the possibility 
 *   to order the appearence of texts within their catagory
 */
$strByPublishOrder = " t.PublishOrder DESC,";

/**
 * If not empty, the variable is used for additional WHERE conditions
 */
if (!empty($strPublishWhere)) {
    $strPublishWhere .= " AND ";
}

/*
    ====================================================
    Check if PAGE Naviagation is active and define its source
        - in First Page or 
        - Archive Navigation
    The parameter "page" (i_RequestPage) comes from
        1. index.php, as paging betwen articles published in First Page, without Archive Navigation
        2. texts.php, from different Archive Navigations and from all texts opened by them
    If ARCHIVE Navigation is active (case 2), get session variables, 
        else continue with the navigation of First Page
    ====================================================
*/

if (intval($i_RequestPage) > 0) {
    $iCurrentPage = intval($i_RequestPage);
    if ($iCurrentPage < 1) {
        $iCurrentPage = 1;
    }
    if (isset($_SESSION["ArchivesNavigation"]) && $_SESSION["ArchivesNavigation"]) {
        $radioArchivesNavigation = true;
        $strFirstHeaderTitle = $_SESSION["FirstHeaderTitle"];
        $strSecondHeaderTitle = $_SESSION["SecondHeaderTitle"];
        $strPublishWhere = $_SESSION["PublishWhere"];
        $date_SearchByDate = $_SESSION["SearchByDate"];
        //$strArchiveQuery = $_SESSION["ArchiveQuery"];
    }
} else {
    $iCurrentPage = 1;
    unset($_SESSION["ArchivesNavigation"]);
    unset($_SESSION["FirstHeaderTitle"]);
    unset($_SESSION["SecondHeaderTitle"]);
    unset($_SESSION["PublishWhere"]);
    unset($_SESSION["SearchByDate"]);
    unset($_SESSION["ArchiveQuery"]);
    unset($_SESSION["RecordCount"]);
}

/*
    strPageURL is included in Text Links
    $strPageNavigationURL is included in Page Navigation (in Forms and Page Links), both for First Page and Archive Paging
    ============================================================
*/
$strPageURL = "texts.php?";

if ($radioArchivesNavigation) {
    $strPageNavigationURL = $strPageURL;
    $_SESSION["ArchivesNavigation"] = true;
    $_SESSION["FirstHeaderTitle"] = $strFirstHeaderTitle;
    $_SESSION["SecondHeaderTitle"] = $strSecondHeaderTitle;
    $_SESSION["PublishWhere"] = $strPublishWhere;
    $_SESSION["SearchByDate"] = $date_SearchByDate;
    $_SESSION["ArchiveQuery"] = $strArchiveQuery;
} else {
    $strPageNavigationURL = "index.php?";
}

/*
    ============================================================
    Get Text Rows for requested Archive Paginations
    - In index.php - Gives introductions for texts published in First Page - AND
    - In texts.php - Gives both introductions for Requested Articles (published in main column), 
        and a Navigation List to articles (published in aside column)
    Only for MySQL
    ============================================================
*/

/**
 * Returns the total number of records for a request
 * @param string $sql : the sql statement
 * @return int : Total number of records
 */
function sx_get_text_pagination_count($sql)
{
    $conn = dbconn();
    $arSql = explode("ORDER BY", $sql);
    $arrSql = explode("FROM ", $arSql[0]);
    $strSql = "SELECT count(*) FROM " . $arrSql[1];
    //echo $strSql ."<hr>";
    $stmt = $conn->query($strSql);
    $iCount = $stmt->fetchColumn();
    if ($iCount) {
        return $iCount;
    } else {
        return 0;
    }
}

/**
 * Returns an array of text records from the requested page (LIMIT left right)
 * @param string $sql : the sql statement
 * @param int $iLeft : the Left LIMIT
 * @param int $iRight : the Right LIMIT
 * @return array|null
 */
function sx_get_text_pagination($sql, $iLeft = 0, $iRight = 0)
{
    $conn = dbconn();
    global $iPageSize, $iCurrentPage;
    if (!isset($_SESSION["RecordCount"]) || intval($_SESSION["RecordCount"]) == 0) {
        $iRecordCount = intval(sx_get_text_pagination_count($sql));
        $_SESSION["RecordCount"] = $iRecordCount;
    } else {
        $iRecordCount = $_SESSION["RecordCount"];
    }
    $GLOBALS['iRecordCount'] = $iRecordCount;
    $iPageCount = ceil($iRecordCount / $iPageSize);
    $GLOBALS['iPageCount'] = $iPageCount;

    if ($iPageCount < $iRecordCount / $iPageSize) {
        $iPageCount = $iPageCount + 1;
    }
    if ($iPageCount > 1) {
        if ($iCurrentPage > $iPageCount) {
            $iCurrentPage = $iPageCount;
        }
        $iStartRecord = ($iPageSize * $iCurrentPage) - $iPageSize;
        $sql = $sql . " LIMIT " . ($iStartRecord + $iLeft) . "," . ($iPageSize + $iRight);
    }

    //echo $sql;
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_NUM);
    if ($rows) :
        return $rows;
    else :
        return null;
    endif;
}
?>