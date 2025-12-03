
/** 
 * DOWNLOAD FILE PNG/SVG
 * Download function, common for downloading PNG and SVG
 * @param {object} dataUrl 
 * @param {string} file 
 */
function download_image(dataUrl, file) {
    var link = document.createElement('a');
    link.download = file;
    link.href = dataUrl;
    link.click();
}

/**
 * PNG
 * Download as PNG to default download folder 
 * @param {string} id 
 * @param {string} file 
 */
function html_to_png_download(id, file) {
    const node = document.getElementById(id);
    const now = new Date().toISOString().replace(/[:.]/g, '-').replace('T', '_').replace('Z', '');
    htmlToImage.toPng(node)
        .then(function (dataUrl) {
            download_image(dataUrl, file + '_' + now + '.png')
        })
        .catch(function (error) {
            console.error('Oops, something went wrong!', error);
        });
}

/** 
 * SVG
 * Download as SVG to default download folder 
 * @param {string} id 
 * @param {string} file 
 */
function html_to_svg_download(id, file) {
    const now = new Date().toISOString().replace(/[:.]/g, '-').replace('T', '_').replace('Z', '');
    htmlToImage.toSvg(document.getElementById(id))
        .then(function (dataUrl) {
            download_image(dataUrl, file + '_' +  now + '.svg');
        });
}


/**
 * PNG to BLOB 
 * Download PNG when File System is not available
 * @param {object} blob 
 * @param {string} filename 
 */
function download_image_blob(blob, filename) {
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

/**
 * Saves PNG in selectable folder using File System
 * Download if File System is not available
 * @param {object} blob 
 * @param {string} file 
 */
async function saveImageToFileSystem(blob, file = "image.png") {
    if ('showSaveFilePicker' in window) {
        const handle = await window.showSaveFilePicker({
            suggestedName: file,
            types: [{
                description: 'Image file',
                accept: { 'image/png': ['.png'] }
            }]
        });

        const writable = await handle.createWritable();
        await writable.write(blob);
        await writable.close();
    } else {
        download_image_blob(blob, file);
    }
}

/**
 * Save PNG by File System or fall back to Download
 * @param {string} id 
 * @param {string} file 
 */
function html_to_png_save(id, file) {
    const node = document.getElementById(id);
    const now = new Date().toISOString().replace(/[:.]/g, '-').replace('T', '_').replace('Z', '');
    htmlToImage.toBlob(node)
        .then(function (blob) {
            saveImageToFileSystem(blob, file + '_' +  now + '.png');
        });
}
