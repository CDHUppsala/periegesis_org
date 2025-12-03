<?php
function sx_getItemsByDateLinks()
{ ?>
    <div class="row flex_align_center bg">
        <h2><?= sx_intRatio . " " . lngMostReadArticlesTitle ?></h2>
        <form class="flex_end" name="TextStats" action="default.php?pg=i" method="post">
            <input placeholder="<?= lngTextID ?>" type="text" name="ItemID" value="" size="9">
            <input class="button" type="submit" name="submit" value="»»">
        </form>
    </div>
<?php
}
function sx_getItemVisitsData(int $limit): array
{
    $conn = dbconn();

    $where = "";
    $params = [':limit' => $limit];
    $whereTitle = lngAllTexts;

    // Optional filter
    $itemId = isset($_POST["ItemID"]) ? (int) $_POST["ItemID"] : 0;
    if ($itemId > 0) {
        $where = " WHERE t.ItemID = :itemId";
        $params[':itemId'] = $itemId;
        $whereTitle = lngTextID . " " . $itemId;
    }

    $sql = "
        SELECT v.TextID, v.TotalVisits, t.ItemTitle, t.InsertDate
        FROM visits_texts AS v
        INNER JOIN items AS t ON v.TextID = t.ItemID
        $where
        ORDER BY v.TotalVisits DESC
        LIMIT :limit
    ";

    $stmt = $conn->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val, PDO::PARAM_INT);
    }
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($whereTitle)) {
        $whereTitle = lngTextStatistics;
    }

    return [
        'results' => $results,   // always the raw rows from DB
        'meta' => [
            'whereTitle'  => $whereTitle,
            'totalVisits' => array_sum(array_column($results, 'TotalVisits')),
            'rows'        => count($results),
            'langCode'    => sx_DefaultAdminLang,
        ]
    ];
}

function sx_renderItemVisits(array $data): void
{
    $results = $data['results'];
    $meta = $data['meta'];

?>
    <div id="statsBG">
        <?php if (!empty($data['results'])): ?>
            <ol>
                <?php foreach ($results as $row): ?>
                    <li>
                        <span><?= number_format($row['TotalVisits'], 0, ",", " ") ?></span>
                        <span><?= lngID . ": " . (int)$row['TextID'] ?></span>
                        <span><?= htmlspecialchars($row['InsertDate']) ?></span>
                        <a target="_blank"
                            href="../../<?= htmlspecialchars($meta['langCode'] . STR_LinkTextPath . $row['TextID']) ?>">
                            <?= htmlspecialchars($row['ItemTitle']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>

        <h3 class="absolute">
            <?= htmlspecialchars($meta['whereTitle']) ?>:
            <?= number_format($meta['totalVisits'], 0, ",", " ") ?>
            <?= " " . lngTotalVisits . " For " . $meta['rows'] . " " . lngMostReadArticlesTitle ?>
        </h3>
    </div>
<?php
}
?>