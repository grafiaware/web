




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
            
            
            
            
            
            
//-----------------------------------------------------------------vs                         
            var element_Header = document.getElementById("header");
            element_Header.style.border =   '10px solid blue'; 
            
//           var element_headline = document.getElementsByTagName("headline");//??? nic nedela asi kvuli cascade,   neni 'zakladni' tag
//           element_headline.style.backgroundColor = "olive";
            
            var class_pageContent = "page-content";  
            var element_class_pageContent = document.getElementsByClassName(class_pageContent);
           // element_class_pageContent.style.border =   '10px solid blue' //dela chybu
          
          //----------------------------------
            const divikContent= document.createElement('div');
            divikContent.style.border = '10px solid yellow';           
            divikContent.style.backgroundColor = "olive";
            divikContent.textContent = "jsem DIVikContent";
            //element_class_pageContent.appendChild(divikContent); //dela asi chybu
           
            const divik = document.createElement('div');
            divik.style.border = '10px solid yellow'; 
            divik.style.width  = '100px'; 
            divik.style.height  = '100px'; 
            divik.textContent = "ČAU, jsem DIVik";
            
            
            var element_Overlay = document.getElementById("myOverlay");
            element_Overlay.style.border =   '10px solid blue'; 
            element_Overlay.appendChild(divikContent);            
            
            var id_Sidenav = "mySidenav";                     
            var element_Sidenav = document.getElementById(id_Sidenav);
            element_Sidenav.appendChild(divik);
                        
            element_Overlay.appendChild(divik); //pak neni v Sidenav
                      
            
            // 1. Vytvoření <img> elementu
            const obrazek = document.createElement('img');           
            const obrazek_1 = document.createElement('img');            

            // 2. Nastavení atributu src (cesty k obrázku)
            obrazek.src = './public/site/common/img/L+H50.gif';
            obrazek_1.src = './public/site/common/img/L+H50.gif';

            //// 3. Volitelné nastavení dalších atributů
            obrazek.alt = 'Popis obrázku';
            obrazek.width = 100; // šířka v pixelech        
            obrazek_1.alt = 'Popis obrázku';
            obrazek_1.width = 100; // šířka v pixelech      
            

            // 4. Připojení obrázku do dokumentu (např. do <body>)
            element_Sidenav.appendChild(obrazek);
            element_Overlay.appendChild(obrazek_1);
          
            //----------------------------------------- 
            //element_class_templatePaper.appendChild(divik);   //????
            //element_headline.appendChild(divik);   
//-----------------------------------------------------------------vs-konec






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
