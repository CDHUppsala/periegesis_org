
/**
 * ========================================================
 * Public Sphere 2025-12
 * FILE: ps_map_popup_hover.js
 * FUNCTIONS FOR Hovering, Zooming and Popup content
 * Applied for all geometry types: Points, Lines and Polygons
 * ========================================================
 */

/* Defined automatically to widen the hovering area for lines
    - base_UsedStyle - base_LineStyle or base_PolygonStyle;
    - hover_UsedStyle - hover_LineStyle or hover_PolygonStyle
*/
let base_UsedStyle, hover_UsedStyle;

function createClusterGroup(radius) {
    return L.markerClusterGroup({
        maxClusterRadius: radius,
        spiderfyOnMaxZoom: true,
        zoomToBoundsOnClick: true
    });
}

function getClusterRadiusByZoom(zoom) {
    if (zoom <= 6) return 60;
    if (zoom <= 10) return 30;
    return 20;
}


function attachZoomLogic(map, open_MapLayers) {
    map.on('zoomend', () => {
        const zoom = map.getZoom();
        const newRadius = getClusterRadiusByZoom(zoom);

        open_MapLayers.forEach(entry => {
            if (entry.cluster) {
                const geoLayer = entry.cluster.getLayers()[0]; // assuming one geoLayer per cluster
                map.removeLayer(entry.cluster);
                const newCluster = createClusterGroup(newRadius);
                newCluster.addLayer(geoLayer);
                map.addLayer(newCluster);
                entry.cluster = newCluster;
            }
        });
    });
}

function handle_LayerPopupHover(map, map_Legends, layerGroup, file_Name, format, is_Filtered = false, source_FileName = '') {

    const marker_Color = get_MarkerColors();
    let count_Places = 0;
    let geometryType = "Areas";

    const { open_MapLayers, map_Manager, info_Legend } = map_Legends;
    attachZoomLogic(map, open_MapLayers);

    // Cluster groups with dynamic radius based on current zoom
    const zoom = map.getZoom();
    const cluster_Radius = getClusterRadiusByZoom(zoom);
    const marker_ClusterGroup = createClusterGroup(cluster_Radius);



    const normalized_GeoJSON = normalize_GeometryCollections(layerGroup);
    // Merge identical (duplicate) Polygons
    const cleaned_GeoJson = clean_IdenticalPolygons(normalized_GeoJSON);

    let isGeoLine = false;
    const geoLayer = L.geoJSON(cleaned_GeoJson, {

        pointToLayer: function (feature, latlng) {
            let icon = null;
            if (typeof place_Icons === 'object' && place_Icons !== null) {
                const place_type = get_PlaceType(feature.properties);

                if (place_type !== null && place_type !== 'NULL' && place_type !== 'unknown') {
                    icon = get_CustomIcon(place_type);
                    if (!icon) {
                        //console.warn(`No custom icon found for place type: ${place_type}`);
                    }
                }
            }
            if (!icon) {
                icon = marker_Color;
            }
            count_Places++;
            return L.marker(latlng, { icon });
        },
        onEachFeature: function (feature, layer) {
            const geoType = layer.feature?.geometry?.type;

            if (geoType === "Point") {
                geometryType = "Places";
            } else {
                if (geoType === "Line" || geoType === "LineString" || geoType === "MultiLineString" || format === 'gpx') {
                    isGeoLine = true;
                    base_UsedStyle = base_LineStyle;
                    hover_UsedStyle = hover_LineStyle;
                    layer.setStyle(base_UsedStyle);

                    let hitLayer = L.geoJSON(feature, {
                        style: {
                            color: "#000000",
                            weight: 15,
                            opacity: 0,
                            fillOpacity: 0
                        },
                        interactive: true,
                        bubblingMouseEvents: false,
                        onEachFeature: (f, hitL) => {
                            hitL.on("mouseover", () => {
                                layer.fire("mouseover");
                                layer.bringToFront();
                                hitL.bringToFront();
                            });
                            hitL.on("mouseout", () => layer.fire("mouseout"));
                            hitL.on("click", (e) => layer.fire("click", e));
                        }
                    });

                    hitLayer.addTo(map);
                } else {
                    base_UsedStyle = base_PolygonStyle;
                    hover_UsedStyle = hover_PolygonStyle
                    if (isGeoLine === false) {
                        layer.setStyle(base_UsedStyle);
                    } else {
                        layer.setStyle(base_LineStyle);
                    }
                }

                layer.on("mouseover", () => {
                    layer.bringToFront();
                    layer.setStyle(hover_UsedStyle);
                });

                layer.on("mouseout", () => {
                    layer.setStyle(base_UsedStyle);
                });
            }

            // Clean properties of merged identical (duplicate) Polygons
            const cleanMergedProps = normalize_MergedFeatureProps(feature.properties);
            layer.bindPopup(sanitize_PopupContent(cleanMergedProps));
        },
        style: base_UsedStyle
    });

    // Add geoLayer to cluster group (instead of adding to local group)
    marker_ClusterGroup.addLayer(geoLayer);

    // Create local group and add cluster group
    const local_Group = L.featureGroup().addTo(map);
    local_Group.addLayer(marker_ClusterGroup);

    // To add a delete button only for filtered layers - not the initial file
    open_MapLayers.forEach(entry => {
        entry.visible = false;
        if (entry.layer && map.hasLayer(entry.layer)) {
            map.removeLayer(entry.layer);
        }
    });
    map_Manager.update();

    // Check if bounds are valid and non-empty before calling zoom
    const bounds = marker_ClusterGroup.getBounds();
    if (bounds.isValid()) {
        map.fitBounds(bounds);
    } else {
        file_Name = 'No results for this filter'
    }

    open_MapLayers.push({
        name: file_Name,
        data: cleaned_GeoJson,
        layer: local_Group,
        format, format,
        type: geometryType,
        visible: true,
        filter: is_Filtered,
        sourceFileName: source_FileName
    });
    map_Manager.update();

    const count_Areas = geoLayer.getLayers().filter(l => typeof l.setStyle === "function").length;
    placesCount = count_Places;
    areasCount = count_Areas;
    info_Legend.update(placesCount, areasCount);

}


/**
 * Attaching one listener/ Event delegation to map_Instance, not to popups or buttons directly.
 * @param {*} map_Instance Defines the JS scope
 */
function attach_PopupCopyHandlers(map_Instance) {

    map_Instance.addEventListener('click', (e) => {
        const b = e.target.closest('button');
        if (!b || !map_Instance.contains(b)) return;

        if (e.target.classList.contains('copy_PopupContent')) {
            // clone keeping DOM - to enable removing buttons and other elements
            const parent = e.target.parentElement.cloneNode(true);
            if (!parent) return;

            const btn = parent.querySelectorAll("button");
            btn.forEach(b => b.remove());

            // Remove all <span class="modal_click"> elements, if anny
            const spans = parent.querySelectorAll("span.modal_click");
            spans.forEach(span => span.remove());

            // Replace <br> tags with newline characters
            parent.innerHTML = parent.innerHTML.replace(/<br\s*\/?>/gi, "\n");

            const content = parent.innerText;

            navigator.clipboard.writeText(content)
                .then(() => {
                    alert("Popup content copied:\n" + content);
                })
                .catch(err => {
                    alert("Failed to copy: ", err);
                });
        } else if (e.target.classList.contains('copy_PopupLatLong')) {
            const latlng = e.target.dataset.latlng;
            if (!latlng) return;

            navigator.clipboard.writeText(latlng)
                .then(() => {
                    alert("Copied to clipboard: \n" + latlng);
                })
                .catch(err => {
                    alert("Failed to copy: ", err);
                });
        }
    });
}

function normalize_GeometryCollections(layerGroup) {

    const geojson = layerGroup.toGeoJSON();
    //const geojson = layerGroup;

    const normalizedFeatures = geojson.features.flatMap((feature) => {
        if (feature.geometry?.type === "GeometryCollection") {
            const geometries = feature.geometry.geometries;

            const polygons = geometries.filter(g => g.type === "Polygon").map(g => g.coordinates);
            const others = geometries.filter(g => g.type !== "Polygon");

            const result = [];

            if (polygons.length > 0) {
                result.push({
                    type: "Feature",
                    geometry: {
                        type: "MultiPolygon",
                        coordinates: polygons
                    },
                    properties: feature.properties
                });
            }

            others.forEach((geom) => {
                result.push({
                    type: "Feature",
                    geometry: geom,
                    properties: feature.properties
                });
            });

            return result;
        }

        return feature; // unchanged
    });

    return {
        type: "FeatureCollection",
        features: normalizedFeatures
    };
}


// Handle multiple polygons with identical layers
function clean_IdenticalPolygons(geojson) {
    const seen = {};
    const result = [];

    geojson.features.forEach(f => {
        const hash = JSON.stringify(f.geometry);
        const g = f.geometry;

        if (!g || g.type === "Point") {
            // Keep Point geometry untouched
            result.push(f);
            return;
        }

        if (!seen[hash]) {
            // First time seeing this geometry
            seen[hash] = {
                feature: f,
                entries: [f.properties]

            };
            result.push(f);
        } else {
            // Duplicate geometry → push its properties
            seen[hash].entries.push(f.properties);

            // Tag the first feature as merged
            const first = seen[hash].feature;
            first.properties.merged_entries = seen[hash].entries;
        }
    });

    return result;
}

function normalize_MergedFeatureProps(props) {
    const p = { ...props };
    if (Array.isArray(p.merged_entries)) {
        p._mergedCount = p.merged_entries.length;
        delete p.merged_entries;
    }
    return p;
}
