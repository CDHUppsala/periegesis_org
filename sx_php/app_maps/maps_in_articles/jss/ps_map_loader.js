/**
 * ========================================================
 * Public Sphere 2025-12
 * FILE: ps_map_loader.js
 * LOADS THE BASIC openStreetMap layer for each map instance/scope
 *  - Each map instance, defined by its ID (MAP_ID) is cloased in a JavsSript scope
 * ========================================================
 */




function toggle_MapWidescreen(map, map_Instance) {
    const slot = map_Instance.parentElement;

    if (!map_Instance.classList.contains('is-widescreen')) {
        // ENTER fullscreen
        slot.style.height = map_Instance.offsetHeight + 'px';
        map_Instance.classList.add('is-widescreen');
        document.body.classList.add('map-lock');
    } else {
        // EXIT fullscreen
        map_Instance.classList.remove('is-widescreen');
        document.body.classList.remove('map-lock');
        slot.style.height = '';
    }

    //setTimeout(() => map.invalidateSize(), 200);
    requestAnimationFrame(() => map.invalidateSize());
}

function init_MapInstance(map_Instance) {
    const source_FileNames = map_Instance.dataset.path ?? '';

    const map_El = map_Instance.querySelector('.map');
    const map_Filters = map_Instance.querySelector('.filters-wrapper');
    const map_MetaData = map_Instance.parentElement.querySelector('.map-meta-data');

    const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    });

    const map = L.map(map_El, config).setView([default_Lat, default_Lon], default_Zoom);

    osm.addTo(map);

    //setTimeout(() => map.invalidateSize(), 50);
    requestAnimationFrame(() => {
        map.invalidateSize(true);
    });

    const map_TileLayers = {
        "Open Street Map": osm,
        "OSM France": OpenStreetMap_France,
        "Esri_World Topo Map": Esri_WorldTopoMap,
        "Terrain": topo,
        "Satellite": satellite,
        "Satellite + Labels": satelliteLabels
    };

    L.control.layers(map_TileLayers).addTo(map);

    const map_Legends = init_MapLegends(map);

    const scope_Constants = [map, map_Legends, map_Instance, map_Filters, map_MetaData];

    if (source_FileNames) {
        const server_FileURL = `${maps_PlacesFolder}/${source_FileNames}?v=2025-12`;
        const extension = source_FileNames.split('.').pop().toLowerCase();
        const file_Name = source_FileNames.split('/').pop();
        fetch(server_FileURL)
            .then(response => {
                return extension === 'kml' || extension === 'csv' ?
                    response.text() :
                    response.json();
            })
            .then(content => {
                load_File_ByExtension(scope_Constants, content, extension, file_Name);
            });
    } else {
        console.warn('The file ' + source_FileNames + ' cannot be loaded');
    }

    attach_PopupCopyHandlers(map_Instance);

    map_Instance.querySelector('.toggle_MapScreen').addEventListener('click', () =>
        toggle_MapWidescreen(map, map_Instance)
    );
}

(function () {
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.map-wrapper').forEach(init_MapInstance);
    });

})();