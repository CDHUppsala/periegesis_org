<?php
/**
 * Related TEXTS
 */

// Get as Associateiv Array ALL Rows for Related Texts Published in First Page

function sx_getRelatedTextsFirstPageAll($ids,$incs)
{
    $_retval = null;
    $conn = dbconn();
    $where_incs = "";
    if(!empty($incs)) {
        $incs = array_unique($incs);
        $where_incs = " OR (TextID IN (". trim(str_repeat(', ?', count($incs)), ', ').") OR IncludeInTextID IN (". trim(str_repeat(', ?', count($incs)), ', ')."))";
    }
    $sql = "SELECT t.TextID, t.IncludeInTextID, t.Title, t.PublishedDate, 
        t.Coauthors, a.FirstName, a.LastName,
        t.FirstPageMediaURL, t.TopMediaURL
        FROM " . sx_TextTableVersion . " AS t 
        LEFT JOIN text_authors AS a ON t.AuthorID = a.AuthorID
        WHERE (IncludeInTextID IN (". trim(str_repeat(', ?', count($ids)), ', ') .")
        " . $where_incs .") ". str_LanguageAnd . "
        ORDER BY IncludeInTextID DESC, TextID DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute(array_merge($ids,$incs,$incs));
    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($row) {
        $_retval = $row;
    }
    $row = null;
    $stmt = null;
    return $_retval;
}

/*
Get includied texts for every TextID Published in First Page
Uses the results from sx_getRelatedTextsFirstPageAll (=arrInc) function
 */

function sx_getRelatedTextsFirstPageByID($textID, $incID, $arrInc) {
    if (return_Filter_Integer($textID) == 0) {
        $textID = 0;
    }
    if (return_Filter_Integer($incID) == 0) {
        $incID = 0;
    }
    if (empty($arrInc)) {
        exit();
    }
    $radioFirstLoop = false;
    $radioCloseTag = false;
    $iRs = count($arrInc) / 2;
    foreach ($arrInc as $row) {
        $text_ID = $row['TextID'];
        $inc_ID = $row['IncludeInTextID'];
        $t = $row['Title'];
        $pd = $row['PublishedDate'];
        $n = $row['FirstName'];
        if (!empty($n)) {
            $n .= " " . $row['LastName'] . ", ";
        }
        $c = $row['Coauthors'];
        if (!empty($c)) {
            $n .= $c . ", ";
        }
        if (intval($incID) > 0) {
            if (intval($text_ID) == intval($incID) || (intval($inc_ID) == intval($incID) && intval($text_ID) != intval($textID))) {
                if ($radioFirstLoop == false) {
                    $radioFirstLoop = true;
                    $radioCloseTag = true;
                    ?>
                    <fieldset class="related_texts">
                        <legend><?=lngRelatedTexts?></legend>
                        <div class="nav_aside">
                        <ul>
                    <?php
                } ?>
                    <li><a title="<?=$pd?>" href="texts.php?tid=<?=$text_ID?>"><?="<span>" . $n . "</span> " . $t?></a></li>
                <?php 
            }
        } else {
            if (intval($inc_ID) == intval($textID)) {
                if ($radioFirstLoop == false) {
                    $radioFirstLoop = true;
                    $radioCloseTag = true;?>
                    <fieldset class="related_texts">
                        <legend><?=lngRelatedTexts?></legend>
                        <div class="nav_aside">
                        <ul>
                <?php }?>
                    <li><a title="<?=$pd?>" href="texts.php?tid=<?=$text_ID?>"><?="<span>" . $n . "</span> " . $t?></a></li>
            <?php }
        }
    }
    if ($radioCloseTag) {?>
            </ul>
        </div>
    </fieldset>
<?php }
}
