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
    <title>Ancient & Modern Greek Map Viewer, Search & Downloads | Digital Periegesis</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="language" content="en" />
    <meta name="creator" content="Public Sphere" />
    <meta name="title" content="Ancient & Modern Greek Map Viewer, Search & Downloads | Digital Periegesis" />
    <meta name="description" content="Interactive viewer for ancient and modern Greek geography. Pausanias' travels, archaeology, local administration, and custom geodata to search and download." />
    <meta name="keywords" content="ancient Greece maps, geohistorical data, Greek administration levels, Greek census maps, ancient sites, GeoJSON viewer, KML viewer, map search, coordinates search, Pausanias, geodata download, historical geography, Greece geodata, Wikidata, ELSTAT" />

    <meta property="og:title" content="Ancient & Modern Greek Map Viewer, Search & Downloads | Digital Periegesis" />
    <meta property="og:description" content="Interactive viewer for ancient and modern Greek geography. Pausanias' travels, archaeology, local administration, and custom geodata to search and download." />
    <meta property="og:site_name" content="Digital Periegesis" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://www.periegesis.org/en/map_search.php" />
    <meta property="og:image" content="https://www.periegesis.org/images/logo/Digital_Periegesis_bg.svg">

    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="Ancient & Modern Greek Map Viewer, Search & Downloads | Digital Periegesis">
    <meta name="twitter:description" content="Interactive viewer for ancient and modern Greek geography. Pausanias' travels, archaeology, local administration, and custom geodata to search and download.">
    <meta name="twitter:image" content="https://www.periegesis.org/images/logo/Digital_Periegesis_bg.png">

    <link rel="icon" type="image/svg+xml" href="../images/logo/favicon.svg">
    <link rel="canonical" href="https://www.periegesis.org/en/map_search.php" />

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Map",
            "name": "Periegesis Geo-Historical Map Viewer",
            "description": "Interactive viewer for ancient and modern Greek geography. View ancient sites, Greek administrative levels, and custom geodata with search by coordinates, place names, and clicks.",
            "url": "https://www.periegesis.org/en/map_search.php",
            "image": "https://www.periegesis.org/images/logo/Digital_Periegesis_bg.svg",
            "keywords": [
                "Pausanias description of Greece",
                "ancient Greece maps",
                "geohistorical data",
                "Greek administration levels",
                "Greek census maps",
                "ancient sites",
                "GeoJSON viewer",
                "KML viewer",
                "GPX viewer",
                "CSV map viewer",
                "map search",
                "coordinates search",
                "geodata download",
                "historical geography",
                "Greece geodata"
            ],
            "isAccessibleForFree": "true",
            "creator": {
                "@type": "Organization",
                "name": "Pausanias Digital Periegesis"
            },
            "audience": [{
                    "@type": "ScholarlyAudience",
                    "name": "Students of Ancient Greece and Rome"
                },
                {
                    "@type": "PublicAudience",
                    "name": "Travelers interested in Greek history and geography"
                }
            ]
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link href="../sxApps/ps_maps/map_search/css/ps_maps.css" rel="stylesheet">

    <link rel="stylesheet" href="../sxApps/ps_maps/assets/leaflet/leaflet.css">
    <script src="../sxApps/ps_maps/assets/leaflet/leaflet.js"></script>

    <script src="../sxApps/ps_maps/assets/leaflet-omnivore/leaflet-omnivore.min.js"></script>

    <link rel="stylesheet" href="../sxApps/ps_maps/assets/leaflet-markercluster/MarkerCluster.css">
    <link rel="stylesheet" href="../sxApps/ps_maps/assets/leaflet-markercluster/MarkerCluster.Default.css">
    <script src="../sxApps/ps_maps/assets/leaflet-markercluster/leaflet.markercluster.js"></script>

    <script src="../sxApps/ps_maps/assets/papaparse/papaparse.min.js"></script>
    <script src="../sxApps/ps_maps/assets/wellknown/wellknown.min.js"></script>

</head>

<body>
    <div class="map_container">
        <div id="MapHeaderWrapper" class="map_header_wrapper">
            <div class="home_links">
                <a title="Home to Pausanias Digital Periegesis" href="index.php">
                    <img class="img_big" src="../images/logo/Periegesis.svg" alt="Logo Digital Periegesis" />
                    <img class="img_small" src="../images/logo/Digital_Periegesis_small.svg" alt="Logo Digital Periegesis" />
                </a>
            </div>
            <div class="right_fix">


                <button class="button_svg" onclick="toggle_PanelTools()" title="Tools for filtering and conversion between GeoJson, KML and CSV Files">
                    <svg id="sx_gear" viewBox="0 0 32 32">
                        <path d="M15 5 h2 l 2 -4 5.5 2.25 -1.5 4.25 1.5 1.5 4.25 -1.5 2.25 5.5 -4 2 0 2 4 2 -2.25 5.5 -4.25 -1.5 -1.5 1.5 1.5 4.25 -5.5 2.25 -2 -4 -2 0 -2 4 -5.5 -2.25 1.5 -4.25 -1.5 -1.5 -4.25 1.5 -2.25 -5.5 4 -2 0 -2 -4 -2 2.25 -5.5 4.25 1.5 1.5 -1.5 -1.5 -4.25 5.5 -2.25z m1 3 a1 1 0 0 0 0 16  a1 1 0 0 0 0 -16z m0 3 a1 1 0 0 1 0 10 a1 1 0 0 1 0 -10">
                        </path>
                    </svg>
                </button>

                <button class="button_svg" onclick="toggle_PanelDownloads()" title="Download GeoJson, KML and CSV Files">
                    <svg id="sx_download" viewBox="0 0 32 32">
                        <path d="M5 20 h-3 v8 q0 2 2 2 h24 q2 0 2 -2 v-8 h-3v6 q0 1 -1 1 h-20 q-1 0 -1-1z M14.5 3 v15.5 l -5.5 -5.5 a1 1 0 0 0 -2 2 l 8 8 q1 1 2 0 l 8 -8 a 1 1 0 0 0 -2 -2 l -5.5 5.5 v-15.5 a1 1 0 0 0 -3 0 z">
                        </path>
                    </svg>
                </button>


                <button id="FileSystemButton" class="button_svg" title="Load local Map Files"
                    aria-label="Load Local File">
                    <svg id="sx_folder" viewBox="0 0 32 32">
                        <path d="M3 1 q-2 0 -2 2 v4h16 l -6-6z M1 9 v20 q0 2 2 2 h26 q2 0 2 -2 v-18 q 0 -2 -2 -2 z"></path>
                    </svg>
                </button>
                <input type="file" id="User_FileLoader" accept=".json,.geojson,.kml,.topojson,.csv,.gpx" style="display: none;">

                <button id="SearchInputsToggle" class="button_svg" title="Search by Latitude and Longitude or by Place Name"
                    aria-label="Show/Hide search inputs in mobiles">
                    <svg id="sx_search" viewBox="0 0 32 32">
                        <path
                            d="M 30.5 26.5 l -5 -5 q -1 -1 -2 -1 h -1 a -12 -12 0 1 0 -2 2 v 1 q 0 1 1 2 l 5 5 q 1 1 2 0 l 2 -2 q 1 -1 0 -2 m -12 -8 a 1 1 0 0 1 -11 -11 a 1 1 0 0 1 11 11">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="flex_buttons">

                <button id="ClearAllButton" class="button_svg" title="Clear all areas and places" aria-label="Clear All">
                    <svg id="sx_clear_bold" viewBox="0 0 32 32">
                        <path
                            d="M2 6 a1 1 0 0 1 4 -4 l10 10 10-10 a1 1 0 0 1 4 4 l-10 10 10 10 a1 1 0 0 1 -4 4 l-10 -10 -10 10 a1 1 0 0 1 -4-4 l10-10z">
                        </path>
                    </svg>
                </button>
                <button onclick="toggle_PanelInformation()" class="button_svg" title="Show/Hide Information"
                    aria-label="Show/Hide Information">
                    <svg id="sx_info_big_square" viewBox="0 0 32 32">
                        <path
                            d="M14 1 q-2 0 -2 2 v4 q0 2 2 2 h4 q2 0 2 -2 v-4 q0 -2 -2 -2z M14 12 q-2 0 -2 2 v15 q0 2 2 2 h4 q2 0 2 -2 v-15 q0 -2 -2 -2z">
                        </path>
                    </svg>
                </button>

                <button id="ToggleFilterElements" class="button_svg" title="Show/Hide Filter Elements" aria-label="Show/Hide Filter Elements">
                    <svg id="sx_sreen_to_top" viewBox="0 0 32 32">
                        <path d="M3 1 q-2 0 -2 2 v10 q0 2 2 2 h26 q2 0 2 -2 v-10 q0-2 -2-2z M3 17 q-2 0 -2 2 v10 q0 2 2 2 h26 q2 0 2 -2 v-10 q0-2 -2-2z M 16 2 l 12 6 h-8 v6h-8v-6h-8z M 12 18 h8v12h-8z">
                        </path>
                    </svg>
                    <svg id="sx_sreen_to_botton" style="display: none;" viewBox="0 0 32 32">
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
                        <label
                            title="Hide/Show Popup when clicking on Markers or Map Areas.">
                            <input type="checkbox" id="HideProperties"> Hide Popups
                        </label>
                    </div>
                    <div>
                        <label title="Enables/Disables Click Searching on the map">
                            <input type="checkbox" id="UseClickSearch"> Click to Search
                        </label>
                    </div>
                </div>
            </div>
            <div id="SearchInputs">
                <div>
                    <input type="text" id="SearchLat" name="Lat" placeholder="Latitude" value="">
                    <input type="text" id="SearchLng" name="Lng" placeholder="and Longitude" value="">
                </div>
                <div>
                    <input type="text" id="SearchPlaceName" name="PlaceName" placeholder="or Place Name"
                        title="Clear the Coordinates before New Place Search" value="">
                    <button id="SearchReset" type="reset">Clear</button>
                    <button id="SearchMap">Search</button>
                </div>
            </div>
            <div id="FiltersWrapper">
                <span>Filter&nbsp;byy:&nbsp;</span>
                <div id="BookFilterContainer">
                    <select id="BookFilter">
                        <option value="">Book</option>
                    </select>
                </div>
                <div id="RegionFilterContainer">
                    <select id="RegionFilter">
                        <option value="">AND Region</option>
                    </select>
                </div>
                <div id="TypeFilterContainer">
                    <select id="TypeFilter">
                        <option value="">AND Type A</option>
                    </select>
                </div>
                <div id="TypeFilterContainer_2">
                    <select id="TypeFilter_2">
                        <option value="">OR Type B</option>
                    </select>
                </div>
                <div id="TextFilterContainer">
                    <input type="text" id="TextFilter" placeholder="Free Text">
                    <button id="SubmitTextFilter">Search</button>
                    <button title="Download the FIRST CHECKED File or Filter as GeoJson File" id="DownloadFilteredGeojson">GeoJSON</button>
                    <button title="Download the FIRST CHECKED File or Filter as CSV File" id="DownloadFilteredCSV">CSV</button>
                </div>
            </div>

        </div>
        <div id="map"></div>
    </div>


    <script src="../sxApps/ps_maps/map_search/js/ps_ms_config.js"></script>
    <script src="../sxApps/ps_maps/map_search/js/ps_ms_mapLoader.js"></script>
    <script src="../sxApps/ps_maps/map_search/js/ps_ms_mapLegends.js"></script>
    <script src="../sxApps/ps_maps/map_search/js/ps_ms_mapSearch.js"></script>
    <script src="../sxApps/ps_maps/map_search/js/ps_ms_mapPopupHover.js"></script>
    <script src="../sxApps/ps_maps/map_search/js/ps_ms_fileLoader.js"></script>
    <script src="../sxApps/ps_maps/map_search/js/ps_ms_fileSelector.js"></script>
    <script src="../sxApps/ps_maps/map_search/js/ps_ms_fileSearch.js"></script>
    <script src="../sxApps/ps_maps/map_search/js/ps_ms_mapClearing.js"></script>
    <script src="../sxApps/ps_maps/map_search/js/ps_ms_panels.js"></script>



    <?php
    include __DIR__ . "/panel_Downloads.html";
    include __DIR__ . "/panel_Tools.html";
    include __DIR__ . "/panel_Info.html";
    ?>

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
    <script src="../sxApps/ps_maps/map_search/js/ps_load_modal_html.js"></script>

</body>

</html>