
/**
 * ========================================================
 * Public Sphere 2025-12
 * FILE: ps_map_files_loader.js
 * COMMON FUNCTIONS FOR Loading map files as layers on the map
 * Supported file formats: GeoJson, KML, TopoJson, GPX, CSV, JSON
 * ========================================================
 */

// Global variables to save actual Field Names of loaded CSV files, including names 
// for Latitutde and Longitude, to be reused when filtered geojson is converted to CSV
let csv_Headers = null;
let csv_Latitude = 'Lat';
let csv_Longitude = 'Lon';

function load_File_ByExtension(scope_Constants, file_Content, file_Extension, file_Name) {
    const extension = file_Extension.toLowerCase();

    try {
        // Parse content if it's a string
        const isJSON = ['geojson', 'topojson', 'json'].includes(extension);
        const data = isJSON && typeof file_Content === 'string'
            ? JSON.parse(file_Content)
            : file_Content;

        switch (extension) {
            case 'geojson': {
                load_File_GeoJSON(scope_Constants, data, extension, file_Name);
                break;
            }
            case 'topojson': {
                load_File_TopoJSON(scope_Constants, data, extension, file_Name);
                break;
            }
            case 'kml': {
                load_File_KML(scope_Constants, file_Content, extension, file_Name);
                break;
            }
            case 'gpx': {
                load_File_GPX(scope_Constants, file_Content, extension, file_Name);
                break;
            }
            case 'csv': {
                load_File_CSV(scope_Constants, file_Content, extension, file_Name);
                break;
            }
            case 'json': {
                if (data.type === "Topology") {
                    load_File_TopoJSON(scope_Constants, data, "topojson", file_Name);
                } else if (data.type === "FeatureCollection" || data.type === "Feature") {
                    load_File_GeoJSON(scope_Constants, data, 'geojson', file_Name);
                } else if (Array.isArray(data)) {
                    load_File_JSON(scope_Constants, data, extension, file_Name);
                } else {
                    console.error("Unknown JSON format");
                }
                break;
            }
            default:
                console.warn("Unsupported file type: " + extension);
        }
    } catch (err) {
        console.error("Parsing error:", err);
        console.warn("Invalid or unreadable file.");
    }
}


function load_File_JSON(scope_Constants, data, extension, file_Name) {
    let geojson;

    if (isGeoJSON(data)) {
        geojson = data;
    } else if (Array.isArray(data)) {
        geojson = convert_SimpleJSONtoGeoJSON(data);
    } else {
        console.warn("Unrecognized file structure.");
        return;
    }
    //[map, map_Legends, map_Instance, map_Filters, map_MetaData]
    const [map, map_Legends, , map_Filters] = scope_Constants;

    populate_Filters(map, map_Filters, map_Legends, geojson, file_Name)

    const layerGroup = L.geoJSON(geojson);
    handle_LayerPopupHover(map, map_Legends, layerGroup, file_Name, extension);
}

function load_File_GeoJSON(scope_Constants, data, extension, file_Name) {
    if (data.type === "FeatureCollection" || data.type === "GeometryCollection") {

        const [map, map_Legends, map_Filters] = scope_Constants;
        populate_Filters(map, map_Filters, map_Legends, data, file_Name)

        const geoLayer = L.geoJSON(data);
        handle_LayerPopupHover(map, map_Legends, geoLayer, file_Name, extension);

    } else {
        console.warn("Unrecognized JSON structure.");
    }
}

function load_File_TopoJSON(scope_Constants, topoJSON, extension, file_Name) {
    const topoLayer = omnivore.topojson.parse(topoJSON);

    const geojson = topoLayer.toGeoJSON();

    const [map, map_Legends, map_Filters] = scope_Constants;

    populate_Filters(map, map_Filters, map_Legends, geojson, file_Name)

    handle_LayerPopupHover(map, map_Legends, topoLayer, file_Name, extension);

}

function load_File_KML(scope_Constants, kmlText, extension, file_Name) {
    const parser = new DOMParser();
    const kmlDom = parser.parseFromString(kmlText, 'text/xml');

    const [map, map_Legends, , map_Filters, map_MetaData] = scope_Constants;

    // 1. Remove Document-level <name>
    kmlDom.querySelectorAll('Document > name').forEach(n => n.remove());

    // 2. Extract metadata from Document-level ExtendedData
    const [mapMeta, mapMetsAll] = extract_KMLMetadata(kmlDom);

    // 3. Display metadataq in the website
    display_MetadataIntoWebsite(map_MetaData,file_Name, mapMetsAll)

    // 4. Inject metadata into placemarks
    inject_MetadataIntoPlacemarks(kmlDom, mapMeta);

    // 5. Remove Document-level ExtendedData
    kmlDom.querySelectorAll('Document > ExtendedData').forEach(ed => ed.remove());

    // 6. Parse
    const kmlLayer = omnivore.kml.parse(kmlDom);
    const geojson = kmlLayer.toGeoJSON();

    geojson.features.forEach(f => {
        if (
            f.properties &&
            (
                f.properties.name === undefined ||
                f.properties.name === null ||
                f.properties.name === "Unnamed"
            )
        ) {
            delete f.properties.name;
        }
    });
    populate_Filters(map, map_Filters, map_Legends, geojson, file_Name);
    handle_LayerPopupHover(map, map_Legends, kmlLayer, file_Name, extension);
}


function load_File_GPX(scope_Constants, gpxText, extension, file_Name) {
    const parser = new DOMParser();
    const gpxDom = parser.parseFromString(gpxText, 'text/xml');

    const gpxLayer = omnivore.gpx.parse(gpxDom);

    const geojson = gpxLayer.toGeoJSON();

    const [map, map_Legends, , map_Filters] = scope_Constants;

    populate_Filters(map, map_Filters, map_Legends, geojson, file_Name)

    handle_LayerPopupHover(map, map_Legends, gpxLayer, file_Name, extension)
}


// Use papaparse.min.js to deal even with malformed CSV files
function load_File_CSV(scope_Constants, csvText, extension, file_Name) {
    // Parse and normalize CSV into GeoJSON - Get actual format
    let [geojson, format] = normalize_CsvToGeoJson(csvText);
    if(!format) {
        format = extension
    }
    const [map, map_Legends, , map_Filters] = scope_Constants;

    populate_Filters(map, map_Filters, map_Legends, geojson, file_Name)

    // Create Leaflet layer from GeoJSON
    const geoLayer = L.geoJson(geojson);

    handle_LayerPopupHover(map, map_Legends, geoLayer, file_Name, format);
}
/*
    ==================
    Help functions called from the above load_file_XXX() functions
    ==================
 */


function extract_KMLMetadata(kmlDom) {
    const meta = {};
    const meta_All = {};

    const docExtended = kmlDom.querySelector('Document > ExtendedData');
    if (!docExtended) return [meta, meta_All];

    docExtended.querySelectorAll('Data').forEach(d => {
        const key = d.getAttribute('name')?.toLowerCase();
        const val = d.querySelector('value')?.textContent.trim();
        if (!key || !val) return;

        meta_All[key] = val
        if (['author', 'updated', 'version'].includes(key)) {
            meta[key] = val;
        }
    });

    return [meta, meta_All];
}

function inject_MetadataIntoPlacemarks(kmlDom, meta) {
    if (!Object.keys(meta).length) return;

    kmlDom.querySelectorAll('Placemark').forEach(pm => {
        let ext = pm.querySelector('ExtendedData');
        if (!ext) {
            ext = kmlDom.createElement('ExtendedData');
            pm.appendChild(ext);
        }

        Object.entries(meta).forEach(([key, val]) => {
            // Do not overwrite existing placemark data
            if (ext.querySelector(`Data[name="${key}"]`)) return;

            const data = kmlDom.createElement('Data');
            data.setAttribute('name', key);

            const value = kmlDom.createElement('value');
            value.textContent = val;

            data.appendChild(value);
            ext.appendChild(data);
        });
    });
}

function display_MetadataIntoWebsite(map_MetaData, file, meta) {
    if (!Object.keys(meta).length) return;

    let html = `<p><em>${file}</em>`;
    Object.entries(meta).forEach(([key, val]) => {
        html += `, <b>${key}:</b> ${val}`
    });
    map_MetaData.innerHTML = html + '</p>';
}


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


function console_log(Name, layer) {
    console.log(Name);
    layer.eachLayer(function (layer) {
        console.log('Geometry: ', layer.feature.geometry);
        console.log('Properties: ', layer.feature.properties);
    });

}


// ================================
// Imortant! Send back the format (geo-fotmat) of the CSV file
// Used with conversion back to CSV file
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

    // 3) Detect Lat/Lon fields in ordinary CSV file
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
// Helpers
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

function parse_EmbeddedGeoJson(rows, field) {
    const features = [];

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
                if (!g || typeof g.type !== "string") return;
                features.push({
                    type: "Feature",
                    geometry: g,
                    properties: { ...row },
                    id: `${index}_${subIndex}`
                });
            });
            return;
        }

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

        const { [latfield]: _, [lonfield]: __, ...props } = row;

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

// ============================
// Detect Lat/Lon fields (merged approach)
// ============================
function detect_LatLonFields(headers) {
    const normalized = headers.map(h => h.trim());

    const latCandidates = [
        'lat', 'latitude', 'representative_latitude', 'reprLat'
    ];
    const lonCandidates = [
        'lon', 'lng', 'long', 'longitude', 'representative_longitude', 'reprLong'
    ];

    // First try exact match (case-insensitive)
    let latfield = normalized.find(h => latCandidates.some(c => c.toLowerCase() === h.toLowerCase())) || null;
    let lonfield = normalized.find(h => lonCandidates.some(c => c.toLowerCase() === h.toLowerCase())) || null;

    // If not found, fallback to flexible regex search
    if (!latfield) latfield = normalized.find(h => /lat/i.test(h)) || null;
    if (!lonfield) lonfield = normalized.find(h => /lon|lng/i.test(h)) || null;

    return { latfield, lonfield };
}


