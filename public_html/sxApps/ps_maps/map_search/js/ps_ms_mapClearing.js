
/**
 * ========================================================
 * Public Sphere 2025-12
 * FILE: ps_ms_mapClearing.js
 * Clears all loaded map places and map areas, if they exist
 * ========================================================
 */


document.getElementById("ClearAllButton").addEventListener("click", () => {

    // Clear all local GeoJSON areas from the common group of areas
    loaded_ToMapLayersGroup.clearLayers();

    // Clean the legend for loaded map file areas
    open_MapLayers.length = 0;
    map_Manager.update();

    // Reset global variables and update the legend for counting places and areas
    placesCount = 0;
    areasCount = 0;
    //info_Legend.update(0, 0);

    // Reset the marker color counter
    get_MarkerColors('reset');

    // Check and clear search system
    if (typeof clear_SearchSystem === "function") {
        clear_SearchSystem();
    }

    // Reset selectors to default option
    reset_SelectOption();

    // Zoon the wondow to places and map areas boundaries only with first load
    first_FileToLoad = true;

    const user_FileInput = document.getElementById('User_FileLoader');
    if (user_FileInput) user_FileInput.value = '';

    // Reset and Hide select elements for filtering fields
    document.getElementById("BookFilter").innerHTML = '';
    document.getElementById("TypeFilter").innerHTML = '';
    document.getElementById("RegionFilter").innerHTML = '';

    document.getElementById('TypeFilterContainer').style.display = 'none';
    document.getElementById('TypeFilterContainer_2').style.display = 'none';
    document.getElementById('RegionFilterContainer').style.display = 'none';
    document.getElementById('BookFilterContainer').style.display = 'none';
    document.getElementById('TextFilterContainer').style.display = 'none';
    document.getElementById('FiltersWrapper').style.display = 'none';

    document.getElementById('sx_sreen_to_top').style.display = 'inline';
    document.getElementById('sx_sreen_to_botton').style.display = 'none';
    document.getElementById('ToggleFilterElements').style.display = 'none';


    // Zoom to default and reposition the map
    setTimeout(() => {
        map.setView([default_Lat, default_Lon], default_Zoom);
        re_positionMap();
    }, 150);

});