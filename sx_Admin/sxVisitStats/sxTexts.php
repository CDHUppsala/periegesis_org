<?php
function sx_getTextByDateLinks()
{ ?>
    <div class="row flex_align_center bg">
        <h2><?= sx_intRatio . " " . lngMostReadArticlesTitle ?></h2>
        <div>
            <b>Published:</b>
            <a class="button" href="default.php?pg=t&t=0"><?= lngToday ?></a>
            <a class="button" href="default.php?pg=t&t=1"><?= lngTodayYesterday ?></a>
            <a class="button" href="default.php?pg=t&t=7"><?= lngLastWeek ?></a>
            <a class="button" href="default.php?pg=t&t=30"><?= lngLastMonth ?></a>
            <a class="button" href="default.php?pg=t&t=92"><?= lngLastQuarter ?></a>
            <a class="button" href="default.php?pg=t&t=183"><?= lngLastSixMonths ?></a>
            <a class="button" href="default.php?pg=t&t=365"><?= lngLastYear ?></a>
            <a class="button" href="default.php?pg=t&t=1000"><?= lngAllTexts ?></a>
        </div>
        <form class="flex_end" name="TextStats" action="default.php?pg=t" method="post">
            <input placeholder="<?= lngTextID ?>" type="text" name="TextID" value="" size="9">
            <input class="button" type="submit" name="submit" value="»»">
        </form>
    </div>
<?php
}

function sx_getTextVisits($x)
{
    $strWhere = "";
    $strWhereTitle = lngAllTexts;

    /**
     * For search of particular Text ID
     */
    $intRFTextID = 0;
    if (!empty($_POST["TextID"])) {
        $intRFTextID = (int) $_POST["TextID"];
    }

    if (intval($intRFTextID) > 0) {
        $strWhere = " WHERE t.TextID = " . $intRFTextID;
        $strWhereTitle = lngTextID . " " . $intRFTextID;
    }

    /**
     * For search of Date Period
     * Set default (start page statestics) to
     *      either all texts (A number > 365 get the most read texs without date period)
     *      or define a date period in number of dates up to 365 days
     */
    $intCountFrom = $_GET["t"] ?? '';
    if ($intCountFrom == "" || !is_numeric($intCountFrom)) {
        $intCountFrom = 1000;
    }

    if (intval($intCountFrom) <= 365 && intval($intRFTextID) == 0) {
        if (STR_TextTableVersion === 'texts') {
            $strWhere = " WHERE (t.PublishedDate >= '" . sx_AddToDate(date('Y-m-d'), -$intCountFrom) . "') ";
        } else {
            $strWhere = " WHERE (t.InsertDate >= '" . sx_AddToDate(date('Y-m-d'), -$intCountFrom) . "') ";
        }

        if (intval($intCountFrom) == 0) {
            $strWhereTitle = lngTextsPublished . " " . lngToday;
        } elseif (intval($intCountFrom) == 1) {
            $strWhereTitle = lngTextsPublished . " " . lngTodayYesterday;
        } elseif (intval($intCountFrom) == 7) {
            $strWhereTitle = lngTextsPublished . " " . lngLastWeek;
        } elseif (intval($intCountFrom) == 30) {
            $strWhereTitle = lngTextsPublished . " " . lngLastMonth;
        } elseif (intval($intCountFrom) == 92) {
            $strWhereTitle = lngTextsPublished . " " . lngLastQuarter;
        } elseif (intval($intCountFrom) == 183) {
            $strWhereTitle = lngTextsPublished . " " . lngLastSixMonths;
        } elseif (intval($intCountFrom) == 365) {
            $strWhereTitle = lngTextsPublished . " " . lngLastYear;
        }
    } ?>


    <?php
    $conn = dbconn();
    $aResults = null;

    if (STR_TextTableVersion === 'texts') {
        $sql = "SELECT v.TextID, v.TotalVisits, t.LanguageID, t.Title, t.PublishedDate
            FROM visits_texts AS v
            INNER JOIN " . STR_TextTableVersion . " AS t
                ON v.TextID = t.TextID
                " . $strWhere . "
            ORDER BY v.TotalVisits DESC LIMIT ?";
    } else {
        $sql = "SELECT v.TextID, v.TotalVisits, t.LanguageID, t.Title, t.InsertDate
            FROM visits_texts AS v
            INNER JOIN " . STR_TextTableVersion . " AS t
                ON v.TextID = t.ArticleID
                " . $strWhere . "
            ORDER BY v.TotalVisits DESC LIMIT ?";
    }

    //echo $sql;
    $smtp = $conn->prepare($sql);
    $smtp->execute([$x]);
    $rs = $smtp->fetchAll();
    if ($rs) {
        $aResults = $rs;
    }
    $rs = null;
    $smtp = null;

    if ($strWhereTitle == "") {
        $strWhereTitle = lngTextStatistics;
    }
    $iRows = 0;
    $intTotalTextVisits = 0;
    ?>
    <div id="statsBG">
        <?php
        if (is_array($aResults)) { ?>
            <ol>
                <?php
                $iRows = count($aResults);
                $intTotalTextVisits = 0;
                for ($iR = 0; $iR < $iRows; $iR++) {
                    $intLoopID = $aResults[$iR][0];
                    $intLoopValue = $aResults[$iR][1];
                    $strLanguageCode = sx_getLanguageCodesFromID($aResults[$iR][2]);
                    $strTitle = $aResults[$iR][3];
                    $datePublishedDate = $aResults[$iR][4];
                    $intTotalTextVisits = $intTotalTextVisits + $intLoopValue; ?>
                    <li><span><?= number_format($intLoopValue, 0, ",", " ") ?></span>
                        <span><?= lngID . ": " . $intLoopID ?></span> <span><?= $datePublishedDate ?></span>
                        <a target="_blank" href="../../<?= $strLanguageCode . STR_LinkTextPath . $intLoopID ?>"><?= $strTitle ?></a>
                    </li>
                <?php
                } ?>
            </ol>
        <?php
        } ?>
        <h3 class="absolute">
            <?= $strWhereTitle ?>:
            <?= number_format($intTotalTextVisits, 0, ",", " ") . " " .  lngTotalVisits . " For " . $iRows . " " . lngMostReadArticlesTitle  ?>
        </h3>
    </div>
<?php
} ?>