window.addEventListener("load", function () {
    var uploader = new plupload.Uploader({
        runtimes: 'html5,html4',
        browse_button: 'pick_files',
        url: '../sxPlugins/ps_upload/chunk.php?destin=' + sx_DestinationURL + '&prefix=' + sx_PrefixFileName,
        chunk_size: '1mb',
        filters: {
            max_file_size: '1000mb',
            mime_types: [{
                title: "Video and Presentation files",
                extensions: "mp3,mp4,ogg,webm,ppt,pptx,pps,ppsx,odp"
            }]
        },
        init: {
            PostInit: function () {
                document.getElementById('list_files').innerHTML = '';

                document.getElementById('upload_files').onclick = function () {
                    uploader.start();
                    return false;
                };
            },
            FilesAdded: function (up, files) {
                plupload.each(files, function (file) {
                    document.getElementById('list_files').innerHTML += `<div id="${file.id}"><span>${file.name}</span> <span> ${plupload.formatSize(file.size)} </span> <strong></strong></div>`;
                });
            },
            UploadProgress: function (up, file) {
                document.querySelector(`#${file.id} strong`).innerHTML = `<span>${file.percent}%</span>`;
            },

            Error: function (up, err) {
                console.log(err);
            }
        }
    });
    uploader.init();
});