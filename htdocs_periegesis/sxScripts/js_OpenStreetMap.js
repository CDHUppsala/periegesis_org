let sx_LeafletMap;

function loadMapScenario(lat, lng, title) {

    if (sx_LeafletMap && typeof sx_LeafletMap.remove === 'function') {
        sx_LeafletMap.remove();
        sx_LeafletMap = null;
    }

    sx_LeafletMap = L.map('js_ModalMapContainer').setView([lat, lng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(sx_LeafletMap);

    L.marker([lat, lng]).addTo(sx_LeafletMap)
        .bindPopup(title)
        .openPopup();

    // Important: wait a tick, then fix sizing
    setTimeout(() => {
        sx_LeafletMap.invalidateSize();
    }, 300);



}
