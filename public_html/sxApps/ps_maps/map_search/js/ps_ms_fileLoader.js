
/**
 * ========================================================
 * Public Sphere 2025-12
 * FILE: ps_ms_mapFileLoader.js
 * COMMON FUNCTIONS FOR Loading selected map files as layers on the map
 * Supported file formats: GeoJson, KML, TopoJson, GPX, CSV, JSON
 * ========================================================
 */


// Global variables to save actual Field Names of loaded CSV files, including names 
// for Latitutde and Longitude, to be reused when filtered geojson is converted to CSV
let csv_Headers = null;
let csv_Latitude = 'Lat';
let csv_Longitude = 'Lon';

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
                    load_File_TopoJSON(data, 'topojson', file_Name);
                } else if (data.type === "FeatureCollection" || data.type === "Feature") {
                    load_File_GeoJSON(data, 'geojson', file_Name);
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


function load_File_CSV(csvText, extension, file_Name) {

    // Parse and normalize CSV into GeoJSON - Get actual format
    let [geojson, format] = normalize_CsvToGeoJson(csvText);
    if (!format) {
        format = extension
    }
    populate_Filters(geojson, file_Name)

    // Create Leaflet layer from GeoJSON
    const geoLayer = L.geoJson(geojson);

    // Apply common popup/hover logic
    handle_LayerPopupHover(geoLayer, file_Name, format);

}
/*
    ==================
    Help functions called from the above load_file_XXX() functions
    ==================
 */


function detect_CSV_Delimiter_NU(csvText) {
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


// ================================
// Checks and parses CSV files depending on including Geometry Type.
// Send back the format (geo-format) of the CSV file, used with conversion back to CSV file
// Use papaparse.min.js to deal even with malformed CSV files
// ================================

function normalize_CsvToGeoJson(csvText) {
    const result = Papa.parse(csvText, {
        header: true,
        skipEmptyLines: true,
        dynamicTyping: true,
        transformHeader: h => h.trim()
    });


    if (!result.data.length) {
        alert("CSV empty or invalid");
        return null;
    }

    const headers = Object.keys(result.data[0]).map(h => h.trim());
    csv_Headers = headers;

    // 1) Detect embedded GeoJSON (content-based)
    const embeddedField = headers.find(h =>
        is_EmbeddedGeoJSON(result.data[0][h])
    );

    if (embeddedField) {
        return [parse_EmbeddedGeoJson(result.data, embeddedField), 'embeded'];
    }

    // 2) Detect WKT field
    const wktField = detect_WKT_Field(headers, result.data[0]);
    if (wktField) {
        return [parseWKTField(result.data, wktField), 'wkt'];
    }

    // 3) Detect Lat/Lon fields
    const { latfield, lonfield } = detect_LatLonFields(headers);
    csv_Latitude = latfield;
    csv_Longitude = lonfield;

    if (latfield && lonfield) {
        return [parse_LatLon(result.data, latfield, lonfield), 'csv'];
    }

    alert("Could not detect geometry in CSV (GeoJSON, WKT, or Lat/Lon fields)");
    console.warn("Headers found:", headers);
    return null;
}


// ============================
// Helpers for CSV file conversion
// ============================

function is_EmbeddedGeoJSON(value) {
    if (typeof value !== 'string') return false;

    const trimmed = value.trim();

    // Must start like JSON
    if (!(trimmed.startsWith('{') || trimmed.startsWith('['))) {
        return false;
    }

    // Quick pre-check to avoid parsing junk
    if (!trimmed.includes('"type"')) return false;

    try {
        const obj = JSON.parse(trimmed);

        // GeoJSON must have a "type"
        if (!obj.type) return false;

        // Detect geometry types
        const geometryTypes = [
            'Point', 'MultiPoint',
            'LineString', 'MultiLineString',
            'Polygon', 'MultiPolygon',
            'GeometryCollection'
        ];

        return geometryTypes.includes(obj.type);
    } catch (e) {
        return false;
    }
}


// Parse GeometryCollection from a specific field name
function parse_EmbeddedGeoJson(rows, field) {
    const features = [];

    const valid_Types = new Set([
        "Point",
        "MultiPoint",
        "LineString",
        "MultiLineString",
        "Polygon",
        "MultiPolygon"
    ]);

    rows.forEach((row, index) => {
        if (!row[field]) return;

        let geometry;
        try {
            geometry = JSON.parse(row[field]);
        } catch {
            console.warn("Invalid embedded geometry at row", index);
            return;
        }

        // unwrap Feature
        if (geometry?.type === "Feature" && geometry.geometry) {
            geometry = geometry.geometry;
        }

        // unwrap FeatureCollection
        if (geometry?.type === "FeatureCollection" && Array.isArray(geometry.features)) {
            geometry.features.forEach((f, subIndex) => {
                if (!f?.geometry || !valid_Types.has(f.geometry.type)) return;

                features.push({
                    type: "Feature",
                    geometry: f.geometry,
                    properties: { ...row, ...f.properties },
                    id: `${index}_${subIndex}`
                });
            });
            return;
        }

        // flatten GeometryCollection
        if (geometry?.type === "GeometryCollection" && Array.isArray(geometry.geometries)) {
            geometry.geometries.forEach((g, subIndex) => {
                if (!g || typeof g.type !== "string" || !valid_Types.has(g.type)) return;

                features.push({
                    type: "Feature",
                    geometry: g,
                    properties: { ...row },
                    id: `${index}_${subIndex}`
                });
            });
            return;
        }

        if (!valid_Types.has(geometry?.type)) return;

        features.push({
            type: "Feature",
            geometry: geometry,
            properties: { ...row },
            id: index
        });
    });

    return { type: "FeatureCollection", features };
}

function parseWKTField(rows, field) {
    const features = [];

    rows.forEach((row, index) => {
        if (!row[field]) return;

        const geom = wellknown.parse(row[field]);
        if (!geom) {
            console.warn("Invalid WKT at row", index);
            return;
        }

        features.push({
            type: "Feature",
            geometry: geom,
            properties: { ...row },
            id: index
        });
    });

    return { type: "FeatureCollection", features };
}

function parse_LatLon(rows, latfield, lonfield) {
    const features = [];

    rows.forEach((row, index) => {
        if (row[latfield] == null || row[lonfield] == null) return;

        const lat = parseFloat(row[latfield]);
        const lon = parseFloat(row[lonfield]);
        if (isNaN(lat) || isNaN(lon)) return;

        const { ...props } = row;

        features.push({
            type: "Feature",
            geometry: { type: "Point", coordinates: [lon, lat] },
            properties: props,
            id: index
        });
    });

    return { type: "FeatureCollection", features };
}

// ============================
// Detect WKT field
// ============================

function detect_WKT_Field(headers, firstRowData) {
    const candidates = ['geometry_wkt', 'geometry', 'wkt', 'geom'];

    // 1) Name-based detection
    const byName = headers.find(h => candidates.includes(h.toLowerCase()));
    if (byName) return byName;

    // 2) Content-based detection (first row)
    const byContent = headers.find(h => is_ContentWKT(firstRowData[h]));
    if (byContent) return byContent;

    return null;
}

function is_ContentWKT(value) {
    if (typeof value !== 'string') return false;

    const trimmed = value.trim().toUpperCase();

    return (
        trimmed.startsWith('POINT(') ||
        trimmed.startsWith('LINESTRING(') ||
        trimmed.startsWith('POLYGON(') ||
        trimmed.startsWith('MULTIPOINT(') ||
        trimmed.startsWith('MULTILINESTRING(') ||
        trimmed.startsWith('MULTIPOLYGON(') ||
        trimmed.startsWith('GEOMETRYCOLLECTION(')
    );
}

