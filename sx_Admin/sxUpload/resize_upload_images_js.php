var sx_FilesToPreviewAndUpload = "";
window.onload = function () {
    // Load fiels from Drob Box
    const dropbox = document.getElementById("dropbox");
    dropbox.addEventListener("dragenter", dragenter, false);
    dropbox.addEventListener("dragover", dragover, false);
    dropbox.addEventListener("drop", drop, false);

    // Load fiels from Select Files
    document.getElementById('imageFiles').addEventListener('change', sxPreviewImagesFS, false);
}

function dragenter(e) {
    e.stopPropagation();
    e.preventDefault();
}

function dragover(e) {
    e.stopPropagation();
    e.preventDefault();
}

function drop(e) {
    e.stopPropagation();
    e.preventDefault();
    var dt = e.dataTransfer;
    var files = dt.files;
    sx_FilesToPreviewAndUpload = files;
    sxPreviewImagesDB(files);
}

function sxPreviewImagesDB(files) {
    if (files.length) {
        var sxPrview = document.getElementById("Preview");
        sxPrview.innerHTML = "";
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            sx_Preview_Images(file, sxPrview);
        }
    }
}

function sxPreviewImagesFS(evt) {
    if (window.File && window.FileReader && window.FileList && window.Blob) {
        var files = evt.target.files;
        if (files.length) {
            var sxPrview = document.getElementById("Preview");
            sxPrview.innerHTML = "";
            sxPrview.innerHTML = "<h2>Images to Resize, Crop and Upload</h2>";
            sx_FilesToPreviewAndUpload = files;
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                sx_Preview_Images(file, sxPrview);
            }
        }
    } else {
        alert('The File APIs are not fully supported in this browser.');
    }
}

/**
 * Load the List of Image Files from both File List and Crop Files
 */

function sx_Preview_Images(file, sxPrview) {
    var fileSize = file.size;
    document.getElementById("ListUploadedImages").innerHTML = "";
    document.getElementById("ListNewSizes").innerHTML = "";
    document.getElementById("PreviewResizedTitle").innerHTML = "";
    document.getElementById("PreviewResized").innerHTML = "";
    if (file.type.startsWith('image/')) {
        var reader = new FileReader();
        reader.onload = function (e) {
            var img = new Image();
            img.src = e.target.result;
            img.onload = function () {
                iTempW = Math.round(img.width);
                iTempH = Math.round(img.height);
                iTempRatio = parseFloat(iTempH / iTempW);
                img.title = "Type: "+ file.type +", Width: " + (iTempW).toLocaleString("el") + ", Height: " + (iTempH).toLocaleString("el") + ", Ratio: " + (iTempRatio) + ", Bytes: " + (fileSize).toLocaleString("el");
                img.height = 100;
                sxPrview.appendChild(img);
            }
        };
        reader.readAsDataURL(file);
    } else {
        alert("You can uppload only Image Files of type: jpg, jpeg, svg and png")
    }
}

/**
 * Preview Resized Images
 */

var sx_intTotalSize = 0;
var sx_strFileSizes = "";

function sx_ResizeAndPreviewImage(radioUpload) {
    if (sx_FilesToPreviewAndUpload.length) {
        if (radioUpload) {
            document.getElementById("ListUploadedImages").innerHTML = "<h2>Uploaded Images</h2>"
        }
        var files = sx_FilesToPreviewAndUpload;
        var maxWidth = document.querySelector("input[name='MaxWidth']:checked").value;
        var maxHeight = document.querySelector("input[name='MaxHeight']:checked").value;
        var intQuality = document.querySelector("input[name='Quality']:checked").value;
        var intCropRatio = document.querySelector("input[name='CropRatio']:checked").value;

        if (intCropRatio != 0) {
            intCropRatio = parseFloat(intCropRatio * 0.0001);
        }
        intQuality = parseFloat(intQuality * 0.01);
        
        document.getElementById("PreviewResizedTitle").innerHTML = "<h2>Preview Resized and Cropped Images</h2>";
        document.getElementById("PreviewResized").innerHTML = "";
        document.getElementById("ListNewSizes").innerHTML = "";
        var eResults = document.createElement('ol');
        sx_intTotalSize = 0;
        sx_strFileSizes = "";

        if (files.length) {
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                sx_Resize_And_Preview(file, maxWidth, maxHeight, intQuality, intCropRatio, radioUpload, eResults)
            }
        }
    } else {
        alert('You must first select one or more Image Files!');
    }
}

function sx_Resize_And_Preview(file, maxWidth, maxHeight, intQuality, intCropRatio, radioUpload, eResults) {
    if (file.type.startsWith('image/')) {
        var img = document.createElement("img");
        var reader = new FileReader();
        reader.onloadend = function (e) {
            img.src = e.target.result;
            img.onload = function () {
                var canvas = document.createElement("canvas");
                var ctx = canvas.getContext("2d");
                ctx.drawImage(img, 0, 0);

                /**
                 * Resize image
                 */

                var MAX_WIDTH = maxWidth;
                var MAX_HEIGHT = maxHeight;
                var width = img.width;
                var height = img.height;
                var iInitialRatio = height / width;
                var radioWideScreen;

                if (width > height) {
                    radioWideScreen = true;
                    if (width > MAX_WIDTH) {
                        height *= MAX_WIDTH / width;
                        width = MAX_WIDTH;
                    }
                } else {
                    radioWideScreen = false;
                    if (height > MAX_HEIGHT) {
                        width *= MAX_HEIGHT / height;
                        height = MAX_HEIGHT;
                    }
                }

                if (radioWideScreen == false) {
                    if (intCropRatio != 0) {
                        // Prepare vertical images with Initial Ratio higher than Crop Ratio for proper cropping
                        if (intCropRatio < iInitialRatio) {
                            width = Math.round(height/intCropRatio)
                            height = Math.round(width * iInitialRatio)
                        }
                    }

                    if (width > MAX_WIDTH) {
                        height *= MAX_WIDTH / width;
                        width = MAX_WIDTH;
                    }
                }

                /**
                 * Crop image
                 */

                var iHighDiff = 0;
                var iNewHightDiff = 0;
                var iWidthDiff = 0;
                var clipX = 0;
                var clipY = 0;
                var cWidth = width;
                var cHeight = height;
                var intMoveTB = document.querySelector("input[name='MoveTB']").value;
                var intMoveLR = document.querySelector("input[name='MoveLR']").value;
                if (intCropRatio != 0) {
                    //* Crop Positions: LT,LM,LB / CT,CM,CB / RT,RM,RB
                    var strCP = document.querySelector("input[name='CropPosition']:checked").value;

                    var cHeight = Math.round(cWidth * intCropRatio);
                    iHighDiff = Math.round(height - cHeight);

                    if ((iHighDiff) > 0) {
                        if (strCP == "CM") {
                            clipY = -Math.round(iHighDiff / 2);
                        } else if (strCP == "CB") {
                            clipY = -iHighDiff +(intMoveTB)*1;
                        } else {
                            clipY = 0 -(intMoveTB)*1;
                        }
                    } else {
                        if (cHeight > MAX_HEIGHT) {
                            cHeight = MAX_HEIGHT
                            cWidth = Math.round(cHeight / intCropRatio)
                        }
                        if (cWidth > MAX_WIDTH) {
                            cWidth = MAX_WIDTH
                        }

                        // Reset the initial width and height of the image, starting from Max-Height
                        height = cHeight
                        width = Math.round(height / iInitialRatio)

                        // Set width difference and reset the height difference
                        iWidthDiff = Math.round(width - cWidth)
                        iNewHightDiff = Math.round(height - cHeight);

                        if (strCP == "CT" || strCP == "CM" || strCP == "CB") {
                            clipX = -Math.round(iWidthDiff / 2);
                            if (strCP == "CM") {
                                clipY = -Math.round(iNewHightDiff / 2);
                            } else if (strCP == "CB") {
                                clipY = -iNewHightDiff +(intMoveTB)*1;
                            } else {
                                clipY = 0 -(intMoveTB)*1;
                            }
                        } else if (strCP == "RT" || strCP == "RM" || strCP == "RB") {
                            clipX = -iWidthDiff +(intMoveLR)*1;
                            if (strCP == "RM") {
                                clipY = -Math.round(iNewHightDiff / 2);
                            } else if (strCP == "RB") {
                                clipY = -iNewHightDiff +(intMoveTB)*1;
                            } else {
                                clipY = 0 -(intMoveTB)*1;
                            }
                        } else { // LT, LM, LB
                            clipX = 0 -(intMoveLR)*1;
                            if (strCP == "LM") {
                                clipY = -Math.round(iNewHightDiff / 2);
                            } else if (strCP == "LB") {
                                clipY = -iNewHightDiff +(intMoveTB)*1;
                            } else {
                                clipY = 0 -(intMoveTB)*1;
                            }
                        }
                    }

                    //alert('W: ' + width + ', H: ' + height + ', IR: ' + iInitialRatio + '\n WD: ' + iWidthDiff + ', HD: ' + iHighDiff + ', NewHD: ' + iNewHightDiff + '\n Ratio: ' + intCropRatio + "," + clipX + "," + clipY + "," + cWidth + "," + cHeight)
                }

                canvas.width = cWidth;
                canvas.height = cHeight;
                var ctx = canvas.getContext("2d");
                ctx.drawImage(img, clipX, clipY, width, height);

                /**
                 * Create resized and Cropped image
                 */

                var strFileType = file.type;
                if (strFileType.toLowerCase() == "image/jpeg" || strFileType.toLowerCase() == "image/jpg") {
                    var DataURL = canvas.toDataURL(file.type, intQuality);
                } else {
                    var DataURL = canvas.toDataURL(file.type);
                }

                var sPlus = " + "
                var radioFirstLoop = false;
                if (sx_intTotalSize == 0) {
                    sPlus = "";
                    radioFirstLoop = true;
                }

                var iTemp = DataURL.length;
                sx_intTotalSize = sx_intTotalSize + iTemp;
                sx_strFileSizes += sPlus + (iTemp).toLocaleString("el");
                document.getElementById("ListNewSizes").innerHTML = "<b>Total Size of Images:</b> " + sx_strFileSizes + " = " + sx_intTotalSize.toLocaleString("el") + " Bytes ";
                var newImg = document.createElement("img");
                newImg.title = "Width: " + (cWidth).toLocaleString("el") + ", Height: " + (cHeight).toLocaleString("el") + ", Ratio: " + (parseFloat(cHeight / cWidth)).toLocaleString("el") + ", Bytes: " + iTemp.toLocaleString("el");
                newImg.src = DataURL;
                document.getElementById("PreviewResized").appendChild(newImg);

                /**
                 * Resized, Cropped and Upload images
                 */

                if (radioUpload) {
                    var strFileName = (file.name).toLowerCase();
                    if(strFileName.endsWith(".svg")) {
                        strFileName = (file.name).slice(0, -3) + 'png';
                    }
                    var radioAddPrefix = document.getElementById("AddPrefix").checked;
                    var strPrefix = document.getElementById("Prefix").value;
                    if (radioAddPrefix && strPrefix !== "") {
                        strFileName = strPrefix +'__'+ strFileName
                    }
                    var strDestinationFolder = document.getElementById("DestinationFolder").value;
                    eResults.innerHTML += "<li><b>" + strFileName + "</b>: " + (DataURL.length).toLocaleString("el") + " bytes</li>";
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'uploader_for_resized_images.php', true);
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    var data = 'ImageSources=' + encodeURIComponent(DataURL) + '&FileNames=' + encodeURIComponent(strFileName) + '&DestinationFolder=' + encodeURIComponent(strDestinationFolder);
                    xhr.onreadystatechange = function (ev) {
                        var sx_ListUploadedImages = document.getElementById('ListUploadedImages');
                        if (xhr.readyState == 4) {
                            if (xhr.status == 200) {
                                sx_ListUploadedImages.appendChild(eResults);
                            } else if (xhr.responseText.length > 0) {
                                alert("This Error Message appears propably because"
                                + "\nthe Server does not allow uploading big images - or long binary strings."
                                + "\nReduce the Quality of the Image or Increase the value of the following parameter in the Server:"
                                + "\nMaximum Requested Entity Body Limit.");
                                alert(xhr.responseText);
                            }
                        }
                    };
                    xhr.send(data);
                }
            }
        }
        reader.readAsDataURL(file);
    }
}