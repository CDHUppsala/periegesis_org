let sx_Leaflet_Map;

function load_Map_Scenario(lat, lng, title) {

    if (sx_Leaflet_Map && typeof sx_Leaflet_Map.remove === 'function') {
        sx_Leaflet_Map.remove();
        sx_Leaflet_Map = null;
    }

    sx_Leaflet_Map = L.map('js_ModalMapContainer').setView([lat, lng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(sx_Leaflet_Map);

    L.marker([lat, lng]).addTo(sx_Leaflet_Map)
        .bindPopup(title)
        .openPopup();

    // Important: wait a tick, then fix sizing
    setTimeout(() => {
        sx_Leaflet_Map.invalidateSize();
    }, 300);



}
