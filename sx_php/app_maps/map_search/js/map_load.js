/**
 * ========================================================
 * LOADS THE BASIC openStreetMap: map_load.js
 * ========================================================
 */

// Initial coordinates (Delphi)
const default_Lat = 38.4839;
const default_Lon = 22.5010;

// config map: set minZoom to 2 for world-history maps
let config = {
    minZoom: 2,
    maxZoom: 18
};
// initial zoom
const default_Zoom = 7;

// Initialize (call) the map
const map = L.map('map', config).setView([default_Lat, default_Lon], default_Zoom);

// Various map layers
// Base map layers  (OpenStreetMap)
const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
});

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
}
);

// Consortium of Ancient World Mappers
const CAWM = L.tileLayer('https://cawm.lib.uiowa.edu/tiles/{z}/{x}/{y}.png', {
    minZoom: 1,
    maxZoom: 11,
    //bounds: mapBounds,
    //opacity: 0.85
});

// Grouped satellite + labels
const satelliteLabels = L.layerGroup([satellite, labels]);


// Add default map layer
osm.addTo(map);


// Add Layer control to change the map layer
L.control.layers({
    "Open Street Map": osm,
    "CAWM Tile": CAWM,
    "OSM France": OpenStreetMap_France,
    "Esri_World Topo Map": Esri_WorldTopoMap,
    "Terrain": topo,
    "Satellite": satellite,
    "Satellite + Labels": satelliteLabels
}).addTo(map);

L.control
    .scale({
        imperial: false,
    })
    .addTo(map);

function positionMap() {
    const bar_height = document.getElementById("map_header_wrapper").clientHeight;
    const map = document.getElementById("map");
    map.style.top = bar_height + 'px';
}

// Wait untill the documemt loads
document.addEventListener('DOMContentLoaded', () => {

    function debounce(fn, delay) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    positionMap();

    window.addEventListener("resize", debounce(positionMap, 200));

});