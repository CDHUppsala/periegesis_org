<!DOCTYPE html>
<html lang="en">

<head>

    <title>Pausanias' Description of Greece – Interactive Map & Bilingual Text</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="language" content="en,el" />
    <meta name="creator" content="Public Sphere" />
    <meta name="author" content="Public Sphere" />
    <meta name="keywords" content="Pausanias, Description of Greece, Periegesis Hellados, Παυσανίας, Ελλάδος περιήγησις, Greek travel literature, ancient Greece, bilingual text, interactive map, historical geography, Greek landmarks" />
    <meta name="description" content="Explore all 10 Books of Pausanias' Description of Greece in English and Greek. Navigate by section, view mapped locations, and discover ancient Greece through interactive text and geography." />
    <link rel="canonical" href="map_periegesis.php" />

    <meta property="og:type" content="website" />
    <meta property="og:title" content="Pausanias' Description of Greece – Interactive Map & Bilingual Text" />
    <meta property="og:description" content="Read Pausanias' 10 Books in English and Greek. Discover mapped ancient places by section." />
    <meta property="og:url" content="map_periegesis.php" />
    <meta property="og:site_name" content="Digital Periegesis" />
    <meta property="og:image" content="https://www.periegesis.org/images/reports/Pausanias_Description_of_Greece_map.png" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Pausanias' Description of Greece – Interactive Map & Bilingual Text" />
    <meta name="twitter:description" content="Explore ancient Greece with Pausanias' bilingual text and interactive maps." />
    <meta name="twitter:image" content="https://www.periegesis.org/images/reports/Pausanias_Description_of_Greece_map.png" />

    <link rel="icon" type="image/svg+xml" href="../images/logo/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />

    <style>
        <?php include __DIR__ . "/css/maps_html.css"; ?>
    </style>

    <?php
    include __DIR__ . "/get_places.php";
    ?>
</head>

<body>
    <div class="map_header_wrapper">
        <div class="map_header_fixed">
            <a title="Home to Digital Periegesis" href="index.php">Periegesis</a> |
            <a title="Search the Map by Coordinates of Place Name" href="map_search.php">Search Maps</a>
        </div>
        <div class="map_header_content">
            <select id="SelectSection"></select>
            <span class="toggle_info" title="Show/Hide Information"></span>
            <span class="toggle_up" title="Hide/Show HTML Text"></span>
        </div>
    </div>
    <div id="HTML_SectionWraper"></div>
    <div id="Map_SectionWrapper"></div>

    <script>
        <?php
        include __DIR__ . "/js/nav__maps.js";
        include __DIR__ . "/js/nav__html.js";
        ?>
    </script>

    <div id="HidenInformation">
        <div>
            <h3>No Morked Place Names</h3>
            <p>Place names in the Greek text are automatically marked using data from the Recogito Annotation Database.
                When places cannot be marked within the text, they appear in a list at the bottom of the Greek text.
            </p>
            <p>There are three main reasons for missing the marking of places within the text:</p>
            <ul>
                <li><b>Spelling</b> differences between the annotated word and the original ancient word:
                    <ul>
                        <li><em>Καρικῇ χεῤῥονήσῳ / Καρικῇ χερρονήσῳ</em> (<a href="map_periegesis.php?b=1.1.3">see, for example, Section: 1.1.3</a>)</li>
                        <li><em>Θρᾳκίας χεῤῥονήσου / Θρᾳκίας χερρονήσου</em> (<a href="map_periegesis.php?b=1.9.8">see, for example, Section: 1.9.8</a>)</li>
                        <li><em>τέμενος (Γῆς) / τέμενος Γῆς</em> (<a href="map_periegesis.php?b=1.18.7">see, for example, Section: 1.18.7</a>)</li>
                    </ul>
                </li>
                <li><b>Overlapping</b>, when annotation of a long phrase is followed by separate annotation of words within that phrase.
                    <ul>
                        <li>For example: <em>τὸ περὶ Μαντίνειαν Ἀθηναίων ἔργον</em> followed by annotation of <em>Μαντίνειαν</em> and <em>Ἀθηναίων</em>
                            (<a href="map_periegesis.php?b=1.3.4">see, for example, Section: 1.3.4</a>)</li>
                    </ul>
                </li>
                <li>When the <b>Start</b> and <b>End</b> number of the name's position in the Greek text is not correct (<a href="map_periegesis.php?b=2.4.7">see, for example, Section: 2.4.7</a>)</li>
            </ul>
            <p><strong>Tip:</strong> Hold Shift and draw a rectangle to zoom manually.</p>
            <hr>
            <p> Pausanias, Description of Greece with an English Translation by W.H.S. Jones, LittD., and H.A. Ormerod, M.A., in 4 Volumes. Cambridge, MA, Harvard University Press; London, William Heinemann Ltd. 1918. A work in the public domain, translation adapted/corrected by Brady Kiesling for ToposText. Thanks to the Annenberg CPB/Project, which provided support to the <a href="http://www.perseus.tufts.edu/hopper/text?doc=Perseus%3Atext%3Q184216.01.0160" target='_blank'>Perseus Project</a> for entering the original translation. </p>
            <div class="close_info">Close</div>
        </div>
    </div>

    <div id="HTML_BooksByParagraph" style="display: none;"></div>

</body>

</html>