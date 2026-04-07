
/**
 * ========================================================
 * Public Sphere 2025-12
 * FILE: ps_ms_mapLegends.js
 * Create information legends on the map
 * ========================================================
 */

const open_MapLayers = [];
const map_Manager = L.control({ position: 'bottomleft' });

map_Manager.onAdd = function (map) {
    this._div = L.DomUtil.create('div', 'layer_legends');
    this.update();
    return this._div;
};

map_Manager.update = function () {

    // 1. Find the last loaded initial file
    const lastInitialIndex = [...open_MapLayers]
        .map((e, i) => ({ e, i }))
        .reverse()
        .find(obj => obj.e.filter === false)?.i;

    //if (lastInitialIndex === undefined) return;

    const lastInitial = open_MapLayers[lastInitialIndex];

    // 2. Check if THIS initial file has filters
    const hasFiltersForLastInitial = open_MapLayers.some(
        e => e.filter === true && e.sourceFileName === lastInitial.name
    );


    // 3. Update visibility
    if (lastInitial) {
        if (hasFiltersForLastInitial) {
            if (lastInitial.visible !== false) {
                map.removeLayer(lastInitial.layer);
                lastInitial.visible = false;
            }
        } else {
            if (lastInitial.visible !== true) {
                map.addLayer(lastInitial.layer);
                lastInitial.visible = true;
            }
            /*
            // Don't zoom with the second file
                    if (!lastInitial._zoomedOnce) {
                        map.fitBounds(lastInitial.layer.getBounds());
                        lastInitial._zoomedOnce = true;
                    }
            */
        }
    }
    // 4. Build the UI (checkboxes reflect updated visibility)
    this._div.innerHTML = `<strong>Loaded Files & Filters</strong>` +
        open_MapLayers.map((entry, i) => {

            const btn_Delete = entry.filter
                ? `<button title="Delete this layer" class="layer_delete" data-index="${i}"><svg viewBox="0 0 32 32"><path d="M3 3 q-1 0 -1 1 v2 q0 1 1 1 h26 q1 0 1 -1 v-2 q0 -1 -1 -1 h-8 q-1 -2 -2 -2 h-6 q-1 0 -2 2z m1 6 q-1 0 -1 1 v18 q0 3 3 3 h20 q3 0 3 -3 v-18 q0 -1 -1 -1z m4 4 a1 1 0 0 1 2 0 v14 a1 1 0 0 1 -2 0z m7 0 a1 1 0 0 1 2 0 v14 a1 1 0 0 1 -2 0z m7 0 a1 1 0 0 1 2 0 v14 a1 1 0 0 1 -2 0z" /></svg></button>`
                : "";

            let search_Class = "";
            if (i === lastInitialIndex) {
                search_Class = ' class="search_object" title="Filtering applies to this file"';
            }

            const checked = entry.visible ? "checked" : "";

            return `
            <div class="layer_loaded">
                <input title="Hide/Show Layers from this File - Format: ${entry.format}, Type: ${entry.type}"
                       type="checkbox" data-index="${i}" ${checked}>
                ${btn_Delete}
                <span${search_Class}>${escape_HTML(entry.name)} [${entry.dataCount}]</span>
                <button title="Zoom to the boundaries of this layer" class="layer_zoom" data-zoom="zoom" data-index="${i}"><svg viewBox="0 0 32 32"><path d="M1 1 v12 l4-4 5 5 4-4 -5-5 4-4z M1 31 h12 l-4-4 5-5 -4-4 -5 5 -4-4z M31 31 v-12 l-4 4 -5-5 -4 4 5 5 -4 4z M31 1 h-12 l 4 4 -5 5 4 4 5-5 4 4z" /></svg></button>
            </div>`;
        }).join("");
};

map_Manager.addTo(map);


// ==============================
// Show/Hide open layers
// ==============================

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


// ==============================
// Zoom to the boundaries of layer - or delete filtered layers
// ==============================

map_Manager._div.addEventListener("click", function (e) {

    const zoomBtn = e.target.closest(".layer_zoom");
    const deleteBtn = e.target.closest(".layer_delete");

    if (zoomBtn) {
        const index = parseInt(zoomBtn.dataset.index);
        const entry = open_MapLayers[index];

        const targetBounds = entry.layer.getBounds();
        const targetCenter = targetBounds.getCenter();
        const targetZoom = map.getBoundsZoom(targetBounds);
        const currentZoom = map.getZoom();

        if (Math.abs(targetZoom - currentZoom) <= 2) {
            map.flyToBounds(targetBounds, { duration: 1.2 });
            return;
        }

        if (targetZoom > currentZoom) {
            map.flyTo(targetCenter, currentZoom, { duration: 0.8 });
            map.once('moveend', () => {
                map.flyTo(targetCenter, targetZoom, { duration: 1.2 });
            });
        } else {
            map.flyTo(map.getCenter(), targetZoom, { duration: 1.2 });
            map.once('moveend', () => {
                map.flyTo(targetCenter, targetZoom, { duration: 0.8 });
            });
        }

        return;
    }

    if (deleteBtn) {
        const index = parseInt(deleteBtn.dataset.index);
        const entry = open_MapLayers[index];

        if (!entry || !entry.layer) return;

        // Remove from map
        if (map.hasLayer(entry.layer)) {
            map.removeLayer(entry.layer);
        }

        // Remove from array
        open_MapLayers.splice(index, 1);
        map_Manager.update();
    }
});



/**
 * ===================================
 * Create legend for number of places and areas
 */
/*
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
*/


// Show/Hude Legends
const toggle_Legends = L.control({ position: 'topleft' });
toggle_Legends.onAdd = function () {
    this._div = L.DomUtil.create('div', 'toggle_legends');
    this.update();
    return this._div;
};
toggle_Legends.update = function () {
    this._div.innerHTML = `
        <svg class="hide_legends" viewBox="0 0 32 32"><path d="M15.5 6 a1 1 0 0 1 2 2 l -7 7 q-1 1 0 2 l 7 7 a1 1 0 0 1 -2 2 l-9 -9 q-1-1 0 -2z m8 0 a1 1 0 0 1 2 2 l -7 7 q-1 1 0 2 l 7 7 a1 1 0 0 1 -2 2 l-9 -9 q-1-1 0 -2z"> </path> </svg>
        <svg class="show_Legends" style="display: none" viewBox="0 0 32 32"> <path d="M8.5 6 a1 1 0 0 0 -2 2 l 7 7 q1 1 0 2 l -7 7 a1 1 0 0 0 2 2 l 9 -9 q1 -1 0 -2 z m8 0 a1 1 0 0 0 -2 2 l 7 7 q1 1 0 2 l -7 7 a1 1 0 0 0 2 2 l 9 -9 q1 -1 0 -2 z"> </path> </svg>
        `;
};
toggle_Legends.addTo(map);


// Show/Hide Legends
toggle_Legends._div.addEventListener('click', function (e) {
    //const legends = e.currentTarget.parentElement.parentElement.querySelectorAll('.layer_legends, .info_legend');
    const legends = e.currentTarget.parentElement.parentElement.querySelectorAll('.layer_legends');
    const hideLegends = e.currentTarget.querySelector('.hide_legends');
    const showLegends = e.currentTarget.querySelector('.show_Legends');

    hideLegends.style.display = (hideLegends.style.display === 'block'
        || hideLegends.style.display === '') ? 'none' : 'block';
    showLegends.style.display = (hideLegends.style.display === 'none') ? 'block' : 'none';

    // Toggle legend visibility
    legends.forEach(el => {
        el.style.display = (el.style.display === 'none') ? 'block' : 'none';
    })
});


// Disable OSM click functions on legends
L.DomEvent.disableClickPropagation(map_Manager._div);
//L.DomEvent.disableClickPropagation(info_Legend._div);
L.DomEvent.disableClickPropagation(toggle_Legends._div);
