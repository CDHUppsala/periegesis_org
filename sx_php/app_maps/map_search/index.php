<!DOCTYPE html>
<html lang="en">
<!--
For converting shape (zip) files to other formats go to: https://mapshaper.org/
Import files "with advance options"
Before submitting:
    For shape Files with Greek text write in the field "import options":
    - encoding=windows-1253 
    - If windows-1253 doesn't work, try encoding=ISO-8859-7 

To save shape file to other formats (.geojson, etc.) you must convert them to wgs84 format
    Open the console and writing: 
        $ -proj wgs84
    or
        $ -proj +init=epsg:4326
    or
        $ -proj wgs84 from=epsg:2100
-->

<head>
    <title>Search in Maps | Interactive GeoJSON, KML, CSV Viewer | Digital Periegesis</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="language" content="en" />
    <meta name="creator" content="Public Sphere" />
    <meta name="title" content="Search in Maps | Interactive GeoJSON, KML, CSV Viewer | Digital Periegesis" />
    <meta name="description" content="Explore and search geographic data interactively. Load and visualize GeoJSON, KML, CSV, and JSON files with map areas, places, and popups. Includes search by coordinates, place name, and click." />
    <meta name="keywords" content="interactive map, OpensStreetMap, search by coordinates, GeoJSON viewer, KML viewer, CSV map, Pausanias, Greek geography, map areas, map places, Digital Periegesis" />

    <meta property="og:type" content="website" />
    <meta property="og:title" content="Search in Maps | Interactive GeoJSON, KML, CSV Viewer" />
    <meta property="og:description" content="Load and explore geographic data interactively. Search by coordinates, place name, or click. Supports GeoJSON, KML, CSV, and JSON formats." />
    <meta property="og:site_name" content="Digital Periegesis" />
    <meta property="og:url" content="https://www.periegesis.org/en/map_search.php" />

    <link rel="icon" type="image/svg+xml" href="../images/logo/favicon.svg">
    <link rel="canonical" href="https://www.periegesis.org/en/map_search.php" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

    <style>
        <?php
        include __DIR__ . "/css/ps_maps.css";
        ?>
    </style>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script src="https://unpkg.com/leaflet-omnivore@0.3.4/leaflet-omnivore.min.js"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/papaparse@5.4.1/papaparse.min.js"></script>
</head>

<body>
    <div id="map_header_wrapper" class="map_header_wrapper">
        <div class="home_links">
            <a title="Home to Pausanias Digital Periegesis" href="index.php">
                <img class="img_big" src="../images/logo/Periegesis.svg" alt="Logo Digital Periegesis" />
                <img class="img_small" src="../images/logo/Digital_Periegesis_small.svg" alt="Logo Digital Periegesis" />
            </a>
        </div>

        <div class="flex_buttons">

            <button id="ToggleLegends" class="svg_wrapper" title="Show/Hide Legends" aria-label="Show/Hide Legends">
                <svg id="sx_screen_to_left" viewBox="0 0 32 32">
                    <path
                        d="M3 1 q-2 0 -2 2 v26 q0 2 2 2 h10 q2 0 2 -2 v-26 q0-2 -2-2z M19 1 q-2 0 -2 2 v26 q0 2 2 2 h10 q2 0 2 -2 v-26 q0-2 -2-2z M30 12 v8h-12v-8z M 14 12 v8h-6 v8 l -6 -12 6 -12 v8z">
                    </path>
                </svg>
                <svg id="sx_screen_to_right" viewBox="0 0 32 32">
                    <path
                        d="M3 1 q-2 0 -2 2 v26 q0 2 2 2 h10 q2 0 2 -2 v-26 q0-2 -2-2z M19 1 q-2 0 -2 2 v26 q0 2 2 2 h10 q2 0 2 -2 v-26 q0-2 -2-2z M30 16 l -6 12 v-8h-6v-8h6v-8z M 14 12 v8 h-12v-8z">
                    </path>
                </svg>
            </button>

            <button id="clearAllButton" class="svg_wrapper" title="Clear all areas and places" aria-label="Clear All">
                <svg id="sx_clear_bold" viewBox="0 0 32 32">
                    <path
                        d="M2 6 a1 1 0 0 1 4 -4 l10 10 10-10 a1 1 0 0 1 4 4 l-10 10 10 10 a1 1 0 0 1 -4 4 l-10 -10 -10 10 a1 1 0 0 1 -4-4 l10-10z">
                    </path>
                </svg>
            </button>
            <button onclick="togglePanelInformation()" class="svg_wrapper" title="Show/Hide Information"
                aria-label="Show/Hide Information">
                <svg id="sx_info_big_square" viewBox="0 0 32 32">
                    <path
                        d="M14 1 q-2 0 -2 2 v4 q0 2 2 2 h4 q2 0 2 -2 v-4 q0 -2 -2 -2z M14 12 q-2 0 -2 2 v15 q0 2 2 2 h4 q2 0 2 -2 v-15 q0 -2 -2 -2z">
                    </path>
                </svg>
            </button>
            <button id="fileSystemButton" class="svg_wrapper" title="Load local JSON/GeoJSON"
                aria-label="Load Local File">
                <svg id="sx_folder" viewBox="0 0 32 32">
                    <path d="M3 1 q-2 0 -2 2 v4h16 l -6-6z M1 9 v20 q0 2 2 2 h26 q2 0 2 -2 v-18 q 0 -2 -2 -2 z"></path>
                </svg>
            </button>
            <input type="file" id="User_FileLoader" accept=".json,.geojson,.kml,.topojson,.csv,.gpx" style="display: none;">

            <button id="searchInputsToggle" class="svg_wrapper" title="Show/Hide search inputs in mobiles"
                aria-label="Show/Hide search inputs in mobiles">
                <svg id="sx_search" viewBox="0 0 32 32">
                    <path
                        d="M 30.5 26.5 l -5 -5 q -1 -1 -2 -1 h -1 a -12 -12 0 1 0 -2 2 v 1 q 0 1 1 2 l 5 5 q 1 1 2 0 l 2 -2 q 1 -1 0 -2 m -12 -8 a 1 1 0 0 1 -11 -11 a 1 1 0 0 1 11 11">
                    </path>
                </svg>
            </button>

            <button id="ToggleFilterElements" class="svg_wrapper" title="Show/Hide Filter Elements" aria-label="Show/Hide Filter Elements">
                <svg id="sx_sreen_to_top" viewBox="0 0 32 32">
                    <path d="M3 1 q-2 0 -2 2 v10 q0 2 2 2 h26 q2 0 2 -2 v-10 q0-2 -2-2z M3 17 q-2 0 -2 2 v10 q0 2 2 2 h26 q2 0 2 -2 v-10 q0-2 -2-2z M 16 2 l 12 6 h-8 v6h-8v-6h-8z M 12 18 h8v12h-8z">
                    </path>
                </svg>
                <svg id="sx_sreen_to_botton" viewBox="0 0 32 32">
                    <path d="M3 1 q-2 0 -2 2 v10 q0 2 2 2 h26 q2 0 2 -2 v-10 q0-2 -2-2z M3 17 q-2 0 -2 2 v10 q0 2 2 2 h26 q2 0 2 -2 v-10 q0-2 -2-2z M 12 2 h8v12h-8 M 12 18 h8 v6h8 l -12 6 l -12 -6 h8z">
                    </path>
                </svg>
            </button>

        </div>
        <div class="flex_selects">
            <select id="MapPlacesSelector" name="MapPlacesSelector">
                <option value="" title="Removes all Places">Map Places</option>
            </select>
            <select id="MapAreasSelector" name="MapAreasSelector">
                <option value="" title="Removes all Areas">Map Areas</option>
            </select>
        </div>
        <div class="flex_checkboxes">
            <div>
                <div>
                    <label id="StackLayersMarkersLabel"
                        title="Map areas and places from different files are added (stacked) upon each other.">
                        <input type="checkbox" id="StackAreasPlaces" /> Stack
                    </label>
                    <button class="svg_wrapper" onclick="togglePanelTools()"
                        title="Tools for filtering and conversion between GeoJson, KML and CSV Files">Tools</button>
                    <button class="svg_wrapper" onclick="togglePanelDownloads()"
                        title="Download GeoJson, KML and CSV Files">Downloads</button>
                </div>
                <div>
                    <label
                        title="Active when a GeoJSON map is loaded. Hide/Show Popup with properties when clicking on map areas.">
                        <input type="checkbox" id="ShowProperties" checked> Show Popups
                    </label>
                    <label title="Enables/Disables Click Searching on the map">
                        <input type="checkbox" id="ClickSearchToggle" checked> Use Click Search
                    </label>
                </div>
            </div>
        </div>
        <div id="searchInputs">
            <div class="flex_NoGap">
                <input type="text" id="SearchLat" name="Lat" placeholder="Write Latitude" value="">
                <input type="text" id="SearchLng" name="Lng" placeholder="and Longitude" value="">
            </div>
            <div class="flex_NoGap">
                <input type="text" id="SearchPlaceName" name="PlaceName" placeholder="or Place Name"
                    title="Clear the Coordinates before New Place Search" value="">
                <button id="SearchReset" type="reset">Clear</button>
                <button id="SearchMap">Search</button>
            </div>
        </div>
        <div id="filter_Wrapper" class="flex_filters">
            <label id="bookFilterContainer">
                <select id="bookFilter">
                    <option value="">Filter by Book</option>
                </select>
            </label>
            <label id="typeFilterContainer">
                <select id="typeFilter">
                    <option value="">Filter by Type A</option>
                </select><select id="typeSecondFilter">
                    <option value="">and by Type B</option>
                </select>
            </label>
            <label id="regionFilterContainer">
                <select id="regionFilter">
                    <option value="">Filter by Region</option>
                </select>
            </label>
        </div>

    </div>
    <div id="map"></div>

    <script>
        <?php
        include __DIR__ . "/js/map_load.js";
        include __DIR__ . "/js/map_search.js";
        include __DIR__ . "/js/common_functions.js";
        include __DIR__ . "/js/file_load.js";
        include __DIR__ . "/js/file_select.js";
        include __DIR__ . "/js/filter.js";
        include __DIR__ . "/js/panels.js";
        ?>
    </script>

    <div id="PanelDownloads">
        <div class="panel_hidden">
            <div>
                <h2>Download Map Areas</h2>
                <div id="MapAreasDownloads"></div>

                <h2>Download Map Places</h2>
                <div id="MapPlacesDownloads"></div>
            </div>

            <button onclick="togglePanelDownloads()">Close</button>
        </div>
    </div>

    <div id="PanelTools">
        <div class="panel_hidden">
            <h2>Convert Between CSV, GeoJson, KML and GPX Files</h2>
            <div>
                <ul>
                    <li><a target="_blank" href="map_tools.php?p=validate-csv-to-geojson">Validate and convert CSV files to normalized CSV and GeoJson files,</a></li>
                    <li><a target="_blank" href="map_tools.php?p=filter-csv-to-geojson-kml">Filter and Convert CSV files to GeoJson and KML files.</a></li>
                    <li><a target="_blank" href="map_tools.php?p=convert-geojson-to-csv">Convert GeoJson files to CSV files.</a></li>
                    <li><a target="_blank" href="map_tools.php?p=convert-geojson-to-kml">Convert GeoJson files of any Geometry to KML files.</a></li>
                    <li><a target="_blank" href="map_tools.php?p=convert-kml-to-geojson-to-csv">Convert KML files (with ExtendedData) to GeoJson files
                            and, for <b>Point</b> Geometries only, to CSV fiels.</a></li>
                    <li><a target="_blank" href="map_tools.php?p=convert-gpx-to-geojson">Convert GPX files (Waypoints, Tracks, Routes) to GeoJson files.</a></li>
                    <li><a target="_blank" href="map_tools.php?p=split-geojson-by-book-to-geojson-kml">Split a GeoJson file containing all of Pausanias' 10 Books by book and convert them to 10 GeoJson and 2×10 KML Files.</a></li>
                </ul>
            </div>
            <button onclick="togglePanelTools()">Close</button>
        </div>
    </div>

    <div id="PanelInformation">
        <div class="panel_hidden">
            <div class="info_text">
                <?php
                include __DIR__ . "/inc_info.html";
                ?>
            </div>
            <button onclick="togglePanelInformation()">Close</button>
        </div>
    </div>
    <!--
        Special addoptation for Pausanias Digital Periegesis
    -->
    <div id="js_Load_Hidden_HTML" style="display:none;"></div>

    <div id="js_Modal_Window">
        <div id="js_Modal_Content">
            <div id="js_Modal_Close">&times;</div>
            <div id="js_Modal_Data"></div>
        </div>
    </div>
    <script>
        <?php
        include __DIR__ . "/js/modal_html.js";
        ?>
    </script>

</body>

</html>