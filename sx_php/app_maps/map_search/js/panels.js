
/**
 * ========================================================
 * PANELS FOR INFOMATION, DOWNLOADS AND TOOLS

 * ========================================================
 */

function create_DownloadLinks(containerId, folderPath, jsonFile) {
    fetch(`${folderPath}/${jsonFile}?v=${Date.now()}`)
        .then(response => response.json())
        .then(fileList => {
            const container = document.getElementById(containerId);
            container.innerHTML = '';
            fileList.forEach(filename => {
                const link = document.createElement('a');
                link.href = `${folderPath}/${filename}`;
                link.download = filename;
                link.textContent = filename;
                link.style.display = 'block';
                container.appendChild(link);
            });
        })
        .catch(error => {
            console.error(`Error loading ${jsonFile} from ${folderPath}:`, error);
        });
}

function close_Panels() {
    document.querySelector('#PanelInformation>div')?.classList.remove('visible');
    document.querySelector('#PanelTools>div')?.classList.remove('visible');
    document.querySelector('#PanelDownloads>div')?.classList.remove('visible');
}

let load_Links = true;
function togglePanelDownloads() {
    if (load_Links) {
        create_DownloadLinks('MapAreasDownloads', maps_AreasFolder, 'downloads.json');
        create_DownloadLinks('MapPlacesDownloads', maps_PlacesFolder, 'downloads.json');
        load_Links = false;
    }
    document.querySelector('#PanelInformation>div')?.classList.remove('visible');
    document.querySelector('#PanelTools>div')?.classList.remove('visible');
    const panel = document.querySelector('#PanelDownloads>div');
    panel?.classList.toggle('visible');
}

function togglePanelTools() {
    document.querySelector('#PanelInformation>div')?.classList.remove('visible');
    document.querySelector('#PanelDownloads>div')?.classList.remove('visible');
    const panel = document.querySelector('#PanelTools>div');
    panel?.classList.toggle('visible');

}

function togglePanelInformation() {
    document.querySelector('#PanelTools>div')?.classList.remove('visible');
    document.querySelector('#PanelDownloads>div')?.classList.remove('visible');
    const panel = document.querySelector('#PanelInformation>div');
    panel?.classList.toggle('visible');
}