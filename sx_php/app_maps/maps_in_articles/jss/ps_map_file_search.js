
/**
 * ========================================================
 * Public Sphere 2025-12
 * FILE: ps_map_file_searching.js
 * Searching and Filtering loaded GeoJson Object form any supported File formats
 * ========================================================
 */


// Allow multiple possible field/property names (actual keys) as Aliases
// for the 3 filtering keys used by the program.
// Unique values will appear as options in select elements
const key_Aliases = {
    type: ['type', 'place_type', 'work_type', 'timePeriodsKeys', 'city', 'name', '2001deme'],
    region: ['region', '[AnimationClock] Location Reference', 'timePeriodsRange', 'name_en', '2025demosen'],
    passages: ['passages', 'bookid', 'identifier']
};

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

function populate_Filters(map, map_Filters, map_Legends, geojson, file_Name) {
    // Array with the actual key (Field/Property) name used in loaded file that correspond 
    // to predefined Aliases for the standard names: Passages, Region, Type.
    const keyMap = {};

    const features = geojson.features;

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
    map_Filters.querySelector('.book-filter-container').style.display = hasPassages ? 'block' : 'none';
    map_Filters.querySelector('.region-filter-container').style.display = hasRegion ? 'block' : 'none';
    map_Filters.querySelector('.type-filter-container').style.display = hasType ? 'block' : 'none';

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

    const bookFilter = map_Filters.querySelector(".book-filter");
    const regionFilter = map_Filters.querySelector(".region-filter");
    const typeFilter = map_Filters.querySelector(".type-filter");
    const submitTextFilter = map_Filters.querySelector('.submit-text-filter');

    bookFilter.innerHTML = '<option value="">' + keyMap['passages'] + '</option>' + [...bookSet].sort().map(b => `<option value="${b}">${b}</option>`).join('');
    typeFilter.innerHTML = '<option value="">' + keyMap['type'] + '</option>' + [...typeSet].sort().map(t => `<option value="${t}">${t}</option>`).join('');
    regionFilter.innerHTML = '<option value="">' + keyMap['region'] + '</option>' + [...regionSet].sort().map(r => `<option value="${r}">${r}</option>`).join('');

    // Filter by clicking on Search Button, not by change
    //bookFilter.addEventListener('change', () => apply_Filters(map_Filters, map_Legends, geojson, file_Name));
    //typeFilter.addEventListener('change', () => apply_Filters(map_Filters, map_Legends, geojson, file_Name));
    //regionFilter.addEventListener('change', () => apply_Filters(map_Filters, map_Legends, geojson, file_Name));
    submitTextFilter.addEventListener('click', () => apply_Filters(map, map_Filters, keyMap, map_Legends, geojson));

    // Download buttons
    const geoBtn = map_Filters.querySelector('.download-geojson');
    geoBtn.addEventListener('click', () => download_GeoJSON(map_Legends));

    const csvBtn = map_Filters.querySelector('.download-csv');
    csvBtn.addEventListener('click', () => download_CSV(map_Legends));

}

// Check the name of filtering fields indepandent of case
function get_PropertyCI(obj, targetKey) {
    if (!targetKey || typeof targetKey !== 'string') return undefined;

    const entry = Object.entries(obj)
        .find(([k]) => k.toLowerCase() === targetKey.toLowerCase());
    return entry?.[1];
}

function get_BaseName(fileName) {
    const pos = fileName.lastIndexOf(".");
    return pos >= 0 ? fileName.substring(0, pos) : fileName;
}


function apply_Filters(map, map_Filters, keyMap, map_Legends, geoJSON) {
    // Grab filter values once
    const bookVal = map_Filters.querySelector(".book-filter").value;
    const regionVal = map_Filters.querySelector(".region-filter").value;
    const typeFirstVal = map_Filters.querySelector(".type-filter").value;
    const textVal = map_Filters.querySelector(".text-filter").value.trim();
    const q = textVal.toLowerCase();

    const has_AnyFilterValue = [bookVal, typeFirstVal, regionVal, textVal].some(Boolean);

    const filtered = geoJSON.features.filter(f => {
        const props = f.properties;

        const passages = parse_MaybeJSON(get_PropertyCI(props, keyMap.passages)) || [];
        const types = parse_MaybeJSON(get_PropertyCI(props, keyMap.type)) || [];
        const region = (get_PropertyCI(props, keyMap.region) || '');

        // BOOK (case sensitive, exact prefix match)
        const bookMatch =
            !bookVal ||
            passages.some(p => typeof p === "string" && p.startsWith(bookVal + "."));

        // TYPE dual select (case sensitive, exact)
        const selectedTypes = [typeFirstVal].filter(Boolean);

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
        if (regionVal) search_Name += '_' + regionVal;
        if (typeFirstVal) search_Name += '_' + typeFirstVal;
        if (textVal) search_Name += '_' + textVal;

    } else {
        //search_Name = 'Original File';
        alert('Please chose options or enter free text!');
        return;
    }

    //Normalize search name and save it in a list (set)
    // to avoid different searches for, e.g., Athens and athens
    const { unique_Filters } = map_Legends;

    const normalizedName = search_Name.trim().toLowerCase();
    if (unique_Filters.has(normalizedName)) {
        alert('Filter already exists in Loaded File & Filters: ' + search_Name);
        return;
    }
    unique_Filters.add(normalizedName);

    const { open_MapLayers } = map_Legends;

    let format = 'geojson'
    const initial_File = open_MapLayers.find(f => f.filter === false);

    const initial_FileName = initial_File.name;
    format = initial_File.format;

    const geoLayer = L.geoJson(filtered);
    handle_LayerPopupHover(map, map_Legends, geoLayer, search_Name, format, true, initial_FileName);
}


/**
 * ========================================
 * Download GeoJson
 * ========================================
 */

function download_GeoJSON(map_Legends) {
    const { open_MapLayers } = map_Legends;
    const firstVisible = open_MapLayers.find(f => f.visible);

    if (!firstVisible) {
        alert("Checked a file or a filter to download.");
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

    const blob = new Blob(
        [JSON.stringify(geojsonData, null, 2)],
        { type: "application/json" }
    );

    const a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = `${download_FileName}.geojson`;
    a.click();
}


/**
 * ========================================
 * Download CSV
 * ========================================
 */

function download_CSV(map_Legends) {

    const { open_MapLayers } = map_Legends;
    const firstVisible = open_MapLayers.find(f => f.visible);

    if (!firstVisible) {
        alert("Checked a file or a filter to download.");
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

    let rows = null;
    if (geo_Format === 'embeded') {
        rows = geoData.map(f => {
            const props = clean_MergedProperties(f.properties ?? {});
            geoType = f.geometry?.type ?? "";
            return props;
        });

    } else {

        let geoType = 'point';

        rows = geoData.map(f => {
            const props = clean_MergedProperties(f.properties ?? {});

            //row.geometry_type = f.geometry?.type ?? "";
            geoType = f.geometry?.type.toLowerCase() ?? "";

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
}

// ======================================
// Helpers
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
