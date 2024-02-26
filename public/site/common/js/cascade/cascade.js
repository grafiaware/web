/**
 * Načte pomocí funkce fetchElement() nové obsahy všech elementů, které jsou potomky zadaného elementu nebo dokumentu a mají třídu zadaného jména.
 * Nalezené potomky nahradí za nově načtené elementy.
 * Z každého nalezeného potomka použije hodnotu atributu data-red-apiuri jako URI požadavku, pomocí kterého získá nový obsah - HTML text.
 * Volání probíhá rekurzivně. Na nově načtený element (a jeho potomky) je znovu volána tato funkce a pokud jsou v nové načteném elementu nalezeny elementy se zadaným jménem třídy,
 * jsou rekurzivně nahrazeny nově načtenými obsahy.
 *
 * @param {Element} element Element nebo Document
 * @param {String} className
 * @returns {Promise}
 */
export function loadSubsequentElements(element, className) {
    if (element.nodeName==='#document') {
        console.log(`cascade: Run loadSubsequentElements() for document.`);
    } else {
        console.log(`cascade: Run loadSubsequentElements() for element ${element.tagName}.`);
    }

    // elements is a live HTMLCollection of found elements
    // Warning: This is a live HTMLCollection. Changes in the DOM will reflect in the array as the changes occur. If an element selected by this array no longer qualifies for the selector, it will automatically be removed. Be aware of this for iteration purposes.
    var cascadeElements = element.getElementsByClassName(className);
    console.log(`cascade: ${cascadeElements.length} elements for cascade founded by class="${className}".`);
    return fetchContents(cascadeElements);
}

/**
 * 
 * @param {HTMLCollection} cascadeElements
 * @returns {Promise}
 */
function fetchContents(cascadeElements) {    
    var cascadeElementsArray = Array.from(cascadeElements);  // kopie z HTMLCollection, která je live collection
    let loadSubPromises = cascadeElementsArray.map(elementToCascade => fetchElementContent(elementToCascade));

    if (cascadeElements.length) {console.log(`cascade: Calling of fetchContents() fetched next ${loadSubPromises.length} element contents.`);}
    // Promise.allSettled just waits for all promises to settle, regardless of the result. The resulting array has:
    //    {status:"fulfilled", value:result} for successful responses,
    //    {status:"rejected", reason:error} for errors.

    return Promise.allSettled(loadSubPromises);
}

/**
 * Získá HTML řetězec pomocí HTTP GET requestu na adresu (url) s hlavičkou Cache-Control a přidanou hlavičkou X-Cascade.
 * 
 * - adresu url získá z atributu rodičovského HTML elementu "data-red-apiuri"
 * - hlavičku Cache-Control získá z atributu rodičovského HTML elementu "data-red-cache-control" - slouží k požadavku na reload obsahu pro případ, 
 *   kdy obsah je v editačním režimu, jinak je nastavena tak, že se obsah načte jen jednou a používá se z cache 
 * - hlavičku X-Cascade odesílá s hodnotou "Do not store request" - tato hlavička je signál, aby PresentationFrontControlerAbstract neukládal tento 
 *   cascade request jako "last GET"
 * 
 * @param {Element} parentElement
 * @returns {Promise}
 */
function fetchElementContent(parentElement){
    let apiUri = getApiUri(parentElement);
    let cacheControl = getCacheControl(parentElement);

    /// fetch ///
    // fetch vrací Promise, která resolvuje s Response objektem a to v okamžiku, kdy server odpoví a jsou přijaty hlavičky odpovědi - nečeká na stažení celeho response
    // tento return je klíčový - vrací jako návratovou hodnotu hodnotu vrácenou příkazem return v posledním bloku .then - viz https://developer.mozilla.org/en-US/docs/Learn/JavaScript/Asynchronous/Promises
    return fetch(apiUri, {
        method: "GET",      //default
          cache: cacheControl,
          headers: {
            "X-Cascade": "Do not store request",   // příznak pro PresentationFrontControlerAbstract - neukládej request jako last GET
          },
        })
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
        let element = replaceChildren(parentElement, textPromise);  // vrací původní parent element
        listenLinks(element);
        return element;
    })
    .then(targetWithNewContent => {
        return loadSubsequentElements(targetWithNewContent, "cascade");
    })
    .catch(e => {
        throw new Error(`cascade: There has been a problem with fetch from ${apiUri}. Reason:` + e.message);
    });
};

function selectTarget(cascadeElement) {
    let targetId = getTargetId(cascadeElement);
    return document.getElementById(targetId);
}

/**
 * Vrací hodnotu atributu "data-red-apiuri".
 *
 * @param {Element} element
 * @returns {String}
 */
function getApiUri(element) {
    if (element.hasAttribute("data-red-apiuri")) {
        return element.getAttribute("data-red-apiuri"); // element.attributes.getNamedItem("data-red-apiuri").nodeValue;
    } else {
        console.error(`Cascade: element nemá povinný atribut "data-red-apiuri" ${element}`);
    }
}

function setApiUri(element, apiUri) {
    return element.setAttribute("data-red-apiuri", apiUri); // element.attributes.getNamedItem("data-red-apiuri").nodeValue;
}

/**
 * Vrací hodnotu atributu "data-red-cache-control".
 *
 * @param {Element} element
 * @returns {String}
 */
function getCacheControl(element) {
    return element.hasAttribute("data-red-cache-control") ? element.getAttribute("data-red-cache-control") : 'default'; // element.attributes.getNamedItem("data-red-cache-control").nodeValue : 'default';
}

/**
 * Testuje existenci atributu "data-red-target-id".
 *
 * @param {Element} element
 * @returns {String}
 */
function hasTargetId(element) {
    return  element.hasAttribute("data-red-target-id");
}

/**
 * Vrací hodnotu atributu "data-red-target-id".
 *
 * @param {Element} element
 * @returns {String}
 */
function getTargetId(element) {
    if (hasTargetId(element)) {
        return element.getAttribute("data-red-target-id"); // element.attributes.getNamedItem("data-red-target-id").nodeValue;
    } else {
        console.error(`Cascade: element nemá povinný atribut "data-red-target-id" ${element}`);
    }
}

/**
 * Převede zadaný text na elementy.
 *
 * @param {String} html HTML representing any number of sibling elements
 * @return {NodeList}
 */
function htmlToElements(html) {
//    Vytvoří nový template element a pokusí se vložit hodnotu parametru html jako innerHtml template elementu, 
//    pokud prohlížeč uspěje s parsováním textu jako html, vloží nové elementy jako potomky    
    var template = document.createElement('template');
    template.innerHTML = html;
    return template.content.childNodes;
}

/**
 * Nahradí potomky zadaného elementu novými potomky vzniklými parsováním zadaného textu jako HTML.
 *
 * @param {Element} parentElement - element jehož obsah bude nahrazován
 * @param {String} newHtmlTextContent HTML string obsahující elementy pro vložení do rodičovského elementu
 * @returns {Element} parentElement s nahrazeným obsahem
 */
function replaceChildren(parentElement, newHtmlTextContent) {
    var newElements = htmlToElements(newHtmlTextContent);
    var cnt = newElements.length;  // live collection - v replaceChildren se "spotřebuje", length se musí zjistit před použitím
    parentElement.replaceChildren(...newElements);  // odstraní staré a přidá nové elementy
    console.log("cascade: Replaced children of element "+parentElement.tagName+" data-red-apiuri: "+parentElement.getAttribute('data-red-apiuri')+" with collection of "+cnt+".");
    return parentElement;
};

/////////////// menu
/**
 * 
 * @param {Element} loaderElement
 * @returns {undefined}
 */
function listenLinks(loaderElement) {  
    let contentTarget = null;
    if (hasTargetId(loaderElement)) {
        contentTarget = document.getElementById(getTargetId(loaderElement));
    }    
    let previousAnchor = null;  // proměnná pro uložení event.currentTarget musí být mimo tělo event handleru
    // na všechny <a> v elementu s třídou 'navigation' přidá event listener
    let navs = loaderElement.getElementsByClassName('navigation');  // CascadeLoaderFactory
    let navsCnt = navs.length;
        console.log(`cascade: Try to listen links in `+ loaderElement.getAttribute('data-red-apiuri') + ' - ' + navsCnt + ' navs found.');
    for (const navigation of [...navs]) {
        let anchors = navigation.querySelectorAll("a");
        for (const anchor of [...anchors]) {
        console.log(`cascade: Listen links match `+anchors.length+' anchors.');
            anchor.addEventListener("click", event => {
                if(contentTarget===null) {
                    return true;
                } else {
                    if (previousAnchor) {
                        previousAnchor.classList.remove("presented");
                    }
                    let currentAnchor = event.currentTarget;
                    let currentLi = currentAnchor.parentElement;
                    currentAnchor.classList.add("presented");
                    contentTarget.setAttribute('data-red-apiuri', currentAnchor.getAttribute('data-red-content-api-uri'));
                    fetchElementContent(contentTarget);
                    previousAnchor = currentAnchor;
                    event.preventDefault();
                    return false;
                }
            });
            if (anchor.classList.contains("presented")) {
                previousAnchor = anchor;
            }
        }    
    }
}