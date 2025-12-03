<?php
function sx_getAsideTextLinks()
{
    $conn = dbconn();
    $return = null;
    $sql = "SELECT 
        t.TextID, 
        t.Title, 
        t.SubTitle, 
        t.AuthorID, 
        a.FirstName, 
        a.LastName, 
        a.Photo, 
        t.Coauthors, 
        t.Source, 
        t.PublishedMedia, 
        t.PublishedMediaLink, 
        t.PublishedDate, 
        t.HideDate,
        t.UseAuthorPhoto, 
        t.FirstPageMediaURL, 
        t.FirstPageMediaNotes, 
        t.FirstPageMediaPlace, 
        t.FirstPageText 
    FROM texts AS t LEFT JOIN text_authors AS a ON t.AuthorID = a.AuthorID 
    WHERE t.PublishAside = True 
    AND (t.PublishedDate <= '" . date('Y-m-d') . "' OR t.PublishedDate IS NULL) 
        AND t.Publish = True " . str_LanguageAnd . " 
        ORDER BY t.PublishOrder DESC , 
        t.PublishedDate DESC, 
        t.TextID DESC " . str_LimitFirstPage;
    //echo $sql;
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rs = $stmt->fetchAll(PDO::FETCH_NUM);
    if ($rs) {
        $return = $rs;
    }
    $stmt = null;
    $rs = null;
    return $return;
}

$a_Rows = null;
if ($radio_UseAsideTexts) {
    $a_Rows = sx_getAsideTextLinks();
}
if (is_array($a_Rows)) {
    if (!empty($str_AsideTextsTitle)) { ?>
        <h2 class="head_aside"><span><?= ($str_AsideTextsTitle) ?></span></h2>
    <?php }
    $iRows = count($a_Rows);
    for ($r = 0; $r < $iRows; $r++) { ?>
        <article>
            <?php
            $iTextID = $a_Rows[$r][0];
            $strTitle = $a_Rows[$r][1];
            $strSubTitle = $a_Rows[$r][2];
            $iAuthorID = $a_Rows[$r][3];
            $strFirstName = $a_Rows[$r][4];
            $strLastName = $a_Rows[$r][5];
            $strPhoto = $a_Rows[$r][6];
            $strCoauthors = $a_Rows[$r][7];
            $strSource = $a_Rows[$r][8];
            $strPublishedMedia = $a_Rows[$r][9];
            $strPublishedMediaLink = $a_Rows[$r][10];
            $datePublishedDate = $a_Rows[$r][11];
            $radioHideDate = $a_Rows[$r][12];
            $radioUseAuthorPhoto = $a_Rows[$r][13];
            $strFirstPageMediaURL = $a_Rows[$r][14];
            $strFirstPageMediaNotes = $a_Rows[$r][15];
            $strFirstPageMediaPlace = $a_Rows[$r][16];
            $memoFirstPageText = $a_Rows[$r][17];

            $strAll = "";
            if (intval($iAuthorID) > 0) {
                $strAll = $strFirstName . " " . $strLastName;
                $strAll = '<a class="opacity_link" title="' . lngAuthorAllTexts . '" href="texts.php?authorID=' . $iAuthorID . '">' . $strAll . "</a>";
            }
            if ($strCoauthors != "") {
                if ($strAll != "") {
                    $strAll = $strAll . ", ";
                }
                $strAll = $strAll . $strCoauthors;
            }
            if ($strSource != "") {
                if ($strAll != "") {
                    $strAll = $strAll . ", ";
                }
                $strAll = $strAll . $strSource;
            }
            if ($strPublishedMedia != "") {
                $tagLeft = "";
                $tagRight = "";
                if ($strPublishedMediaLink != "") {
                    $tagLeft = return_Left_Link_Tag($strPublishedMediaLink, "opacity_link");
                    $tagRight = "</a>";
                }
                if ($strAll != "") {
                    $strAll = $strAll . ", ";
                }
                $strAll = $strAll . $tagLeft . $strPublishedMedia . $tagRight;
            }
            if ($strAll != "") {
                $strAll = $strAll . ", ";
            }
            $strAll = $strAll . $datePublishedDate;
            ?>
            <h3><a href="texts.php?tid=<?= $iTextID ?>"><?= $strTitle ?></a></h3>
            <?php
            if ($strSubTitle != "") { ?>
                <h4><?= $strSubTitle ?></h4>
            <?php }
            if ($strAll != "") { ?>
                <h5><?= $strAll ?></h5>
            <?php }
            if (!empty($strPhoto) && $radioUseAuthorPhoto && $radio_UseAsideTextImg) {
                if ($strFirstPageMediaPlace == "") {
                    $strFirstPageMediaPlace = "Left";
                }
                get_Any_Media($strPhoto, $strFirstPageMediaPlace, "", "texts.php?authorID=" . $iAuthorID);
            } elseif (!empty($strFirstPageMediaURL) && $radio_UseAsideTextImg) {
                if (strpos($strFirstPageMediaURL, ";") > 0) {
                    $strFirstPageMediaURL = trim(explode(";", $strFirstPageMediaURL)[0]);
                }
                get_Any_Media($strFirstPageMediaURL, $strFirstPageMediaPlace, $strFirstPageMediaNotes);
            }

            if ($radio_UseAsideTextIntro && !empty($memoFirstPageText)) { ?>
                <div class="text text_small">
                    <?= $memoFirstPageText ?>
                </div>
                <div class="align_right">
                    <a href="texts.php?tid=<?= $iTextID ?>"><?= lngReadMore ?></a>
                </div>
            <?php } ?>
        </article>
<?php }
$a_Rows = null;

} ?>