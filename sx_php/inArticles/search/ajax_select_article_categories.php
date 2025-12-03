<?php
include dirname(dirname(__DIR__)) . "/sx_config.php";

$intGroupID = 0;
if (isset($_GET["groupID"])) {
    $intGroupID = (int) $_GET["groupID"];
}

if (intval($intGroupID) > 0) {
    $openConn;
    $aResults = null;
    $sql = "SELECT ArticleCategoryID, CategoryName" . str_LangNr . " AS CategoryName 
    FROM article_categories 
    WHERE ArticleGroupID = ? AND Hidden = False
    ORDER BY CategoryName";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$intGroupID]);
    $rs = $stmt->fetchAll(PDO::FETCH_NUM);
    if ($rs) {
        $aResults = $rs;
    }
    $stmt = null;
    $rs = null;
    $closeConn;
    if (is_array($aResults) && !empty($aResults)) {
        echo '<option value="0">' . lngSelectAllCategories . '</option>';
        $iRows = count($aResults);
        for ($r = 0; $r < $iRows; $r++) { ?>
            <option value="<?= $aResults[$r][0] ?>"><?= $aResults[$r][1] ?></option>
<?php
        }
    } else {
        echo '<option value="0">No Category Found</option>';
    }
} else {
    echo '<option value="0">' . lngSelectAllCategories . '</option>';
} ?>