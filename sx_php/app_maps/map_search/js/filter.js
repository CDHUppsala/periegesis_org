// Global array with actual key name used in the object
const keyMap = {};

// Allow multiple possible field or property names (actual keys) per filtering key
const key_Aliases = {
    type: ['type', 'timePeriodsKeys'],
    region: ['region', '[AnimationClock] Location Reference', 'timePeriodsRange', '2025regionalentity en'],
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

function populate_Filters(geo_json, file_Name) {
    const features = geo_json.features;

    // Reset before checking
    show_ConvertPreviws = false;
    // Check if fields for filtering exists in the CSV file (type/Type, region/Region, etc.)
    const hasType = features.some(f =>
        hasAnyPropertyCI(f.properties, key_Aliases.type)
    );
    const hasRegion = features.some(f =>
        hasAnyPropertyCI(f.properties, key_Aliases.region)
    );
    const hasPassages = features.some(f =>
        hasAnyPropertyCI(f.properties, key_Aliases.passages)
    );

    // Hide select elements for non-existing filtering fields
    document.getElementById('typeFilterContainer').style.display = hasType ? 'block' : 'none';
    document.getElementById('regionFilterContainer').style.display = hasRegion ? 'block' : 'none';
    document.getElementById('bookFilterContainer').style.display = hasPassages ? 'block' : 'none';

    if (hasType || hasRegion || hasPassages) {
        show_ConvertPreviws = true;
        document.getElementById('filter_Wrapper').style.display = 'flex';
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

        // TYPE
        const types = parse_MaybeJSON(f.properties[keyMap['type']]);
        types.forEach(t => t && typeSet.add(t));

        // PASSAGES - EXTRACT BOOK NUMBER
        const passages = parse_MaybeJSON(f.properties[keyMap['passages']]);
        passages.forEach(p => {
            const prefix = p.split('.')[0];
            if (prefix) bookSet.add(prefix);
        });

        // REGION
        const region = f.properties[keyMap['region']];
        if (region) regionSet.add(region.trim());

    });

    const bookFilter = document.getElementById('bookFilter');
    const typeFilter = document.getElementById('typeFilter');
    const typeSecondFilter = document.getElementById('typeSecondFilter');
    const regionFilter = document.getElementById('regionFilter');

    bookFilter.innerHTML = '<option value="">Filter by Book</option>' + [...bookSet].sort().map(b => `<option value="${b}">${b}</option>`).join('');
    typeFilter.innerHTML = '<option value="">Filter by Type A</option>' + [...typeSet].sort().map(t => `<option value="${t}">${t}</option>`).join('');
    typeSecondFilter.innerHTML = '<option value="">and by Type B</option>' + [...typeSet].sort().map(t => `<option value="${t}">${t}</option>`).join('');
    regionFilter.innerHTML = '<option value="">Filter by Region</option>' + [...regionSet].sort().map(r => `<option value="${r}">${r}</option>`).join('');

    bookFilter.onchange = () => applyFilters(geo_json, file_Name);
    typeFilter.onchange = () => applyFilters(geo_json, file_Name);
    typeSecondFilter.onchange = () => applyFilters(geo_json, file_Name);
    regionFilter.onchange = () => applyFilters(geo_json, file_Name);

    // Reposition the map to account for the height of filtering select elements
    positionMap();

}

// Check the name of filtering fields indepandent of case
function get_PropertyCI(obj, targetKey) {
    if (!targetKey || typeof targetKey !== 'string') return undefined;

    const entry = Object.entries(obj)
        .find(([k]) => k.toLowerCase() === targetKey.toLowerCase());
    return entry?.[1];
}

function applyFilters(geoJSON, file_Name) {
    const bookVal = document.getElementById('bookFilter').value;
    const typeFirstVal = document.getElementById('typeFilter').value;
    const typeSecondVal = document.getElementById('typeSecondFilter').value;
    const regionVal = document.getElementById('regionFilter').value;

    const filtered = geoJSON.features.filter(f => {
        const passagesRaw = get_PropertyCI(f.properties, keyMap['passages']);
        const passages = parse_MaybeJSON(passagesRaw);

        const typeRaw = get_PropertyCI(f.properties, keyMap['type']);
        const types = parse_MaybeJSON(typeRaw);

        const region = get_PropertyCI(f.properties, keyMap['region']) || '';

        // BOOK
        const bookMatch =
            !bookVal ||
            passages.some(p => typeof p === "string" && p.startsWith(bookVal + '.'));

        // TYPE (correct dual-select logic)
        const selectedTypes = [typeFirstVal, typeSecondVal].filter(v => v);
        const typeMatch =
            selectedTypes.length === 0 ||
            selectedTypes.some(t => types.includes(t));

        // REGION
        const regionMatch =
            !regionVal ||
            region === regionVal;

        return bookMatch && typeMatch && regionMatch;
    });

    const geoLayer = L.geoJson(filtered);
    handle_LayerPopupHover(geoLayer, file_Name, 'geojson');
}


// Hide/Show Filter Elements
document.getElementById('ToggleFilterElements').addEventListener('click', () => {
    const top_Icon = document.getElementById('sx_sreen_to_top');
    const botton_Icon = document.getElementById('sx_sreen_to_botton');
    const filter = document.getElementById('filter_Wrapper');

    // Toggle SVG visibility
    const isTop_tVisible = window.getComputedStyle(top_Icon).display !== 'none';
    top_Icon.style.display = isTop_tVisible ? 'none' : 'inline';
    botton_Icon.style.display = isTop_tVisible ? 'inline' : 'none';

    // Toggle legend visibility
    filter.style.display = (filter.style.display === 'none') ? 'flex' : 'none';
    positionMap();
});