# Digital Periegesis — Search in Maps

This is the main interactive mapping tool of the Digital Periegesis project. It allows users to explore, search, and visualize geographic data from Pausanias' *Description of Greece* and other historical or user-provided datasets.

---

## 🌍 Features

- **Base Map Navigation**: OpenStreetMap and alternative base layers.
- **Search Tools**: Search by coordinates, place name, or map click.
- **Map Areas**: Load thematic or administrative layers from server or local files.
- **Map Places**: Load place-based datasets (e.g., Pausanias' Books) from server or local files.
- **Local File Loader**: Upload your own `.geojson`, `.kml`, `.csv`, or `.json` files.
- **Popups**: Display sanitized property data with clickable links.

---

## GeometryCollection Handling

Leaflet does not natively render `GeometryCollection` features as unified visual layers. To ensure consistent interactivity (hover and popup behavior) across all geometries, this project includes a preprocessing step that normalizes `GeometryCollection` features before rendering.

### What the normalization does:
- If a `GeometryCollection` contains only `Polygon` geometries, it is converted into a single `MultiPolygon` feature.
- If it contains mixed geometry types (e.g., `Polygon`, `Point`, `LineString`), the `Polygon` geometries are grouped into a `MultiPolygon`, and the remaining geometries are split into separate features.

The normalization is handled by the `normalizeGeometryCollections()` utility before passing data to `L.geoJSON()`.

---

## Supported Geometry Types

This application supports all standard GeoJSON geometry types that Leaflet can render natively:

| Geometry Type       | Leaflet Support | Notes |
|---------------------|------------------|-------|
| `Point`             | ✅ Full support   | Rendered as markers, clustered if enabled |
| `MultiPoint`        | ✅ Full support   | Rendered as multiple markers |
| `LineString`        | ✅ Full support   | Styled as polylines |
| `MultiLineString`   | ✅ Full support   | Styled as grouped polylines |
| `Polygon`           | ✅ Full support   | Styled as filled areas with hover/popup |
| `MultiPolygon`      | ✅ Full support   | Treated as a single region with unified interactivity |
| `GeometryCollection`| ⚠️ Partial support | Leaflet does not render this as a unified layer. To ensure consistent behavior, the app preprocesses these features:
- If all geometries are `Polygon`, they are merged into a single `MultiPolygon`.
- Mixed geometry types are split into individual features with shared properties.

This preprocessing ensures:
- ✅ Unified hover and popup behavior for regions
- ✅ No double rendering
- ✅ Compatibility with Leaflet’s rendering model

---

## 📁 Folder Structure

```
/app_view_to_maps/
├── tools/
│   ├── validate_csv_to_geojson.html
│   ├── filter_csv_to_geojson_kml.html
│   └── ...
├── map_areas/
│   └── index.json
├── map_places/
│   └── index.json
```

---

## 🧩 Adding New Tools

1. Create a new HTML file in `/map_tools/` using underscores in the filename.
2. Add a new entry in `tools.php` using a hyphenated SEO-friendly alias:
   ```php
   $toolMap = [
     'convert-csv-to-geojson' => 'convert_csv_to_geojson',
     ...
   ];
   ```

---

## 🌐 SEO & Metadata

Each tool page includes:
- Unique `<title>` and `<meta>` tags
- Canonical URLs using SEO-friendly query strings (e.g., `tools.php?p=convert-csv-to-geojson`)

---

## 🧪 Testing

- Test all tools with valid and invalid files.
- Confirm proper rendering of popups, map overlays, and search results.
- Validate metadata and canonical tags using browser dev tools or SEO validators.

---

## 🤝 Credits

Developed by [Public Sphere](https://www.publicsphere.gr)  
Part of the [Digital Periegesis](https://www.periegesis.org) project.

---

## 📬 Contact

For questions or contributions, contact: `info@periegesis.org`
```
