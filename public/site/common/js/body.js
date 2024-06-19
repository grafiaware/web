//=== ui elementy ===

/**
 * Funkce scrolluje stránku na pozici kotvy, pokud je v adrese uveden "fragment", tedy část url za znakem #.
 * Používá jQuery animaci, tu lze nastavit.
 * @returns {undefined}
 */
function scrollToAnchorPosition() {
    if(window.location.hash) {   // window.location.hash je fragment
        $('html, body').animate({
            scrollTop: $(window.location.hash).offset().top, // - 20
            opacity: 'o.4'
        }, 150);
        $('html, body').animate({
            scrollTop: $(window.location.hash).offset().top, // - 20
            opacity: '1'
        }, 1000);        
    }
    // Příklad w3schools
//      // Add smooth scrolling to all links
//  $("a").on('click', function(event) {
//
//    // Make sure this.hash has a value before overriding default behavior
//    if (this.hash !== "") {
//      // Prevent default anchor click behavior
//      event.preventDefault();
//
//      // Store hash
//      var hash = this.hash;
//
//      // Using jQuery's animate() method to add smooth page scroll
//      // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
//      $('html, body').animate({
//        scrollTop: $(hash).offset().top
//      }, 800, function(){
//   
//        // Add hash (#) to URL when done scrolling (default click behavior)
//        window.location.hash = hash;
//      });
//    } // End if
//  });
    
}

//
//
//
//
//=== cascade load of components ===

import {loadSubsequentElements} from "./cascade/cascade.js";
import {initElements} from "./initLoadedElements/initElements.js";

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
            let resultComponents = await loadSubsequentElements(document, navConfig.cascadeClass);
            console.debug(resultComponents);
            console.log("body: load elements fullfilled");
            
            listenPopState();
            
            initElements();
            console.log("body: init loaded elements finished");
            console.log("body: Scroll onto anchor position.");
            scrollToAnchorPosition();

//            listenLinks();
        };
        init(); 
    }
};

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
