
/**
 * ========================================================
 * Public Sphere 2025-12
 * FILE: ps_config.js
 * Basic Configurations
 * ========================================================
 */

// Global variables
const maps_ParentFolder = '../imgMedia';

const maps_AreasFolder = `${maps_ParentFolder}`;
const maps_PlacesFolder = `${maps_ParentFolder}`;

// List of Custom icon markers
const custom_iconsFolder = `${maps_ParentFolder}/maps/`;
const custom_iconsFile = `${maps_ParentFolder}/maps/icons/place_icons.json?v=2025-10-26`;

// Default application icon markers, as colored SVG images
const marker_IconsFolder = `${maps_ParentFolder}/maps/sx_icons`;
const search_MarkerIcon = `${maps_ParentFolder}/maps/sx_icons/ps_marker_red.svg`;

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



const geoStyles = {
    point: null, // handled via marker, not style

    line: {
        base: {
            color: 'blue',
            weight: 3,
            opacity: 0.85,
            fillOpacity: 0
        },
        hover: {
            color: '#ff0000',
            weight: 3,
            opacity: 0.85,
            fillOpacity: 0
        }
    },

    polygon: {
        base: {
            color: 'brown',
            weight: 3,
            opacity: 0.65,
            fillOpacity: 0.1
        },
        hover: {
            color: '#ff0000',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.05
        }
    }
};



const styleByType = {

    // REGIONS / AREAS
    "chora/region": {
        base: { color: "#6b8f5a", weight: 3, fillOpacity: 0.18 },
        hover: { color: "#4f6f3f", weight: 3, fillOpacity: 0.28 }
    },

    "mound/dike": {
        base: { color: "#8a8f5c", weight: 3, fillOpacity: 0.15 },
        hover: { color: "#6f7346", weight: 3, fillOpacity: 0.25 }
    },

    plain: {
        base: { color: "#97813eff", weight: 3, fillOpacity: 0.25 },
        hover: { color: "#d2b86a", weight: 3, fillOpacity: 0.35 }
    },

    valley: {
        base: { color: "#5f9ea0", weight: 3, fillOpacity: 0.2 },
        hover: { color: "#3f7f82", weight: 3, fillOpacity: 0.3 }
    },

    forest: {
        base: { color: "#2e7d32", weight: 3, fillOpacity: 0.35 },
        hover: { color: "#388e3c", weight: 3, fillOpacity: 0.45 }
    },

    rock: {
        base: { color: "#7a7a7a", weight: 3, fillOpacity: 0.28 },
        hover: { color: "#5f5f5f", weight: 3, fillOpacity: 0.38 }
    },

    river: {
        base: { color: "#2b7cd3", weight: 3, opacity: 0.9 },
        hover: { color: "#4aa3ff", weight: 3, opacity: 1 }
    },

    paraplous: {
        base: { color: "#1aa6b7", weight: 3, dashArray: "6,4", opacity: 0.9 },
        hover: { color: "#3ccde3", weight: 3, dashArray: "6,4", opacity: 1 }
    },

    "road hodos": {
        base: { color: "#8b5a2b", weight: 3 },
        hover: { color: "#b8793a", weight: 3 }
    },

    "road leophoros": {
        base: { color: "#d17c00", weight: 4 },
        hover: { color: "#ffa133", weight: 4 }
    },

    hieron: {
        base: { color: "#6a3fa0", weight: 3, fillOpacity: 0.3 },
        hover: { color: "#8b5fd3", weight: 3, fillOpacity: 0.4 }
    },

    "mythic/historic site": {
        base: { color: "#b64032", weight: 3, fillOpacity: 0.3 },
        hover: { color: "#e05c4c", weight: 3, fillOpacity: 0.4 }
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


const key_TypeMap = [
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

