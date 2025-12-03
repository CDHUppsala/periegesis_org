<?php


/**
 * Returns an array of texts that are related to the ID of the currently open Text
 * @param int $tid : the ID of the currently open text
 * @param int $incid : the ID of the initial text that started the relationship:
 *      - if $incid > 0, the query returns all other texts that are related to it.
 *      - if $incid = 0, the query checks if $tid started a relationship 
 *          and returns all texts that are related to it
 * @return array|null
 */
function sx_getRelatedTextsByAnyID($tid, $incid = 0)
{
    $return = null;
    $conn = dbconn();
    $sql = "SELECT t.TextID, t.Title, t.PublishedDate, t.HideDate, 
        t.Coauthors, a.FirstName, a.LastName, a.Photo,
        t.FirstPageMediaURL, t.TopMediaURL,
		IF(t.FirstPageText IS NULL,t.MainText,t.FirstPageText) AS ShortText
        FROM " . sx_TextTableVersion . " AS t
        LEFT JOIN text_authors AS a ON t.AuthorID = a.AuthorID
        WHERE t.PublishedDate <= CURDATE() 
            AND (IF(:incid > 0, 
                ((t.IncludeInTextID=:incid2 AND t.TextID Not In (:incid3) AND t.TextID Not In (:tid)) OR t.TextID=:incid4),
                t.IncludeInTextID=:tid1)
            ) " . str_LanguageAnd . "
        ORDER BY t.PublishedDate DESC , t.TextID DESC LIMIT 12";
    //echo $sql;
    $stmt = $conn->prepare($sql);
    $stmt->execute([':incid' => $incid, ':incid2' => $incid, ':incid3' => $incid, ':incid4' => $incid, ':tid' => $tid, ':tid1' => $tid]);
    $rs = $stmt->fetchAll(PDO::FETCH_NUM);
    if ($rs) {
        $return = $rs;
    }
    $stmt = null;
    $rs = null;
    return $return;
}

/**
 * To be used with an open Article, on the elements Aside or Main (at the bottom) to show:
 * 		A list of related texts
 * 		To inlude images and introductory text sett sx_RelatedTextsByCards to true
 */

if (intval($int_TextID) > 0 && $radio_UseRelatedTexts) {
    if (empty($iIncludeInTextID)) {
        $iIncludeInTextID = 0;
    }

    $arRows = sx_getRelatedTextsByAnyID($int_TextID, $iIncludeInTextID);

    if (is_array($arRows)) {
        if (sx_RelatedTextsByCards) { ?>
            <section class="grid_cards_wrapper">
                <h2><?= lngRelatedTexts ?></h2>
                <?php
                sx_getTextInCards($arRows, false);
                ?>
            </section>
        <?php
            /**
             * Thefollowing is a list with a legend, just in case!
             */
        } else { ?>
            <section class="jqNavSideToBeCloned">
                <fieldset class="related_texts">
                    <legend><?= lngRelatedTexts ?></legend>
                    <nav class="nav_aside">
                        <ul>
                            <?php
                            $iRows = count($arRows);
                            for ($r = 0; $r < $iRows; $r++) {
                                $dDate = $arRows[$r][2] . ", ";
                                if ($arRows[$r][3]) {
                                    $dDate = "";
                                }
                                $c = $arRows[$r][4];
                                $n = $arRows[$r][5];
                                if (!empty($n)) {
                                    $n .= " " . $arRows[$r][6] . ", ";
                                }
                                if (!empty($c)) {
                                    $n .= $c . ", ";
                                }
                            ?>
                                <li><a href="texts.php?tid=<?= $arRows[$r][0] ?>"><span><?= $n . $dDate ?></span> <?= $arRows[$r][1] ?></a></li>
                            <?php
                            } ?>
                        </ul>
                    </nav>
                </fieldset>
            </section>
<?php
        }
    }
}
