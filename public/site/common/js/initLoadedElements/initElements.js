

export function initElements() {
    initLoadedElements();
    console.log("initElements: init loaded elements");
    if (isTinyMCEDefined()) {
        loadAndInitTiny();    
        console.log("initElements: init loaded editable elements");
    }
    initJqueryEvents();
    console.log("initElements: set jQuery events on loaded elements");
    console.log("initElements: finished");        
}

const loadAndInitTiny = async () => {
        await import("../tinyinit/TinyInit.js")  // lazy import TinyInit
            .then((tinyInitModule) => {
                tinyInitModule.initEditors();
        console.log('initElements: Load tiny init module.')
            })
            .catch((err) => {
                console.error(err.fileName + ":" + err.message);
            });

        initLoadedEditableElements();
};

/**
 * HACK! Závisí na tinymce. Tato proměnná je definována v editačním režimu - pokud bylo načteno TinyMce  (viz konfigurace a Layout kontroler)
 *
 * @returns {undefined}
 */
function isTinyMCEDefined() {
    return typeof tinymce!=='undefined';
}

/**
 * Volá funkce, které mají být volány po událostech na elementech DOM načtených dynamicky.
 * Tato sada funkcí je určena pro elementy používané k editaci obsahu.
 *
 * @returns {undefined}
 */
function initLoadedEditableElements() {
    //semantic-ui dropdown (použitý např. pro přihlašování)
    //$('.ui.dropdown').dropdown();
    ////odhlášení reaguje na najetí (hover) (odhlaseni pres ikonu)
//    $('.logout .button').dropdown({on: 'hover'});
    //menu semantic-ui dropdown reaguje na událost hover
//    $('.svisle-menu .ui.dropdown').dropdown({on: 'hover'});
    //výběr šablony pro stránku - vlastní dropdown, protože jsou dva vnořený
    //$('.ui.selection.dropdown').dropdown();

    $('.calendarWrap .ui.calendar').calendar({
        type: 'date',
        today: true,
        firstDayOfWeek: 1,
        text: {
            days: ['Ne', 'Po', 'Út', 'St', 'Čt', 'Pá', 'So'],
            months: ['Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec'],
            today: 'Dnes'
        },
        formatter: {
            date: function (date, settings) {
                if (!date) return '';
                var day = date.getDate();
                var month = date.getMonth() + 1;
                var year = date.getFullYear();
                return day + '. ' + month + '. ' + year;}
        }
    });
    
    ////////////////////////////////
     //výběr obrázku pro representanta
    const fileInput = document.getElementById('hidden-file-input');
    const selectedFileName = document.getElementById('selected-file-name');
    const changeFileBtn = document.getElementById('change-file-btn');
    const removeFileBtn = document.getElementById('remove-file-btn');
    if (fileInput) {
    // Kliknutí na tlačítko "Vybrat obrázek"
    document.getElementById('upload-image-btn').addEventListener('click', () => {
        fileInput.click();
    });

    // Změna souboru
    fileInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (!file) return;

        // Kontrola velikosti souboru
        const maxFileSize = 2 * 1024 * 1024; // 2 MB
        if (file.size > maxFileSize) {
            alert('Velikost souboru přesahuje 2 MB. Vyberte menší soubor.');
            fileInput.value = ''; // Reset inputu
            return;
        }

        // Zobrazení názvu souboru a aktivace tlačítek
        selectedFileName.textContent = file.name;
        changeFileBtn.style.display = 'inline-block'; //změnit obrázek
        removeFileBtn.style.display = 'inline-block'; //odstranit obrázek
    });

    // Kliknutí na tlačítko "Změnit obrázek"
    changeFileBtn.addEventListener('click', () => {
        fileInput.click();
    });

    // Kliknutí na tlačítko "Odstranit obrázek"
    removeFileBtn.addEventListener('click', () => {
        fileInput.value = ''; // Vymazání hodnoty inputu
        selectedFileName.textContent = 'Žádný soubor nebyl vybrán'; // Reset textu
        changeFileBtn.style.display = 'none'; // Skrytí tlačítek
        removeFileBtn.style.display = 'none';
    });
    }
     ////////////////////////////////
}

/**
 * Volá funkce, které mají být volány po událostech na elementech DOM načtených dynamicky.
 *
 * @returns {undefined}
 */
function initLoadedElements() {
//=== ui elementy ===
    

    //prihlaseni (otevreni/zavreni pres ikonu)
    $('.btn-login').click(function(){
        $(this).siblings('.menu-login').toggle();
    });
    //modalni okno pro prihlaseni
    $('.btn-login').click(function(){
        $('.page.modal.login').modal({
            useCSS   : true,
        })
        .modal('show');
    });
    //modalni okno pro registraci
    $('.btn-register').click(function(){
        $('.page.modal.register').modal({
            useCSS   : true,
        })
        .modal('show');
    });
    //modalni okno pro vyberFirmy
    $('.btn-vyberFirmy').click(function(){
        $('.page.modal.vyberFirmy').modal({
            useCSS   : true,
        })
        .modal('show');
    });
    $('.page.modal.vyberFirmyVynuceny').modal({   // komponent representationAction pro representanta více firem
        closable: false,
        useCSS   : true,
    })
    .modal('show');    
    $('.ui.hide.button').click(function(){
       $('.page.modal').modal('hide');
    });
    
    //flyout pro zasady ochrany osobnich udaju


    //flash message
    $('.flashtoast')
        .toast({
            displayTime: 5000
        })
    ;

    $('.btn-poznamky').on("click",
        function(){
            $(this).siblings('.poznamky').toggle("slow");
    });



    //veletrh online
    //checkbox v registraci (zastupuji vystavovatele)
    $('.exhibitor.checkbox')
        .checkbox()
        .first().checkbox({
            onChecked: function() {
                $('.input-company').addClass('show'); //objeví se input pro vyplnění názvu společnosti
                $('.input-company').attr("required", true); //pole s názvem musí být vyplněno
            },
            onUnchecked: function() {
                $('.input-company').removeClass('show');
                $('.input-company').attr("required", false);
                $('.register-info-representative').hide();
                $('.register .positive.button').removeClass('disabled');
            }
        });
        
        
    $('.register-info-button').on('click', function(){
        $('.register-info-text').toggle(
                function(){$('.register-info-button').toggleClass('basic', 2000).toggleClass('priority', 2000);}
        );
    }); 
    $('.register-info-text .close.icon').on('click', function(){
        $('.register-info-text').hide(
                function(){$('.register-info-button').toggleClass('basic').toggleClass('priority');}
        );
    }); 
    $('.register input[name=info]').keyup(function(){
        $('.register-info-representative').show();
        $('.register .positive.button').addClass('disabled');
        if($(this).val().length === 0) {
            $('.register-info-representative').hide();
            $('.register .positive.button').removeClass('disabled');
        }
        
    });
    $('.register-info-representative .button').on('click', function(){
        $('.register-info-representative').hide();
        $('.register .positive.button').removeClass('disabled');  
    });
    
    

    //odebrání atributu required u hesla, pokud uživatel klikne na "zapomněl jsem heslo"
    $('.password-btn').on('click', function(){
        $('input[type="password"]').attr("required", false); 
    });
    
    
//            zobrazení  modálního okna po kliknutí button
//            $('#id_modal_element').modal('attach events', '.class_button', 'show');

    $('#modal_facebook').modal('attach events', '.btn-fb', 'show');
    $('#modal_instagram').modal('attach events', '.btn-ig', 'show');
    $('#modal_youtube').modal('attach events', '.btn-yt', 'show');
    $('#modal_linkedin').modal('attach events', '.btn-in', 'show');
    $('#modal_twitter').modal('attach events', '.btn-tw', 'show');
    $('#modal_letaky').modal('attach events', '.btn-letaky', 'show');
    $('#modal_chat').modal('attach events', '.btn-chat', 'show');

    //semantic-ui popup "bublina" (použitý např. ikony v online-stáncích)
    $('.popup.icon').popup();

    //semantic-ui accordion (použitý např. pro výpis informací v profilu přihlášeného uživatele)
    $('.styled.accordion').accordion();
    
    //a tab is a hidden section of content activated by a menu
    $('.tabular.menu .item').tab();

    //rozbalení formuláře osobních údajů pro "chci nazávat kontakt"
    $('.profil-visible').on('click', function(){
        $(this).closest('.navazat-kontakt').find('.profil.hidden').toggle();
    });
    

    // Detect request animation frame
    var scroll = window.requestAnimationFrame ||
    // IE Fallback
    function(callback){ window.setTimeout(callback, 1000/60)};
    var elementsToShow = document.querySelectorAll('.show-on-scroll');

    function loop() {
        elementsToShow.forEach(function (element) {
            if (isElementInViewport(element)) {
                element.classList.add('is-visible');
            } else {
                element.classList.remove('is-visible');
            }
        });
        scroll(loop);
    }


    // Helper function from: http://stackoverflow.com/a/7557433/274826
    function isElementInViewport(el) {
        // special bonus for those using jQuery
        if (typeof jQuery === "function" && el instanceof jQuery) {
            el = el[0];
        }
        var rect = el.getBoundingClientRect();
        return (
            (rect.top <= 0
            && rect.bottom >= 0)
            ||
            (rect.bottom >= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.top <= (window.innerHeight || document.documentElement.clientHeight))
            ||
            (rect.top >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight))
        );
    }

    // Call the loop for the first time
//    loop();
    

    ////////////////////////////////
    var ram = document.getElementById("googleform");
    if (ram !== null) {
        ram.scrolling =  "no";
        var loadCounter = 0;
        ram.onload = function() {
            loadCounter += 1;

            if (loadCounter === 2) {
                var ramDokument = ram.contentDocument || ra+m.contentWindow.document;
                ram.style.height = ramDokument.documentElement.scrollHeight + "px";
            }
        };
    }
    
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//contents in circle
//
//

//    var jsTemplateCircleEdit = document.querySelectorAll('.qqqwokk');
    var jsTemplateCircleEdit = document.querySelectorAll('[data-template="contents_in_circle"]');
    if (jsTemplateCircleEdit.length !== 0) {
        var Position = {
            ellipse : function(n, rx, ry, so, wh, clsP, cls, cw) {
                var m = document.getElementsByClassName('contents_in_circle'),
                 ss = document.styleSheets;
                ss[0].insertRule(clsP + ' { position: relative; left: 50%; transform: translateX(-50%); border-radius: 50%; box-shadow: inset 0 0 ' + wh + 'px ' + wh/4 +'px #abcfcf; background: transparent; width: ' + String((rx * 2) + wh) + 'px; height: ' + String((ry * 2) + wh) + 'px; }', 1);
                ss[0].insertRule(cls + '{ background: #e2fbff; color: black; text-align: center; font-family: "Open Sans Condensed", sans-serif; border-radius: 100%; transition: transform 0.2s ease; width: ' + wh + 'px; height: ' + wh + 'px; line-height: ' + wh + 'px;}', 1);
                ss[0].insertRule(cls + ':hover { transform: scale(1.1); cursor: pointer; background: white; }', 1);
                m.className = clsP;
                for (var i = 0; i < n; i++) {
                  var c = document.getElementById('circle').getElementsByTagName('section')[i];
                  c.style.top = String(ry + -ry * Math.cos((360 / n / 180) * (i + so) * Math.PI)) + 'px';
                  c.style.left = String(rx + rx * (cw ? Math.sin((360 / n / 180) * (i + so) * Math.PI) : -Math.sin((360 / n / 180) * (i + so) * Math.PI))) + 'px';
                  c.style.width = 'max-content';
                  c.style.position = 'absolute';
                }
            }
        };
        var lenghtD = document.getElementById('circle').getElementsByTagName('section').length;
        Position.ellipse(lenghtD, 180, 180, 0, 100, '.contents_in_circle', '#circle content', true);
        /*
        Usage: Position.ellipse(n, rx, ry, so, wh, idd, cls, cw);
        where n = number of divs,
              rx = radius along X-axis,
              ry = radius along Y-axis,
              so = startOffset,
              wh = width/height of divs,
              idd = id of main div(ellipse),
              cls = className of divs;
              cw = clockwise(true/false)
        */
    }
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//carousel
//
//
    var jsTemplateCarousel = document.querySelectorAll('[data-template="carousel"]');
    if (jsTemplateCarousel.length !== 0) {
        (function($){
            var carouselObal = $(".carousel-wrap");

            carouselObal.children(":not(:last)").hide();

            var sliderInterval = setInterval(function() {
                carouselObal.children(":last").fadeOut(2000, function() {
                        $(this).prependTo(carouselObal);
                }).prev().fadeIn(2000);
            }, 4000);

            carouselObal.on("click", function() {
                clearInterval(sliderInterval);
            });


            $(".angle.left").click(function(){
                clearInterval(sliderInterval);
                carouselObal.children(":first").fadeOut(0, function() {
                        $(this).appendTo(carouselObal);
                }).prev().fadeIn(0);
            });
            $(".angle.right").click(function(){
                clearInterval(sliderInterval);
                carouselObal.children(":last").fadeOut(0, function() {
                        $(this).prependTo(carouselObal);
                }).prev().fadeIn(0);
            });
        })(jQuery);
    }
}

/**
 * Funkce scrolluje stránku na pozici kotvy, pokud je v adrese uveden "fragment", tedy část url za znakem #, tedy v javascriptu window.location.hash
 * Používá jQuery animaci, tu lze nastavit.
 * @returns {undefined}
 */
export function scrollToAnchorPosition() {
    if(window.location.hash) {
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

function initJqueryEvents() {
    
    //semantic-ui dropdown (použitý např. pro přihlašování)
    $('.ui.dropdown')
      .dropdown()
    ;
    //odhlášení reaguje na najetí (hover) (odhlaseni pres ikonu)
//    $('.logout .button .menu').dropdown({
//        on: 'click'
//    });
    //prihlaseni (otevreni/zavreni pres ikonu)
    $('.btn-login').click(function(){
        $(this).siblings('.menu-login').toggle();
    });
    //modalni okno pro zmenu hesla
    $('.btn-zmena-hesla').click(function(){
        $('.page.modal.zmena-hesla').modal({
            useCSS   : true
        })
        .modal('show');
    });
    //modalni okno pro prihlaseni
    $('.btn-login').click(function(){
        $('.page.modal.login').modal({
            closable: false,
            useCSS   : true,
        })
        .modal('show');
    });
    //modalni okno pro registraci
    $('.btn-register').click(function(){
        $('.page.modal.register').modal({
            closable: false,
            useCSS   : true,
        })
        .modal('show');
    });
    //modalni okno pro ochranu osobních údajů
    $('.btn-osudaje').click(function(){
        $('.page.modal.osudaje').modal({
            useCSS   : true
        })
        .modal('show');
    });
    $('.ui.hide.button').click(function(){
       $('.page.modal').modal('hide');
    });
    
    //ověření youtube url adresy
    // Najdeme input podle ID
        const youtubeInput = document.getElementById("youtubeUrl");

        // Ověříme, zda input existuje
        if (youtubeInput) {
            const message = document.getElementById("message");

            // Přidáme event listener na vstup do pole
            youtubeInput.addEventListener("input", function () {
                const urlInput = youtubeInput.value;

                // Regulární výraz pro ověření YouTube URL
//                const youtubeRegex = /^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[a-zA-Z0-9_-]{11}$/;
                // https://stackoverflow.com/questions/19377262/regex-for-youtube-url
                const youtubeRegex = /^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube(?:-nocookie)?\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|live\/|v\/)?)([\w\-]+)(\S+)?$/;
                if (youtubeRegex.test(urlInput)) {
                    message.textContent = "Platná YouTube URL adresa!";
                    message.style.color = "green";
                } else {
                    message.textContent = "Neplatná YouTube URL adresa. Zkontrolujte prosím vstup.";
                    message.style.color = "red";
                }
            });
        }


    //flash message
//    $('.flashtoast')
//        .toast({
//            displayTime: 5000
//        })
//    ;

    $('.btn-poznamky').on("click",
        function(){
            $(this).siblings('.poznamky').toggle("slow");
    });

    //menu semantic-ui dropdown reaguje na událost hover
//    $('.svisle-menu .ui.dropdown').dropdown({
//       on: 'hover'
//    });


    //odeslani prihlasovaciho formulare pri stisku klavesy Enter
//    $('.loginEnterKey').keyup(function(event){
//        if(event.keyCode === 13){
//            $('.positive.button').click();
//        }
//    });

    //veletrh online
    //checkbox v registraci (zastupuji vystavovatele)
    $('.ui.checkbox').checkbox();

    $('.exhibitor.checkbox')
        .checkbox()
        .first().checkbox({
            onChecked: function() {
                $('.input-company').addClass('show'); //objeví se input pro vyplnění názvu společnosti
                $('.input-company').attr("required", true); //pole s názvem musí být vyplněno
            },
            onUnchecked: function() {
                $('.input-company').removeClass('show');
                $('.input-company').attr("required", false);
              ;
            }
        });

    //odebrání atributu required u hesla, pokud uživatel klikne na "zapomněl jsem heslo"
    $('.tertiary.button').on('click', function(){
        $('.notRequired').attr("required", false);
    }); 
}
