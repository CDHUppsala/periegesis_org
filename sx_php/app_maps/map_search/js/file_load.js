
/**
 * ========================================================
 * LOADS SELECTED FILE (from file_select.js) TO THE MAP
 * ========================================================
 */

function load_File_ByExtension(file_Content, file_Extension, file_Name) {
    const extension = file_Extension.toLowerCase();
    // Reset for every new file load
    map_metadata = {};

    try {
        // Parse content if it's a string
        const isJSON = ['geojson', 'topojson', 'json'].includes(extension);
        const data = isJSON && typeof file_Content === 'string'
            ? JSON.parse(file_Content)
            : file_Content;

        switch (extension) {
            case 'geojson': {
                load_File_GeoJSON(data, extension, file_Name);
                break;
            }
            case 'topojson': {
                load_File_TopoJSON(data, extension, file_Name);
                break;
            }
            case 'kml': {
                load_File_KML(file_Content, extension, file_Name);
                break;
            }
            case 'gpx': {
                load_File_GPX(file_Content, extension, file_Name);
                break;
            }
            case 'csv': {
                load_File_CSV(file_Content, extension, file_Name);
                break;
            }
            case 'json': {
                if (data.type === "Topology") {
                    load_File_TopoJSON(data, extension, file_Name);
                } else if (data.type === "FeatureCollection" || data.type === "Feature") {
                    load_File_GeoJSON(data, extension, file_Name);
                } else if (Array.isArray(data)) {
                    load_File_JSON(data, extension, file_Name);
                } else {
                    console.error("Unknown JSON format");
                    alert("Unrecognized JSON structure.");
                }
                break;
            }
            default:
                alert("Unsupported file type: " + extension);
        }
    } catch (err) {
        console.error("Parsing error:", err);
        alert("Invalid or unreadable file.");
    }
}


function load_File_JSON(data, extension, file_Name) {
    let geojson;

    if (isGeoJSON(data)) {
        geojson = data;
    } else if (Array.isArray(data)) {
        geojson = convert_SimpleJSONtoGeoJSON(data);
    } else {
        alert("Unrecognized file structure.");
        return;
    }

    populate_Filters(geojson, file_Name)

    const layerGroup = L.geoJSON(geojson);
    handle_LayerPopupHover(layerGroup, file_Name, extension);
}

function load_File_GeoJSON(data, extension, file_Name) {
    if (data.type === "FeatureCollection" || data.type === "GeometryCollection") {
        // Extract map metadata (if exists)
        map_metadata = data.properties || {};

        populate_Filters(data, file_Name)

        const geoLayer = L.geoJSON(data);
        handle_LayerPopupHover(geoLayer, file_Name, extension);

        //console_log(geoLayer);
    } else {
        alert("Unrecognized JSON structure.");
    }

}

function load_File_TopoJSON(topoJSON, extension, file_Name) {
    const topoLayer = omnivore.topojson.parse(topoJSON);

    const geojson = topoLayer.toGeoJSON();
    populate_Filters(geojson, file_Name)

    handle_LayerPopupHover(topoLayer, file_Name, extension);

}

function load_File_KML(kmlText, extension, file_Name) {
    const parser = new DOMParser();
    const kmlDom = parser.parseFromString(kmlText, 'text/xml');

    // Extract map metadata (if exists)
    if (kmlDom.querySelectorAll('Document > ExtendedData').length) {
        kmlDom.querySelectorAll('Document > ExtendedData > Data').forEach(data => {
            const name = data.getAttribute('name');
            const value = data.querySelector('value')?.textContent.trim();
            map_metadata[name] = value;
        });
        console.log(map_metadata);
    }

    const kmlLayer = omnivore.kml.parse(kmlDom);

    const geojson = kmlLayer.toGeoJSON();
    populate_Filters(geojson, file_Name)

    handle_LayerPopupHover(kmlLayer, file_Name, extension)
}

function load_File_GPX(gpxText, extension, file_Name) {
    const parser = new DOMParser();
    const gpxDom = parser.parseFromString(gpxText, 'text/xml');

    const gpxLayer = omnivore.gpx.parse(gpxDom);

        const geojson = gpxLayer.toGeoJSON();
    populate_Filters(geojson, file_Name)

    handle_LayerPopupHover(gpxLayer, file_Name, extension)

    // console_log(gpxLayer);
}


// Use papaparse.min.js to deal even with malformed CSV files
function load_File_CSV(csvText, extension, file_Name) {
    // Parse and normalize CSV into GeoJSON
    const geojsonRaw = normalize_CsvToGeoJson(csvText);

    /*
   console.log('geojsonRaw: ', geojsonRaw);
   const errors = validateGeoJSON(geojsonRaw);
   if (errors.length) {
       console.error("INVALID COORDS FOUND:", errors);
   }
  */

    // Filter out broken ones
    const geojson = stripInvalidFeatures(geojsonRaw);

    populate_Filters(geojson, file_Name)

    // Create Leaflet layer from GeoJSON
    const geoLayer = L.geoJson(geojson);

    // Apply common popup/hover logic
    handle_LayerPopupHover(geoLayer, file_Name, 'geojson');

}
/*
    ==================
    Help functions called from the above load_file_XXX() functions
    ==================
 */

function detect_CSV_LatLonFields(csvText) {
    const headerLine = csvText.split('\n')[0];
    const headers = headerLine.split(',').map(h => h.trim());

    let latfield = headers.find(h =>
        ['lat', 'latitude', 'representative_latitude'].includes(h.toLowerCase())
    ) || 'Lat';

    let lonfield = headers.find(h =>
        ['lon', 'lng', 'long', 'longitude', 'representative_longitude'].includes(h.toLowerCase())
    ) || 'Lon';

    return { latfield, lonfield };
}

function detect_CSV_Delimiter(csvText) {
    const firstLine = csvText.split('\n')[0];

    const delimiters = [',', ';', '\t', '|'];
    let bestMatch = ',';
    let maxFields = 0;

    for (const delim of delimiters) {
        const fields = firstLine.split(delim);
        if (fields.length > maxFields) {
            maxFields = fields.length;
            bestMatch = delim;
        }
    }

    return bestMatch;
}

function isGeoJSON(data) {
    return data?.type === "FeatureCollection" && Array.isArray(data.features);
}

function convert_SimpleJSONtoGeoJSON(data) {
    const features = data.map(entry => {
        // Normalize keys to lowercase
        const keys = Object.keys(entry).reduce((acc, key) => {
            acc[key.toLowerCase()] = key;
            return acc;
        }, {});

        // Detect latitude and longitude keys
        const latKey = keys['lat'] || keys['latitude'];
        const lngKey = keys['lng'] || keys['lon'] || keys['long'] || keys['longitude'];

        if (!latKey || !lngKey) return null;

        const lat = parseFloat(entry[latKey]);
        const lon = parseFloat(entry[lngKey]);

        if (isNaN(lat) || isNaN(lon)) return null;

        return {
            type: "Feature",
            geometry: {
                type: "Point",
                coordinates: [lon, lat]
            },
            properties: { ...entry }
        };
    }).filter(f => f !== null);

    return {
        type: "FeatureCollection",
        features
    };
}


function console_log(layer) {
    layer.eachLayer(function (layer) {
        console.log('Geometry: ', layer.feature.geometry);
        console.log('Properties: ', layer.feature.properties);
    });

}

/**
 * =========================================
 * Use papaparse.min.js instead to deal 
 * even with malformed CSV files
 * =========================================
 */


function detect_LatLonFields(headers) {
    const normalized = headers.map(h => h.trim());

    const latCandidates = ['lat', 'Lat', 'LAT', 'latitude', 'Latitude', 'LATITUDE', 'representative_latitude', 'reprLat'];
    const lonCandidates = ['lon', 'Lon', 'LON', 'lng', 'Lng', 'LNG', 'long', 'Long', 'LONG', 'longitude', 'Longitude', 'LONGITUDE', 'representative_longitude', 'reprLong'];

    const latfield = normalized.find(h => latCandidates.includes(h)) || null;
    const lonfield = normalized.find(h => lonCandidates.includes(h)) || null;

    return { latfield, lonfield };
}

// Use papaparse.min.js to normalize csv for geojson
function normalize_Csv______ToGeoJson(csvText) {
    const result = Papa.parse(csvText, {
        header: true,
        skipEmptyLines: true,
        dynamicTyping: true,
        transformHeader: h => h.trim()
    });

    if (result.errors.length) {
        console.warn('CSV parse warnings:', result.errors);
    }

    const headers = Object.keys(result.data[0] || {}).map(h => h.trim());

    // Check for WKT field first
    const wktField = detect_WKT_Field(headers);

    // If WKT exists → use WKT parsing mode
    if (wktField) {
        const features = result.data
            .filter(row => row[wktField])
            .map((row, index) => {
                const geom = parseWKT(row[wktField]);
                const props = { ...row };
                delete props[wktField];

                return {
                    type: "Feature",
                    geometry: geom,
                    properties: props,
                    id: index
                };
            });

        return {
            type: "FeatureCollection",
            features
        };
    }

    // No WKT - Use lat/lon logic
    const { latfield, lonfield } = detect_LatLonFields(headers);

    if (!latfield || !lonfield) {
        alert('Could not detect latitude/longitude or WKT field.');
        return null;
    }

    const features = result.data
        .filter(row => row[latfield] != null && row[lonfield] != null)
        .map((row, index) => {
            const { [latfield]: lat, [lonfield]: lon, ...props } = row;
            return {
                type: 'Feature',
                geometry: {
                    type: 'Point',
                    coordinates: [parseFloat(lon), parseFloat(lat)]
                },
                properties: props,
                id: index
            };
        });

    return {
        type: 'FeatureCollection',
        features
    };
}

function normalize_CsvToGeoJson(csvText) {
    const result = Papa.parse(csvText, {
        header: true,
        skipEmptyLines: true,
        dynamicTyping: true,
        transformHeader: h => h.trim()
    });

    if (result.errors.length) {
        console.warn('CSV parse warnings:', result.errors);
    }

    const headers = Object.keys(result.data[0] || {}).map(h => h.trim());

    // ---------------------------------------------------------
    // 1) DETECT EMBEDDED GEOJSON GEOMETRY FIELD
    // ---------------------------------------------------------
    const embeddedField = headers.find(h =>
        h.toLowerCase().includes("geometry")
        && result.data[0][h]
        && typeof result.data[0][h] === "string"
        && result.data[0][h].trim().startsWith("{")
    );

    if (embeddedField) {
        console.log("Embedded GeoJSON detected in column:", embeddedField);

        const features = [];

        result.data.forEach((row, index) => {
            if (!row[embeddedField]) return;

            let geometry = null;

            try {
                geometry = JSON.parse(row[embeddedField]);
            } catch (e) {
                console.warn("Invalid embedded geometry at row", index, e);
                return;
            }

            if (!geometry || !geometry.type) return;

            const props = { ...row };
            delete props[embeddedField];

            features.push({
                type: "Feature",
                geometry: geometry,
                properties: props,
                id: index
            });
        });

        return {
            type: "FeatureCollection",
            features
        };
    }

    // ---------------------------------------------------------
    // 2) DETECT WKT FIELD
    // ---------------------------------------------------------
    const wktField = detect_WKT_Field(headers);

    if (wktField) {
        console.log("WKT detected in column:", wktField);

        const features = result.data
            .filter(row => row[wktField])
            .map((row, index) => {
                const geom = parseWKT(row[wktField]);
                const props = { ...row };
                delete props[wktField];

                return {
                    type: "Feature",
                    geometry: geom,
                    properties: props,
                    id: index
                };
            });

        return {
            type: "FeatureCollection",
            features
        };
    }

    // ---------------------------------------------------------
    // 3) DETECT LAT/LON FIELD PAIR
    // ---------------------------------------------------------
    const { latfield, lonfield } = detect_LatLonFields(headers);

    if (latfield && lonfield) {
        // console.log("Lat/Lon detected:", latfield, lonfield);

        const features = result.data
            .filter(row => row[latfield] != null && row[lonfield] != null)
            .map((row, index) => {
                const { [latfield]: lat, [lonfield]: lon, ...props } = row;
                return {
                    type: "Feature",
                    geometry: {
                        type: "Point",
                        coordinates: [parseFloat(lon), parseFloat(lat)]
                    },
                    properties: props,
                    id: index
                };
            });

        return {
            type: "FeatureCollection",
            features
        };
    }

    // ---------------------------------------------------------
    // 4) NOTHING MATCHED
    // ---------------------------------------------------------
    alert("Could not detect Geometry, WKT, or Latitude/Longitude fields.");
    console.warn("Headers found:", headers);
    return null;
}


/*
    For WKT
    =========================================
*/

// For CSV files containing WKT
function detect_WKT_Field(headers) {
    const candidates = ['geometry_wkt', 'geometry', 'wkt', 'geom'];
    return headers.find(h => candidates.includes(h.toLowerCase())) || null;
}

function parseWKT(wkt) {
    if (!wkt || typeof wkt !== "string") return null;

    // Clean up BOM, quotes, trailing/leading spaces
    wkt = wkt.trim().replace(/^\uFEFF/, "").replace(/^"|"$/g, "");

    // ======================
    // POINT
    // ======================
    if (wkt.startsWith("POINT")) {
        const raw = wkt.replace(/^POINT\s*\(/, "").replace(/\)$/, "").trim();
        const coords = raw.split(/\s+/).map(n => parseFloat(n));
        return {
            type: "Point",
            coordinates: coords
        };
    }

    // ======================
    // LINESTRING
    // ======================
    if (wkt.startsWith("LINESTRING")) {
        const raw = wkt
            .replace(/^LINESTRING\s*\(/, "")
            .replace(/\)$/, "")
            .trim();

        const coords = raw
            .split(",")
            .map(pair => {
                return pair
                    .trim()
                    .split(/\s+/)
                    .map(n => parseFloat(n));
            });

        return {
            type: "LineString",
            coordinates: coords
        };
    }

    // ======================
    // POLYGON
    // ======================
    if (wkt.startsWith("POLYGON")) {
        const raw = wkt
            .replace(/^POLYGON\s*\(\(/, "")
            .replace(/\)\)$/, "")
            .trim();

        const coords = raw
            .split(",")
            .map(pair => {
                return pair
                    .trim()
                    .split(/\s+/)
                    .map(n => parseFloat(n));
            });

        return {
            type: "Polygon",
            coordinates: [coords]
        };
    }

    console.warn("Unsupported WKT:", wkt);
    return null;
}

// Basically, For CSV filws with WKT, but applied to any CSV file
function stripInvalidFeatures(geojsonObj) {
    const cleaned = {
        type: "FeatureCollection",
        features: []
    };

    for (let i = 0; i < geojsonObj.features.length; i++) {
        const feature = geojsonObj.features[i];

        // Validate coords depending on geometry type
        const geom = feature.geometry;
        if (!geom) continue;

        let coords = geom.coordinates;

        let isValid = true;

        const checkCoordPair = (pair) => {
            return (
                Array.isArray(pair) &&
                pair.length === 2 &&
                !isNaN(pair[0]) &&
                !isNaN(pair[1])
            );
        };

        if (geom.type === "Point") {
            isValid = checkCoordPair(coords);

        } else if (geom.type === "LineString") {
            for (let p of coords) {
                if (!checkCoordPair(p)) {
                    isValid = false;
                    break;
                }
            }

        } else if (geom.type === "Polygon") {
            for (let ring of coords) {
                for (let p of ring) {
                    if (!checkCoordPair(p)) {
                        isValid = false;
                        break;
                    }
                }
                if (!isValid) break;
            }
        }

        if (isValid) cleaned.features.push(feature);
    }

    return cleaned;
}

// For WKT: Optionally, check for none Valid coordinates before Leaflet parses them
function validateGeoJSON(geojson) {
    let errors = [];

    geojson.features.forEach((f, fi) => {
        const geom = f.geometry;

        if (!geom) {
            errors.push(`Feature ${fi} has no geometry`);
            return;
        }

        const type = geom.type;
        const coords = geom.coordinates;

        function checkCoord(c, path) {
            if (!Array.isArray(c) || c.length < 2) {
                errors.push(`Invalid coord at ${path}: ${JSON.stringify(c)}`);
                return;
            }
            const [lon, lat] = c;

            if (isNaN(lon) || isNaN(lat)) {
                errors.push(`NaN at ${path}: [${lon}, ${lat}]`);
            }
        }

        if (type === "LineString") {
            coords.forEach((c, ci) => checkCoord(c, `Feature[${fi}].coords[${ci}]`));
        }

        else if (type === "Polygon") {
            coords.forEach((ring, ri) =>
                ring.forEach((c, ci) =>
                    checkCoord(c, `Feature[${fi}].ring[${ri}].point[${ci}]`)
                )
            );
        }

        else if (type === "Point") {
            checkCoord(coords, `Feature[${fi}].point`);
        }
    });

    return errors;
}

/*
    For CSV with Embeded GeoJson Structure
    =========================================
*/

function parse_Csv_WithEmbeddedGeoJson(csvText) {
    const rows = Papa.parse(csvText, {
        header: true,
        dynamicTyping: false,
        skipEmptyLines: true
    }).data;

    const features = [];

    rows.forEach((row, index) => {
        const geomField = row["[Location] Geometry"];

        if (!geomField) return;

        let geometry = null;

        try {
            // Convert from string to real geoJSON object
            geometry = JSON.parse(geomField);
        } catch (e) {
            console.warn("Invalid embedded geometry at row", index, e);
            return;
        }

        // Skip if geometry is broken
        if (!geometry || !geometry.type) return;

        // Create Feature
        const feature = {
            type: "Feature",
            geometry: geometry,
            properties: {},
            id: index
        };

        // Add all other fields as properties
        for (const key in row) {
            if (key !== "[Location] Geometry") {
                feature.properties[key] = row[key];
            }
        }

        features.push(feature);
    });

    return {
        type: "FeatureCollection",
        features: features
    };
}
