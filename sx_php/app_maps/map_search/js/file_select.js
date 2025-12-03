/**
 * ========================================================
 * SELECT FILE TO LOAD - From File System or Select Options
 * ========================================================
 */


// Populate the select element for layer files
let map_LayerList = [];
fetch(`${maps_AreasFolder}/index.json?v=${Date.now()}`)
    .then(response => response.json())
    .then(data => {
        map_LayerList = data; // Store the array globally

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
let map_MarkersList = [];
fetch(`${maps_PlacesFolder}/index.json?v=${Date.now()}`)
    .then(r => r.json())
    .then(data => {
        // save the list as associative array (object) in a variable
        map_MarkersList = data;

        // Add the index and name of files to the select element
        const selector = document.getElementById('MapPlacesSelector');
        data.forEach((filename, index) => {
            const option = document.createElement('option');
            option.value = index;
            option.textContent = filename;
            selector.appendChild(option);
        });
    });

function load_MapFile(index, option) {
    // Get the name of the chosen file by its index
    if (Number.isInteger(index)) {
        let file_Name = null;
        let server_fileURL = null;
        if (option === 'marker') {
            file_Name = map_MarkersList[index];
            server_fileURL = `${maps_PlacesFolder}/${file_Name}?v=2025-10-26`;

        } else {
            file_Name = map_LayerList[index];
            server_fileURL = `${maps_AreasFolder}/${file_Name}?v=2025-10-26`;
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

// Load a Map Lyers file
document.getElementById("MapAreasSelector").addEventListener("change", function () {
    const selected_Index = parseInt(this.value);
    load_MapFile(selected_Index, 'layer');
});

// Triggers the selection of a JSON file from the list of options
document.getElementById("MapPlacesSelector").addEventListener("change", function () {
    const selected_Index = parseInt(this.value);
    load_MapFile(selected_Index, 'marker');
});


// Clicking triggers a hidden fiel input to open windows file system 
document.getElementById("fileSystemButton").addEventListener("click", function () {
    document.getElementById("User_FileLoader").click();
});

// Windows file system
document.getElementById("User_FileLoader").addEventListener("change", function (event) {
    const file = event.target.files[0];
    if (!file) return;
    const user_File = file['name'];
    const extension = user_File.split('.').pop().toLowerCase();

    // Reset the value of the input opened by the File System to enable reloading the same file
    document.getElementById('User_FileLoader').value = '';

    const reader = new FileReader();
    reader.onload = function (e) {

        load_File_ByExtension(e.target.result, extension, user_File);

    };
    reader.readAsText(file);

});