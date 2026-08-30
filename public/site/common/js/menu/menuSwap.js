import {
    fetchCascadeContent,
    setApiUri,
    closestCascadeElement,
    onContentLoaded,
} from "../cascade/cascade.js";

const conf = {
    elementApiUri: "data-red-apiuri",
    contentApiUri: "data-red-content",
    targetId: "data-nav-target-id",
    apiAction: "apiAction",
    navigationClass: "navigation",
    itemElementName: 'li',
    itemLeafClass: "leaf",
    itemParentClass: "parent",
    itemIdPrefix: "item_"
};

function cascadeHeaderName() {
    return (typeof navConfig !== 'undefined' && navConfig.cascadeHeader)
        ? navConfig.cascadeHeader
        : 'X-Cascade';
}

// proměnná pro uložení event.currentTarget — musí být mimo tělo event handleru
var previousItem = null;

/** Pořadí navigace: pozdější klik / refresh zahodí nedokončené přepnutí. */
let navSeq = 0;

const driverAbort = new WeakMap();

let menuSwapInitialized = false;

/**
 * Registruje delegované click handlery (jednou) a po každém cascade loadu si zapamatuje presented položku.
 */
export function initMenuSwap() {
    if (menuSwapInitialized) {
        return;
    }
    menuSwapInitialized = true;
    document.addEventListener('click', onDocumentClick);
    onContentLoaded(onCascadeContentLoaded);
}

function onCascadeContentLoaded(loaderElement) {
    if (!hasTargetId(loaderElement)) {
        return;
    }
    const navs = loaderElement.getElementsByClassName(conf.navigationClass);
    console.log(`menuSwap: Menu loader ` + loaderElement.getAttribute(conf.elementApiUri) + ' - ' + navs.length + ' navs found.');
    rememberPresentedItem(loaderElement);
}

function rememberPresentedItem(loaderElement) {
    const presentedDriver = loaderElement.querySelector('.presented');
    if (presentedDriver) {
        const item = presentedDriver.closest(conf.itemElementName);
        if (item) {
            previousItem = item;
        }
    }
}

function hasTargetId(element) {
    return element.hasAttribute(conf.targetId);
}

function getTargetId(element) {
    if (hasTargetId(element)) {
        return element.getAttribute(conf.targetId);
    } else {
        console.error(`menuSwap: element nemá povinný atribut ${conf.targetId} ${element}`);
    }
}

function onDocumentClick(event) {
    const form = event.target.closest('form.' + conf.apiAction);
    if (form) {
        const button = event.target.closest('button');
        if (button && (button.disabled || button.classList.contains('disabled'))) {
            event.preventDefault();
            event.stopPropagation();
            return;
        }
        event.preventDefault();
        event.stopPropagation();
        submitApiActionForm(form, event.target.closest('button'));
        return;
    }

    const item = event.target.closest(conf.itemElementName);
    if (!item) {
        return;
    }
    const navigation = item.closest('.' + conf.navigationClass);
    if (!navigation) {
        return;
    }
    let loaderElement;
    try {
        loaderElement = closestCascadeElement(navigation);
    } catch (e) {
        return;
    }
    if (!hasTargetId(loaderElement) || !loaderElement.contains(item)) {
        return;
    }
    const contentTarget = document.getElementById(getTargetId(loaderElement));
    if (null === contentTarget) {
        console.error(`menuSwap: contentTarget je null.`);
        return;
    }
    event.preventDefault();
    event.stopPropagation();
    if (item === previousItem) {
        return;
    }
    runNavigation(item, contentTarget);
}

function submitApiActionForm(formElement, button) {
    var actionUri = null;
    if (button !== null) {
        actionUri = button.getAttribute("formaction");
    }
    if (actionUri === null) {
        actionUri = formElement.getAttribute("action");
    }
    fetch(actionUri, {
        method: 'PUT',
        body: new URLSearchParams(new FormData(formElement))
    }).then((response) => {
        if (response.ok) {
            return response.json().then(json => {
                console.log(`menuSwap: Content to ${actionUri} sent.`);
                fetchFreshContent(formElement, json);
            }).catch(err => {
                console.log(`menuSwap: Response status was ok but the body was empty or not JSON. ${err}`);
            });
        } else {
            return response.json().catch(err => {
                console.log(`menuSwap: Response status was not ok and the body was not JSON. ${err}`);
                throw new Error(`HTTP error! Status: ${response.status}`);
            }).then(parsedValue => {
                throw new Error(parsedValue.error);
            });
        }
    }).catch(e => {
        throw new Error(`menuSwap: There has been a problem with fetch with PUT ${actionUri}. Reason:` + e.message);
    });
}

function fetchFreshContent(formElement, json) {
    let refresh = json.refresh;

    if (refresh === "norefresh") {
        return;
    } else if (refresh === "closest") {
        let loaderElement = closestCascadeElement(formElement);
        fetchCascadeContent(loaderElement);
    } else if (refresh === "item") {
        let loaderElement = closestCascadeElement(formElement);
        itemAndContentChange(loaderElement, json);
    } else if (refresh === "navigation") {
        let loaderElement = closestCascadeElement(formElement);
        // Nejdřív počkat na nová menu (nové/přesunuté položky musí být v DOM), teprve pak presented + content
        refreshNavigationMenus().then(() => {
            itemAndContentChange(loaderElement, json);
        }).catch(e => {
            console.error(`menuSwap: navigation refresh failed. ${e}`);
            window.location.reload();
        });
    } else if (refresh === "document") {
        window.location.reload();
    } else {
        window.location.reload();
    }
}

/**
 * Reload všech cascade loaderů, které obsahují .navigation (svislé menu, koš, bloky, …).
 * @returns {Promise}
 */
function refreshNavigationMenus() {
    const navs = document.getElementsByClassName(conf.navigationClass);
    const menuLoaders = new Set();
    for (const navigation of [...navs]) {
        try {
            menuLoaders.add(closestCascadeElement(navigation));
        } catch (e) {
            console.warn(`menuSwap: navigation without cascade loader: ${e.message}`);
        }
    }
    return Promise.all([...menuLoaders].map(navElement => fetchCascadeContent(navElement)));
}

function itemAndContentChange(loaderElement, json) {
    if (hasTargetId(loaderElement)) {
        const contentTarget = document.getElementById(getTargetId(loaderElement));
        if (!contentTarget) {
            console.error(`menuSwap: contentTarget #${getTargetId(loaderElement)} not found.`);
            return;
        }
        if (json.targeturi !== undefined) {
            contentTarget.setAttribute(conf.elementApiUri, json.targeturi);
        }
        if (json.newitemuid !== undefined) {
            let currentItem = document.getElementById(conf.itemIdPrefix + json.newitemuid);
            if (!currentItem) {
                console.warn(`menuSwap: item_${json.newitemuid} not found after refresh; loading content only.`);
                if (json.targeturi !== undefined) {
                    fetchCascadeContent(contentTarget);
                }
                return;
            }
            runNavigation(currentItem, contentTarget);
        } else {
            runNavigation(previousItem, contentTarget);
        }
    } else {
        console.warn("menuSwap: No target defined in loader element");
        window.location.reload();
    }
}

function runNavigation(currentItem, contentTarget) {
    const seq = ++navSeq;
    // presenteddriver musí doběhnout dřív než content — nastavuje PresentationStatus.menuItem do session
    menuAction(currentItem).then(() => {
        if (seq !== navSeq) {
            return;
        }
        switchContent(currentItem, contentTarget);
    });
}

function menuAction(currentItem) {
    if (previousItem !== currentItem) {
        let currentHref = itemDriver(currentItem).getAttribute('href');
        history.pushState({}, "", currentHref);
    }
    return switchItem(currentItem);
}

function switchItem(currentItem) {
    const driversReady = getNewDrivers(previousItem, currentItem);
    shrinkAndExpandChildrenOnPath(previousItem, currentItem);
    previousItem = currentItem;
    return driversReady;
}

function switchContent(currentItem, contentTarget) {
    let newContentApiUri = itemDriver(currentItem).getAttribute(conf.contentApiUri);
    setApiUri(contentTarget, newContentApiUri);
    fetchCascadeContent(contentTarget);
}

function itemDriver(itemElement) {
    return itemElement.children[0];
}

function getNewDrivers(previousItem, currentItem) {
    function fetchDriver(item, apiUri, cacheControl) {
        const prevController = driverAbort.get(item);
        if (prevController) {
            prevController.abort();
        }
        const controller = new AbortController();
        driverAbort.set(item, controller);
        const seq = navSeq;

        return fetch(apiUri, {
            method: "GET",
            cache: cacheControl,
            signal: controller.signal,
            headers: {
                [cascadeHeaderName()]: "fetch driver"
            }
        })
        .then(response => {
            if (seq !== navSeq) {
                return null;
            }
            if (response.ok) {
                return response.text();
            } else {
                throw new Error(`menuSwap: HTTP error! Status: ${response.status}`);
            }
        })
        .then(html => {
            if (html === null || seq !== navSeq) {
                return item;
            }
            let element = replaceDriverContent(item, html);
            console.log(`menuSwap: Fetched and replaced driver ${apiUri}`);
            return element;
        })
        .catch(e => {
            if (e.name === 'AbortError') {
                return item;
            }
            throw new Error(`menuSwap: There has been a problem with fetch from ${apiUri}. Reason:` + e.message);
        });
    }

    const promises = [];
    // stejná položka: nenačítej ne-presented driver, jinak položka blikne presented → a → presented
    if (previousItem && previousItem !== currentItem) {
        let driverApi = itemDriver(previousItem).getAttribute('data-red-driver');
        promises.push(fetchDriver(previousItem, driverApi, 'default'));
    }
    let presentedDriverApi = itemDriver(currentItem).getAttribute('data-red-presenteddriver');
    // Critical path: PresentationStatus.menuItem (title pro static šablony) se nastaví tady
    promises.push(fetchDriver(currentItem, presentedDriverApi, 'default'));
    return Promise.all(promises);
}

function replaceDriverContent(itemElement, newHtmlTextContent) {
    var template = document.createElement('template');
    template.innerHTML = newHtmlTextContent;
    var newElements = template.content.childNodes;
    var cnt = newElements.length;
    if (cnt > 1) {
        console.warn(`menuSwap: New driver as children of element ${itemElement.tagName} with attribute ${conf.elementApiUri}: ${itemElement.getAttribute(conf.elementApiUri)} has ${cnt} element(s).`);
    } else {
        itemDriver(itemElement).replaceWith(newElements[0]);
    }
    return itemElement;
}

function getOnPathItemElements(element) {
    let pathElements = [];
    if (element.tagName.toLowerCase() === conf.itemElementName) {
        pathElements.push(element);
    }
    if (!element.parentElement) {
        return pathElements;
    }
    let parent = element.parentElement;
    while (parent) {
        if (parent.tagName.toLowerCase() === conf.itemElementName) {
            pathElements.push(parent);
        }
        parent = parent.parentElement;
    }
    return pathElements;
}

function shrinkAndExpandChildrenOnPath(previousItem, currentItem) {
    if (previousItem) {
        let parentElements = getOnPathItemElements(previousItem);
        parentElements.forEach(element => { element.classList.remove(conf.itemParentClass); });
    }
    let parentElements = getOnPathItemElements(currentItem);
    parentElements.forEach(element => {
        if (!element.classList.contains(conf.itemLeafClass)) {
            element.classList.add(conf.itemParentClass);
        }
    });
}
