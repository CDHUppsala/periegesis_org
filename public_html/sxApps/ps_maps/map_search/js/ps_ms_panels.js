
/**
 * ========================================================
 * Public Sphere 2025-12
 * FILE: ps_ms_panels.js
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
    document.querySelector('#PanelInformation>div')?.classList.remove('visible_panels');
    document.querySelector('#PanelTools>div')?.classList.remove('visible_panels');
    document.querySelector('#PanelDownloads>div')?.classList.remove('visible_panels');
}

let load_Links = true;
function toggle_PanelDownloads() {
    if (load_Links) {
        create_DownloadLinks('MapAreasDownloads', maps_AreasFolder, 'downloads.json');
        create_DownloadLinks('MapPlacesDownloads', maps_PlacesFolder, 'downloads.json');
        load_Links = false;
    }
    document.querySelector('#PanelInformation')?.classList.remove('visible_panels');
    document.querySelector('#PanelTools')?.classList.remove('visible_panels');
    const panel = document.querySelector('#PanelDownloads');
    panel?.classList.toggle('visible_panels');
}

function toggle_PanelTools() {
    document.querySelector('#PanelInformation')?.classList.remove('visible_panels');
    document.querySelector('#PanelDownloads')?.classList.remove('visible_panels');
    const panel = document.querySelector('#PanelTools');
    panel?.classList.toggle('visible_panels');

}

function toggle_PanelInformation() {
    document.querySelector('#PanelTools')?.classList.remove('visible_panels');
    document.querySelector('#PanelDownloads')?.classList.remove('visible_panels');
    const panel = document.querySelector('#PanelInformation');
    panel?.classList.toggle('visible_panels');
}

