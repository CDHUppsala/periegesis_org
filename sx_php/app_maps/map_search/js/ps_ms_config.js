
/**
 * ========================================================
 * Public Sphere 2025-12
 * FILE: ps_ms_config.js
 * Basic Configurations of Global variables
 * ========================================================
 */

// Default folder paths
const maps_ParentFolder = '../imgMedia/maps';

const maps_AreasFolder = `${maps_ParentFolder}/map_areas`;
const maps_PlacesFolder = `${maps_ParentFolder}/map_places`;

// List of Custom icon markers
const custom_iconsFile = `${maps_ParentFolder}/icons/place_icons.json?v=2025-10-26`;

// Default application icon markers, as colored SVG images
const marker_IconsFolder = `${maps_ParentFolder}/sx_icons`;
const search_MarkerIcon = `${maps_ParentFolder}/sx_icons/ps_marker_red.svg`;



// ==========================================
// GLOBAL VARIABLE RELATED:
//  1. to addoptation to specific requirewment of a prject
//  2. to design of map layers
// =========================================

// Global array with the three key name for filtering that are actually used in the loaded file
// Must be reset with every loading of a file, to allow file stacking
// File search/filtering is pursued only on the last loaded file
let keyMap = {};

// Multiple possible aliases of field/property names for the 3 filtering keys.
// Actual values for loaded file are saved in keyMap
const key_Aliases = {
    type: ['type', 'place_type', 'work_type', 'featureTypes', 'city', 'name', '2001deme'],
    region: ['region', '[AnimationClock] Location Reference', 'timePeriodsRange', 'name_en', '2025demosen'],
    passages: ['passages', 'bookid', 'identifier']
};

// Semantic values of the filtering key "type", above, can be given explicit styling. 
const featured_TypesMap = [
    "chora/region",
    "forest",
    "hieron",
    "mound/dike",
    "mythic/historic site",
    "paraplous",
    "plain",
    "river",
    "road hodos",
    "road leophoros",
    "rock",
    "tomb mnema",
    "tomb taphos",
    "trophy",
    "valley"
];



// Point geometry uses markers
// Define here the default styling values for lines and polygons
const style_Geometries = {
    point: null, // handled via marker, not style

    line: {
        base: {
            color: 'blue',
            weight: 4,
            opacity: 0.85,
            fillOpacity: 0
        },
        hover: {
            color: '#ff0000',
            weight: 5,
            opacity: 0.85,
            fillOpacity: 0
        }
    },

    polygon: {
        base: {
            color: 'green',
            weight: 1.5,
            opacity: 0.65,
            fillOpacity: 0.1
        },
        hover: {
            color: 'brown',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.05
        }
    }
};


// Define here the style for features types (see above)
const style_FeaturedTypes = {

    // REGIONS / AREAS
    "chora/region": {
        base: { color: "#6b8f5a", weight: 2, fillOpacity: 0.15 },
        hover: { color: "#4f6f3f", weight: 3, fillOpacity: 0.20 }
    },

    "mound/dike": {
        base: { color: "#8a8f5c", weight: 3, fillOpacity: 0.15 },
        hover: { color: "#6f7346", weight: 3, fillOpacity: 0.25 }
    },

    plain: {
        base: { color: "#97813eff", weight: 2, fillOpacity: 0.15 },
        hover: { color: "#d2b86a", weight: 3, fillOpacity: 0.20 }
    },

    valley: {
        base: { color: "#5f9ea0", weight: 2, fillOpacity: 0.2 },
        hover: { color: "#3f7f82", weight: 2, fillOpacity: 0.3 }
    },

    forest: {
        base: { color: "#2e7d32", weight: 2, fillOpacity: 0.15 },
        hover: { color: "#388e3c", weight: 3, fillOpacity: 0.20 }
    },

    rock: {
        base: { color: "#7a7a7a", weight: 2, fillOpacity: 0.28 },
        hover: { color: "#5f5f5f", weight: 3, fillOpacity: 0.38 }
    },

    river: {
        base: { color: "#2b7cd3", weight: 3, opacity: 0.9 },
        hover: { color: "#4aa3ff", weight: 3, opacity: 1 }
    },

    paraplous: {
        base: { color: "#1aa6b7", weight: 4, dashArray: "6,4", opacity: 0.9 },
        hover: { color: "#3ccde3", weight: 5, dashArray: "6,4", opacity: 1 }
    },

    "road hodos": {
        base: { color: "#8b5a2b", weight: 4 },
        hover: { color: "#b8793a", weight: 6 }
    },

    "road leophoros": {
        base: { color: "#d17c00", weight: 4 },
        hover: { color: "#ffa133", weight: 6 }
    },

    hieron: {
        base: { color: "#6a3fa0", weight: 4, fillOpacity: 0.15 },
        hover: { color: "#8b5fd3", weight: 6, fillOpacity: 0.20 }
    },

    "mythic/historic site": {
        base: { color: "#b64032", weight: 2, fillOpacity: 0.15 },
        hover: { color: "#e05c4c", weight: 3, fillOpacity: 0.20 }
    },

    trophy: {
        base: { color: "#9c7a1a", weight: 3, fillOpacity: 0.35 },
        hover: { color: "#c9a23a", weight: 3, fillOpacity: 0.45 }
    },

    "tomb mnema": {
        base: { color: "#6b5c50", weight: 3, fillOpacity: 0.3 },
        hover: { color: "#8b7a6a", weight: 3, fillOpacity: 0.4 }
    },

    "tomb taphos": {
        base: { color: "#4e4a47", weight: 3, fillOpacity: 0.3 },
        hover: { color: "#6a6561", weight: 3, fillOpacity: 0.4 }
    }
};



//  ========================================================
//  Global variables and functions
//  ========================================================

// Get map metadata from KML and GeoJson files, if any
let map_metadata = {};

// Check if custom icons for marking exists (else, default icons are used)
// If yes, save them as an array of objects.
let place_Icons = {};
fetch(custom_iconsFile)
    .then(response => {
        if (!response.ok) {
            //throw new Error(`File not found or inaccessible: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        place_Icons = data;
    })
    .catch(error => {
        //console.warn("Could not load place icons file:", error.message);
    });



// ============================
// Detect Lat/Lon fields: headers is an object - or set array
// ============================
function detect_LatLonFields(headers) {
    const normalized = headers.map(h => h.trim());

    const latCandidates = [
        'lat', 'latitude', 'representative_latitude', 'reprLat'
    ];
    const lonCandidates = [
        'lon', 'lng', 'long', 'longitude', 'representative_longitude', 'reprLong'
    ];

    // First try exact match (case-insensitive)
    let latfield = normalized.find(h => latCandidates.some(c => c.toLowerCase() === h.toLowerCase())) || null;
    let lonfield = normalized.find(h => lonCandidates.some(c => c.toLowerCase() === h.toLowerCase())) || null;

    // If not found, fallback to flexible and dangerous regex search
    if (!latfield) latfield = normalized.find(h => /lat/i.test(h)) || null;
    if (!lonfield) lonfield = normalized.find(h => /lon|lng/i.test(h)) || null;

    return { latfield, lonfield };
}

// For debugging only
function console_log(layer) {
    layer.eachLayer(function (layer) {
        console.log('Geometry: ', layer.feature.geometry);
        console.log('Properties: ', layer.feature.properties);
    });

}
