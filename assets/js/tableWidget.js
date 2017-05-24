/*
 (C) www.dhtmlgoodies.com, October 2005

 This is a script from www.dhtmlgoodies.com. You will find this and a lot of other scripts at our website.

 Terms of use:
 You are free to use this script as long as the copyright message is kept intact. However, you may not
 redistribute, sell or repost it without our permission.

 Thank you!

 www.dhtmlgoodies.com
 Alf Magne Kalleland

 */
var tableWidget_tableCounter = 0;
var tableWidget_arraySort = new Array();
var tableWidget_okToSort = true;
var activeColumn = new Array();
var arrowImagePath = "images/"; // Path to arrow images

function addEndCol(obj) {
    if (document.all)return;
    var rows = obj.getElementsByTagName('TR');
    for (var no = 0; no < rows.length; no++) {
        var cell = rows[no].insertCell(-1);
        cell.innerHTML = ' ';
        cell.style.width = '13px';
        cell.width = '13';

    }

}

function highlightTableHeader() {
    this.className = 'tableWigdet_headerCellOver';
    if (document.all) {   // I.E fix for "jumping" headings
        var divObj = this.parentNode.parentNode.parentNode.parentNode;
        this.parentNode.style.top = divObj.scrollTop + 'px';
    }
}

function deHighlightTableHeader() {
    this.className = 'tableWidget_headerCell';
}

function mousedownTableHeader() {
    this.className = 'tableWigdet_headerCellDown';
    if (document.all) {   // I.E fix for "jumping" headings
        var divObj = this.parentNode.parentNode.parentNode.parentNode;
        this.parentNode.style.top = divObj.scrollTop + 'px';
    }
}

function sortNumeric(a, b) {

    a = a.replace(/,/, '.');
    b = b.replace(/,/, '.');
    a = a.replace(/[^\d\.\/]/g, '');
    b = b.replace(/[^\d\.\/]/g, '');
    if (a.indexOf('/') >= 0)a = eval(a);
    if (b.indexOf('/') >= 0)b = eval(b);
    return a / 1 - b / 1;
}


function sortString(a, b) {

    if (a.toUpperCase() < b.toUpperCase()) return -1;
    if (a.toUpperCase() > b.toUpperCase()) return 1;
    return 0;
}
function cancelTableWidgetEvent() {
    return false;
}

function sortTable() {
    if (!tableWidget_okToSort)return;
    tableWidget_okToSort = false;
    /* Getting index of current column */
    var obj = this;
    var indexThis = 0;
    while (obj.previousSibling) {
        obj = obj.previousSibling;
        if (obj.tagName == 'TD')indexThis++;
    }
    var images = this.getElementsByTagName('IMG');

    if (this.getAttribute('direction') || this.direction) {
        direction = this.getAttribute('direction');
        if (navigator.userAgent.indexOf('Opera') >= 0)direction = this.direction;
        if (direction == 'ascending') {
            direction = 'descending';
            this.setAttribute('direction', 'descending');
            this.direction = 'descending';
        } else {
            direction = 'ascending';
            this.setAttribute('direction', 'ascending');
            this.direction = 'ascending';
        }
    } else {
        direction = 'ascending';
        this.setAttribute('direction', 'ascending');
        this.direction = 'ascending';
    }


    if (direction == 'descending') {
        images[0].style.display = 'inline';
        images[0].style.visibility = 'visible';
        images[1].style.display = 'none';
    } else {
        images[1].style.display = 'inline';
        images[1].style.visibility = 'visible';
        images[0].style.display = 'none';
    }


    var tableObj = this.parentNode.parentNode.parentNode;
    var tBody = tableObj.getElementsByTagName('TBODY')[0];

    var widgetIndex = tableObj.id.replace(/[^\d]/g, '');
    var sortMethod = tableWidget_arraySort[widgetIndex][indexThis]; // N = numeric, S = String
    if (activeColumn[widgetIndex] && activeColumn[widgetIndex] != this) {
        var images = activeColumn[widgetIndex].getElementsByTagName('IMG');
        images[0].style.display = 'none';
        images[1].style.display = 'inline';
        images[1].style.visibility = 'hidden';
        if (activeColumn[widgetIndex])activeColumn[widgetIndex].removeAttribute('direction');
    }

    activeColumn[widgetIndex] = this;

    var cellArray = new Array();
    var cellObjArray = new Array();
    for (var no = 1; no < tableObj.rows.length; no++) {
        var content = tableObj.rows[no].cells[indexThis].innerHTML + '';
        cellArray.push(content);
        cellObjArray.push(tableObj.rows[no].cells[indexThis]);
    }

    if (sortMethod == 'N') {
        cellArray = cellArray.sort(sortNumeric);
    } else {
        cellArray = cellArray.sort(sortString);
    }

    if (direction == 'descending') {
        for (var no = cellArray.length; no >= 0; no--) {
            for (var no2 = 0; no2 < cellObjArray.length; no2++) {
                if (cellObjArray[no2].innerHTML == cellArray[no] && !cellObjArray[no2].getAttribute('allreadySorted')) {
                    cellObjArray[no2].setAttribute('allreadySorted', '1');
                    tBody.appendChild(cellObjArray[no2].parentNode);
                }
            }
        }
    } else {
        for (var no = 0; no < cellArray.length; no++) {
            for (var no2 = 0; no2 < cellObjArray.length; no2++) {
                if (cellObjArray[no2].innerHTML == cellArray[no] && !cellObjArray[no2].getAttribute('allreadySorted')) {
                    cellObjArray[no2].setAttribute('allreadySorted', '1');
                    tBody.appendChild(cellObjArray[no2].parentNode);
                }
            }
        }
    }

    for (var no2 = 0; no2 < cellObjArray.length; no2++) {
        cellObjArray[no2].removeAttribute('allreadySorted');
    }

    tableWidget_okToSort = true;


}

function initTableWidget(objId, width, height, sortArray) {
    width = width + '';
    height = height + '';
    var obj = document.getElementById(objId);
    obj.parentNode.className = 'widget_tableDiv';
    if (navigator.userAgent.indexOf('MSIE') >= 0) {
        obj.parentNode.style.overflowY = 'auto';
    }
    tableWidget_arraySort[tableWidget_tableCounter] = sortArray;
    if (width.indexOf('%') >= 0) {
        obj.style.width = width;
        obj.parentNode.style.width = width;
    } else {
        obj.style.width = width + 'px';
        obj.parentNode.style.width = width + 'px';
    }

    if (height.indexOf('%') >= 0) {
        obj.style.height = height;
        obj.parentNode.style.height = height;

    } else {
        obj.style.height = height + 'px';
        obj.parentNode.style.height = height + 'px';
    }
    obj.id = 'tableWidget' + tableWidget_tableCounter;
    addEndCol(obj);

    obj.cellSpacing = 0;
    obj.cellPadding = 0;
    obj.className = 'tableWidget';
    var tHead = obj.getElementsByTagName('THEAD')[0];
    var cells = tHead.getElementsByTagName('TD');
    for (var no = 0; no < cells.length; no++) {
        cells[no].className = 'tableWidget_headerCell';
        cells[no].onselectstart = cancelTableWidgetEvent;
        if (no == cells.length - 1) {
            cells[no].style.borderRight = '0';
        }
        if (sortArray[no]) {
            cells[no].onmouseover = highlightTableHeader;
            cells[no].onmouseout = deHighlightTableHeader;
            cells[no].onmousedown = mousedownTableHeader;
            cells[no].onmouseup = highlightTableHeader;
            cells[no].onclick = sortTable;

            var img = document.createElement('IMG');
            img.src = arrowImagePath + 'arrow_up.gif';
            cells[no].appendChild(img);
            img.style.visibility = 'hidden';

            var img = document.createElement('IMG');
            img.src = arrowImagePath + 'arrow_down.gif';
            cells[no].appendChild(img);
            img.style.display = 'none';


        } else {
            cells[no].style.cursor = 'default';
        }


    }
    var tBody = obj.getElementsByTagName('TBODY')[0];
    if (document.all && navigator.userAgent.indexOf('Opera') < 0) {
        tBody.className = 'scrollingContent';
        tBody.style.display = 'block';
    } else {
        tBody.className = 'scrollingContent';
        tBody.style.height = (obj.parentNode.clientHeight - tHead.offsetHeight) + 'px';
        if (navigator.userAgent.indexOf('Opera') >= 0) {
            obj.parentNode.style.overflow = 'auto';
        }
    }

    for (var no = 1; no < obj.rows.length; no++) {
        obj.rows[no].onmouseover = highlightDataRow;
        obj.rows[no].onmouseout = deHighlightDataRow;
        for (var no2 = 0; no2 < sortArray.length; no2++) {  /* Right align numeric cells */
            if (sortArray[no2] && sortArray[no2] == 'N')obj.rows[no].cells[no2].style.textAlign = 'right';
        }
    }
    for (var no2 = 0; no2 < sortArray.length; no2++) {  /* Right align numeric cells */
        if (sortArray[no2] && sortArray[no2] == 'N')obj.rows[0].cells[no2].style.textAlign = 'right';
    }

    tableWidget_tableCounter++;
}


function highlightDataRow() {
    if (navigator.userAgent.indexOf('Opera') >= 0)return;
    this.className = 'tableWidget_dataRollOver';
    if (document.all) {   // I.E fix for "jumping" headings
        var divObj = this.parentNode.parentNode.parentNode;
        var tHead = divObj.getElementsByTagName('TR')[0];
        tHead.style.top = divObj.scrollTop + 'px';

    }
}

function deHighlightDataRow() {
    if (navigator.userAgent.indexOf('Opera') >= 0)return;
    this.className = null;
    if (document.all) {   // I.E fix for "jumping" headings
        var divObj = this.parentNode.parentNode.parentNode;
        var tHead = divObj.getElementsByTagName('TR')[0];
        tHead.style.top = divObj.scrollTop + 'px';
    }
}
