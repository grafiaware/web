    //semantic-ui dropdown (použitý např. pro přihlašování)
    $('.ui.dropdown')
      .dropdown()
    ;

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

    //menu semantic-ui dropdown reaguje na událost hover
    $('.svisle-menu .ui.dropdown').dropdown({
       on: 'hover'
    });


    //odeslani prihlasovaciho formulare pri stisku klavesy Enter
    $('.loginEnterKey').keyup(function(event){
        if(event.keyCode === 13){
            $('.positive.button').click();
        }
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
              ;
            }
        });

    //odebrání atributu required u hesla, pokud uživatel klikne na "zapomněl jsem heslo"
    $('.tertiary.button').on('click', function(){
        $('.notRequired').attr("required", false);
    });


document.onreadystatechange = function () {
//    if (event.target.readyState === 'interactive') {   // Alternative to DOMContentLoaded event
    if (document.readyState === 'complete') {  // Alternative to load event
        const init = async () => {
        console.log("document ready state is complete, waiting for loadSubsequentElements()");
        let result = await loadSubsequentElements(document, "red_loaded");
            console.log(result);
            console.log("loadSubsequentElements fullfilled, run initLoaded");
            initLoaded();
        }
        init(); // async - volá initLoaded()
    }
}

//$(window).on('load',function(){
//    initLoaded();
//    console.log("window load");
//});


/**
 * HACK! Provede initLoadedElements() a pokud je definována proměnná tinymce, provede initLoadedEditableElements().
 * Závisí na tinymce. Tato proměnná je případně definována v šabloně 'tinyConfig' (viz konfigurace a Layout kontroler)
 *
 * @returns {undefined}
 */
function initLoaded() {
    initLoadedElements();
    if (typeof tinymce!=='undefined') {
        initLoadedEditableElements();
    }
}

/**
 * Volá funkce, které mají být volány po událostech na elementech DOM načtených dynamicky.
 * Tato sada funkcí je určena pro elementy používané k editaci obsahu.
 *
 * @returns {undefined}
 */
function initLoadedEditableElements() {
            tinymce.remove();
            tinymce.init(editTextConfig);
            tinymce.init(editHtmlConfig);
            tinymce.init(editMceEditableConfig);
            tinymce.init(selectTemplateArticleConfig);
            tinymce.init(selectTemplatePaperConfig);
            tinymce.init(selectTemplateMultipageConfig);

            //pro editaci pracovního popisu pro přihlášené uživatele
            tinymce.init(editWorkDataConfig);
            //rozbalení formuláře osobních údajů pro "chci nazávat kontakt"
                $('.profil-visible').on('click', function(){
                $('.profil.hidden').css('display', 'block');
            });

            //semantic-ui dropdown (použitý např. pro přihlašování)
            $('.ui.dropdown').dropdown();
            //menu semantic-ui dropdown reaguje na událost hover
            $('.svisle-menu .ui.dropdown').dropdown({on: 'hover'});
            //výběr šablony pro stránku - vlastní dropdown, protože jsou dva vnořený
            $('.ui.selection.dropdown').dropdown();

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
}

/**
 * Volá funkce, které mají být volány po událostech na elementech DOM načtených dynamicky.
 *
 * @returns {undefined}
 */
function initLoadedElements() {
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
            //Vyuziti lokalniho uloziste pro menu
            //Menu v editacnim rezimu obsahuje moznost Nezavirat menu. Pri kliknuti na tuto volbu se prepina ikona u textu Nezavirat menu, aby bylo poznat, jestli je volba aktivni
            //Kdyz je volba aktivni, uklada se do uloziste trida .open k divu, ve kterem je menu, trida .check k volbe Nezavirat menu a trida .none k ikone pres kterou se zavira menu "rucne"
            //Trida .open u divu#mySidenav a .none u ikony slim-icon je nastavena ve stylech rules-layout.less. Trida .check, ktera meni ikonu je vyuzita ze sady ikon semantic-ui
            $('.hamburger_dontclose').click(function(){
                $(this).children('i').toggleClass("check");
                $(this).siblings('a').children('.close.slim-icon').toggleClass("none");
                $(this).parent('.close-item').parent('#mySidenav').toggleClass("open").css('display', 'block');
                var iconCheck = $(this).children('i');
                if(iconCheck.hasClass("check")){
                    var hamburgerClose = "open";
                    var hamburgerCloseIcon = "check";
                    var hamburgerSlimIcon = "none";
                }
                else{
                    var hamburgerClose = "";
                    var hamburgerCloseIcon = "";
                    var hamburgerSlimIcon = "";
                }
                localStorage.setItem('hamburgerClose', hamburgerClose);
                localStorage.setItem('hamburgerCloseIcon', hamburgerCloseIcon);
                localStorage.setItem('hamburgerSlimIcon', hamburgerSlimIcon);
            });
            $(document).ready(function(){
                //po nacteni stranky se pridaji k menu tridy, ktere byly ulozeny
                $('#mySidenav').addClass(localStorage.getItem('hamburgerClose'));
                $('.hamburger_dontclose').children('i').addClass(localStorage.getItem('hamburgerCloseIcon'));
                $('.close.slim-icon').addClass(localStorage.getItem('hamburgerSlimIcon'));
            });
            //konec Vyuziti lokalniho uloziste pro menu


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

            // Call the loop for the first time
            loop();

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

//            var jsTemplateCircleEdit = document.querySelectorAll('.qqqwokk');
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
            var jsTemplateCarousel = document.querySelectorAll('.template-multipage [data-template="carousel"]');
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
