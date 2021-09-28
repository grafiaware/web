/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @param {String} html HTML representing a single element
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
 * Používá funkci htmlToElement(), proto očekává, že zadaný text je HTML string, který má jeden kořenový element.
 * Element může mít potomky, ale v kořenu nesmí být sada elementů.
 *
 * @param {Element} replacedElement nahrazovaný element
 * @param {String} responseText
 * @returns {Element} Nový element, kterým byl nahrazen starý element
 */
function replaceElement(replacedElement, responseText) {
    newElement = htmlToElement(responseText);
    replacedElement.replaceWith(newElement);  // přidá nový a odstraní starý element
    console.log("Replaced element "+replacedElement.tagName+" with element "+newElement.tagName+".");
    return newElement;
};

/**
 * Stáhne HTML z adresy apiUri a nahradí element s identifikátorem id
 * @param {String} id
 * @param {String} apiUri
 * @returns {String}
 */
function fetchElementContent(replacedElement){
    let apiUri = getApiUri(replacedElement);
    /// fetch ///
    // fetch vrací Promise, která resolvuje s Response objektem a to v okamžiku, kdy server odpoví a jsou přijaty hlavičky odpovědi - nečeká na stažení celeho response
    // tento return je klíčový - vrací jako návratovou hodnotu hodotu vrýcenou pžíkazen returm v posledním bloku .then - viz https://developer.mozilla.org/en-US/docs/Learn/JavaScript/Asynchronous/Promises
    return fetch(apiUri)
    .then(response => {
      if (response.ok) {  // ok je true pro status 200-299, jinak je vždy false
          // pokud došlo k přesměrování: status je 200, (mohu jako druhý paremetr fetch dát objekt s hodnotou např. redirect: 'follow' atd.) a také porovnávat response.url s požadovaným apiUri
          return response.text(); //vrací Promise, která resolvuje na text až když je celý response je přijat ze serveru
      } else {
          throw new Error(`HTTP error! Status: ${response.status}`);  // will only reject on network failure or if anything prevented the request from completing.
      }
    })
    .then(textPromise => {
        console.log(`Loading content from ${apiUri}.`);
        return replaceElement(replacedElement, textPromise);  // vrací nový Element
    })
    .then(newElement => {
        loadSubPromise = loadSubsequentElements(newElement, "red_loaded");
        return loadSubPromise;
    })
    .catch(e => {
      console.log(`There has been a problem with fetch from ${apiUri}. Reason:` + e.message);
      throw new Error(`There has been a problem with fetch from ${apiUri}. Reason:` + e.message);
    });

    /// xhr ///
//    var xhr = new XMLHttpRequest();
//
//    xhr.onreadystatechange = function() {
//        if (this.readyState == 4) {
//            if (this.status == 200) {
//                //document.getElementById(id).innerHTML = xhr.responseText;
//                console.log("Loaded content for element with id: "+id+".");
//                replaceElementAndSubElements(id, responseText);
//            } else {
//                console.log("Failed load of content for element with id: "+id+",this.readyState:"+this.readyState+", this.status:"+this.status);
//            }
//        }
//    };
//    xhr.open("GET", apiUri, true);
//    xhr.setRequestHeader("X-Powered-By", "RED Loader");
////    xhr.responseType = "document";   // XML nebo HTML
//    xhr.send();
};

/**
 * Vrací hodnotu atributu "data-red-apiuri".
 *
 * @param {Element} element
 * @returns {String}
 */
function getApiUri(element) {
    return  element.attributes.getNamedItem("data-red-apiuri").nodeValue;
}

/**
 * Načte pomocí funkce fetchElement() nové obsahy všech elementů, které jsou potomky zadaného elementu nebo dokumentu. Potomky hledá podle zadaného jména třídy.
 * Nalezené potomky nahradí za nově načtené elementy.
 * Z každého nalezeného potomka použije hodnotu atributu data-red-apiuri jako URI požadavku, pomocí kterého získá nový obsah - HTML text.
 * Volání p robíhá rekurzivně. Na nově načtený element (a jeho potomky) je znovu volána tato funkce a pokud jsou v nové načteném elementu nalezeny elementy se zadaným jménom třídy,
 * jsou rekurzivně nahrazeny nově načtenými obsahy.
 *
 * @param {Element} element Element nebo Document
 * @param {String} className
 * @returns {Promise}
 */
function loadSubsequentElements(element, className) {
    if (element.nodeName=='#document') {
        console.log(`Run loadSubsequentElements() for document.`);
    } else {
        console.log(`Run loadSubsequentElements() for element ${element.tagName}.`);
    }

    // elements is a live HTMLCollection of found elements
    // Warning: This is a live HTMLCollection. Changes in the DOM will reflect in the array as the changes occur. If an element selected by this array no longer qualifies for the selector, it will automatically be removed. Be aware of this for iteration purposes.
    var subElements = element.getElementsByClassName(className);
    console.log(`Nalezeno ${subElements.length} potomkovských elementů s class=${className}.`);
    var subElementsArray = Array.from(subElements);
    let loadSubPromises = subElementsArray.map(subElement => fetchElementContent(subElement));


    if (subElements.length) {console.log(`Volání fetchElementContent() pro subelementy načetlo dalších ${loadSubPromises.length} obsahů.`);}
    // Promise.allSettled just waits for all promises to settle, regardless of the result. The resulting array has:

//    {status:"fulfilled", value:result} for successful responses,
//    {status:"rejected", reason:error} for errors.

    return Promise.allSettled(loadSubPromises);
}

///////////////////////////////////////////////
// Dynamic Script Loading Template

//const loadDynamicScript = (callback) => {
//  const existingScript = document.getElementById('scriptId');
//
//  if (!existingScript) {
//    const script = document.createElement('script');
//    script.src = 'url'; // URL for the third-party library being loaded.
//    script.id = 'libraryName'; // e.g., googleMaps or stripe
//    document.body.appendChild(script);
//
//    script.onload = () => {
//      if (callback) callback();
//    };
//  }
//
//  if (existingScript && callback) callback();
//};
//
//var loadJS = function(url, implementationCode, location){
//    //url is URL of external file, implementationCode is the code
//    //to be called from the file, location is the location to
//    //insert the <script> element
//
//    var scriptTag = document.createElement('script');
//    scriptTag.src = url;
//
//    scriptTag.onload = implementationCode;
//    scriptTag.onreadystatechange = implementationCode;
//
//    location.appendChild(scriptTag);
//};
//var yourCodeToBeCalled = function(){
////your code goes here
//}
//loadJS('yourcode.js', yourCodeToBeCalled, document.body);

//////////////////////////////////////////////