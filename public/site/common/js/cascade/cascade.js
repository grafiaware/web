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
    return fetchCascadeContents(cascadeElements);
}

/**
 * 
 * @param {HTMLCollection} cascadeElements
 * @returns {Promise}
 */
function fetchCascadeContents(cascadeElements) {    
    var cascadeElementsArray = Array.from(cascadeElements);  // kopie z HTMLCollection, která je live collection
    let loadSubPromises = cascadeElementsArray.map(elementToCascade => fetchCascadeContent(elementToCascade));

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
 * S nově načteným obsahem provede:  
 *   Přidá event listenery pro akce menu - volá listenLinks() pro případ, že nový obsah obsahuje nevigaci
 *   Rekurzivně volá loadSubsequentElements(), tím zajistí načtení případných dalších elementů v kaskádě
 * 
 * @param {Element} parentElement
 * @returns {Promise}
 */
function fetchCascadeContent(parentElement){
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
}

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

function fetchNewContent(parentElement, apiUri, cacheControl){

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
        let element = replaceChildren(parentElement, textPromise);  // vrací původní parent element
        return element;
    })
    .catch(e => {
        throw new Error(`cascade: There has been a problem with fetch from ${apiUri}. Reason:` + e.message);
    });
}

/////////////// menu
// proměnné společné pro všechna menu - klik do jiného menu musí skrýt položky z předtím používaného menu
    var previousItem = null;  // proměnná pro uložení event.currentTarget musí být mimo tělo event handleru

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
    // na všechny <li> v elementu s třídou 'navigation' přidá event listener
    let navs = loaderElement.getElementsByClassName('navigation');  // CascadeLoaderFactory
    let navsCnt = navs.length;
    console.log(`cascade: Try to listen links in `+ loaderElement.getAttribute('data-red-apiuri') + ' - ' + navsCnt + ' navs found.');
    for (const navigation of [...navs]) {
        let items = navigation.querySelectorAll("li");
        console.log(`cascade: Listen links match `+items.length+' items.');
        for (const item of [...items]) {
            // když event listener z nějakého důvodu nepracuje, pro vede se dafault akce elementu anchor -> volá se načtení celé stránky
            item.addEventListener("click", event => {
                if(contentTarget===null) {
                    return true;
                } else {
                    let currentItem = event.currentTarget;  // e.target is the element that triggered the event (e.g., the user clicked on) e.currentTarget is the element that the event listener is attached to
                    // item
                    if (previousItem) {
                        styleAsNotPresented(previousItem);                        
                        shrinkChildrenOnPath(previousItem);
                    }
                    styleAsPresented(currentItem);
                    expandChildrenOnPath(currentItem);
                    // aktuální item uložen pro příští klik
                    previousItem = currentItem;
                    
                    // content
                    // příprava elementu pro obsah - nastavím 'data-red-apiuri' na API path pro nový obsah
                    contentTarget.setAttribute('data-red-apiuri', itemAnchor(currentItem).getAttribute('data-red-content-api-uri'));
                    // získání a výměna nového obsahu v cílovém elementu
                    fetchCascadeContent(contentTarget);
                                        
                    // event
                    // vypnutí default akce eventu - default akce eventu je volání href uvedené v anchor elementu - načte se  celá stránka
                    event.preventDefault();
                    // konec šírení eventu
                    event.stopPropagation();
                }
            }
            );
            if (itemAnchor(item).classList.contains("presented")) {
                previousItem = item;
            }
        }        
    }
}

/**
 * Vrací pole elementů sousedících se zadaným elementem, zadaný element není ve výsledku zahrnut. Jedná se tedy o HTML sourozence s vynecháním zadaného elementu.
 * 
 * @param {Element} element
 * @returns {Array|getNeighbors.siblings}
 */
//function getNeighbors(element) {
//    // for collecting siblings
//    let siblings = []; 
//    // if no parent, return no sibling
//    if(!element.parentNode) {
//        return siblings;
//    }
//    // first child of the parent node
//    let sibling  = element.parentNode.firstChild;
//    // collecting siblings
//    while (sibling) {
//        if (sibling.nodeType === 1 && sibling !== element) {  // type 1 = Element node
//            siblings.push(sibling);
//        }
//        sibling = sibling.nextSibling;
//    }
//    return siblings;
//}

/**
 * Vrací anchor element obsažený v item elementu
 * @param {type} itemElement
 * @returns {unresolved}
 */
function itemAnchor(itemElement) {
    return itemElement.children[0];
}

function styleAsPresented(element){
    itemAnchor(element).classList.add("presented");
}

function styleAsNotPresented(element){
    itemAnchor(element).classList.remove("presented");    
}

/**
 * Vybere všechny elemnty typu LI na cestě k zadanému elementu, zadaný element JE ve výsledku zahrnut.
 * 
 * @param {type} element
 * @returns {Array|getOnPathItemElements.pathElements}
 */
function getOnPathItemElements(element) {
    // for collecting elements
    let pathElements = []; 
        if (element.tagName === "LI") { 
            pathElements.push(element);
        }
    // if no parent, return no elements
    if(!element.parentElement) {
        return pathElements;
    }
    // first child of the parent node
    let parent  = element.parentElement;
    // collecting parents
    while (parent) {   // ?? closest("li")
        if (parent.tagName === "LI") { 
            pathElements.push(parent);
        }
        parent = parent.parentElement;
    }
    return pathElements;
}

/**
 * Slouží pro stylování - pro skrytí potomků v menu
 * Odstraní třídu "parent" všem elementům na cestě (viz getOnPathElements()).
 * 
 * @param {Element} previousAnchor
 * @returns {undefined}
 */
function shrinkChildrenOnPath(previousAnchor) {
    let parentElements = getOnPathItemElements(previousAnchor);
    parentElements.forEach(element => {element.classList.remove("parent")})
}

/**
 * Slouží pro stylování - pro zviditelnění potomků v menu.
 * Přidá do všech elementů na cestě (viz getOnPathElements()), které neobsahují třídu "leaf" (tedy nejsou listy=mají potomky) třídu "parent".
 * 
 * @param {Element} currentAnchor
 * @returns {undefined}
 */
function expandChildrenOnPath(currentAnchor) {
    let parentElements = getOnPathItemElements(currentAnchor);    
    parentElements.forEach(element => {
        if(!element.classList.contains("leaf")) {
            element.classList.add("parent");
        }
    });
}