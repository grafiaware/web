//funkce se volaji v souboru svislemenu.php
function hamburger_open() {
    document.getElementById("mySidenav").style.display = "block";
    document.getElementById("myOverlay").style.display = "block";
}
function hamburger_close() {
    document.getElementById("mySidenav").style.display = "none";
    document.getElementById("myOverlay").style.display = "none";
}

function togleTemplateSelect(event, id) {
//    $('#'+id).toggle();
    var elm = document.getElementById(id);
    if (elm.style.display=="block") {
        elm.style.display = "none";
    } else {
        elm.style.display = "block";
    }
    event.preventDefault();
    event.stopPropagation();
}

/**
 * @param {String} HTML representing a single element
 * @return {Element}
 */
function htmlToElement(html) {
    var template = document.createElement('template');
    html = html.trim(); // Never return a text node of whitespace as the result
    template.innerHTML = html;
    return template.content.firstChild;
}

/**
 * @param {String} HTML representing any number of sibling elements
 * @return {NodeList}
 */
//function htmlToElements(html) {
//    var template = document.createElement('template');
//    template.onload = function() {
//        console.log("template onload");
//    }
//    template.innerHTML = html;
//    return template.content.childNodes;
//}

/**
 * Nahradí element se zadaným id elementem s potomky vzniklým parsováním zadaného textu jako HTML.
 * Používá funkci htmlToElement(), proto očekává, že zadaný text je HTML string, který má jeden kořenový element. Element může mít potomky, ale v kořenu nesmí být sada elementů.
 *
 * @param {type} id
 * @param {type} text
 * @returns {undefined}
 */
function replaceElementWithId(id, text) {
    var elm = document.getElementById(id);
    elm.replaceWith(htmlToElement(text));  // přidá nový a odstraní starý element - možná nespustí onlad na elm ?? onload na template?

}

function replaceElement(id, apiUri){
    var xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function() {
        if (this.readyState == 4) {
            if (this.status == 200) {
                //document.getElementById(id).innerHTML = xhr.responseText;
                replaceElementWithId(id, xhr.responseText);
                console.log("Loaded element for id: "+id+".");
            } else {
                console.log("Load of element failed for id: "+id+",this.readyState:"+this.readyState+", this.status:"+this.status);
            }
        }
    };
    xhr.open("GET", apiUri, true);
    xhr.setRequestHeader("X-Powered-By", "RED Loader");
//    xhr.responseType = "document";   // XML nebo HTML
    xhr.send();
};

