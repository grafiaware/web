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

// proměnná pro uložení event.currentTarget — musí být mimo tělo event handleru
var previousItem = null;

/**
 * Registruje připojení menu navigace po každém načtení cascade obsahu.
 */
export function initMenuSwap() {
    onContentLoaded(attachToLoader);
}

function attachToLoader(loaderElement) {
    if (!hasTargetId(loaderElement)) {
        return;
    }
    listenLinks(loaderElement);
    listenFormsWithApiAction(loaderElement);
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

function listenFormsWithApiAction(loaderElement) {
    let formsWithApiAction = loaderElement.querySelectorAll("." + conf.apiAction);

    formsWithApiAction.forEach((form) => {
        form.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            var formElement = event.currentTarget;
            var but = event.target.closest("button");
            var actionUri = null;
            if (but !== null) {
                actionUri = but.getAttribute("formaction");
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
        });
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
        let navs = document.getElementsByClassName(conf.navigationClass);
        for (const navigation of [...navs]) {
            let navElement = closestCascadeElement(navigation);
            fetchCascadeContent(navElement);
        }
        itemAndContentChange(loaderElement, json);
    } else if (refresh === "document") {
        window.location.reload();
    } else {
        window.location.reload();
    }
}

function listenLinks(loaderElement) {
    if (hasTargetId(loaderElement)) {
        const contentTarget = document.getElementById(getTargetId(loaderElement));
        let navs = loaderElement.getElementsByClassName(conf.navigationClass);
        let navsCnt = navs.length;
        console.log(`menuSwap: Try to listen links in ` + loaderElement.getAttribute(conf.elementApiUri) + ' - ' + navsCnt + ' navs found.');
        for (const navigation of [...navs]) {
            let items = navigation.querySelectorAll(conf.itemElementName);
            console.log(`menuSwap: Listen links match ` + items.length + ' items.');
            for (const item of [...items]) {
                item.addEventListener("click", linkListener.bind(contentTarget));
                if (itemDriver(item).classList.contains("presented")) {
                    previousItem = item;
                }
            }
        }
    } else {
        console.warn(`menuSwap: Loader element s api uri: ${loaderElement.getAttribute(conf.elementApiUri)} nemá atribut ${conf.targetId}.`);
    }
}

function linkListener(event) {
    let currentItem = event.currentTarget;
    if (null === this) {
        console.error(`menuSwap: linkListener není navázán na element contentTarget - contentTarget je null.`);
    }
    let contentTarget = this;
    menuAction(currentItem, contentTarget);
    switchContent(currentItem, contentTarget);
    event.preventDefault();
    event.stopPropagation();
}

function itemAndContentChange(loaderElement, json) {
    if (hasTargetId(loaderElement)) {
        const contentTarget = document.getElementById(getTargetId(loaderElement));
        if (json.targeturi !== undefined) {
            contentTarget.setAttribute(conf.elementApiUri, json.targeturi);
        }
        if (json.newitemuid !== undefined) {
            let currentItem = document.getElementById(conf.itemIdPrefix + json.newitemuid);
            menuAction(currentItem, contentTarget);
            switchContent(currentItem, contentTarget);
        } else {
            menuAction(previousItem, contentTarget);
            switchContent(previousItem, contentTarget);
        }
    } else {
        console.warn("menuSwap: No target defined in loader element");
        window.location.reload();
    }
}

function menuAction(currentItem, contentTarget) {
    if (previousItem !== currentItem) {
        let currentHref = itemDriver(currentItem).getAttribute('href');
        history.pushState({}, "", currentHref);
    }
    switchItem(currentItem);
    currentItem.addEventListener("click", linkListener.bind(contentTarget));
    listenFormsWithApiAction(currentItem);
}

function switchItem(currentItem) {
    getNewDrivers(previousItem, currentItem);
    shrinkAndExpandChildrenOnPath(previousItem, currentItem);
    previousItem = currentItem;
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
        return fetch(apiUri, {
            method: "GET",
            cache: cacheControl,
            headers: {
                "X-Cascade": "fetch driver"
            }
        })
        .then(response => {
            if (response.ok) {
                return response.text();
            } else {
                throw new Error(`menuSwap: HTTP error! Status: ${response.status}`);
            }
        })
        .then(textPromise => {
            let element = replaceDriverContent(item, textPromise);
            listenFormsWithApiAction(item);
            console.log(`menuSwap: Fetched and replaced driver ${apiUri}`);
            return element;
        })
        .catch(e => {
            throw new Error(`menuSwap: There has been a problem with fetch from ${apiUri}. Reason:` + e.message);
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
    var template = document.createElement('template');
    template.innerHTML = newHtmlTextContent;
    var newElements = template.content.childNodes;
    var cnt = newElements.length;
    if (cnt > 1) {
        console.warn(`menuSwap: New driver as children of element "+itemElement.tagName+" with attribute ${conf.elementApiUri}: ${itemElement.getAttribute(conf.elementApiUri)} has ${cnt} element(s).`);
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
