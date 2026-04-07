<!DOCTYPE html>
<html lang="en">

<head>

    <title>Pausanias' Description of Greece – Interactive Map & Bilingual Text</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="language" content="en,el" />
    <meta name="creator" content="Public Sphere" />
    <meta name="author" content="Public Sphere" />

    <link rel="icon" type="image/svg+xml" href="../images/logo/favicon.svg">

    <style>
        <?php include __DIR__ . "/css/maps_html.css"; ?>
    </style>

    <?php
    $type = $_GET['type'] ?? '';
    if ($type === 'places') {
        include __DIR__ . "/php/get_places.php";
    } else {
        include __DIR__ . "/php/get_types.php";
    }
    ?>
</head>

<body>

    <div class="map_header_wrapper">
        <div class="map_header_fixed">
            <a title="Home to Digital Periegesis" href="index.php">dp</a> |
            <a title="Search the Map by Coordinates of Place Name" href="map_search.php">SM</a>
        </div>
        <div class="map_header_content">
            <button id="SearchErrors">Get Annotation Errors</button>
            <label><input id="SaveHints" type="checkbox"> Save Hints</label>
            <label><input id="SaveMarkedText" type="checkbox"> Save Marked Text</label>
            <button id="SearchStartEnd">Search Start-End Errors</button>
        </div>
    </div>
    <div id="HTML_Books"></div>
    <div id="HTML_BooksByParagraph"></div>

    <script>
        <?php
        if ($type === 'places') {
            include __DIR__ . "/js/search_places.js";
        } else {
            include __DIR__ . "/js/search_types.js";
        }
        ?>
    </script>

</body>

</html>