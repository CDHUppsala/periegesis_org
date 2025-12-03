<?php
/**
 * The function takes an Author ID and search in book-to-authors table all books including this Author ID
 * - WHERE a.AuthorID = ?
 * Returns a string with Book IDs separated by comma (,)
 */
function sxAuthorsByBookID($sWhere, $id)
{
    $conn = dbconn();
    $sBooks = "";
    $bResults = null;
    $sql = "SELECT DISTINCT ba.BookID 
        FROM book_to_authors AS ba 
        LEFT JOIN book_authors AS a 
        ON ba.AuthorID = a.AuthorID " . $sWhere . "
        ORDER BY ba.BookID DESC ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $rs = $stmt->fetchAll(PDO::FETCH_NUM);
    if ($rs) {
        $bResults = $rs;
    }
    $rs = null;
    $stmt = null;
    if (is_array($bResults)) {
        $rows = count($bResults);
        for ($r = 0; $r < $rows; $r++) {;
            if (!empty($sBooks)) {
                $sBooks = $sBooks . ",";
            }
            $sBooks = $sBooks . $bResults[$r][0];
        }
    }
    $bResults = null;
    return  $sBooks;
}

function sx_getBooksNav()
{
    $conn = dbconn();
    $sql = "SELECT 
        bg.BookGroupID AS BookGroupID,
        bg.BookGroupName" . str_LangNr . " AS BookGroupName,
        bc.BookCategoryID AS BookCategoryID,
        bc.BookCategoryName" . str_LangNr . " AS BookCategoryName,
        bsc.BookSubCategoryID AS BookSubCategoryID,
        bsc.BookSubCategoryName" . str_LangNr . " AS BookSubCategoryName
    FROM
        ((book_groups bg
        LEFT JOIN book_categories bc ON ((bg.BookGroupID = bc.BookGroupID)))
        LEFT JOIN book_subcategories bsc ON ((bc.BookCategoryID = bsc.BookCategoryID)))
	WHERE bg.Hidden = 0 AND (bc.Hidden = 0 OR bc.Hidden IS NULL) AND (bsc.Hidden = 0 OR bsc.Hidden IS NULL) 
    ORDER BY bg.Ordering DESC, BookGroupID , bc.Ordering DESC, BookCategoryID , bsc.Ordering DESC, BookSubCategoryID";
    $stmt = $conn->query($sql);
    $rs = $stmt->fetchAll(PDO::FETCH_NUM);
    if ($rs) {
        return $rs;
    } else {
        return null;
    }
}

function sx_getBookStars($id, $radio)
{
    $conn = dbconn();
    $iTotalVotes = 0;
    $sql = "SELECT TotalVotes, TotalValues 
        FROM book_survey 
        WHERE BookID = " . $id;
    $stmt = $conn->query($sql);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $iTotalVotes = $rs["TotalVotes"];
        $iTotalValues = $rs["TotalValues"];
    }
    $rs = null;
    $stmt = null;
    if (intval($iTotalVotes) > 0) {
        $iTemp = ($iTotalValues / $iTotalVotes); ?>
        <p><?= lngAverage . ": " . round($iTemp, 1) ?>
            <?php
            if ($radio) { ?>
                <span class="five_stars"><span style="width:<?= round($iTemp * 20) ?>%"></span></span>
            <?php
            } ?>
        </p>
    <?php
    }
}

function sx_getBookStarsImage($floor)
{
    if (intval($floor) > 0) { ?>
        <p><b><?= lngAverage ?>:</b> <?= number_format($floor, 2) ?>
            <span class="five_stars"><span style="width:<?= round($floor * 20) ?>%"></span></span>
        </p>
    <?php
    }
}

function sx_getLinkTagsForBooks($strRequestLink, $strRequestLinkName, $radioPDF = false)
{
    if ($strRequestLink != "") {
        if (stripos($strRequestLink, "http://") !== false || stripos($strRequestLink, "https://") !== false) {
            $aTagOpen = '<a target="_blank" href="' . $strRequestLink . '">';
            $aTagClose = "</a>";
        } elseif (stripos($strRequestLink, "www.") !== false) {
            $aTagOpen = '<a target="_blank" href="http://' . $strRequestLink . '">';
            $aTagClose = "</a>";
        } elseif (stripos($strRequestLink, "../") !== false) {
            $aTagOpen = '<a target="_blank" href="' . $strRequestLink . '">';
            $aTagClose = "</a>";
        } elseif (is_numeric($strRequestLink)) {
            if ($radioPDF) {
                $aTagOpen = '<a target="_top" href="' . sx_LANGUAGE_PATH . "ps_PDF.php?archID=" . $strRequestLink . '">';
            } else {
                $aTagOpen = '<a target="_top" href="' . sx_LANGUAGE_PATH . "articles.php?tid=" . $strRequestLink . '">';
            }
            $aTagClose = "</a>";
        } else {
            $aTagOpen = '<a target="_blank" href="' . $strRequestLink . '">';
            $aTagClose = "</a>";
        }
    } else {
        $aTagOpen = "";
        $aTagClose = "";
    }
    echo $aTagOpen . $strRequestLinkName . $aTagClose;
}

function sx_getBackArrow()
{ ?>
    <a title="<?= lngClickHereToReturn ?>" href="javascript:history.back()"><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_left_xl"></use>
        </svg></a>
<?php
}
function sx_getSorting()
{ ?>
    <a title="<?= lngChangeOrder ?>" id="jqSortDefinitionList" href="javascript:void(0)"><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_sort_alpha_asc"></use>
        </svg></a>
<?php
}
function sx_getPrintBookList()
{ ?>
    <a title="<?= lngOpenForPrinting ?>" href="sx_PrintPage.php?print=books&<?= $_SERVER["QUERY_STRING"] ?>" onclick="openCenteredWindow(this.href,'<?= time(); ?>','700','');return false;"><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_print"></use>
        </svg></a>
<?php
}
function sx_getBookInfo()
{ ?>
    <span title="<?= lngClickOnTheCodeForDetails ?>"><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_info"></use>
        </svg></span>
<?php
}
?>