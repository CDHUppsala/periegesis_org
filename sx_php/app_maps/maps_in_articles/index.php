<?php
// Check if Leaflet has been loaded on the Head of the page 
$include_LeafletScript = true;
if (defined('SX_includePlaceMaps') && SX_includePlaceMaps) {
    $include_LeafletScript = false;
}

if ($include_LeafletScript) { ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<?php
}

if ($loade_MediaMapScritps) { ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/papaparse@5.4.1/papaparse.min.js"></script>
    <script src="https://unpkg.com/leaflet-omnivore@0.3.4/leaflet-omnivore.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/wellknown@latest/wellknown.min.js"></script>
    <script>
        <?php
        include __DIR__ . "/js/ps_config.js";
        include __DIR__ . "/js/ps_globals.js";
        include __DIR__ . "/js/ps_map_popup_hover.js";
        include __DIR__ . "/js/ps_map_legends.js";
        include __DIR__ . "/js/ps_map_file_search.js";
        include __DIR__ . "/js/ps_map_files_loader.js";
        include __DIR__ . "/js/ps_map_tiles.js";
        include __DIR__ . "/js/ps_map_loader.js";
        ?>
    </script>
<?php
} ?>

<div class="map-slot">
    <div class="map-wrapper" data-path="<?php echo $source_FilePath ?>">
        <div class="filters-wrapper">
            <div class="flex_wrap">
                <div>Filter&nbsp;by:&nbsp;</div>
                <div class="flex_nowrap select-container">
                    <div class="book-filter-container">
                        <select class="book-filter">
                            <option value="">Book</option>
                        </select>
                    </div>

                    <div class="type-filter-container">
                        <select class="type-filter">
                            <option value="">AND Type</option>
                        </select>
                    </div>

                    <div class="region-filter-container">
                        <select class="region-filter">
                            <option value="">AND Region</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="text-filter-container">
                <input type="text" class="text-filter" placeholder="Free Text">
                <button class="submit-filters">Search</button>
                <button class="reset_filters">Clear</button>
                <button class="toggle_MapScreen" title="Toggle Fullscreen">
                    <svg id="sx_fullscreen" viewBox="0 0 32 32">
                        <path d="m1 1 v8 l 3-3 7 7 2-2 -7-7 3-3z M1 31 h8 l-3-3 7-7 -2-2 -7 7 -3-3z M31 31 v-8 l-3 3 -7-7 -2 2 7 7 -3 3z M31 1 h-8 l 3 3 -7 7 2 2 7-7 3 3z">
                        </path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="map_in_media">
            <div class="map" id="<?php echo $map_ID ?>"></div>
        </div>
    </div>
    <div class="map-meta-data"></div>
    <div class="download-wrapper">
        <span>Download as: </span>
        <button class="download-geojson" title="Only the FIRST checked File or Filter is downloaded">GeoJSON</button>
        <button class="download-csv" title="Only the FIRST checked File or Filter is downloaded">CSV</button>
    </div>
</div>