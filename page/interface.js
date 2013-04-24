function onSelectionClick(ev)
{
	refreshSelection(ev.currentTarget.name);
}

function refreshSelection(elementName)
{
	var elements = document.getElementsByName(elementName);
	for (var i = 0; i < elements.length; i++)
	{
		var element = elements[i];
		if (element.checked == true)
		{
			element.parentNode.className = "active-selection";
		}
		else
		{
			element.parentNode.className = "selection";
		}
	}
}

function clearSelection(elementName)
{
	var elements = document.getElementsByName(elementName);
	for (var i = 0; i < elements.length; i++)
	{
		var element = elements[i];
		element.checked = false;
		element.parentNode.className = "selection";
	}
}

function addCloseCheck(message)
{
	window.onbeforeunload = function (evt)
	{
		if (typeof(evt) == "undefined")
		{
			evt = window.event;
		}
		if (evt)
		{
			evt.returnValue = message;
		}
		return message;
	}
}

function removeCloseCheck()
{
	window.onbeforeunload = null;
}

function setLoading(text)
{
	var div = document.createElement("div");
	div.setAttribute("class", "loading-screen");
	div.innerHTML = "<span>" + text + "</span>";
	
	document.getElementsByTagName("body")[0].appendChild(div);
}

function setPaletteForButtons(elementName, colors)
{
	var elements = document.getElementsByName(elementName);
	for (var i = 0; i < elements.length; i++)
	{
		var element = elements[i];
		if (i < colors.length)
		{
			element.setAttribute("style", "visibility:visible; background-color:" + colors[i] + ";");
		}
		else
		{
			element.setAttribute("style", "visibility:hidden;");
		}
	}
}

function onPaletteSelectionClick(ev)
{
	refreshSelection(ev.currentTarget.name);
	clearSelection("bg_color");
	var palette = ev.currentTarget.value;
	setPaletteForButtons("bg_color_image", palettes[palette]);
}

function validateNewForm()
{
	var isOk = true;
	var form = document.forms["newForm"];
	
/*	if (!validateElement(isAnyChecked(form["pieces"]), "piecesWarning"))
	{
		isOk = false;
	}*/

	if (!validateElement(isAnyChecked(form["pencils[]"]), "pencilsWarning"))
	{
		isOk = false;
	}
	
	if (!validateElement(isAnyChecked(form["palette"]), "colorsWarning"))
	{
		isOk = false;
	}

	if (!validateElement(isAnyChecked(form["bg_color"]), "bgColorWarning"))
	{
		isOk = false;
	}
	
	if (!validateElement(form["title"].value != null && form["title"].value != "", "titleWarning"))
	{
		isOk = false;
	}
	
	if (!isOk)
	{
		alert("You are not ready. Look for the advices on the page!");
	}
	return isOk;
}

function validateDrawing(needsTop, needsBottom)
{
	var isOk = true;
	var form = document.forms["drawForm"];
	
	if (needsBottom && !validateElement(hasSomethingInBottomLine(), "bottomWarning"))
	{
		isOk = false;
	}
	if (needsTop && !validateElement(hasSomethingInTopLine(), "topWarning"))
	{
		isOk = false;
	}

	if (form["creator"] && !validateElement(form["creator"].value != null && form["creator"].value != "", "creatorWarning"))
	{
		isOk = false;
	}

	if (!isOk)
	{
		alert("You are not ready. Look for the advices on the page!");
	}
	return isOk;
}

function validateElement(isValid, warningElementId)
{
	var warningElement = document.getElementById(warningElementId);
	if (isValid)
	{
		warningElement.style.display = "none";
	}
	else
	{
		warningElement.style.display = "inline";
	}
	return isValid;
}

function isAnyChecked(elements, minChecked)
{
	if (minChecked === undefined)
	{
		minChecked = 1;
	}
	for (var i = 0; i < elements.length; i++)
	{
		if (elements[i].checked)
		{
			minChecked--;
			if (minChecked == 0)
			{
				return true;
			}
		}
	}
	return false;
}
