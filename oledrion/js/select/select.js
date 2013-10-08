function removeOptionSelected(selectName)
{
  var elSel = document.getElementById(selectName);
  var i;
  for (i = elSel.length - 1; i>=0; i--) {
    if (elSel.options[i].selected) {
      elSel.remove(i);
    }
  }
}

function selectAll(selectBox,selectAll) {
	// have we been passed an ID
	if (typeof selectBox == "string") {
		selectBox = document.getElementById(selectBox);
	}
	// is the select box a multiple select box?
	if (selectBox.type == "select-multiple") {
		for (var i = 0; i < selectBox.options.length; i++) {
			selectBox.options[i].selected = selectAll;
		}
	}
}