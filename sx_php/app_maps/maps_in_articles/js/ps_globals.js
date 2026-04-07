
/**
 * ========================================================
 * Public Sphere 2025-12
 * FILE: ps_globals.js
 * Global function for Marker colors and for sanitizing Popup content
 * ========================================================
 */


/**
 * @param {*} obj : JSON content or GeoJSON feature.properties
 * @returns Sanitized strings for Key names and Key values
 *  - URL address are converted to clickable links
 *  - Set manual limits to the number of displayed properties
 */


function sanitize_PopupContent(obj) {
    if (!obj || typeof obj !== "object") return "";

    // Clone to avoid mutating original feature
    const clean = {};

    const is_JunkValue = v =>
        v == null ||  // null or undefined
        v === "" ||
        v === "Unnamed" ||
        String(v).toLowerCase() === "undefined" ||
        String(v).toLowerCase() === "null";

    Object.entries(obj).forEach(([key, value]) => {
        if (is_JunkValue(value)) return;
        clean[key] = value;
    });

    const entries = Object.entries(clean);
    const visibleEntries = entries.slice(0, 26);

    const headers = Object.values(visibleEntries).map(([key]) => key.trim());
    const { latfield, lonfield } = detect_LatLonFields(headers)
    let latValue = null;
    let lonValue = null;
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
            return `<strong>${safeKey}</strong>: <em>(empty)</em>`;
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

            if (latfield && lonfield) {
                if (k === lonfield) latValue = v;
                if (k === latfield) lonValue = v;
            }
        }
        return `<strong>${safeKey}</strong>: ${safeValue}`;

    })
        .filter(Boolean)
        .join("<br>");

    const remainingCount =
        entries.length - visibleEntries.length;
    const moreMessage =
        remainingCount > 0
            ? `<br><em>...and ${remainingCount} more properties</em>`
            : "";

    const copy_All = `<hr><button class="copy_PopupContent">Copy All</button>`;

    let copyLatLong = '';

    if (latValue && lonValue) {
        //copyLatLong = `<button onclick="copy_LatLong('${latValue}, ${lonValue}')">Copy LatLong</button>`;
        copyLatLong = `<button class="copy_PopupLatLong" data-LatLng="${latValue + ', ' + lonValue}">Copy LatLong</button>`;
    }

    return content + moreMessage + copy_All + copyLatLong;
}


// =====================================
// Helpers
// =====================================

// Check both key name and value structure to safeguard that 
// the referent is a book section of type number.number.number
function is_BookChapterSection(key, val) {
    const allowedKeys = ['passages', 'bookid', 'identifier'];
    if (!allowedKeys.includes(key.toLowerCase())) {
        return false;
    }
    const parts = val.split(".");
    return parts.length === 3 && parts.every(p => /^\d+$/.test(p));
}

function display_mergedProps(merged) {
    if (merged.length === 0) return;
    // Save the first objec as base for comparizon (is already displaye in popup)
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

    // Stop at the first closing bracket ']'
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

function escape_HTML(str) {
    return String(str).replace(/[&<>"']/g, s => ({
        "&": "&amp;", "<": "&lt;", ">": "&gt;",
        '"': "&quot;", "'": "&#39;"
    }[s]));
}


// Adapt to Periegesis Project: Creates links for every queried Book ID in an array:
//<span class="modal_click" title="Open Section in Modal Window" onclick="load_modal_html('${safeID}')">MW</span>;
function get_BookLinks(val) {
    const ids = parse_JsonArray(val);
    if (!Array.isArray(ids)) return val;
    return ids.map(id => {
        const safeID = escape_HTML(id);
        return `[<a title="Open section in Read Pausanias with Maps" href="map_periegesis.php?b=${encodeURIComponent(safeID)}" target="_blank">${safeID}</a>]`;
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

// To deal with property values structured as JSON, 
// containing multiple links from the same source
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
            let linkName = safeURL;
            if (linkName.length > 20) {
                linkName = '[Click Here]';
            }
            return item.startsWith('http')
                ? `<a href="${safeURL}" target="_blank">${linkName}</a>`
                : escape_HTML(item);
        }).join(', ');
    }

    if (!has_SpaceInFirstN(str, 40)) {
        return `<span style="word-break: break-all;">${str}</span>`;
    }

    return escape_HTML(str);
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
            iconUrl: custom_iconsFolder + '/' + icon_Path,
            iconSize: [40, 40],
            iconAnchor: [20, 20],
            popupAnchor: [0, -24]
        });
    } else {
        return null;
    }
};


/**
 * ===============================================
 * Helpers for styling non point geometries
 *================================================
 */


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
            //return "collection";
            return "Polygon";

        default:
            return null;
    }
}

function getFeatureType(feature) {
    if (!feature?.properties) return null;
    const sample_Props = feature?.properties || {};

    const key = get_ActualKey(sample_Props, 'type');

    if (!key) return null;
    const val = feature.properties[key];

    if (!val) return null;;

    const clean = String(val).toLowerCase().trim();

    if (key_TypeMap.includes(clean)) {
        return clean;
    }
    return null;
}
