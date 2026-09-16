/**
 * Inline editace titulku položky menu (contenteditable).
 * Závislost na DriverRendererEditable: <p> uvnitř <div> s atributy data-red-item-title-uri a data-original-title.
 */

function restoreItemState(targetElement) {
    targetElement.innerHTML = targetElement.getAttribute('data-original-title');
    targetElement.blur();
}

function acceptedElement(targetElement) {
    return targetElement.nodeName === 'P' && targetElement.parentNode.nodeName === 'DIV';
}

function sendOnEnter(event) {
    var escPressed = event.which === 27,
        nlPressed = event.which === 13,
        targetElement = event.target,
        url;

    if (acceptedElement(targetElement)) {
        if (escPressed) {
            restoreItemState(targetElement);
        } else if (nlPressed) {
            url = targetElement.getAttribute('data-red-item-title-uri');

            if (!targetElement.innerText || !targetElement.innerText.trim()) {
                restoreItemState(targetElement);
                alert(`Nelze odeslat titulek, titulek je prázdný nebo obsahuje jen mezery nebo neviditelné znaky.`);
            } else {
                const formData = new FormData();
                formData.append("title", targetElement.textContent);
                formData.append("original-title", targetElement.getAttribute('data-original-title'));

                fetch(url, {
                    method: "POST",
                    cache: "no-cache",
                    credentials: "same-origin",
                    body: formData
                })
                .then(response => {
                    if (response.ok) {
                        return response.text();
                    } else {
                        alert(`Selhalo: ${response.status}`);
                        throw new Error(`title: HTTP error in sendOnEnter! Status: ${response.status}`);
                    }
                })
                .then(textPromise => {
                    console.log(`title: Set title by ${url}.`);
                    alert("Provedeno: " + JSON.parse(textPromise).message);
                })
                .catch(e => {
                    throw new Error(`title: There has been a problem with fetch post to ${url}. Reason:` + e.message);
                });

                targetElement.blur();
                event.preventDefault();
                event.stopPropagation();
            }
        }
    }
}

export function initTitleEditing() {
    document.addEventListener("keydown", sendOnEnter, true);
}

initTitleEditing();
