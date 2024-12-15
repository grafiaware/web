




//=== ui elementy ===

window.onhashchange = scrollToAnchorPosition;

/**
 * Funkce scrolluje stránku na pozici kotvy, pokud je v adrese uveden "fragment", tedy část url za znakem #.
 * Používá jQuery animaci, tu lze nastavit.
 * @returns {undefined}
 */
function scrollToAnchorPosition() {
    if(window.location.hash) {   // window.location.hash je fragment
        var locHash = window.location.hash.substring(1);
        var element_to_scroll_to = document.getElementById(locHash);
        element_to_scroll_to.scrollIntoView();    
//        element_to_scroll_to.scrollIntoView({
//            behavior: 'smooth',
//            block: 'start'
//        });
            
    }
}

//
//
//
//
//=== cascade load of components ===

import {loadSubsequentElements} from "./cascade/cascade.js";
import {initElements} from "./initLoadedElements/initElements.js";

showLoader();        
//            showLoaded();            

/**
 * po onreadystatechange volá funkci loadSubsequentElements() (z cascade.js)
 * loadSubsequentElements() kaskádně načte obsahy z API nahradí jimi elementy v dokumentu. Nahrazuje elementy nalezené podle třídy (class) "cascade"
 *
 * @returns {undefined}
 */
document.onreadystatechange = function () {
//    if (event.target.readyState === 'interactive') { 

if (document.readyState === 'complete') {
        // https://stackoverflow.com/questions/10777684/how-to-use-queryselectorall-only-for-elements-that-have-a-specific-attribute-set
        const init = async () => {
            console.log("body: document ready state is complete, waiting for loadSubsequentElements()");
            let resultComponents = await loadSubsequentElements(document, navConfig.cascadeClass);
            console.debug(resultComponents);
            console.log("body: load elements fullfilled");
            
            listenPopState();
            
            initElements();
            showLoaded();            
            console.log("body: init loaded elements finished");
            scrollToAnchorPosition();
        };
        init(); 
    } else {
        showLoader();        
    }
};

function showLoader() {
         document.getElementById('loaded').style.visibility="hidden";
         document.getElementById('loader').style.visibility="visible";    
}

function showLoaded() {
         document.getElementById('loaded').style.visibility="visible";
         document.getElementById('loader').style.visibility="hidden";        
}

/**
 * Vyvolá reload stránek s url uložených metodou pushState (používá se v cascade.js po načtení nového obsahu)
 * 
 * To znamená, ne při stisku back nečtu (reload) celou stránku a do pushState se musí ukládat url s api pro načtení celé stránky - např web/v1/page/item/664230b979ccd
 * @returns {undefined}
 */
function listenPopState() {
    window.onpopstate = function(event) {    
        if(event && event.state) {
            location.reload(); 
        }
    }
}
