//funkce se volaji v souboru svislemenu.php
function hamburger_open() {
    document.getElementById("mySidenav").style.display = "block";
    document.getElementById("myOverlay").style.display = "block";
}
function hamburger_close() {
    document.getElementById("mySidenav").style.display = "none";
    document.getElementById("myOverlay").style.display = "none";
}

function initLoadedEditableElements() {
            tinymce.remove();
            tinymce.init(editTextConfig);
            tinymce.init(editHtmlConfig);
            tinymce.init(selectPaperTemplateConfig);


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

            $('.edit_kalendar .ui.calendar').calendar({
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

}

function replaceElement(id, apiUri){
    var xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function() {
        if (this.readyState == 4) {
            if (this.status == 200) {
                // náhrada innerHtml:
                document.getElementById(id).innerHTML = xhr.responseText;
                initLoadedElements();
            } else {
                console.log("Load of element failed for id: "+id+",this.readyState:"+this.readyState+", this.status:"+this.status);
            }
        }
    };
    xhr.open("GET", apiUri, true);
    xhr.setRequestHeader("X-Powered-By", "RED Loader");
//    xhr.responseType = "document";   // XML nebo HTML
    xhr.send();
};

function replaceElementEditable(id, apiUri){
    var xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function() {
        if (this.readyState == 4) {
            if (this.status == 200) {
                document.getElementById(id).innerHTML = xhr.responseText;
                initLoadedElements();
                initLoadedEditableElements();
            } else {
                console.log("Load of editable element failed for id: "+id+",this.readyState:"+this.readyState+", this.status:"+this.status);
            }
        }
    };
    xhr.open("GET", apiUri, true);
    xhr.setRequestHeader("X-Powered-By", "RED Loader");
    xhr.send();
};
