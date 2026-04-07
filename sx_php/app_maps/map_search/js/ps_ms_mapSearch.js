
/**
 * ========================================================
 * Public Sphere 2025-12
 * FILE: ps_ms_mapSearch.js
 * Search the map by latitude/longitude or by place name
 * Or click a location on the map to get its Latitude, longitude and place name
 */

// Global input variables
const search_Lat = document.getElementById("SearchLat");
const Search_Lng = document.getElementById("SearchLng");
const Search_Place = document.getElementById("SearchPlaceName");

let use_ClickSearch = document.getElementById("UseClickSearch").checked;
let search_marker = null;
let clickTimeout = null;

// Click a point on the map to get its Latitude, longitude and place name
function handleMapClickSearching(e) {
    clickCancelled = false;
    clickTimeout = setTimeout(() => {
        if (clickCancelled) return;

        const lat = e.latlng.lat;
        const lon = e.latlng.lng;

        search_Lat.value = lat;
        Search_Lng.value = lon;

        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
            .then(response => response.json())
            .then(data => {
                if (clickCancelled) return;
                Search_Place.value = data.display_name;
                // const title = "You clicked near: n" + data.display_name + "\nLatitude: " + lat + "\nLongitude: " + lon;
                const title = data.display_name;
                loadMapScenario(lat, lon, title, true);
            });
    }, 200);
}

function handleMapDoubleClick() {
    if (clickTimeout) {
        clearTimeout(clickTimeout);
        clickTimeout = null;
        clickCancelled = true;
    }
}

// Toggle Click Searching
function initialize_MapClickSearch(search) {
    const mapElement = map.getContainer();
    if (search) {
        map.on('dblclick', handleMapDoubleClick);
        map.on('click', handleMapClickSearching);
        mapElement.style.cursor = 'pointer';
    } else {
        map.off('click', handleMapClickSearching);
        map.off('dblclick', handleMapDoubleClick);
        mapElement.style.cursor = 'grab';
    }
}

function round_decimal(n, d = 5) {
    const num = parseFloat(n);
    return Number.isFinite(num) ? Number(num.toFixed(d)) : null;
}

function loadMapScenario(lat, lng, title, show_LatLng = true) {

    const customIcon = L.icon({
        iconUrl: search_MarkerIcon,
        iconSize: [38, 38],
        iconAnchor: [19, 38],
        popupAnchor: [0, -40]
    });
    const rouns_Lat = round_decimal(lat);
    const round_Lng = round_decimal(lng);

    const lat_Lng = `Lat/Lng: ${rouns_Lat},${round_Lng}`;
    const latLng = `${rouns_Lat},${round_Lng}`;
    const copy_All = `<hr><button onclick="copy_SearchResults('${title}\\n${lat_Lng}')">Copy All</button>`;
    const copy_LatLng = `<button onclick="copy_SearchResults('${latLng}')">Copy Lat/Lng</button>`;

    let content_LatLng = '<br><b>Lat:</b> ' + lat + '<br><b>Lng:</b> ' + lng ;
    if (show_LatLng === false) {
        content_LatLng = '';
    }

    if (search_marker) {
        search_marker
            .setLatLng([lat, lng])
            .bindPopup(title + content_LatLng + copy_All + copy_LatLng)
            .openPopup();
    } else {
        search_marker = L.marker([lat, lng], { icon: customIcon })
            .addTo(map)
            .bindPopup(title + content_LatLng + copy_All + copy_LatLng, { autoPan: false })
            .openPopup();
    }
}

function copy_SearchResults(str) {
    navigator.clipboard.writeText(str)
        .then(() => {
            alert("Copied to clipboard: \n" + str);
        })
        .catch(err => {
            alert("Failed to copy: ", err);
        });
}

// Gets place name from coordinates
function reverseGeocode(lat, lon) {
    fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json`, {
        headers: {
            'User-Agent': 'Digital Periegesis (info@periegesis.org)'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data && data.display_name) {
                loadMapScenario(lat, lon, data.display_name);
            } else {
                loadMapScenario(lat, lon, "Unknown Location");
            }
        })
        .catch(error => {
            console.error("Reverse geocoding failed:", error);
        });
}


// Handles submisions from the search form
document.getElementById("SearchMap").addEventListener("click", function (event) {
    event.preventDefault();

    let latitude = search_Lat.value.trim();
    let longitude = Search_Lng.value.trim();
    let place_name = Search_Place.value.trim();

    if (latitude && longitude) {
        if (place_name) {
            loadMapScenario(latitude, longitude, place_name);
        } else {
            reverseGeocode(latitude, longitude);
        }
    } else if (place_name) {
        fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(place_name)}&format=json`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    const place = data[0];
                    latitude = place.lat;
                    longitude = place.lon;
                    place_name = place.display_name;
                    // Update inputs
                    search_Lat.value = latitude;
                    Search_Lng.value = longitude;

                    loadMapScenario(latitude, longitude, place_name);
                } else {
                    alert("No results found for the place name.");
                }
            })
            .catch(error => {
                console.error("Error fetching location:", error);
            });
    } else {
        alert("Please enter either coordinates or a place name.");
    }

});

function removeSearchMarker() {
    if (search_marker) {
        map.removeLayer(search_marker);
        search_marker = null;
    }
}

document.getElementById("SearchReset").addEventListener("click", function () {
    removeSearchMarker();
});

document.getElementById("UseClickSearch").addEventListener("change", function () {
    use_ClickSearch = this.checked;
    initialize_MapClickSearch(use_ClickSearch);
    removeSearchMarker();
});

initialize_MapClickSearch(use_ClickSearch);

// In mobiles, search inputs are hiden, so, toggle search inputs' visibility
document.getElementById("SearchInputsToggle").addEventListener("click", function () {
    const el = document.getElementById('SearchInputs');
    el.style.display = (el.style.display === 'flex') ? 'none' : 'flex';
    re_positionMap();
});

document.getElementById("SearchReset").addEventListener("click", function () {
    document.getElementById("SearchLat").value = "";
    document.getElementById("SearchLng").value = "";
    document.getElementById("SearchPlaceName").value = "";
});
