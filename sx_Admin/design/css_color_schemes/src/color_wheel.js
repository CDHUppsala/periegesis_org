/**
* degreesToRadians
*
* @param {number} degrees
* @returns {number}  radians
*/
function degreesToRadians(degrees) {
    return degrees * (Math.PI / 180);
}
/**
 * generateColorWheel
 *
 * @param {number} [size=400]
 * @param {string} [centerColor="white"]
 * @returns {HTMLCanvasElement}
 */
function generateColorWheel(size, centerColor) {
    if (size === void 0) {
        size = 400;
    }
    if (centerColor === void 0) {
        centerColor = "white";
    }
    //Generate main canvas to return
    var canvas = document.createElement("canvas");
    var ctx = canvas.getContext("2d");
    canvas.width = canvas.height = size;
    //Generate canvas clone to draw increments on
    var canvasClone = document.createElement("canvas");
    canvasClone.width = canvasClone.height = size;
    var canvasCloneCtx = canvasClone.getContext("2d");
    //Initiate variables
    var angle = 0;
    var hexCode = [255, 0, 0];
    var pivotPointer = 0;
    var colorOffsetByDegree = 4.322;
    //For each degree in circle, perform operation
    while (angle++ < 360) {
        //find index immediately before and after our pivot
        var pivotPointerbefore = (pivotPointer + 3 - 1) % 3;
        var pivotPointerAfter = (pivotPointer + 3 + 1) % 3;
        //Modify colors
        if (hexCode[pivotPointer] < 255) {
            //If main points isn't full, add to main pointer
            hexCode[pivotPointer] = (hexCode[pivotPointer] + colorOffsetByDegree > 255 ? 255 : hexCode[pivotPointer] + colorOffsetByDegree);
        } else if (hexCode[pivotPointerbefore] > 0) {
            //If color before main isn't zero, subtract
            hexCode[pivotPointerbefore] = (hexCode[pivotPointerbefore] > colorOffsetByDegree ? hexCode[pivotPointerbefore] - colorOffsetByDegree : 0);
        } else if (hexCode[pivotPointer] >= 255) {
            //If main color is full, move pivot
            hexCode[pivotPointer] = 255;
            pivotPointer = (pivotPointer + 1) % 3;
        }
        //clear clone
        canvasCloneCtx.clearRect(0, 0, size, size);
        //Generate gradient and set as fillstyle
        var grad = canvasCloneCtx.createRadialGradient(size / 2, size / 2, 0, size / 2, size / 2, size / 2);
        grad.addColorStop(0, centerColor);
        grad.addColorStop(1, "rgb(" + hexCode.map(function (h) {
            return Math.floor(h);
        }).join(",") + ")");
        canvasCloneCtx.fillStyle = grad;
        //draw full circle with new gradient
        canvasCloneCtx.globalCompositeOperation = "source-over";
        canvasCloneCtx.beginPath();
        canvasCloneCtx.arc(size / 2, size / 2, size / 2, 0, Math.PI * 2);
        canvasCloneCtx.closePath();
        canvasCloneCtx.fill();
        //Switch to "Erase mode"
        canvasCloneCtx.globalCompositeOperation = "destination-out";
        //Carve out the piece of the circle we need for this angle
        canvasCloneCtx.beginPath();
        canvasCloneCtx.arc(size / 2, size / 2, 0, degreesToRadians(angle + 1), degreesToRadians(angle + 1));
        canvasCloneCtx.arc(size / 2, size / 2, size / 2 + 1, degreesToRadians(angle + 1), degreesToRadians(angle + 1));
        canvasCloneCtx.arc(size / 2, size / 2, size / 2 + 1, degreesToRadians(angle + 1), degreesToRadians(angle - 1));
        canvasCloneCtx.arc(size / 2, size / 2, 0, degreesToRadians(angle + 1), degreesToRadians(angle - 1));
        canvasCloneCtx.closePath();
        canvasCloneCtx.fill();
        //Draw carved-put piece on main canvas
        ctx.drawImage(canvasClone, 0, 0);
    }
    //return main canvas
    return canvas;
}

//Get color wheel canvas
var el_center_white = document.getElementById('center_white');
var el_center_black = document.getElementById('center_black');

var colorWheelWhite = generateColorWheel(600,'white');
el_center_white.appendChild(colorWheelWhite);
var colorWheelBlack = generateColorWheel(600,'black');
el_center_black.appendChild(colorWheelBlack);

var el_color_wheel = document.getElementById('color_wheel');
var el_mouse_over_color = document.getElementById('mouse_over_color');
var el_clicked_rgb_color = document.getElementById('clicked_rgb_color');
var el_clicked_hex_color = document.getElementById('clicked_hex_color');


/**
 * colorWheelMouse
 *
 * @param {MouseEvent} evt
 * @param {canvas} canvas
 */
function colorWheelMouse(evt,canvas) {
    var ctx = canvas.getContext("2d");
    var data = ctx.getImageData(evt.offsetX, evt.offsetY, 1, 1);
    var RGB_color = data.data.slice(0, 3).join(',');
    el_mouse_over_color.innerHTML = RGB_color
}
colorWheelWhite.onmousemove = function(evt) {
    colorWheelMouse(evt, this);
};
colorWheelBlack.onmousemove = function(evt) {
    colorWheelMouse(evt, this);
};

/**
 * colorWheelClick
 *
 * @param {ClickEvent} evt
 * @param {canvas} canvas
 */

function colorWheelClick(evt, canvas) {
    var ctx = canvas.getContext("2d");
    var data = ctx.getImageData(evt.offsetX, evt.offsetY, 1, 1);
    var RGB_color = data.data.slice(0, 3).join(',');
    el_color_wheel.style.backgroundColor = "rgb(" + RGB_color +")";
    el_clicked_rgb_color.innerHTML = RGB_color;
    const [r, g, b] = RGB_color.split(",").map(Number);
    el_clicked_hex_color.innerHTML = rgbToHex(r, g, b);
}
colorWheelWhite.onclick = function(evt) {
    colorWheelClick(evt, this);
};
colorWheelBlack.onclick = function(evt) {
    colorWheelClick(evt, this);
};

function rgbToHex(r, g, b) {
    if (r < 0 || r > 255 || g < 0 || g > 255 || b < 0 || b > 255) {
        throw new Error("Invalid RGB value");
    }

    const hexR = r.toString(16).padStart(2, '0');
    const hexG = g.toString(16).padStart(2, '0');
    const hexB = b.toString(16).padStart(2, '0');

    return `#${hexR}${hexG}${hexB}`;
}

