import {initElements} from "../initLoadedElements/initElements.js";

const conf = {
    "apiUri": "data-red-apiuri",
    "content": "data-red-content",
    "cacheControl": "data-red-cache-control",
    "targetId": "data-red-target-id",
    "apiAction": "apiAction",
    "navigationClass": "navigation",
    "itemLeafClass": "leaf",
    "itemParentClass": "parent"
};

var cascadeClassName = 'cascade';

/**
 * 
 * @param {Document} document Document
 * @param {String} className
 * @returns {Promise}
 */
export function loadSubsequentElements(document, className) {
    cascadeClassName = className;
    history.replaceState({}, "", document.URL);  // https://developer.mozilla.org/en-US/docs/Web/API/History_API/Working_with_the_History_API#using_replacestate
    return fetchCascadeContents(document);
}

/**
 * Načte pomocí funkce fetchElement() nové obsahy všech elementů, které jsou potomky zadaného elementu nebo dokumentu a mají třídu zadaného jména.
 * Nalezené potomky nahradí za nově načtené elementy.
 * Z každého nalezeného potomka použije hodnotu atributu conf.apiUri jako URI požadavku, pomocí kterého získá nový obsah - HTML text.
 * Volání probíhá rekurzivně. Na nově načtený element (a jeho potomky) je znovu volána tato funkce a pokud jsou v nové načteném elementu nalezeny elementy se zadaným jménem třídy,
 * jsou rekurzivně nahrazeny nově načtenými obsahy.
 * 
 * @param {Element} element Element nebo Document
 * @returns {Promise}
 */
function fetchCascadeContents(element) { 
    if (element.nodeName==='#document') {
        console.log(`cascade: Run loadSubsequentElements() for document.`);
    } else {
        console.log(`cascade: Run loadSubsequentElements() for element ${element.tagName}.`);
    }

    // elements is a live HTMLCollection of found elements
    // Warning: This is a live HTMLCollection. Changes in the DOM will reflect in the array as the changes occur. If an element selected by this array no longer qualifies for the selector, it will automatically be removed. Be aware of this for iteration purposes.
    var cascadeElements = element.getElementsByClassName(cascadeClassName);
    console.log(`cascade: ${cascadeElements.length} child elements for cascade founded by class="${cascadeClassName}".`);    
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
 * - adresu url získá z atributu rodičovského HTML elementu conf.apiUri
 * - hlavičku Cache-Control získá z atributu rodičovského HTML elementu conf.cacheControl - slouží k požadavku na reload obsahu pro případ, 
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
        }).then(response => {
      if (response.ok) {  // ok je true pro status 200-299, jinak je vždy false
          // pokud došlo k přesměrování: status je 200, (mohu jako druhý paremetr fetch dát objekt s hodnotou např. redirect: 'follow' atd.) a také porovnávat response.url s požadovaným apiUri
          return response.text(); //vrací Promise, která resolvuje na text až když je celý response je přijat ze serveru
      } else {
          throw new Error(`cascade: HTTP error! Status: ${response.status}`);  // will only reject on network failure or if anything prevented the request from completing.
      }
    }).then(textPromise => {
        console.log(`cascade: Loading content from ${apiUri}.`);
        return replaceChildren(parentElement, textPromise);  // vrací původní parent element
    }).then(parentWithNewContent => {
        listenLinks(parentWithNewContent);
        listenFormsWithApiAction(parentWithNewContent);
        return fetchCascadeContents(parentWithNewContent);
    }).then(allSettledPromise => {
        initElements();
    }).catch(e => {
        throw new Error(`cascade: There has been a problem with fetch from ${apiUri}. Reason:` + e.message);
    });
}

function getTargetElement(cascadeElement) {
    let targetId = getTargetId(cascadeElement);
    return document.getElementById(targetId);
}

/**
 * Vrací hodnotu atributu conf.apiUri.
 *
 * @param {Element} element
 * @returns {String}
 */
function getApiUri(element) {
    if (element.hasAttribute(conf.apiUri)) {
        return element.getAttribute(conf.apiUri); // element.attributes.getNamedItem(conf.apiUri).nodeValue;
    } else {
        console.error(`Cascade: element nemá povinný atribut conf.apiUri ${element}`);
    }
}

function setApiUri(element, apiUri) {
    return element.setAttribute(conf.apiUri, apiUri); // element.attributes.getNamedItem(conf.apiUri).nodeValue;
}

/**
 * Vrací hodnotu atributu conf.cacheControl.
 *
 * @param {Element} element
 * @returns {String}
 */
function getCacheControl(element) {
    return element.hasAttribute(conf.cacheControl) ? element.getAttribute(conf.cacheControl) : 'default'; // element.attributes.getNamedItem(conf.cacheControl).nodeValue : 'default';
}

/**
 * Testuje existenci atributu conf.targetId.
 *
 * @param {Element} element
 * @returns {String}
 */
function hasTargetId(element) {
    return  element.hasAttribute(conf.targetId);
}

/**
 * Vrací hodnotu atributu conf.targetId.
 *
 * @param {Element} element
 * @returns {String}
 */
function getTargetId(element) {
    if (hasTargetId(element)) {
        return element.getAttribute(conf.targetId); // element.attributes.getNamedItem(conf.targetId).nodeValue;
    } else {
        console.error(`Cascade: element nemá povinný atribut conf.targetId ${element}`);
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
    console.log("cascade: Replaced children of element "+parentElement.tagName+" with api uri attribute: "+parentElement.getAttribute(conf.apiUri)+" with collection of "+cnt+".");
    return parentElement;
};

/////////////// form s apiAction
function listenFormsWithApiAction(loaderElement) {

    let formsWithApiAction = loaderElement.querySelectorAll("."+conf.apiAction);

    formsWithApiAction.forEach((form) => {form.addEventListener('click', (event) => {
        event.preventDefault();
        // TODO do something here to show user that form is being submitted
        var formElement = event.currentTarget;
        var but = event.target.closest("button")
        var actionUri = but.getAttribute("formaction");   // button formaction - klik by na elem uvnitř buttonu - např. i
        if (actionUri === null) {
            var actionUri = formElement.getAttribute("action");     // form action
        }
        fetch(actionUri, {
            method: 'POST',
            body: new URLSearchParams(new FormData(formElement))
        }).then((response) => {
            if (response.ok) {
                console.log(`cascade: Send content to ${actionUri}.`);
                return response.text(); // or response.json() or whatever the server sends
            } else {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
        }).then(textPromise => {
            fetchClosestCascadeContent(formElement);
        }).catch(e => {
            throw new Error(`cascade: There has been a problem with fetch with POST ${actionUri}. Reason:` + event.message);
        });
    })
    })
}

function fetchClosestCascadeContent(formElement) {
    let loaderElemeent = formElement.closest("."+cascadeClassName);
    fetchCascadeContent(loaderElemeent);    
}

/////////////// menu
// proměnné společné pro všechna menu - klik do jiného menu musí skrýt položky z předtím používaného menu
var previousItem = null;  // proměnná pro uložení event.currentTarget musí být mimo tělo event handleru

/**
 * Volá se v funkci fetchCascadeContent() po načtení "kaskádního" obsahu. Připojí event listenery pro click event na elementech items.
 * 
 * Může se volat rekurzivně, pokud načtený obsah bude obsahovat element(y), definované zde jako navs. Pak po kliknutí na položku menu vznikne event, 
 * načtou se nové drivery a nový obsah pomocí funkce fetchCascadeContent(). Ve funkci fetchCascadeContent() se znovu volá listenLinks().
 * 
 * @param {Element} loaderElement
 * @returns {undefined}
 */
function listenLinks(loaderElement) {  
    let cacheControl = getCacheControl(loaderElement);
    
    if (hasTargetId(loaderElement)) {
        const contentTarget = document.getElementById(getTargetId(loaderElement));
        // na všechny <li> v elementu s třídou conf.navigationClass přidá event listener
        let navs = loaderElement.getElementsByClassName(conf.navigationClass);
        let navsCnt = navs.length;
        console.log(`cascade: Try to listen links in `+ loaderElement.getAttribute(conf.apiUri) + ' - ' + navsCnt + ' navs found.');
        for (const navigation of [...navs]) {
            let items = navigation.querySelectorAll("li");
            console.log(`cascade: Listen links match `+items.length+' items.');
            for (const item of [...items]) {
                // když event listener z nějakého důvodu nepracuje, provede se default akce elementu anchor -> volá se načtení celé stránky
                item.addEventListener("click", linkListener.bind(contentTarget));
                if (itemDriver(item).classList.contains("presented")) {  // první previousItem po načtení menu - závisí va class "presented" - ŠPATNĚ
                    previousItem = item;
                }
            }        
        }        
    } else {
        console.warn(`cascade: Loader element s api uri: ${loaderElement.getAttribute(conf.apiUri)} nemá atribut ${conf.targetId}.`);
    }
}

/**
 * 
 * @param {Event} event
 * @returns {unresolved}
 */
function linkListener(event) {
    let currentItem = event.currentTarget;  // e.target is the element that triggered the event (e.g., the user clicked on) e.currentTarget is the element that the event listener is attached to
    // item
    if (previousItem !== currentItem) {
        // href pro history.pushState, získá se z href atributu elementu <a> v driveru, v tuto chvíli je ještě 
        let currentHref = itemDriver(currentItem).getAttribute('href');
        // toggle drivers
        getNewDrivers(previousItem, currentItem);                        
        shrinkAndExpandChildrenOnPath(previousItem, currentItem);
        // aktuální item uložen pro příští klik
        previousItem = currentItem;

        // content
        // použije hodnotu atributu 'data-red-content' z driveru a nastaví atribut conf.apiUri cílového elementu na API path pro nový obsah
        let newContentApiUri = itemDriver(currentItem).getAttribute('data-red-content');
        let contentTarget = this;  // bind
        contentTarget.setAttribute(conf.apiUri, newContentApiUri);
        // získání a výměna nového obsahu v cílovém elementu
        fetchCascadeContent(contentTarget);
        history.pushState({}, "", currentHref);
    }
    // event
    // vypnutí default akce eventu - default akce eventu je volání href uvedené v anchor elementu - načte se  celá stránka
    event.preventDefault();
    // konec šírení eventu
    event.stopPropagation();
};

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
 * Vrací anchor element obsažený v item elementu - funguje jen pro item v needitovatelném stavu
 * @param {type} itemElement
 * @returns {unresolved}
 */
function itemDriver(itemElement) {
    return itemElement.children[0];
}

function getNewDrivers(previousItem, currentItem){
    let presentedDriverApi = itemDriver(currentItem).getAttribute('data-red-driver');
    getDriver(currentItem, presentedDriverApi, 'default');
    if (previousItem) {
        let driverApi = itemDriver(previousItem).getAttribute('data-red-driver');
        getDriver(previousItem, driverApi, 'default');
    }
}

function getDriver(item, apiUri, cacheControl){

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
        let element = replaceDriver(item, textPromise);  // vrací původní parent element
        return element;
    })
    .catch(e => {
        throw new Error(`cascade: There has been a problem with fetch from ${apiUri}. Reason:` + e.message);
    });
}

function formButtonClick(event) {
    event.stopPropagation();
}

function replaceDriver(itemElement, newHtmlTextContent) {
    var newElements = htmlToElements(newHtmlTextContent);
    var cnt = newElements.length;
    if (cnt>1) {
        console.warn("cascade: New driver as children of element "+itemElement.tagName+" conf.apiUri: "+itemElement.getAttribute(conf.apiUri)+" has "+cnt+" element(s).");        
    } else {
//        itemElement.replaceChild(newElements[0], itemDriver(itemElement));  // odstraní staré a přidá nové elementy
        itemDriver(itemElement).replaceWith(newElements[0]);
        console.log("cascade: New driver as children of element "+itemElement.tagName+" conf.apiUri: "+itemElement.getAttribute(conf.apiUri)+" has "+cnt+" element(s).");
    }
    listenFormsWithApiAction(itemElement);
//    const forms = itemElement.getElementsByTagName("form");
//    for (const form of forms) {    
//        form.addEventListener("click", formButtonClick);    
//    }
    return itemElement;
};

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
    // if no parent, return no parent elements
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
 * Slouží pro stylování - pro skrytí potomků previousItem a zviditelnění potomků currentAnchor v menu
 * Odstraní třídu conf.itemParentClass všem elementům na cestě k položce previousItem (viz getOnPathElements()).
 * Přidá do všech elementů na cestě (viz getOnPathElements()), které neobsahují třídu conf.itemLeafClass (tedy nejsou listy=mají potomky) třídu "parent".
 * 
 * @param {type} previousItem
 * @param {type} currentItem
 * @returns {undefined} */
function shrinkAndExpandChildrenOnPath(previousItem, currentItem) {
    if (previousItem) {    
        let parentElements = getOnPathItemElements(previousItem);
        parentElements.forEach(element => {element.classList.remove(conf.itemParentClass)});
    }
    let parentElements = getOnPathItemElements(currentItem);    
    parentElements.forEach(element => {
        if(!element.classList.contains(conf.itemLeafClass)) {
            element.classList.add(conf.itemParentClass);
        }
    }
    );
}
