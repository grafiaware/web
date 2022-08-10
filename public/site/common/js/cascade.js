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
    console.log("cascade: Replaced element "+replacedElement.tagName+" with element "+newElement.tagName+".");
    return newElement;
};

/**
 *
 * @param {Element} replacedElement
 * @returns {Promise}
 */
function fetchElementContent(replacedElement){
    let apiUri = getApiUri(replacedElement);
    /// fetch ///
    // fetch vrací Promise, která resolvuje s Response objektem a to v okamžiku, kdy server odpoví a jsou přijaty hlavičky odpovědi - nečeká na stažení celeho response
    // tento return je klíčový - vrací jako návratovou hodnotu hodotu vrácenou příkazem return v posledním bloku .then - viz https://developer.mozilla.org/en-US/docs/Learn/JavaScript/Asynchronous/Promises
    return fetch(apiUri)
    .then(response => {
      if (response.ok) {  // ok je true pro status 200-299, jinak je vždy false
          // pokud došlo k přesměrování: status je 200, (mohu jako druhý paremetr fetch dát objekt s hodnotou např. redirect: 'follow' atd.) a také porovnávat response.url s požadovaným apiUri
          return response.text(); //vrací Promise, která resolvuje na text až když je celý response je přijat ze serveru
      } else {
          throw new Error(`cascade: HTTP error! Status: ${response.status}`);  // will only reject on network failure or if anything prevented the request from completing.
      }
    })
    .then(textPromise => {
        console.log(`cascade: Loading content from ${apiUri}.`);
        return replaceElement(replacedElement, textPromise);  // vrací nový Element
    })
    .then(newElement => {
        loadSubPromise = loadSubsequentElements(newElement, "red_loaded");
        return loadSubPromise;
    })
    .catch(e => {
      console.log(`cascade: There has been a problem with fetch from ${apiUri}. Reason:` + e.message);
      throw new Error(`cascade: There has been a problem with fetch from ${apiUri}. Reason:` + e.message);
    });
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
 * Načte pomocí funkce fetchElement() nové obsahy všech elementů, které jsou potomky zadaného elementu nebo dokumentu a mají třídu zadaného jména.
 * Nalezené potomky nahradí za nově načtené elementy.
 * Z každého nalezeného potomka použije hodnotu atributu data-red-apiuri jako URI požadavku, pomocí kterého získá nový obsah - HTML text.
 * Volání robíhá rekurzivně. Na nově načtený element (a jeho potomky) je znovu volána tato funkce a pokud jsou v nové načteném elementu nalezeny elementy se zadaným jménem třídy,
 * jsou rekurzivně nahrazeny nově načtenými obsahy.
 *
 * @param {Element} element Element nebo Document
 * @param {String} className
 * @returns {Promise}
 */
function loadSubsequentElements(element, className) {
    if (element.nodeName=='#document') {
        console.log(`cascade: Run loadSubsequentElements() for document.`);
    } else {
        console.log(`cascade: Run loadSubsequentElements() for element ${element.tagName}.`);
    }

    // elements is a live HTMLCollection of found elements
    // Warning: This is a live HTMLCollection. Changes in the DOM will reflect in the array as the changes occur. If an element selected by this array no longer qualifies for the selector, it will automatically be removed. Be aware of this for iteration purposes.
    var subElements = element.getElementsByClassName(className);
    console.log(`cascade: ${subElements.length} child elements founded with class="${className}".`);
    var subElementsArray = Array.from(subElements);
    let loadSubPromises = subElementsArray.map(subElement => fetchElementContent(subElement));


    if (subElements.length) {console.log(`cascade: Calling of fetchElementContent() fetched next ${loadSubPromises.length} element contents.`);}
    // Promise.allSettled just waits for all promises to settle, regardless of the result. The resulting array has:

//    {status:"fulfilled", value:result} for successful responses,
//    {status:"rejected", reason:error} for errors.

    return Promise.allSettled(loadSubPromises);
}
