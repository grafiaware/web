// viz local\site\common\templates\layout\cascade\loaderElement.php
import {reinitEditableContent} from "../initLoadedElements/initElements.js";

const conf = {
    cascadeClass: 'cascade',
    elementApiUri: "data-red-apiuri",
    cacheControl: "data-red-cache-control",
};

const contentLoadedHooks = [];

/** true během počátečního loadSubsequentElements() — reinicializace Tiny se provede až v body.js/initElements() */
let cascadeInitialLoadInProgress = false;

/**
 * Registruje callback volaný po každém úspěšném načtení obsahu do cascade elementu.
 * Používá menuSwap.js pro připojení navigace v menu.
 *
 * @param {function(Element): void} callback
 */
export function onContentLoaded(callback) {
    contentLoadedHooks.push(callback);
}

function notifyContentLoaded(element) {
    for (const hook of contentLoadedHooks) {
        hook(element);
    }
}

/**
 * @param {Document} document Document
 * @param {String} className
 * @returns {Promise}
 */
export function loadSubsequentElements(document, className) {
    conf.cascadeClass = className;
    history.replaceState({}, "", document.URL);
    cascadeInitialLoadInProgress = true;
    return fetchCascadeContents(document).finally(() => {
        cascadeInitialLoadInProgress = false;
    });
}

/**
 * Načte pomocí fetchCascadeContent() nové obsahy všech potomků se třídou cascade.
 *
 * @param {Element} element Element nebo Document
 * @returns {Promise}
 */
function fetchCascadeContents(element) {
    if (element.nodeName === '#document') {
        console.log(`cascade: Run loadSubsequentElements() for document.`);
    } else {
        console.log(`cascade: Run loadSubsequentElements() for element ${element.tagName}.`);
    }

    var cascadeElements = element.getElementsByClassName(conf.cascadeClass);
    console.log(`cascade: ${cascadeElements.length} child elements for cascade founded by class="${conf.cascadeClass}".`);
    var cascadeElementsArray = Array.from(cascadeElements);
    let loadSubPromises = cascadeElementsArray.map(elementToCascade => fetchCascadeContent(elementToCascade));

    if (cascadeElements.length) {
        console.log(`cascade: Calling of fetchContents() fetched next ${loadSubPromises.length} element contents.`);
    }

    return Promise.allSettled(loadSubPromises);
}

/**
 * Získá HTML řetězec pomocí HTTP GET requestu na adresu z atributu data-red-apiuri.
 *
 * @param {Element} parentElement
 * @returns {Promise}
 */
export function fetchCascadeContent(parentElement) {
    let apiUri = getApiUri(parentElement);
    let cacheControl = getCacheControl(parentElement);

    return fetch(apiUri, {
        method: "GET",
        cache: cacheControl,
        headers: {
            "X-Cascade": "do not store request",
        },
    }).then(response => {
        if (response.ok) {
            return response.text();
        } else {
            throw new Error(`cascade: HTTP error! Status: ${response.status}`);
        }
    }).then(textPromise => {
        console.log(`cascade: Loading content from ${apiUri}.`);
        return replaceChildren(parentElement, textPromise);
    }).then(parentWithNewContent => {
        notifyContentLoaded(parentWithNewContent);
        return fetchCascadeContents(parentWithNewContent);
    }).then(() => {
        console.log(`cascade: Loaded content from ${apiUri}.`);
        if (!cascadeInitialLoadInProgress) {
            return reinitEditableContent();
        }
    }).catch(e => {
        throw new Error(`cascade: There has been a problem with fetch from ${apiUri}. Reason:` + e.message);
    });
}

export function getApiUri(element) {
    if (element.hasAttribute(conf.elementApiUri)) {
        return element.getAttribute(conf.elementApiUri);
    } else {
        console.error(`Cascade: element nemá povinný atribut conf.apiUri ${element}`);
    }
}

export function setApiUri(element, apiUri) {
    return element.setAttribute(conf.elementApiUri, apiUri);
}

function getCacheControl(element) {
    return 'default';
}

export function closestCascadeElement(element) {
    let closest = element.closest("." + conf.cascadeClass);
    if (closest === null) {
        throw new Error(`cascade: No closest element with cascade class for : ${element}`);
    }
    return closest;
}

function htmlToElements(html) {
    var template = document.createElement('template');
    template.innerHTML = html;
    return template.content.childNodes;
}

function replaceChildren(parentElement, newHtmlTextContent) {
    var newElements = htmlToElements(newHtmlTextContent);
    var cnt = newElements.length;
    parentElement.replaceChildren(...newElements);
    console.log(`cascade: Replaced children of element ${parentElement.tagName} with api uri attribute: ${parentElement.getAttribute(conf.elementApiUri)} with collection of ${cnt}.`);
    return parentElement;
}
