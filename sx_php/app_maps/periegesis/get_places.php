
<?php
// Include only places, as type of annotation
$sql = "SELECT
    RowID,
    BookID,
    StartNum,
    EndNum,
    QuoteName,
    Link,
    Lat,
    Lng,
    PlaceType,
    CONCAT_WS(
        NULLIF(Label, ''),
        NULLIF(Tags, ''),
        NULLIF(Comments, '')
    ) AS Description
FROM vw_annotations_start_end
WHERE 
    Type = 'PLACE'
    AND Lat IS NOT NULL
    AND Lat <> '0.0'
ORDER BY Book, Chapter, Paragraph, StartNum";

$stmt = $conn->prepare($sql);
$stmt->execute();
$rawResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

$transformed = [];

foreach ($rawResults as $row) {
    $QuoteName = htmlspecialchars($row['QuoteName'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $linkHref = $row['Link'] ?? '';
    if (!empty($linkHref)) {
        $linkHref  = htmlspecialchars($linkHref, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
    $hostName  = extractHostName($linkHref);
    $PlaceType = $row['PlaceType'];
    if (!empty($PlaceType)) {
        $PlaceType = str_replace(',', ', ', $PlaceType);
    }
    $coments = $row['Description'];
    if (!empty($coments)) {
        $coments = str_replace('|', ' | ', $coments);
    }

    $linkQuoteName = '<a title="Open in ' . $hostName . '" target="_blank" href="' . $linkHref . '">' . $QuoteName . '</a>';

    $transformed[] = [
        "RowID" => $row['RowID'],
        "BookID" => $row['BookID'],
        "StartNum" => $row['StartNum'],
        "EndNum" => $row['EndNum'],
        "PlaceName" => $row['QuoteName'],
        "Link" => $linkQuoteName,
        "PlaceType" => $PlaceType,
        "Comments" => $coments,
        "Lat" => $row['Lat'],
        "Lng" => $row['Lng']
    ];
}


function extractHostName($url)
{
    $parsed = parse_url($url);
    return $parsed['host'] ?? 'external site';
}
/*
echo '<pre>';
print_r($transformed);
echo '</pre>';
exit;
*/
// Output as JavaScript object...
echo "<script>const object_PlacesBySection = " . json_encode($transformed, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ";</script>";

// ... OR call by ajax
//  header('Content-Type: application/json');
//  echo json_encode($transformed, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
