// 0. Get all unique book sections to creates the select element
// The JavaScript object object_PlacesBySection is created in get_places.php
let unique_sections = [];
let loop_section = '';

object_PlacesBySection.forEach(place => {
    if (loop_section !== place.BookID) {
        unique_sections.push(place.BookID);
    }
    loop_section = place.BookID;
});

// Start with the first section
let current_book_id = unique_sections[0];

// 1. Create the select element with all unique sections as options
const SelectSection = document.getElementById('SelectSection');
SelectSection.innerHTML = ''; // Clear old options

const defaultOption = document.createElement('option');
defaultOption.value = '';
defaultOption.textContent = 'Choose Section';
SelectSection.appendChild(defaultOption);

unique_sections.forEach(value => {
    const option = document.createElement('option');
    option.value = value;
    option.textContent = value;
    SelectSection.appendChild(option);
});

SelectSection.addEventListener('change', function () {
    const selectedValue = this.value;
    update_Map_BySection(selectedValue);
    update_HTML_BySection(selectedValue);
});


// 2. Initialize the Map Once
let sectionMap;
let section_Markers = [];

function init_SectionMap() {
    if (!sectionMap) {
        sectionMap = L.map('Map_SectionWrapper').setView([38.4839, 22.5010], 7); // Delphi coordinates

        // Base layers
        const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(sectionMap);

        const OpenStreetMap_France = L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
            maxZoom: 20,
            attribution: '&copy; OpenStreetMap France | &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        });
        const Esri_WorldTopoMap = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ, TomTom, Intermap, iPC, USGS, FAO, NPS, NRCAN, GeoBase, Kadaster NL, Ordnance Survey, Esri Japan, METI, Esri China (Hong Kong), and the GIS User Community'
        });

        const topo = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenTopoMap'
        });

        const satellite = L.tileLayer('https://services.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '© Esri & contributors'
        });

        // Layer control
        L.control.layers({
            "Open Street Map": osm,
            "OSM France": OpenStreetMap_France,
            "Esri World Topo Map": Esri_WorldTopoMap,
            "Terrain": topo,
            "Satellite": satellite
        }).addTo(sectionMap);

    }
}

// 3. Filter and Update Markers
function get_PlacesForSection(sectionCode) {
    return object_PlacesBySection.filter(place => place.BookID === sectionCode);
}

let places_Array;

// 4. Add markers to the map for current section places
function update_Map_BySection(sectionCode) {
    init_SectionMap();

    // Clear old markers
    section_Markers.forEach(marker => sectionMap.removeLayer(marker));
    section_Markers = [];

    places_Array = get_PlacesForSection(sectionCode);

    places_Array.forEach(place => {
        const marker = L.marker([parseFloat(place.Lat), parseFloat(place.Lng)])
            .addTo(sectionMap)
            .bindPopup(`<strong>Lat:</strong> ${place.Lat}<br>
                <strong>Lon:</strong> ${place.Lng}<br>
                <strong>Place:</strong> ${place.PlaceName}<br>
                <strong>Type:</strong> ${place.PlaceType}<br>
                <strong>Comments:</strong> ${place.Comments}`);
        section_Markers.push(marker);
    });

    if (section_Markers.length > 0) {
        const group = new L.featureGroup(section_Markers);
        sectionMap.fitBounds(group.getBounds().pad(0.2));
    }
}

document.addEventListener("DOMContentLoaded", function () {
    update_Map_BySection(current_book_id);
});
