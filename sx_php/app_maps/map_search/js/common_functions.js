/**
 * ========================================================
 * COMMON FUNCTIONS FOR file_select.js AND file_load.js
 * ========================================================
 */

// Global variables
const maps_ParentFolder = '../imgMedia/maps';

const maps_AreasFolder = `${maps_ParentFolder}/map_areas`;
const maps_PlacesFolder = `${maps_ParentFolder}/map_places`;

// List of Custom icon markers
const custom_iconsFile = `${maps_ParentFolder}/icons/place_icons.json?v=2025-10-26`;

// Default application icon markers, as colored SVG images
const marker_IconsFolder = `${maps_ParentFolder}/sx_icons`;
const search_MarkerIcon = `${maps_ParentFolder}/sx_icons/ps_marker_red.svg`;

// Get map metadata from KML and GeoJson files, if any
let map_metadata = {};

// Check if custom icons for marking exists (else, default icons are used)
let place_Icons = {};
fetch(custom_iconsFile)
    .then(response => {
        if (!response.ok) {
            throw new Error(`File not found or inaccessible: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        place_Icons = data;
    })
    .catch(error => {
        // console.warn("Could not load place icons file:", error.message);
    });

const base_PolygonStyle = {
    color: '#445544',
    weight: 2,
    opacity: 0.65,
    fillOpacity: 0.1
};

const hover_PolygonStyle = {
    color: "#ff0000",
    weight: 2,
    opacity: 1,
    fillOpacity: 0.05
}

const base_LineStyle = {
    color: '#0000ff',
    weight: 3,
    opacity: 0.85,
    fillOpacity: 0.05
};

const hover_LineStyle = {
    color: '#ff0000',
    weight: 3,
    opacity: 0.85,
    fillOpacity: 0.05
}

let base_UsedStyle, hover_UsedStyle;
//let base_UsedStyle = base_PolygonStyle;
//let hover_UsedStyle = hover_PolygonStyle

// Zoon wondow to places or map areas boundaries only with first load
let first_FileToLoad = true;

// Group Multiple Places and Areas from both user and server files 
// Used to hide/show subgrops, toggle popups and remove all Places and Areas simultaneously 
const loaded_ToMapLayersGroup = L.featureGroup().addTo(map);

// Anables/Disables multiple map areas added upon each other
let use_Stacking = document.getElementById("StackAreasPlaces").checked;
document.getElementById("StackAreasPlaces").addEventListener("change", function () {
    use_Stacking = this.checked;
});


/**
 * @param {*} obj : JSON content or GeoJSON feature.properties
 * @returns Sanitized strings for Key names and Key values
 *  - URL address are converted to clickable links
 *  - Set manual limits to the number of displayed properties
 */

function sanitize_PopupContent(obj) {
    const entries = Object.entries(obj);
    const visibleEntries = entries.slice(0, 26);

    // Extract map metadata, if exists
    let content_meta = "";
    if (Object.entries(map_metadata).length) {
        const updateKeys = Object.keys(map_metadata).filter(
            key => key.toLowerCase() === "updated" || key.toLowerCase() === "version"
        );
        if (updateKeys.length) {
            content_meta += `<br><strong>${updateKeys}</strong>: ${map_metadata[updateKeys]}`;
        }
        const authorKeys = Object.keys(map_metadata).filter(
            key => key.toLowerCase() === "author"
        );
        if (authorKeys.length) {
            content_meta += `<br><strong>${authorKeys}</strong>: ${map_metadata[authorKeys]}`;
        }
    }

    // Convert entries to popup lines
    let footer = "";
    const content = visibleEntries.map(([k, v]) => {
        // Skip internal merge counter
        if (k === "_mergedCount") return "";

        const safeKey = escape_HTML(k);

        if (v == null || v === '') {
            return `<strong>${safeKey}</strong>: <em>(empty)</em>`;
        }

        let safeValue = v;
        // For Pausanias' digital periegesis
        if (safeKey.toLowerCase() === 'passages' || safeKey === 'BookID' || safeKey === 'Identifier') {
            safeValue = get_BookLinks(safeValue);
        } else {
            safeValue = format_ValueToLink(safeValue);
        }
        return `<strong>${safeKey}</strong>: ${safeValue}`;

    })
        .filter(Boolean)
        .join("<br>");

    // Footer: merged entry count
    if (obj._mergedCount) {
        footer = `<hr><em>(${obj._mergedCount} merged entries - popup from the 1st entry)</em>`;
    }

    const remainingCount =
        entries.length - visibleEntries.length -
        (obj._mergedCount ? 1 : 0);

    const moreMessage =
        remainingCount > 0
            ? `<br><em>...and ${remainingCount} more properties</em>`
            : "";

    return content + content_meta + footer + moreMessage;
}

function escape_HTML(str) {
    return String(str).replace(/[&<>"']/g, s => ({
        "&": "&amp;", "<": "&lt;", ">": "&gt;",
        '"': "&quot;", "'": "&#39;"
    }[s]));
}


// Creates links for every queried Book ID in an array:
function get_BookLinks(val) {
    const ids = parse_JsonArray(val);
    return ids.map(id => {
        const safeID = escape_HTML(id);
        return `[<a title="Open section in Read Pausanias with Maps" href="map_periegesis.php?b=${encodeURIComponent(safeID)}" target="_blank">${safeID}</a>]
        <span class="modal_click" title="Open Section in Modal Window" onclick="load_modal_html('${safeID}')">MW</span>`;
    }).join(', ');
}

// Returns an array from JSON-like formats:
function parse_JsonArray(val) {
    if (!val) return [];
    try {
        if (Array.isArray(val)) return val;
        if (typeof val === 'string') {
            const normalized = val.replace(/'/g, '"').trim();
            if (normalized.startsWith('[')) {
                return JSON.parse(normalized);
            }
            return normalized.split(',').map(s => s.trim());
        }
        return [String(val)];
    } catch (e) {
        // console.warn('Failed to parse Book IDs:', e);
        return [];
    }
}

// Add other source IDs than Q12345678 for wikidata
function linkifyIdentifier(str) {
    const patterns = {
        wikidata: /^Q\d+$/,
    };

    if (patterns.wikidata.test(str)) {
        const url = `https://www.wikidata.org/wiki/${str}`;
        return `<a href="${escape_HTML(url)}" target="_blank">${escape_HTML(str)}</a>`;
    }

    return null;
}

// To deal with property values structured as JSON, containing multiple links from the same source
function format_ValueToLink(value) {
    const str = String(value).trim();

    const link_wikidata = linkifyIdentifier(str)
    if (link_wikidata) return link_wikidata;

    // Parse as JSON array
    const jvalue = parse_JsonArray(value);
    if (Array.isArray(jvalue)) {
        return jvalue.map(v => {
            const item = String(v).trim();
            const safeURL = escape_HTML(item);
            return item.startsWith('http')
                ? `<a href="${safeURL}" target="_blank">${safeURL}</a>`
                : escape_HTML(item);
        }).join(', ');
    }

    return escape_HTML(str);
}


/**
 * Used only for JSON files, for map places 
 * Extracts coodinate Key names and values from cas-insensitive variants of
 * lat/lon, lat/lng and latitude/longitude (so, even LAT/LON is accepted) 
 * @param {*} obj : JSON content (as object, or associative array)
 * @returns Coordinates with the Key names lat and lon and their value
 */
function extractLatLon_NotUsed(obj) {
    const keys = Object.keys(obj).reduce((acc, key) => {
        const lower = key.toLowerCase();
        if (["lat", "latitude", "representative_latitude"].includes(lower)) acc.lat = key;
        if (["lon", "lng", "long", "longitude", "representative_longitude"].includes(lower)) acc.lon = key;
        return acc;
    }, {});

    if (obj[keys.lat] && obj[keys.lon]) {
        return [parseFloat(obj[keys.lat]), parseFloat(obj[keys.lon])];
    }
    return null;
}

let index_MarkerColor = 1;
/**
 * Loops between alternative marker colors
 * index_MarkerColor starts from 1 to use 0 (red color) for special purposes
 * @param {*} action
 * : empty (default = null) for looping to next color, 
 * : "reset" to reset the index
 * : integer for a sepecific color index
 * @returns 
 */
function get_MarkerColors(action = null) {
    const colorNames = [
        "red", "green", "purple", "magenta", "indigo", "orange",
        "yellow", "lime", "maroon", "navy", "olive", "coral"
    ];

    function getColorName(index) {
        return colorNames[index % colorNames.length];
    }

    function getIcon(index) {
        const color = getColorName(index);
        return L.icon({
            iconUrl: `${marker_IconsFolder}/ps_marker_${color}.svg`,
            iconSize: [38, 38],
            iconAnchor: [19, 52],
            popupAnchor: [0, -52]
        });
    }

    function registerLoad() {
        index_MarkerColor++;
        return getIcon(index_MarkerColor - 1);
    }

    function reset() {
        index_MarkerColor = 1;
    }
    if (action === 'reset') {
        reset();
    } else if (Number.isInteger(action)) {
        return getIcon(action);
    } else {
        return registerLoad();
    }
};

/**
 * ===================================
 * Create legend for number of places and areas
 */
let areasCount = 0;
let placesCount = 0;

const info_Legend = L.control({ position: 'bottomleft' });

info_Legend.onAdd = function (map) {
    this._div = L.DomUtil.create('div', 'info_legend');
    this.update();
    return this._div;
};

info_Legend.update = function (places_Count = 0, areas_Count = 0) {
    this._div.innerHTML = `
        <strong>Last Data</strong><br>
        Areas: ${areas_Count}<br>
        Places: ${places_Count}
    `;
    areasCount = areas_Count;
    placesCount = places_Count;
};

info_Legend.addTo(map);


/**
 * ===================================
 * Create a Legend Control Box for Map Areas
 */

const open_MapLayers = [];
const map_Manager = L.control({ position: 'bottomleft' });

map_Manager.onAdd = function (map) {
    this._div = L.DomUtil.create('div', 'layer_legends');
    this.update();
    return this._div;
};

map_Manager.update = function () {
    this._div.innerHTML = `<strong>Loaded Files</strong><br>` +
        open_MapLayers.map((entry, i) => {
            const checked = entry.visible ? "checked" : "";
            return `
            <button title="Zoom to map layer boundaries" class="legend_button" data-zoom="zoom" data-index="${i}">z</button>
            <label> <input title="Format: ${entry.format}, Type: ${entry.type}" type="checkbox" data-index="${i}" ${checked}>
                ${escape_HTML(entry.name)}
            </label><br>`;
        }).join("");
};
map_Manager.addTo(map);

// Show/Hide open layers
map_Manager._div.addEventListener("change", function (e) {
    const index = parseInt(e.target.dataset.index);
    const entry = open_MapLayers[index];
    if (entry.visible) {
        map.removeLayer(entry.layer);
    } else {
        map.addLayer(entry.layer);
    }
    entry.visible = !entry.visible;
});

// Zoom to an open layer

map_Manager._div.addEventListener("click", function (e) {
    if (!e.target.dataset.zoom) return;

    const index = parseInt(e.target.dataset.index);
    const entry = open_MapLayers[index];
    const targetBounds = entry.layer.getBounds();
    const targetCenter = targetBounds.getCenter();
    const targetZoom = map.getBoundsZoom(targetBounds);
    const currentZoom = map.getZoom();

    // Small zoom difference → just fly normally
    if (Math.abs(targetZoom - currentZoom) <= 2) {
        map.flyToBounds(targetBounds, { duration: 1.2 });
        return;
    }

    if (targetZoom > currentZoom) {
        // Zooming in: move to new center first, then zoom in
        map.flyTo(targetCenter, currentZoom, { duration: 0.8 });
        map.once('moveend', () => {
            map.flyTo(targetCenter, targetZoom, { duration: 1.2 });
        });
    } else {
        // Zooming out: zoom out first, then move to new center
        map.flyTo(map.getCenter(), targetZoom, { duration: 1.2 });
        map.once('moveend', () => {
            map.flyTo(targetCenter, targetZoom, { duration: 0.8 });
        });
    }
});



// Disable OSM click functions on legends
L.DomEvent.disableClickPropagation(map_Manager._div);
L.DomEvent.disableClickPropagation(info_Legend._div);

// Hide/Show Legends
document.getElementById('ToggleLegends').addEventListener('click', () => {
    const leftIcon = document.getElementById('sx_screen_to_left');
    const rightIcon = document.getElementById('sx_screen_to_right');
    const legends = document.querySelectorAll('.layer_legends, .info_legend');

    // Toggle SVG visibility
    const isLeftVisible = window.getComputedStyle(leftIcon).display !== 'none';
    leftIcon.style.display = isLeftVisible ? 'none' : 'inline';
    rightIcon.style.display = isLeftVisible ? 'inline' : 'none';

    // Toggle legend visibility
    legends.forEach(el => {
        el.style.display = (el.style.display === 'none') ? 'block' : 'none';
    });
});

// Reset select elements to default option, both or separately
function reset_SelectOption() {
    const json_Selector = document.getElementById("MapPlacesSelector");
    const geojson_Selector = document.getElementById("MapAreasSelector");
    if (json_Selector) json_Selector.selectedIndex = 0;
    if (geojson_Selector) geojson_Selector.selectedIndex = 0;
}
function reset_GeoJSON_SelectOption() {
    const geojson_Selector = document.getElementById("MapAreasSelector");
    if (geojson_Selector) geojson_Selector.selectedIndex = 0;
}
function reset_JSON_SelectOption() {
    const json_Selector = document.getElementById("MapPlacesSelector");
    if (json_Selector) json_Selector.selectedIndex = 0;
}

/**
 * Clears all loaded map places and map areas, if they exist
 */
document.getElementById("clearAllButton").addEventListener("click", () => {

    // Clear all local GeoJSON areas from the common group of areas
    loaded_ToMapLayersGroup.clearLayers();

    // Clean the legend for loaded map file areas
    open_MapLayers.length = 0;
    map_Manager.update();

    // Reset global variables and update the legend for counting places and areas
    placesCount = 0;
    areasCount = 0;
    info_Legend.update(0, 0);

    // Reset the marker color counter
    get_MarkerColors('reset');

    // Check and clear search system
    if (typeof clear_SearchSystem === "function") {
        clear_SearchSystem();
    }

    // Reset selectors to default option
    reset_SelectOption();

    // Zoon the wondow to places and map areas boundaries only with first load
    first_FileToLoad = true;

    const user_FileInput = document.getElementById('User_FileLoader');
    if (user_FileInput) user_FileInput.value = '';

    // Zoom to default set view after a delay
    setTimeout(() => {
        map.setView([default_Lat, default_Lon], default_Zoom);
    }, 300);

    // Reset and Hide select elements for filtering fields
    document.getElementById('bookFilter').innerHTML = '';
    document.getElementById('typeFilter').innerHTML = '';
    document.getElementById('regionFilter').innerHTML = '';

    document.getElementById('typeFilterContainer').style.display = 'none';
    document.getElementById('regionFilterContainer').style.display = 'none';
    document.getElementById('bookFilterContainer').style.display = 'none';

    positionMap();

});

function bind_Popups(layer) {
    layer.eachLayer(function (featureLayer) {
        const props = featureLayer.feature.properties;
        if (props) {
            featureLayer.bindPopup(sanitize_PopupContent(props));
        }
    });
}

function unbind_Popups(layer) {
    layer.eachLayer(function (featureLayer) {
        featureLayer.unbindPopup();
    });
}

function bind_PopupsToGroup(group) {
    group.eachLayer(function (layer) {
        if (!layer) return;

        // Handle individual places with feature properties
        if (layer instanceof L.Marker && layer.feature) {
            const content = sanitize_PopupContent(layer.feature.properties);
            layer.bindPopup(content);
        }

        // Handle GeoJSON areas
        else if (layer instanceof L.GeoJSON) {
            bind_Popups(layer);
        }

        // Handle nested groups recursively
        else if (layer instanceof L.LayerGroup || layer instanceof L.FeatureGroup) {
            if (typeof layer.eachLayer === 'function') {
                bind_PopupsToGroup(layer); // Recursive call
            }
        }

        // Handle vector areas (polygons, polylines) with feature properties
        else if (layer.feature && layer.feature.properties) {
            const content = sanitize_PopupContent(layer.feature.properties);
            layer.bindPopup(content);
        }
    });
}

function unbind_PopupsFromGroup(group) {
    group.eachLayer(function (layer) {
        if (!layer) return;

        // Unbind from places
        if (layer instanceof L.Marker) {
            layer.unbindPopup();
        }

        // Unbind from GeoJSON areas
        else if (layer instanceof L.GeoJSON) {
            unbind_Popups(layer);
        }

        // Unbind from nested groups recursively
        else if (layer instanceof L.LayerGroup || layer instanceof L.FeatureGroup) {
            if (typeof layer.eachLayer === 'function') {
                unbind_PopupsFromGroup(layer); // Recursive call
            }
        }

        // Unbind from vector areas
        else if (typeof layer.unbindPopup === 'function') {
            layer.unbindPopup();
        }
    });
}

// Checkbox listener to toggle popups across all areas
let show_FeaturePropertie = document.getElementById("ShowProperties").checked;

document.getElementById("ShowProperties").addEventListener("change", function () {
    show_FeaturePropertie = this.checked;
    if (loaded_ToMapLayersGroup) {
        if (show_FeaturePropertie) {
            bind_PopupsToGroup(loaded_ToMapLayersGroup);
        } else {
            unbind_PopupsFromGroup(loaded_ToMapLayersGroup);
        }
    }
});

// Deals with the four formats of places: ["aaa", "bbb"], ['aaa', 'bbb'], "aaa, bbb", and "aaa"
// Returns always "aaa"
const get_PlaceType = (props) => {
    let rawType = props.Type || props.type;
    if (!rawType) return null;
    try {
        let parsed;
        if (typeof rawType === 'string') {
            // Normalize single-quoted JSON-like strings
            const normalized = rawType.replace(/'/g, '"');
            parsed = JSON.parse(normalized);
        } else {
            // A single string or already parsed JSON (e.g. array or object)
            parsed = rawType;
        }
        // Returns either the string or the first index value
        return Array.isArray(parsed) ? parsed[0] : parsed;

    } catch (error) {
        if (typeof rawType === 'string') {
            if (rawType.includes(',')) {
                return rawType.split(',')[0].trim();
            }
            return rawType.trim();
        }

        return rawType;
    }
};

const get_CustomIcon = (place_type) => {
    const icon_Path = place_Icons[place_type] || null;
    if (icon_Path) {
        return L.icon({
            iconUrl: maps_ParentFolder + '/' + icon_Path,
            iconSize: [40, 40],
            iconAnchor: [20, 20],
            popupAnchor: [0, -24]
        });
    } else {
        return null;
    }
};


function createClusterGroup(radius) {
    return L.markerClusterGroup({
        maxClusterRadius: radius,
        spiderfyOnMaxZoom: true,
        zoomToBoundsOnClick: true
    });
}

function getClusterRadiusByZoom(zoom) {
    if (zoom <= 6) return 60;
    if (zoom <= 10) return 30;
    return 20;
}

map.on('zoomend', () => {
    const zoom = map.getZoom();
    const newRadius = getClusterRadiusByZoom(zoom);

    open_MapLayers.forEach(entry => {
        if (entry.cluster) {
            const geoLayer = entry.cluster.getLayers()[0]; // assuming one geoLayer per cluster
            map.removeLayer(entry.cluster);
            const newCluster = createClusterGroup(newRadius);
            newCluster.addLayer(geoLayer);
            map.addLayer(newCluster);
            entry.cluster = newCluster;
        }
    });
});

function handle_LayerPopupHover(layerGroup, name, format) {
    const marker_Color = get_MarkerColors();
    let count_Places = 0;
    let geometryType = "Areas";

    // Cluster groups with dynamic radius based on current zoom
    const zoom = map.getZoom();
    const cluster_Radius = getClusterRadiusByZoom(zoom);
    const marker_ClusterGroup = createClusterGroup(cluster_Radius);

    const normalized_GeoJSON = normalize_GeometryCollections(layerGroup);

    // Merge identical (duplicate) Polygons
    const deduped = dedupe_IdenticalPolygons(normalized_GeoJSON);

    let isGeoLine = false;
    const geoLayer = L.geoJSON(deduped, {

        pointToLayer: function (feature, latlng) {
            let icon = null;
            if (typeof place_Icons === 'object' && place_Icons !== null) {
                const place_type = get_PlaceType(feature.properties);

                if (place_type !== null && place_type !== 'NULL' && place_type !== 'unknown') {
                    icon = get_CustomIcon(place_type);
                    if (!icon) {
                        console.warn(`No custom icon found for place type: ${place_type}`);
                    }
                }
            }
            if (!icon) {
                icon = marker_Color;
            }
            count_Places++;
            return L.marker(latlng, { icon });
        },
        onEachFeature: function (feature, layer) {
            const geoType = layer.feature?.geometry?.type;
            //console.log('geoType: ', geoType)

            if (geoType === "Point") {
                geometryType = "Places";
            } else {
                if (geoType === "Line" || geoType === "LineString" || geoType === "MultiLineString" || format === 'gpx') {
                    isGeoLine = true;
                    base_UsedStyle = base_LineStyle;
                    hover_UsedStyle = hover_LineStyle
                    layer.setStyle(base_UsedStyle);

                    // Create a transparent wide stroke for hit detection
                    const hitLayer = L.geoJSON(feature, {
                        style: {
                            color: "#000000",
                            weight: 15,
                            opacity: 0,
                            fillOpacity: 0,
                        },
                        interactive: true,
                        bubblingMouseEvents: false,
                        onEachFeature: (f, hitL) => {
                            hitL.on("mouseover", () => layer.fire("mouseover"));
                            hitL.on("mouseout", () => layer.fire("mouseout"));
                            hitL.on("click", (e) => layer.fire("click", e));
                        }
                    });

                    hitLayer.addTo(map);
                } else {
                    base_UsedStyle = base_PolygonStyle;
                    hover_UsedStyle = hover_PolygonStyle
                    if (isGeoLine === false) {
                        layer.setStyle(base_UsedStyle);
                    } else {
                        layer.setStyle(base_LineStyle);
                    }
                }

                layer.on("mouseover", () => layer.setStyle(hover_UsedStyle));
                layer.on("mouseout", () => layer.setStyle(base_UsedStyle));
            }

            if (show_FeaturePropertie) {

                // Clean properties of merged identical (duplicate) Polygons
                const cleanMergedProps = normalize_MergedFeatureProps(feature.properties);

                //layer.bindPopup(sanitize_PopupContent(feature.properties));
                layer.bindPopup(sanitize_PopupContent(cleanMergedProps));
            }
        },
        style: base_UsedStyle
    });

    // Add geoLayer to cluster group (instead of adding to local group)
    marker_ClusterGroup.addLayer(geoLayer);

    // Create local group and add cluster group
    const local_Group = L.featureGroup().addTo(map);
    local_Group.addLayer(marker_ClusterGroup);

    if (!use_Stacking) {
        loaded_ToMapLayersGroup.clearLayers();
        open_MapLayers.length = 0;
    }

    // Add the local group to the parent group
    loaded_ToMapLayersGroup.addLayer(local_Group);
    open_MapLayers.push({
        name,
        layer: local_Group,
        format,
        type: geometryType,
        visible: true
    });

    map_Manager.update();
    if (first_FileToLoad) {
        map.fitBounds(marker_ClusterGroup.getBounds());
        first_FileToLoad = false;
    }

    const count_Areas = geoLayer.getLayers().filter(l => typeof l.setStyle === "function").length;
    placesCount = count_Places;
    areasCount = count_Areas;
    info_Legend.update(placesCount, areasCount);

    // Close eventually open panels
    if (close_Panels !== undefined) {
        close_Panels();
    }
}

function normalize_GeometryCollections(layerGroup) {
    const geojson = layerGroup.toGeoJSON();

    const normalizedFeatures = geojson.features.flatMap((feature) => {
        if (feature.geometry?.type === "GeometryCollection") {
            const geometries = feature.geometry.geometries;

            const polygons = geometries.filter(g => g.type === "Polygon").map(g => g.coordinates);
            const others = geometries.filter(g => g.type !== "Polygon");

            const result = [];

            if (polygons.length > 0) {
                result.push({
                    type: "Feature",
                    geometry: {
                        type: "MultiPolygon",
                        coordinates: polygons
                    },
                    properties: feature.properties
                });
            }

            others.forEach((geom) => {
                result.push({
                    type: "Feature",
                    geometry: geom,
                    properties: feature.properties
                });
            });

            return result;
        }

        return feature; // unchanged
    });

    return {
        type: "FeatureCollection",
        features: normalizedFeatures
    };
}


// Handle multiple polygons with identical layers
function dedupe_IdenticalPolygons(geojson) {
    const seen = {};
    const result = [];

    geojson.features.forEach(f => {
        const hash = JSON.stringify(f.geometry);
        const g = f.geometry;

        if (!g || g.type === "Point") {
            // Keep Point geometry untouched
            result.push(f);
            return;
        }

        if (!seen[hash]) {
            // First time seeing this geometry
            seen[hash] = {
                feature: f,
                entries: [f.properties]

            };
            result.push(f);
        } else {
            // Duplicate geometry → push its properties
            seen[hash].entries.push(f.properties);

            // Tag the first feature as merged
            const first = seen[hash].feature;
            first.properties.merged_entries = seen[hash].entries;
        }
    });

    return result;
}

function normalize_MergedFeatureProps(props) {
    const p = { ...props };
    if (Array.isArray(p.merged_entries)) {
        p._mergedCount = p.merged_entries.length;
        delete p.merged_entries;
    }
    return p;
}
