<?php
include_once __DIR__ . "/functions_Nav_Asides.php";

function sx_getNavTextsPublishedFirstPage()
{
    $conn = dbconn();
    $sql = "SELECT t.TextID, t.Title, t.PublishedDate, t.HideDate, t.Coauthors, a.FirstName, a.LastName
        FROM " . sx_TextTableVersion . " AS t LEFT JOIN text_authors AS a ON t.AuthorID = a.AuthorID
        WHERE Publish = True AND PublishInFirstPage = True " . str_LanguageAnd . " 
        ORDER BY t.PublishOrder DESC, t.PublishedDate DESC, t.TextID DESC ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetchAll();
    if ($row) {
        return $row;
    } else {
        return Null;
    }
    $stmt = null;
}

$aResults = sx_getNavTextsPublishedFirstPage();
if (is_array($aResults)) {
    if (empty($str_FirstPageTextsTitle)) {
        $str_FirstPageTextsTitle = lngRecentArticles;
    } ?>
    <section class="jqNavMainToBeCloned">
        <h2 class="head slide_up jqToggleNextRight"><span><?= $str_FirstPageTextsTitle ?></span></h2>
        <nav class="nav_aside">
            <ul>
                <?php
                $iRows = count($aResults);
                for ($r = 0; $r < $iRows; $r++) {
                    $i_TextID = $aResults[$r][0];
                    $s_Title = $aResults[$r][1];
                    $d_PublishedDate = $aResults[$r][2];
                    $s_Coauthors = $aResults[$r][3];
                    $strNames = $aResults[$r][4];
                    if (!empty($strNames)) {
                        $strNames = ", " . $strNames . " " . $aResults[$r][5];
                        if (!empty($s_Coauthors)) {
                            $strNames = $strNames . ", " . $s_Coauthors;
                        }
                    }
                    if (!empty($strNames)) {
                        $strNames .= ", ";
                    }
                    $strNames .= $d_PublishedDate
                ?>
                    <li><a title="" href="texts.php?tid=<?= $i_TextID ?>"><?= $s_Title ?><span><?= $strNames ?></span></a></li>
                <?php
                } ?>
            </ul>
        </nav>
    </section>
<?php
}
$aResults = "";
?>