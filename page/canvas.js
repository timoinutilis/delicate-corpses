var canvas = null;
var ctx = null;
var drawing = false;
var lastX;
var lastY;
var distSum;
var bufferImageData;
var rectLeft;
var rectTop;
var rectRight;
var rectBottom;
var undoImageData;

function initDrawCanvas(canvasId, drawAreaId, bgColor, pencil, color)
{
	canvas = document.getElementById(canvasId);
	ctx = canvas.getContext('2d');
	ctx.lineWidth = pencil;
	ctx.strokeStyle = color;
	ctx.lineCap = "round";
	ctx.lineJoin = "round";
	
	ctx.fillStyle = bgColor;
	ctx.fillRect(0, 0, canvas.width, canvas.height);
	
	storeBuffer();
	
	var body = document.getElementsByTagName("BODY")[0];
	body.onmousemove = onDrawMouseMove;
	body.onmouseup = onDrawMouseUp;
	body.onmouseout = onDrawMouseOut;
	document.getElementById(drawAreaId).onmousedown = onDrawMouseDown;
}

function setLineWidth(w)
{
	ctx.lineWidth = w;
}

function setColor(c)
{
	ctx.strokeStyle = c;
}

function onDrawMouseDown(event)
{
	var pos = getMousePos(event);
	storeUndo();
	ctx.beginPath();
	ctx.moveTo(pos.x - 0.0001, pos.y);
	ctx.lineTo(pos.x, pos.y);
	ctx.stroke();
	lastX = pos.x;
	lastY = pos.y;
	distSum = 0;
	drawing = true;
	resetBufferRect(pos.x, pos.y);
	return false;
}

function onDrawMouseUp(event)
{
	var pos = getMousePos(event);
	if (drawing)
	{
		ctx.lineTo(pos.x, pos.y);
		restoreBuffer();
		ctx.stroke();
		addToBufferRect(pos.x, pos.y);
		storeBuffer();
		drawing = false;
		return false;
	}
	return true;
}

function onDrawMouseMove(event)
{
	var pos = getMousePos(event);
	if (drawing)
	{
		distSum += dist(pos.x, pos.y, lastX, lastY);
		if (distSum >= 5)
		{
			ctx.lineTo(pos.x, pos.y);
			restoreBuffer();
			ctx.stroke();
			addToBufferRect(pos.x, pos.y);
			distSum = 0;
		}
		lastX = pos.x;
		lastY = pos.y;
		return false;
	}
	return true;
}

function onDrawMouseOut(event)
{
	event = event ? event : window.event;
	var from = event.relatedTarget || event.toElement;
	if (!from || from.nodeName == "HTML")
	{
		onDrawMouseUp(event);
	}
}

function dist(x1, y1, x2, y2)
{
	var distX = (x2 - x1);
	var distY = (y2 - y1);
	return Math.sqrt((distX * distX) + (distY * distY));
}

function resetBufferRect(x, y)
{
	var size = ctx.lineWidth / 2;
	rectLeft = x - size;
	rectTop = y - size;
	rectRight = x + size;
	rectBottom = y + size;
}

function addToBufferRect(x, y)
{
	var size = ctx.lineWidth / 2;
	rectLeft = Math.min(x - size, rectLeft);
	rectTop = Math.min(y - size, rectTop);
	rectRight = Math.max(x + size, rectRight);
	rectBottom = Math.max(y + size, rectBottom);
	//debug("Rect: " + rectLeft + ", " + rectTop + " - " + rectRight + ", " + rectBottom);
}

function storeBuffer()
{
	bufferImageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
}

function restoreBuffer()
{
	ctx.putImageData(bufferImageData, 0, 0, rectLeft, rectTop, rectRight - rectLeft, rectBottom - rectTop);
}

function storeUndo()
{
	undoImageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
}

function undo()
{
	var tempImageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
	ctx.putImageData(undoImageData, 0, 0);
	undoImageData = tempImageData;
	storeBuffer();
	return true;
}

function getCanvasDataURL()
{
	return canvas.toDataURL();
}

function getMousePos(evt)
{
    var obj = canvas;
    var top = 0;
    var left = 0;
    while (obj.tagName != 'BODY')
    {
        top += obj.offsetTop;
        left += obj.offsetLeft;
        obj = obj.offsetParent;
    }
 
    var mouseX = evt.clientX - left + window.pageXOffset;
    var mouseY = evt.clientY - top + window.pageYOffset;
    return {
        x: mouseX,
        y: mouseY
    };
}

function hasSomethingInTopLine()
{
	return hasSomethingInLine(0);
}

function hasSomethingInBottomLine()
{
	return hasSomethingInLine(canvas.height - 1);
}

function hasSomethingInLine(y)
{
	var data = ctx.getImageData(0, y, canvas.width, 1).data;
	for (var x = 1; x < canvas.width; x++)
	{
		if (pixelColor(data, x - 1) != pixelColor(data, x))
		{
			return true;
		}
	}
	return false;
}

function pixelColor(data, x)
{
	return (data[x * 4] * 256 * 256) + (data[x * 4 + 1] * 256) + (data[x * 4 + 2]);
}

function debug(text)
{
	document.getElementById("debug").innerHTML = text;
}
