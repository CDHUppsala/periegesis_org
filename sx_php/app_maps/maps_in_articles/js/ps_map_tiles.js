
/**
 * ========================================================
 * Public Sphere 2025-12
 * FILE: ps_tile.js
 * Tiles
 * ========================================================
 */

/*
let config = {
    minZoom: 2,
    maxZoom: 18,
    preferCanvas: true
};

*/
// Initial coordinates: Akropolis
const default_Lat = 37.97169;
const default_Lon = 23.72632;

// config map: set minZoom to 2 for world-history maps
let config = {
    minZoom: 2,
    maxZoom: 18
};

// initial zoom
const default_Zoom = 7;

// Various map layers

const OpenStreetMap_France = L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
    maxZoom: 20,
    attribution: '&copy; OpenStreetMap France | &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
});

const CartoDB = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; CartoDB & OpenStreetMap',
    subdomains: 'abcd',
    maxZoom: 19
});

// Terain map layer
const topo = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenTopoMap'
});

const Esri_WorldTopoMap = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
    attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ, TomTom, Intermap, iPC, USGS, FAO, NPS, NRCAN, GeoBase, Kadaster NL, Ordnance Survey, Esri Japan, METI, Esri China (Hong Kong), and the GIS User Community'
});

// Satellite layer
const satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    attribution: 'Tiles © Esri — Source: Esri, Earthstar Geographics'
});

const labels = L.tileLayer(
    'https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
    attribution: 'Labels © Esri — Source: Esri, HERE, Garmin, (and others)',
    pane: 'overlayPane'
});

// Grouped satellite + labels
const satelliteLabels = L.layerGroup([satellite, labels]);

