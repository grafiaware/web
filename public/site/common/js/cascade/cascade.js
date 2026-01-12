//import {initElements} from "../initLoadedElements/initElements_1.js";

// viz local\site\common\templates\layout\cascade\loaderElement.php
const conf = {
    cascadeClass: 'cascade',
    elementApiUri: "data-red-apiuri",
    contentApiUri: "data-red-content",
    cacheControl: "data-red-cache-control",
    targetId: "data-nav-target-id",
    //TODO: touto změnou je vypnuto OFF -> cascade off
//    apiAction: "apiAction",
//    navigationClass: "navigation",

    apiAction: "apiAction",
    navigationClass: "navigation",
    itemElementName: 'li',
    itemLeafClass: "leaf",
    itemParentClass: "parent",
    itemIdPrefix: "item_"
};


// cache control vypnuto ve funkci getCacheControl()
var apiPrefix = "red/v1/";
var api = {
    driver: "red/v1/driver/",
    presenteddriver: "red/v1/presenteddriver/",
};

/**
 * 
 * @param {Document} document Document
 * @param {String} className
 * @returns {Promise}
 */
export function loadSubsequentElements(document, className) {
    conf.cascadeClass = className;
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
    var cascadeElements = element.getElementsByClassName(conf.cascadeClass);
    console.log(`cascade: ${cascadeElements.length} child elements for cascade founded by class="${conf.cascadeClass}".`);    
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
            "X-Cascade": "do not store request",   // příznak pro PresentationStatus - neukládej request jako last GET
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
        console.log(`cascade: Loaded content from ${apiUri}.`);
//        initElements();  // import z initElements.js
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
    if (element.hasAttribute(conf.elementApiUri)) {
        return element.getAttribute(conf.elementApiUri); // element.attributes.getNamedItem(conf.apiUri).nodeValue;
    } else {
        console.error(`Cascade: element nemá povinný atribut conf.apiUri ${element}`);
    }
}

function setApiUri(element, apiUri) {
    return element.setAttribute(conf.elementApiUri, apiUri); // element.attributes.getNamedItem(conf.apiUri).nodeValue;
}

/**
 * Vrací hodnotu atributu conf.cacheControl.
 *
 * @param {Element} element
 * @returns {String}
 */
function getCacheControl(element) {
    return 'default'; //element.hasAttribute(conf.cacheControl) ? element.getAttribute(conf.cacheControl) : 'default'; // element.attributes.getNamedItem(conf.cacheControl).nodeValue : 'default';
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
    console.log(`cascade: Replaced children of element ${parentElement.tagName} with api uri attribute: ${parentElement.getAttribute(conf.elementApiUri)} with collection of ${cnt}.`);
    return parentElement;
};


/////////////// menu
// proměnné společné pro všechna menu - klik do jiného menu musí skrýt položky z předtím používaného menu
var previousItem = null;  // proměnná pro uložení event.currentTarget musí být mimo tělo event handleru

/////////////// form s apiAction
function listenFormsWithApiAction(loaderElement) {

    let formsWithApiAction = loaderElement.querySelectorAll("."+conf.apiAction);

    formsWithApiAction.forEach((form) => {
        form.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();  // nutné - jinak dojde k probublání eventu refresh stránky (response status je (200 OK)
            // TODO do something here to show user that form is being submitted
            var formElement = event.currentTarget;
            var but = event.target.closest("button");
            if (but !== null) {
                var actionUri = but.getAttribute("formaction");   // button formaction - klik by na elem uvnitř buttonu - např. i
            }
            if (actionUri === null) {
                var actionUri = formElement.getAttribute("action");     // form action
            }
//                    formElement.addEventListener('submit', function(event) {  
//            event.preventDefault(); // Prevents the default form submission
//        });
            fetch(actionUri, {
                method: 'PUT',
                body: new URLSearchParams(new FormData(formElement))
            }).then((response) => {           
                if (response.ok) {
                    return response.json().then(json => {
                        console.log(`cascade: Content to ${actionUri} sent.`);
                        console.log(`cascade: Response status was ok and the body could be parsed.`);
                        fetchFreshContent(formElement, json);
                    }).catch(err => {
                        console.log(`cascade: Response status was ok but the body was empty or not JSON. ${err}`);
    //                    return { response };
                    });

                } else {
                    return response.json().catch(err => {
                        console.log(`cascade: Response status was not ok and the body was not JSON. ${err}`);
                        throw new Error(`HTTP error! Status: ${response.status}`);
    //                    throw new Error(response.statusText);
                    }).then(parsedValue => {
                        console.log(`cascade: Response status was not ok and the body was JSON.`);
                        throw new Error(parsedValue.error); // assuming our API returns an object with an error property
                    });
                }            
            }).catch(e => {
                throw new Error(`cascade: There has been a problem with fetch with POST ${actionUri}. Reason:` + event.message);
            });
        });

    });
}

function fetchFreshContent(formElement, json) {
    let refresh = json.refresh;
    
    if (refresh==="norefresh") {  // pro tinyMce
        return;
    } else if (refresh==="closest") {  // formulář v elementu mimo menu (např. article
        // loader element nemá definovaný target, formulář je potomkem loader elementu -> změna stavu po POSTu se může projevit změnou obsahu loader elemntu
        let loaderElement = closestCascadeElement(formElement);
        fetchCascadeContent(loaderElement);        
    } else if (refresh==="item") {  // formulář v item menu, který ovlivňuje content (např. toggle)
        let loaderElement = closestCascadeElement(formElement);
        itemAndContentChange(loaderElement, json);
    } else if (refresh==="navigation") {    // formulář v item menu, který ovlivňuje strukturu menu a případně content
        let loaderElement = closestCascadeElement(formElement);
        let navs = document.getElementsByClassName(conf.navigationClass);
        for (const navigation of [...navs]) {
            let navElement = closestCascadeElement(navigation);
            fetchCascadeContent(navElement);        
        }
        itemAndContentChange(loaderElement, json);        
    } else if (refresh==="document") {  // formulář, který ovlivňuje kdeco
        window.location.reload();                
    } else {
        window.location.reload();                        
    }
}

function closestCascadeElement(element) {
    let closest = element.closest("."+conf.cascadeClass);
    if (closest === null) {
        throw new Error(`cascade: No closest element with cascade class for : ${element}`);
    }
    return closest;
}

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
        console.log(`cascade: Try to listen links in `+ loaderElement.getAttribute(conf.elementApiUri) + ' - ' + navsCnt + ' navs found.');
        for (const navigation of [...navs]) {
            let items = navigation.querySelectorAll(conf.itemElementName);
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
        console.warn(`cascade: Loader element s api uri: ${loaderElement.getAttribute(conf.elementApiUri)} nemá atribut ${conf.targetId}.`);
    }
}

/**
 * 
 * @param {Event} event
 * @returns {unresolved}
 */
function linkListener(event) {
    let currentItem = event.currentTarget;  // e.target is the element that triggered the event (e.g., the user clicked on) e.currentTarget is the element that the event listener is attached to
    let contentTarget = this;  // bind
    menuAction(currentItem, contentTarget);
    switchContent(currentItem, contentTarget);    
    // event
    // vypnutí default akce eventu - default akce eventu je volání href uvedené v anchor elementu - načte se  celá stránka
    event.preventDefault();
    // konec šírení eventu
    event.stopPropagation();
};

function itemAndContentChange(loaderElement, json) {
    if (hasTargetId(loaderElement)) {        
        // loader element má definovaný target -> změna stavu po POSTu se může projevit změnou obsahu target elementu
        const contentTarget = document.getElementById(getTargetId(loaderElement));
        if (json.targeturi !== undefined) {  // response obsahuje nové uri pro získání nového contentu
            contentTarget.setAttribute(conf.elementApiUri, json.targeturi);
        }
//            fetchCascadeContent(contentTarget); // načtení je v menuaction    // znovunačtení obsahu - bez změny api uri, jen pro refresh obsahu, který může být změněn postem nebo s novým uri pro nový (zaměněný) obsah
        if (json.newitemuid !== undefined) {  // response obsahuje newitemuid nového item pro záměnu obsahu předešlého a nového driveru
            let currentItem = document.getElementById(conf.itemIdPrefix + json.newitemuid);
            menuAction(currentItem, contentTarget);
            switchContent(currentItem, contentTarget);               
        } else {
            menuAction(previousItem, contentTarget);                
            switchContent(previousItem, contentTarget);               
        }
    } else {
        console.warn("cascade: No target defined in loader element");
        window.location.reload();        
    }    
}

function menuAction(currentItem, contentTarget) {
    if (previousItem !== currentItem) {
        // href pro history.pushState, získá se z href atributu elementu <a> v driveru, v tuto chvíli je currentItem ještě starý
        let currentHref = itemDriver(currentItem).getAttribute('href');        
        history.pushState({}, "", currentHref);        
    }
    // item
    switchItem(currentItem);
    // listenery - click na item (<li>) a <form>
    currentItem.addEventListener("click", linkListener.bind(contentTarget)); // přidávám listener na nový element, bind contentTarget - jedna globální proměnná?
    listenFormsWithApiAction(currentItem);
}


function switchItem(currentItem) {
    // toggle drivers
    getNewDrivers(previousItem, currentItem);                        
    shrinkAndExpandChildrenOnPath(previousItem, currentItem);
    // aktuální item uložen pro příští klik
    previousItem = currentItem;
}

function switchContent(currentItem, contentTarget) {
    // content
    // použije hodnotu atributu conf.content z driveru a nastaví atribut conf.apiUri cílového elementu na API path pro nový obsah
    let newContentApiUri = itemDriver(currentItem).getAttribute(conf.contentApiUri);
    contentTarget.setAttribute(conf.elementApiUri, newContentApiUri);
    // získání a výměna nového obsahu v cílovém elementu
    fetchCascadeContent(contentTarget);    
}

/**
 * Vrací anchor element obsažený v item elementu - funguje jen pro item v needitovatelném stavu
 * @param {type} itemElement
 * @returns {unresolved}
 */
function itemDriver(itemElement) {
    return itemElement.children[0];
}

function getNewDrivers(previousItem, currentItem){
    function fetchDriver(item, apiUri, cacheControl){

        /// fetch ///
        // fetch vrací Promise, která resolvuje s Response objektem a to v okamžiku, kdy server odpoví a jsou přijaty hlavičky odpovědi - nečeká na stažení celeho response
        // tento return je klíčový - vrací jako návratovou hodnotu hodnotu vrácenou příkazem return v posledním bloku .then - viz https://developer.mozilla.org/en-US/docs/Learn/JavaScript/Asynchronous/Promises
        return fetch(apiUri, {
            method: "GET",      //default
              cache: cacheControl,
              headers: {
                "X-Cascade": "fetch driver",   // příznak pro PresentationStatus - neukládej request jako last GET
              },
            })
        .then(response => {
          if (response.ok) {  // ok je true pro status 200-299, jinak je vždy false
              // pokud došlo k přesměrování: status je 200, (mohu jako druhý paremetr fetch dát objekt s hodnotou např. redirect: 'follow' atd.) a také porovnávat response.url s požadovaným apiUri
              return response.text(); //vrací Promise, která resolvuje na text až když je celý response přijat ze serveru
          } else {
              throw new Error(`cascade: HTTP error! Status: ${response.status}`);  // will only reject on network failure or if anything prevented the request from completing.
          }
        })
        .then(textPromise => {
            let element = replaceDriverContent(item, textPromise);  // vrací původní parent element
            listenFormsWithApiAction(item);
            console.log(`cascade: Fetched and replaced driver ${apiUri}`);
            return element;
        })
        .catch(e => {
            throw new Error(`cascade: There has been a problem with fetch from ${apiUri}. Reason:` + e.message);
        });
    }
    
    if (previousItem) {
        let driverApi = itemDriver(previousItem).getAttribute('data-red-driver');
        fetchDriver(previousItem, driverApi, 'default');
    }
    let presentedDriverApi = itemDriver(currentItem).getAttribute('data-red-presenteddriver');
    fetchDriver(currentItem, presentedDriverApi, 'default');

}

function replaceDriverContent(itemElement, newHtmlTextContent) {
    var newElements = htmlToElements(newHtmlTextContent);
    var cnt = newElements.length;
    if (cnt>1) {
        console.warn(`cascade: New driver as children of element "+itemElement.tagName+" with attribute ${conf.elementApiUri}: ${itemElement.getAttribute(conf.elementApiUri)} has ${cnt} element(s).`);        
    } else {
        itemDriver(itemElement).replaceWith(newElements[0]);
    }
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
        if (element.tagName.toLowerCase() === conf.itemElementName) { 
            pathElements.push(element);
        }
    // if no parent, return no parent elements
    if(!element.parentElement) {
        return pathElements;
    }
    // first child of the parent node
    let parent  = element.parentElement;
    // collecting parents
    while (parent) {   // ?? closest(itemElmName)
        if (parent.tagName.toLowerCase() === conf.itemElementName) { 
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
