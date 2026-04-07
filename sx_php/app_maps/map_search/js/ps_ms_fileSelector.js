

/**
 * ========================================================
 * Public Sphere 2025-12
 * FILE: ps_ms_mapFileSelector.js
 * SELECT files of any supported format - From File System or Select Options
 * ========================================================
 */


// ==============================
// Populate the Select elements with options
// ==============================

// Populate the select element for layer files
let map_AreaFilesList = [];
fetch(`${maps_AreasFolder}/index.json?v=${Date.now()}`)
    .then(response => response.json())
    .then(data => {
        map_AreaFilesList = data; // Store the array globally

        const selector = document.getElementById('MapAreasSelector');
        data.forEach((filename, index) => {
            const option = document.createElement('option');
            option.value = index; // Use index as value
            //option.textContent = filename.replace('.geojson', '').replace(/_/g, ' ');
            option.textContent = filename;
            selector.appendChild(option);
        });
    });

// Populate the select element for marker files
let map_PlaceFilesList = [];
fetch(`${maps_PlacesFolder}/index.json?v=${Date.now()}`)
    .then(r => r.json())
    .then(data => {
        // save the list as associative array (object) in a variable
        map_PlaceFilesList = data;

        // Add the index and name of files to the select element
        const selector = document.getElementById('MapPlacesSelector');
        data.forEach((filename, index) => {
            const option = document.createElement('option');
            option.value = index;
            option.textContent = filename;
            selector.appendChild(option);
        });
    });


// ==============================
// Loade selected map files
// ==============================

function load_MapFile(index, option) {
    // Get the name of the chosen file by its index
    if (Number.isInteger(index)) {
        let file_Name = null;
        let server_fileURL = null;
        if (option === 'place') {
            file_Name = map_PlaceFilesList[index];
            server_fileURL = `${maps_PlacesFolder}/${file_Name}?v=2025-12`;
            if (use_Stacking === false) {
                reset_Areas_SelectOption();
            }

        } else {
            file_Name = map_AreaFilesList[index];
            server_fileURL = `${maps_AreasFolder}/${file_Name}?v=2025-12`;
            if (use_Stacking === false) {
                reset_Places_SelectOption();
            }
        }
        const extension = file_Name.split('.').pop().toLowerCase();

        fetch(server_fileURL)
            .then(response => {
                return extension === 'kml' || extension === 'csv'
                    ? response.text()
                    : response.json();
            })
            .then(content => {
                load_File_ByExtension(content, extension, file_Name);
            });

    } else {
        // Remove everything with empty value from the select options
        loaded_ToMapLayersGroup.clearLayers();
        open_MapLayers.length = 0;
        map_Manager.update();
        info_Legend.update(0, 0);
        return;
    }
}

// Triggers the selection of Area file, from select options
document.getElementById("MapAreasSelector").addEventListener("change", function () {
    keyMap = {};
    const selected_Index = parseInt(this.value);
    load_MapFile(selected_Index, 'area');
});

// Triggers the selection of Place file, from select options
document.getElementById("MapPlacesSelector").addEventListener("change", function () {
    keyMap = {};
    const selected_Index = parseInt(this.value);
    load_MapFile(selected_Index, 'place');
});


// Triggers the windows file system to load User's files
document.getElementById("FileSystemButton").addEventListener("click", function () {
    document.getElementById("User_FileLoader").click();
});

// Windows file system
document.getElementById("User_FileLoader").addEventListener("change", function (event) {
    const file = event.target.files[0];
    if (!file) return;
    const user_File = file['name'];
    const extension = user_File.split('.').pop().toLowerCase();

    keyMap = {};

    // Reset the value of the input opened by the File System to enable reloading the same file
    document.getElementById('User_FileLoader').value = '';

    const reader = new FileReader();
    reader.onload = function (e) {

        load_File_ByExtension(e.target.result, extension, user_File);

    };
    reader.readAsText(file);

});