<?php

/**
 * Statistics for digital periegesis
 * Number of (recogito) Annotation per type of Pausania's books 1-10  
 */

$sql = "SELECT Type, Statistics
FROM view_statistics_by_type";
$stmt = $conn->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = null;
?>

<section>
    <div class="grid_cards_wrapper">
        <div class="align_center">
            <h1 class="head"><span>Annotation statistics</span></h1>
            <p>Statistics over the persons, places and events from Pausanias Periegesis that have hitherto been annotated in maps.</p>
        </div>
        <div id="stats" class="grid_cards">
            <?php
            foreach ($rows as $row) {
                $strType = $row['Type'];
                $intStatistics = $row['Statistics'];
                if ($strType == "PERSON") {
                    $icon = "../images/icons/Human-statue.svg";
                    $strType = 'Persons';
                } elseif ($strType == "PLACE") {
                    $icon = "../images/icons/Akropolis.svg";
                    $strType = 'Places';
                } elseif ($strType == "EVENT") {
                    $icon = "../images/icons/Theater.svg";
                    $strType = 'Events';
                } ?>
                <figure class="img_contain">
                    <img src="<?php echo $icon ?>" attr="" style="height:120px">
                    <figcaption class="align_center">
                        <h4><?php echo $strType ?></h4>
                        <p><?php echo number_format($intStatistics, 0, '', ' ') ?></p>
                    </figcaption>
                </figure>
            <?php
            } ?>
        </div>
    </div>
</section>