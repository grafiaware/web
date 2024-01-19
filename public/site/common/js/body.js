
/* global navConfig */

//
//
//
//
//=== cascade load of components ===

import {loadSubsequentElements} from './cascade.js';
import {initLoadedElements, initLoadedEditableElements, scrollToAnchorPosition} from './initLoadedElements/initElements.js';

/**
 * po onreadystatechange volá funkci loadSubsequentElements() (z cascade.js)
 * loadSubsequentElements() kaskádně načte obsahy z API nahradí jimi elementy v dokumentu. Nahrazuje elementy nalezené podle třídy (class) "cascade"
 *
 * @returns {undefined}
 */
document.onreadystatechange = function () {
//    if (event.target.readyState === 'interactive') {   // Alternative to DOMContentLoaded event
    if (document.readyState === 'complete') {  // Alternative to load event
        // https://stackoverflow.com/questions/10777684/how-to-use-queryselectorall-only-for-elements-that-have-a-specific-attribute-set
        const init = async () => {
            console.log("body: document ready state is complete, waiting for loadSubsequentElements()");
            let resultComponents = await loadSubsequentElements(document, navConfig.cascade.class);
            console.log(resultComponents);
            console.log("body: load elements fullfilled");

            if (isTinyMCEDefined()) {
                await import("./TinyInit.js")
                    .then((tinyInitModule) => {
                        tinyInitModule.initEditors();
                    })
                    .catch((err) => {
                        console.error = err.message;
                    });
                
                initLoadedEditableElements();
                console.log("body: initLoaded elements for editable mode");
            }
            initLoadedElements();
            console.log("body: initLoaded elements");
            scrollToAnchorPosition();
            console.log("body: scrolling onto anchor position")
        }
        init(); // async - volá initLoaded()
    }
}

//=== init loaded TinyMce editors ===

/**
 * HACK! Závisí na tinymce. Tato proměnná je definována v editačním reřimu - pokud bylo načteno TinyMce  (viz konfigurace a Layout kontroler)
 *
 * @returns {undefined}
 */
function isTinyMCEDefined() {
    return typeof tinymce!=='undefined';
}

