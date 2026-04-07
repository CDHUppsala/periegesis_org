
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

