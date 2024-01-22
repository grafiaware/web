
/* global navConfig */

//=== cascade load of components ===

import {loadSubsequentElements} from './cascade.js';
import {initLoadedElements, initLoadedEditableElements, scrollToAnchorPosition} from './initLoadedElements/initElements.js';

/**
 * po onreadystatechange volá funkci loadSubsequentElements() (z cascade.js)
 * loadSubsequentElements() kaskádně načte obsahy z API nahradí jimi elementy v dokumentu. Nahrazuje elementy nalezené podle třídy (class) definované 
 * v konfiguraci a předané jako hodnota navConfig.cascade.class ve skriptu navConfig
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
            var linkElements = document.getElementsByClassName('link');
            for (var i = 0, len = linkElements.length; i < len; i++) {
                linkElements[i].addEventListener('click', (event) => link(event), true);
            }
        }

function link(event) {
    let apiuri = navConfig.basePath + event.currentTarget.attributes['data-red-apiuri'].value;
        console.log("body: link to:"+apiuri)

    // go to the another page
//    window.location.replace(apiuri);
    window.location.href = apiuri;
    
//let resultComponents = await loadSubsequentElements(document, 'page-content');
//            console.log(resultComponents);
//            console.log("body: load page content fullfilled");    
}
        
        init(); // async - volá initLoadedElements()
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

