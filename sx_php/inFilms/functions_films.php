<?php

function sx_getFilmsNav()
{
    $conn = dbconn();
    $sql = "SELECT 
        fg.FilmGroupID AS FilmGroupID,
        fg.FilmGroupName" . str_LangNr . " AS FilmGroupName,
        fc.FilmCategoryID AS FilmCategoryID,
        fc.FilmCategoryName" . str_LangNr . " AS FilmCategoryName,
        fsc.FilmSubCategoryID AS FilmSubCategoryID,
        fsc.FilmSubCategoryName" . str_LangNr . " AS FilmSubCategoryName
    FROM
        ((film_groups fg
        LEFT JOIN film_categories fc ON ((fg.FilmGroupID = fc.FilmGroupID)))
        LEFT JOIN film_subcategories fsc ON ((fc.FilmCategoryID = fsc.FilmCategoryID)))
	WHERE fg.Hidden = 0 AND (fc.Hidden = 0 OR fc.Hidden IS NULL) AND (fsc.Hidden = 0 OR fsc.Hidden IS NULL) 
    ORDER BY fg.Ordering DESC, FilmGroupID , fc.Ordering DESC, FilmCategoryID , fsc.Ordering DESC, FilmSubCategoryID";
    $stmt = $conn->query($sql);
    $rs = $stmt->fetchAll(PDO::FETCH_NUM);
    if ($rs) {
        return $rs;
    } else {
        return null;
    }
}

function sx_getFilmStars($id, $radio)
{
    $conn = dbconn();
    $iTotalVotes = 0;
    $sql = "SELECT TotalVotes, TotalValues 
        FROM film_survey 
        WHERE FilmID = " . $id;
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
            <?php if ($radio) { ?>
                <span class="five_stars"><span style="width:<?= round($iTemp * 20) ?>%"></span></span>
            <?php } ?>
        </p>
    <?php }
}

function sx_getFilmStarsImage($floor)
{
    if (intval($floor) > 0) {
    ?>
        <p><b><?= lngAverage ?>:</b> <?= number_format($floor, 2) ?>
            <span class="five_stars"><span style="width:<?= round($floor * 20) ?>%"></span></span>
        </p>
    <?php
    }
}

function sx_getLinkTagsForFilms($strRequestLink, $strRequestLinkName, $radioPDF = false)
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
                $aTagOpen = '<a target="_top" href="' . sx_LANGUAGE_PATH . "texts.php?tid=" . $strRequestLink . '">';
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
<?php }
function sx_getSorting()
{ ?>
    <a title="<?= lngChangeOrder ?>" id="jqSortDefinitionList" href="javascript:void(0)"><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_sort_alpha_asc"></use>
        </svg></a>
<?php }
function sx_getPrintFilmList()
{ ?>
    <a title="<?= lngOpenForPrinting ?>" href="sx_PrintPage.php?print=films&<?= $_SERVER["QUERY_STRING"] ?>" onclick="openCenteredWindow(this.href,'<?= time(); ?>','700','');return false;"><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_print"></use>
        </svg></a>
<?php }
function sx_getFilmInfo()
{ ?>
    <span title="<?= lngClickOnTheCodeForDetails ?>"><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_info"></use>
        </svg></span>
<?php }
?>