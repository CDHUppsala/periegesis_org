
/**
 * ========================================================
 * Public Sphere 2025-12
 * FILE: ps_ms_mapFileSearch.js
 * Searching and Filtering loaded GeoJson Object form any supported File formats
 * ========================================================
 */


/**
 * ========================================================
 * Pipilate Select elements with unique values/options from corresponding Field/Propwerty Names
 * @param {*} geo_json 
 * @param {*} file_Name 
 * ========================================================
 */

function populate_Filters(geo_json, file_Name) {
    const features = geo_json.features;

    // Check if fields for filtering exists in the CSV file (type/Type, region/Region, etc.)
    const hasPassages = features.some(f =>
        hasAnyPropertyCI(f.properties, key_Aliases.passages)
    );
    const hasType = features.some(f =>
        hasAnyPropertyCI(f.properties, key_Aliases.type)
    );
    const hasRegion = features.some(f =>
        hasAnyPropertyCI(f.properties, key_Aliases.region)
    );

    // Hide select elements for non-existing filtering fields
    document.getElementById('BookFilterContainer').style.display = hasPassages ? 'block' : 'none';
    document.getElementById('TypeFilterContainer').style.display = hasType ? 'block' : 'none';
    document.getElementById('TypeFilterContainer_2').style.display = hasType ? 'block' : 'none';
    document.getElementById('RegionFilterContainer').style.display = hasRegion ? 'block' : 'none';

    // Dublicate (Place) Types only for Pausanias Books (remove 1 in Type 1)
    let first_TypeNumber = " 1";
    if (!hasPassages) {
        document.getElementById('TypeFilterContainer_2').style.display = 'none';
        first_TypeNumber = '';
    }

    if (hasType || hasRegion || hasPassages) {
        document.getElementById('FiltersWrapper').style.display = 'flex';
        document.getElementById('TextFilterContainer').style.display = 'block';
        document.getElementById('ToggleFilterElements').style.display = 'block';
    } else {
        document.getElementById('FiltersWrapper').style.display = 'flex';
        document.getElementById('TextFilterContainer').style.display = 'block';
        document.getElementById('ToggleFilterElements').style.display = 'block';
    }

    //Populate the global variable with actual key name
    const sample_Props = features[0]?.properties || {};

    for (const logicalKey in key_Aliases) {
        for (const alias of key_Aliases[logicalKey]) {
            const actual = get_ActualKey(sample_Props, alias);
            if (actual) {
                keyMap[logicalKey] = actual;
                break; // Stop as soon as one alias is found
            }
        }
    }

    const typeSet = new Set();
    const bookSet = new Set();
    const regionSet = new Set();

    features.forEach(f => {

        // PASSAGES - EXTRACT BOOK NUMBER
        const passages = parse_MaybeJSON(f.properties[keyMap['passages']]);
        passages.forEach(p => {
            const prefix = p.split('.')[0];
            if (prefix) bookSet.add(prefix);
        });

        // TYPE
        const types = parse_MaybeJSON(f.properties[keyMap['type']]);
        types.forEach(t => t && typeSet.add(t));

        // REGION
        const region = f.properties[keyMap['region']];
        if (region) regionSet.add(region.trim());

    });

    const bookFilter = document.getElementById("BookFilter");
    const typeFilter = document.getElementById("TypeFilter");
    const typeFilter_2 = document.getElementById("TypeFilter_2");
    const regionFilter = document.getElementById("RegionFilter");
    const submitTextFilter = document.getElementById('SubmitTextFilter');

    bookFilter.innerHTML = '<option value="">' + keyMap['passages'] + '</option>' + [...bookSet].sort().map(b => `<option value="${b}">${b}</option>`).join('');
    typeFilter.innerHTML = '<option value="">' + keyMap['type'] + first_TypeNumber + '</option>' + [...typeSet].sort().map(t => `<option value="${t}">${t}</option>`).join('');
    typeFilter_2.innerHTML = '<option value="">or ' + keyMap['type'] + ' 2</option>' + [...typeSet].sort().map(t => `<option value="${t}">${t}</option>`).join('');
    regionFilter.innerHTML = '<option value="">' + keyMap['region'] + '</option>' + [...regionSet].sort().map(r => `<option value="${r}">${r}</option>`).join('');

    submitTextFilter.onclick = () => apply_Filters(geo_json, file_Name);

    // Reposition the map to account for the height of filtering select elements
    re_positionMap();

    //console.log( [...typeSet].sort().map(t => t).join('\n'))
}


// =================================
// Helpers for populating filters
// =================================

// Check Case-Insensitive property names
function has_Property_CI(obj, targetKey) {
    return Object.keys(obj).some(k => k.toLowerCase() === targetKey.toLowerCase());
}


function hasAnyPropertyCI(obj, aliasList) {
    return aliasList.some(alias =>
        has_Property_CI(obj, alias)
    );
}
// returns the actual key name used in the object
function get_ActualKey(obj, targetKey) {
    const entry = Object.keys(obj).find(k => k.toLowerCase() === targetKey.toLowerCase());
    return entry || null;
}

function parse_MaybeJSON(value) {

    if (value == null) return [];

    // JSON array string? (e.g. "["a","b"]")
    if (typeof value === 'string') {
        const trimmed = value.trim();

        // JSON
        if (trimmed.startsWith('[') && trimmed.endsWith(']')) {
            try {
                return JSON.parse(trimmed);
            } catch {
                return [value];
            }
        }

        // CSV-like list with comma separation
        if (trimmed.includes(',')) {
            return trimmed.split(',').map(v => v.trim()).filter(Boolean);
        }

        // Single string value
        return [trimmed];
    }

    // Real array
    if (Array.isArray(value)) return value;

    // Anything else (numbers, booleans)
    return [value];
}


/**
 * ========================================================
 * Applay Filters (Search by selected options and Free Text)
 * @param {*} geo_json 
 * @param {*} file_Name 
 * @returns 
 * ========================================================
 */

function apply_Filters(geo_json, file_Name) {
    // Grab filter values once
    const bookVal = document.getElementById("BookFilter").value;
    const typeFirstVal = document.getElementById("TypeFilter").value;
    const typeSecondVal = document.getElementById("TypeFilter_2").value;
    const regionVal = document.getElementById("RegionFilter").value;
    const textVal = document.getElementById("TextFilter").value.trim();
    const q = textVal.toLowerCase();

    const has_AnyFilterValue = [bookVal, typeFirstVal, typeSecondVal, regionVal, textVal].some(Boolean);

    const geo_features = geo_json.features;

    const filtered = geo_features.filter(f => {
        const props = f.properties;

        const passages = parse_MaybeJSON(get_PropertyCI(props, keyMap.passages)) || [];
        const types = parse_MaybeJSON(get_PropertyCI(props, keyMap.type)) || [];
        const region = (get_PropertyCI(props, keyMap.region) || '');

        // BOOK (case sensitive, exact prefix match)
        const bookMatch =
            !bookVal ||
            passages.some(p => typeof p === "string" && p.startsWith(bookVal + "."));


        // TYPE dual select (case sensitive, exact)
        const selectedTypes = [typeFirstVal, typeSecondVal].filter(Boolean);

        const typeMatch =
            selectedTypes.length === 0 ||
            selectedTypes.some(t => types.includes(t));

        // REGION dropdown (case sensitive, exact)
        const regionMatch =
            !regionVal ||
            region === regionVal;

        // TEXT SEARCH (case-insensitive, substring)

        let textMatch = true;
        if (q !== "") {
            const book_Match = passages.some(
                p => typeof p === "string" && p.toLowerCase().includes(q)
            );

            const type_Match = types.some(
                p => typeof p === "string" && p.toLowerCase().includes(q)
            );

            const region_Match = region.toLowerCase().includes(q);

            // match against ANY other string property (excluding JSON arrays/objects)
            const other_Match = Object.values(props).some(val => {
                if (typeof val !== "string") return false;

                const trimmed = val.trim();

                return trimmed.toLowerCase().includes(q);
            });

            textMatch = book_Match || type_Match || region_Match || other_Match;
        }

        return bookMatch && typeMatch && regionMatch && textMatch;
    });

    let search_Name = '';
    if (has_AnyFilterValue) {
        search_Name = 'Filtered';
        if (bookVal) search_Name += '_' + bookVal;
        if (typeFirstVal) search_Name += '_' + typeFirstVal;
        if (typeSecondVal) {
            if (typeFirstVal && typeFirstVal === typeSecondVal) {
                search_Name += '';
            } else {
                search_Name += '_' + typeSecondVal;
            }
        }
        if (regionVal) search_Name += '_' + regionVal;
        if (textVal) search_Name += '_' + textVal;
    } else {
        alert('Please chose options or enter free text!');
        return;
    }

    //const firstVisible = open_MapLayers.find(f => f.visible);
    //const format = firstVisible.format;
    //const first_LoadedFile = open_MapLayers[0];
    //const format = first_LoadedFile.format;

    const search_NameExists = open_MapLayers
        .filter(entry => entry.filter)
        .some(entry =>
            entry.name.toLowerCase() === search_Name.toLowerCase() && entry.sourceFileName === file_Name
        );

    if (search_NameExists) {
        alert('The filter ' + search_Name + ' already exists in your Loaded Files and Filters.')
        return;
    }

    const last_Unfiltered = open_MapLayers
        .slice()
        .reverse()
        .find(entry => entry.filter === false);

    const format = last_Unfiltered.format;

    const geoLayer = L.geoJson(filtered);
    handle_LayerPopupHover(geoLayer, search_Name, format, true, file_Name);
}


// ===============================
// Helpers for filtering/searching
// ===============================

// Check the name of filtering fields indepandent of case
function get_PropertyCI(obj, targetKey) {
    if (!targetKey || typeof targetKey !== 'string') return undefined;

    const entry = Object.entries(obj)
        .find(([k]) => k.toLowerCase() === targetKey.toLowerCase());
    return entry?.[1];
}



// ======================================
// Hide/Show Filter Elements
// ======================================

document.getElementById('ToggleFilterElements').addEventListener('click', () => {
    const top_Icon = document.getElementById('sx_sreen_to_top');
    const botton_Icon = document.getElementById('sx_sreen_to_botton');
    const filter = document.getElementById('FiltersWrapper');

    // Toggle SVG visibility
    const isTop_tVisible = window.getComputedStyle(top_Icon).display !== 'none';
    top_Icon.style.display = isTop_tVisible ? 'none' : 'inline';
    botton_Icon.style.display = isTop_tVisible ? 'inline' : 'none';

    // Toggle legend visibility
    filter.style.display = (filter.style.display === 'none') ? 'flex' : 'none';
    re_positionMap();
});


/**
 * ========================================
 * Download GeoJson
 * ========================================
 */

document.getElementById("DownloadFilteredGeojson").addEventListener("click", () => {
    const firstVisible = open_MapLayers.find(f => f.visible);

    if (!firstVisible) {
        alert("Checked a file or a filter to download.\nThe FIRST CHECKED object will be downloaded.");
        return;
    }

    const name = firstVisible.name;
    const is_Filter = firstVisible.filter;
    const source_FileName = firstVisible.sourceFileName;

    let download_FileName;
    if (is_Filter && source_FileName !== '') {
        download_FileName = get_BaseName(source_FileName) + '_' + name
    } else {
        download_FileName = get_BaseName(name);
    }

    // Expand merged geopmetries
    const geoData = expand_MergedFeatures(firstVisible.data);

    const cleanFeatures = geoData.map(f => ({
        type: "Feature",
        geometry: structuredClone(f.geometry),
        properties: clean_MergedProperties(f.properties ?? {})
    }));

    const geojsonData = {
        type: "FeatureCollection",
        features: cleanFeatures
    };

    const blob = new Blob([JSON.stringify(geojsonData, null, 2)], {
        type: "application/json"
    });

    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `${download_FileName}.geojson`;
    a.click();
    URL.revokeObjectURL(url);
});


/**
 * ========================================
 * Download CSV
 * ========================================
 */

document.getElementById("DownloadFilteredCSV").addEventListener("click", () => {

    const firstVisible = open_MapLayers.find(f => f.visible);

    if (!firstVisible) {
        alert("Checked a file or a filter to download.\nThe FIRST CHECKED object will be downloaded.");
        return;
    }

    const name = firstVisible.name;
    const geoData = expand_MergedFeatures(firstVisible.data);
    const geo_Format = firstVisible.format;
    const is_Filter = firstVisible.filter;
    const source_FileName = firstVisible.sourceFileName;

    let download_FileName;
    if (is_Filter && source_FileName !== '') {
        download_FileName = get_BaseName(source_FileName) + '_' + name
    } else {
        download_FileName = get_BaseName(name);
    }

    let geoType = '';
    let rows = null;
    if (geo_Format === 'embeded') {
        rows = geoData.map(f => {
            const props = clean_MergedProperties(f.properties ?? {});
            geoType = f.geometry?.type ?? "";
            return props;
        });

    } else {

        rows = geoData.map(f => {
            const props = clean_MergedProperties(f.properties ?? {});

            //row.geometry_type = f.geometry?.type ?? "";
            geoType = f.geometry?.type ?? "";
            geoType = geoType.toLowerCase();

            // Format "wkt" can include geoType = point
            // For WKT files and for all Non point geomatries
            if (geo_Format === 'wkt' || geoType !== 'point') {
                props.geometry_wkt = geometryToWKT(f.geometry);
            } else {
                props['Longitude'] = f.geometry?.coordinates?.[0] ?? "";
                props['Latitude'] = f.geometry?.coordinates?.[1] ?? "";
            }

            return props;
        });
    }

    const c = Array.from(
        new Set(rows.flatMap(r => Object.keys(r)))
    );

    let columns = c;

    if (geo_Format === 'csv') {
        if (geoType === 'point') {
            columns = [
                ...c.filter(c => c !== 'Longitude' && c !== 'Latitude'),
                'Longitude',
                'Latitude'
            ];
        }
    }

    const csv = [
        columns.join(","),
        ...rows.map(row =>
            columns.map(c => JSON.stringify(row[c] ?? "").replace(/\\"/g, '""')).join(",")
        )
    ].join("\n");

    const blob = new Blob([csv], { type: "text/csv" });
    const url = URL.createObjectURL(blob);

    const a = document.createElement("a");
    a.href = url;
    a.download = `${download_FileName}.csv`;
    a.click();

    URL.revokeObjectURL(url);
});



// ======================================
// Helpers for CSV download
// ======================================


function expand_MergedFeatures(features) {
    const out = [];

    for (const f of features) {
        const baseProps = f.properties ?? {};
        const merged = baseProps.merged_entries;

        if (Array.isArray(merged) && merged.length) {
            for (const entry of merged) {
                out.push({
                    ...f,
                    properties: {
                        ...entry
                    }
                });
            }
        } else {
            out.push(f);
        }
    }

    return out;
}


function get_BaseName(fileName) {
    const pos = fileName.lastIndexOf(".");
    return pos >= 0 ? fileName.substring(0, pos) : fileName;
}


function clean_MergedProperties(props) {
    const clean = {};

    for (const key in props) {
        const val = props[key];

        // drop known circular / internal helpers
        if (key === "merged_entries") continue;

        // keep only JSON-safe primitives
        if (
            val === null ||
            typeof val === "string" ||
            typeof val === "number" ||
            typeof val === "boolean"
        ) {
            clean[key] = val;
        }
    }

    return clean;
}

function geometryToWKT(geometry) {
    if (!geometry) return "";

    const coordsToStr = coords =>
        coords.map(c =>
            Array.isArray(c[0])
                ? `(${coordsToStr(c)})`
                : `${c[0]} ${c[1]}`
        ).join(",");

    switch (geometry.type) {
        case "Point":
            return `POINT (${geometry.coordinates[0]} ${geometry.coordinates[1]})`;

        case "LineString":
            return `LINESTRING (${coordsToStr(geometry.coordinates)})`;

        case "MultiLineString":
            return `MULTILINESTRING (${geometry.coordinates.map(c => `(${coordsToStr(c)})`).join(",")})`;

        case "Polygon":
            return `POLYGON (${geometry.coordinates.map(r => `(${coordsToStr(r)})`).join(",")})`;

        case "MultiPolygon":
            return `MULTIPOLYGON (${geometry.coordinates.map(p =>
                `(${p.map(r => `(${coordsToStr(r)})`).join(",")})`
            ).join(",")})`;

        default:
            return "";
    }
}
