

/**
 * ========================================================
 * Public Sphere 2025-12
 * FILE: ps_ms_mapPopupHover.js
 * FUNCTIONS FOR Hovering, Zooming and Popup content
 * Applied for all geometry types: Points, Lines and Polygons
 * ========================================================
 */



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

    // Convert entries to popup list 
    const content = visibleEntries.map(([k, v]) => {

        if (k === "merged_entries") {
            return '<em><strong>' + v.length + ' Merged Entries</strong> with identical geometry have the following ' +
                'differences from the above first entry:</em><br>' +
                '<div class="max-popup-height">' +
                display_mergedProps(v) +
                '</div>';
        }

        const safeKey = escape_HTML(k);

        if (v == null || v === '') {
            //return `<strong>${safeKey}</strong>: <em>(empty)</em>`;
            return
        }

        let safeValue = v;
        // For Pausanias' digital periegesis
        if (is_BookChapterSection(k, v)) {
            safeValue = get_BookLinks(safeValue);
        } else {

            // Shorten geometry values in popup
            if (is_Field_WKT(k, v) || is_EmbeddedGeoJSON(v)) {
                safeValue = shorten_GeometryValue(safeValue);
            } else {
                safeValue = format_ValueToLink(safeValue);
            }
        }
        return `<strong>${safeKey}</strong>: ${safeValue}`;
    })
        .filter(Boolean)
        .join("<br>");


    const remainingCount = entries.length - visibleEntries.length;
    const moreMessage =
        remainingCount > 0
            ? `<br><em>...and ${remainingCount} more properties</em>`
            : "";

    const copy_All = `<hr><button onclick="copy_Popup(this)">Copy All</button>`;

    let copy_LatLong = '';
    const headers = Object.values(visibleEntries).map(([key]) => key.trim());
    const { latfield, lonfield } = detect_LatLonFields(headers)

    if (latfield && lonfield) {
        const latValue = Object.values(visibleEntries).find(([k]) => k === latfield)?.[1] ?? null;
        const lonValue = Object.values(visibleEntries).find(([k]) => k === lonfield)?.[1] ?? null;

        if (latValue && lonValue) {
            copy_LatLong = `<button onclick="copy_LatLong('${latValue}, ${lonValue}')">Copy LatLong</button>`;
        }
    }

    return content + content_meta + moreMessage + copy_All + copy_LatLong;
}


// ================================
// Helpers
// ================================

// Check both key name and value structure to safeguard that 
// the referent is a book section of type number.number.number
function is_BookChapterSection_byKeyName(key, val) {
    const allowedKeys = ['passages', 'bookid', 'identifier'];
    if (!allowedKeys.includes(key.toLowerCase())) {
        return false;
    } else {
        return true;
    }
}

function is_BookChapterSection(key, val) {
    const allowedKeys = ['passages', 'bookid', 'identifier'];
    if (!allowedKeys.includes(key.toLowerCase())) {
        return false;
    }
    //console.log(val)

    let firstValue;

    // Case 1: Already an array
    if (Array.isArray(val)) {
        firstValue = val[0];
    }

    // Case 2: A string that might contain multiple IDs or be a single ID
    else if (typeof val === 'string') {
        // Try to detect if it's a JSON array string
        const trimmed = val.trim();

        if (trimmed.startsWith('[') && trimmed.endsWith(']')) {
            try {
                const parsed = JSON.parse(trimmed);
                if (Array.isArray(parsed) && parsed.length > 0) {
                    firstValue = parsed[0];
                }
            } catch (e) {
                return false;
            }
        } else {
            // Plain string, possibly containing commas
            firstValue = trimmed.split(",")[0].trim();
        }
    }

    // Unsupported type
    else {
        return false;
    }

    // Ensure we have a string now
    if (typeof firstValue !== 'string') {
        return false;
    }

    // Minimal structural check: number.number.number
    const pattern = /^\d+\.\d+\.\d+$/;

    return pattern.test(firstValue.trim());
}

function display_mergedProps(merged) {
    if (merged.length === 0) return;
    // Save the first objec as base for comparizon
    const first = merged[0]; // baseline object
    let returnStr = '';
    // Loop through all other objects
    for (let i = 1; i < merged.length; i++) {
        const element = merged[i];
        Object.entries(element).forEach(([key, value]) => {
            if (value !== first[key]) {
                if (is_BookChapterSection(key, value)) {
                    safeValue = get_BookLinks(value);
                    returnStr += `<b>${key}</b>: ${safeValue}<br>`;
                } else {
                    returnStr += `<b>${key}</b>: ${value}<br>`;
                }
            }
        });
        returnStr += '<br>';
    }
    return returnStr;
}

// Shorten the value of WKT Field Names in  popups 
function is_Field_WKT_exact_NotUsed(field) {
    const candidates = ['geometry_wkt', 'geometry', 'wkt', 'geom'];
    return candidates.includes(field.toLowerCase());
}

function is_Field_WKT_part_NotUsed(field) {
    const candidates = ['geometry_wkt', 'geometry', 'wkt', 'geom'];
    const f = field.toLowerCase();
    return candidates.some(c => f.includes(c));
}

function is_Field_WKT(field, value) {
    const patterns = [
        /(?:^|[\s_\-\[\]])geometry_wkt(?:$|[\s_\-\[\]])/i,
        /(?:^|[\s_\-\[\]])geometry(?:$|[\s_\-\[\]])/i,
        /(?:^|[\s_\-\[\]])wkt(?:$|[\s_\-\[\]])/i,
        /(?:^|[\s_\-\[\]])geom(?:$|[\s_\-\[\]])/i
    ];

    // 1) Name-based detection
    if (patterns.some(p => p.test(field))) {
        return true;
    }

    // 2) Content-based detection
    if (typeof value === "string" && is_ContentWKT(value)) {
        return true;
    }

    return false;
}

// Check for long values, whitout space
function has_SpaceInFirstN(str, n = 32) {
    return str.slice(0, n).includes(' ');
}

// Show geometries in popups up to the first "]".
function shorten_GeometryValue(value) {
    const str = String(value);

    // Stop geometries at the first closing bracket ']'
    // Do not include ,... for Point geomentry
    const idx = str.indexOf(']');
    if (idx !== -1) {
        let r_value = str.substring(0, idx + 1);
        if (r_value.includes('[[')) {
            r_value = r_value + ', ...';
        }
        return r_value;
    }

    // Fallback: limit to 80 chars
    return str.length > 80 ? str.substring(0, 80) + '...' : str;
}

function copy_Popup(button) {
    // clone keeping DOM - to enable removing buttons and other elements
    const parent = button.parentElement.cloneNode(true);
    const btn = parent.querySelectorAll("button");
    btn.forEach(b => b.remove());

    // Remove all <span class="modal_click"> elements, if anny
    const spans = parent.querySelectorAll("span.modal_click");
    spans.forEach(span => span.remove());

    // Replace <br> tags with newline characters
    parent.innerHTML = parent.innerHTML.replace(/<br\s*\/?>/gi, "\n");

    const content = parent.innerText;

    navigator.clipboard.writeText(content)
        .then(() => {
            alert("Popup content copied:\n" + content);
        })
        .catch(err => {
            alert("Failed to copy: ", err);
        });
}

function copy_LatLong(str) {
    navigator.clipboard.writeText(str)
        .then(() => {
            alert("Copied to clipboard: \n" + str);
        })
        .catch(err => {
            alert("Failed to copy: ", err);
        });
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
    if (!Array.isArray(ids)) return val;
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
            if (normalized.startsWith('[') && normalized.includes('"')) {
                return JSON.parse(normalized);
            }
            return normalized.split(',').map(s => s.trim());
        }
        return [String(val)];
    } catch (e) {
        console.warn('Failed to parse value ' + val + ':', e);
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
    if (Array.isArray(jvalue) && jvalue.length) {
        return jvalue.map(v => {
            const item = String(v).trim();
            const safeURL = escape_HTML(item);
            return item.startsWith('http')
                ? `<a href="${safeURL}" target="_blank">${safeURL}</a>`
                : escape_HTML(item);
        }).join(', ');
    }

    if (!has_SpaceInFirstN(str, 40)) {
        return `<span style="word-break: break-all;">${str}</span>`;
    }

    return escape_HTML(str);
}


/**
 * =========================================================================
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


// Reset select elements to default option, both or separately
function reset_SelectOption() {
    const place_Selector = document.getElementById("MapPlacesSelector");
    const area_Selector = document.getElementById("MapAreasSelector");
    if (place_Selector) place_Selector.selectedIndex = 0;
    if (area_Selector) area_Selector.selectedIndex = 0;
}
function reset_Areas_SelectOption() {
    const area_Selector = document.getElementById("MapAreasSelector");
    if (area_Selector) area_Selector.selectedIndex = 0;
}
function reset_Places_SelectOption() {
    const place_Selector = document.getElementById("MapPlacesSelector");
    if (place_Selector) place_Selector.selectedIndex = 0;
}

function bind_Popups(layer) {
    layer.eachLayer(function (featureLayer) {
        const props = featureLayer.feature.properties;
        if (props) {
            featureLayer.bindPopup(sanitize_PopupContent(props));
        }
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
            layer.eachLayer(function (featureLayer) {
                featureLayer.unbindPopup();
            });
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

let show_FeaturePropertie = !document.getElementById("HideProperties").checked;

document.getElementById("HideProperties").addEventListener("change", function () {
    show_FeaturePropertie = !this.checked;

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

function getGeometryGroup(type) {
    switch (type) {
        case "Point":
        case "MultiPoint":
            return "point";

        case "LineString":
        case "MultiLineString":
            return "line";

        case "Polygon":
        case "MultiPolygon":
            return "polygon";

        case "GeometryCollection":
            return "Polygon";

        default:
            return null;
    }
}

function createHitLayer(targetLayer) {

    const hitLayer = L.polyline(targetLayer.getLatLngs(), {
        weight: 15,
        opacity: 0,
        interactive: true
    });

    hitLayer.on("mouseover", () => {
        targetLayer.fire("mouseover");
        targetLayer.bringToFront();
    });

    hitLayer.on("mouseout", () => {
        targetLayer.fire("mouseout");
    });

    hitLayer.on("click", (e) => {
        const popup = targetLayer.getPopup();
        if (popup) {
            popup.setLatLng(e.latlng);
            targetLayer.openPopup();
        }
    });

    return hitLayer;
}

function getFeatureType(feature) {
    if (!feature?.properties) return null;
    const key = keyMap?.type;
    if (!key) return null;
    const val = feature.properties[key];
    if (!val) return null;;

    const clean = String(val).toLowerCase().trim();

    if (featured_TypesMap.includes(clean)) {
        return clean;
    }
    return null;
}

const semanticTypes = {};
let check_semanticTypes = true;
/**
 * Creates and updated Legends, with download information, and popups and hovering functions
 * @param {obj} layerGroup : the leaflet object
 * @param {str} file_Name : the name of the loaded map file, the original or the filtered
 * @param {str} format : embeded, wkt, and all supported map file formats (geojson, kml, topojson, gxp, csv). I set automatically
 * @param {boolean} is_Filter : true/false
 * @param {str} source_FileName : empty string or the original file name after filtering, add as suffix to the filtered file_Name
 */
function handle_LayerPopupHover(layerGroup, file_Name, format, is_Filter = false, source_FileName = '') {

    const marker_Color = get_MarkerColors();
    let count_Places = 0;
    let geometryType = "Areas";

    // Cluster groups with dynamic radius based on current zoom
    const zoom = map.getZoom();
    const cluster_Radius = getClusterRadiusByZoom(zoom);
    const marker_ClusterGroup = createClusterGroup(cluster_Radius);

    // Normalize and Merge identical (duplicate) Polygons
    const normalized_GeoJSON = normalize_GeometryCollections(layerGroup);
    const cleaned_GeoJson = clean_IdenticalPolygons(normalized_GeoJSON);
    const geoLayer = L.geoJSON(cleaned_GeoJson, {

        pointToLayer: function (feature, latlng) {
            let icon = null;
            if (typeof place_Icons === 'object' && place_Icons !== null) {
                const place_type = get_PlaceType(feature.properties);

                if (place_type && place_type !== 'NULL' && place_type !== 'unknown') {
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

            const geoGroup = getGeometryGroup(feature.geometry?.type);

            // POINTS
            if (geoGroup === "point") {
                geometryType = "Places";
                layer.bindPopup(sanitize_PopupContent(feature.properties));
                return;
            }

            // Lines and Polygons
            // Decide style source
            let styleSet = null;
            const featuredType = getFeatureType(feature);

            if (featuredType && style_FeaturedTypes[featuredType]) {
                styleSet = style_FeaturedTypes[featuredType];

                if (check_semanticTypes && !semanticTypes[featuredType]) {
                    semanticTypes[featuredType] = style_FeaturedTypes[featuredType].base.color;
                }
            } else if (geoGroup && style_Geometries[geoGroup]) {
                styleSet = style_Geometries[geoGroup];
            }

            if (!styleSet) return;

            // Apply style
            layer.setStyle(styleSet.base);


                if (geoGroup === "line") {
                    createHitLayer(layer).addTo(map);
                }



            // Hover (works for polygons and lines)
            layer.on("mouseover", () => {
                layer.bringToFront();
                layer.setStyle(styleSet.hover);
            });
            layer.on("mouseout", () => {
                layer.setStyle(styleSet.base);
            });

            // Popup works for polygons and lines
            layer.bindPopup(sanitize_PopupContent(feature.properties));
        }


    });

    // Add geoLayer to cluster group (instead of adding to local group)
    marker_ClusterGroup.addLayer(geoLayer);

    // Create local group and add cluster group
    const local_Group = L.featureGroup().addTo(map);
    local_Group.addLayer(marker_ClusterGroup);

    if (!use_Stacking && !is_Filter) {
        loaded_ToMapLayersGroup.clearLayers();
        open_MapLayers.length = 0;
    }

    // Add the local group to the parent group
    loaded_ToMapLayersGroup.addLayer(local_Group);

    if (!show_FeaturePropertie) {
        unbind_PopupsFromGroup(loaded_ToMapLayersGroup);
    }


    // Close previous visible layers
    open_MapLayers.forEach(entry => {
        entry.visible = false;
        if (entry.layer && map.hasLayer(entry.layer)) {
            map.removeLayer(entry.layer);
        }
    });
    map_Manager.update();

    const count_Areas = geoLayer.getLayers().filter(l => typeof l.setStyle === "function").length;
    placesCount = count_Places;
    areasCount = count_Areas;
    //info_Legend.update(placesCount, areasCount);

    open_MapLayers.push({
        name: file_Name,
        data: cleaned_GeoJson,
        layer: local_Group,
        format, format,
        type: geometryType,
        visible: true,
        filter: is_Filter,
        sourceFileName: source_FileName,
        dataCount: areasCount + '/' + placesCount
    });

    map_Manager.update();

    if (first_FileToLoad) {
        map.fitBounds(marker_ClusterGroup.getBounds());
        first_FileToLoad = false;
    }

    // Close eventually open panels
    if (close_Panels !== undefined) {
        close_Panels();
    }

    if (check_semanticTypes && Object.keys(semanticTypes).length > 0) {
        const sortedSemanticTypes = Object.fromEntries(
            Object.entries(semanticTypes)
                .sort(([a], [b]) => a.localeCompare(b))
        );
        const select = document.getElementById("TypeFilter_2");
        select.style.backgroundColor = '#aaffaa';
        select.innerHTML = '<option value="">Featured Types</option>';
        Object.entries(sortedSemanticTypes).forEach(([type, color]) => {
            const option = document.createElement("option");
            option.value = type;
            option.textContent = type;
            option.style.backgroundColor = color;
            option.style.color = '#fff';
            select.appendChild(option);
        });

    }
    check_semanticTypes = false;
}

// Converts GeometryCollection to polygons
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

        return feature;
    });

    return {
        type: "FeatureCollection",
        features: normalizedFeatures
    };
}


// Handle multiple polygons with identical layers
function clean_IdenticalPolygons(geojson) {
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
            // Duplicate geometry - push its properties
            seen[hash].entries.push(f.properties);

            // Tag the first feature as merged
            const first = seen[hash].feature;
            first.properties.merged_entries = seen[hash].entries;
        }
    });

    return result;
}
