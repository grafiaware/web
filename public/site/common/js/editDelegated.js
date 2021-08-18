


//zobrazí sadu buttonů s třídou .contentButtons
var showContentButtons =     function(){
        $(this).parent('section').find('.contentButtons').css("display", "flex");
        $(this).css("z-index", "10");
    };
//skryje sadu buttonů s třídou .contentButtons
var hideContentButtons =     function(){
        $(this).parent('section').find('.contentButtons').css("display", "none");
        $(this).css("z-index", "1");
    };
$("body").on("mouseenter", ".cornerWithTools", showContentButtons);
$("body").on("mouseleave", ".cornerWithTools", hideContentButtons);

//calendarWrap = div s buttony pro kalendář a inputy pro výběr datumů ; rozděluje se na kalendář pro content a kalendář pro událost
function showCalendarWrap(thisButton, calendarWrap) {
    //contentButtons - div s tlačítky pro content
    var contentButtons = thisButton.parent(".editContent").parent(".contentButtons");
    //výběr konrétního calendarWrapu podle zvoleného tlačítka
    contentButtons.siblings(calendarWrap).css("display", "flex");
    //přidání třídy pro zvýšení z-indexu tohoto růžku (ostatní růžky nezasahují do kalendáře) - activeCalendar je v author.less
    contentButtons.parent(".cornerWithTools").addClass("activeCalendar");
    //když je zobrazen div pro výběr kalendáře, nechci pod ním (ani u jiných contentů) zobrazovat buttony pro content (.contentButtons) - hideContentButtons je v author.less
    $(".cornerWithTools").addClass("hideContentButtons");
}
//třída .toolsShowDate = tlačítko v sadě buttonů (.contentButtons) - nastavit datum contentu
$("body").on("click", '.button.toolsShowDate',
        function() {
            showCalendarWrap($(this),'.editShowDate');
        }
    );
//třída .toolsEventDate = tlačítko v sadě buttonů (.contentButtons) - nastavit datum události
$("body").on("click", '.button.toolsEventDate',
        function() {
            showCalendarWrap($(this),'.editEventDate');
        }
    );
//třída .hideCalendarWrap = tlačítko rozbaleného kalendáře s popisem Zrušit úpravy (ikona křížku)
$("body").on("click", '.button.hideCalendarWrap',
        function(){
            //smazat třídy u růžku a skrýt div s kalendářem
            $(".cornerWithTools").removeClass("activeCalendar hideContentButtons").find(".calendarWrap").css("display", "none");
        }
    );


//.borderDance - animovaný border kolem editačního tagu (mravenci), animace nastavena v author.less
function showHeight(height, width) {
        //přepisuji vlastnost animation-duration podle výšky a šířky tagu
        $('.mce-edit-focus').css("animation-duration", (height+width)/20+"s");
    };
$("body").on("click", ".borderDance",
        function() {
                showHeight( $( ".mce-edit-focus" ).height(), $( ".mce-edit-focus" ).width() );
            }
    );


//semantic-ui accordion (použitý pro nastavení menu) v editačním režimu
$('.accordion.border')
    .accordion()
;

// EDIT MENU

function sendOnEnter(event) {
    var escPressed = event.which === 27,
    nlPressed = event.which === 13,
    targetElement = event.target,
    acceptedElement = targetElement.nodeName === 'SPAN' && targetElement.parentNode.nodeName === 'P',
    url,
    data = {};

    if (acceptedElement) {
        if (escPressed) {
            // restore state
            document.execCommand('undo');
            targetElement.blur();
        } else if (nlPressed) {
            //hack - odstranění <br/> - innerHTML obsahuje i vložený <br/> tag vzhiklý po stisku enter klávesy
            // FF do obsahu elementu v modu contenteditable="true" vždy při uložení přidá na začátel tag <br/> (kvůli možnosti "kliknout" na element)
            // <br/> tag je odstraněn po změně na conteneditable="false" -> po dobu editace obsahu elementu je na žačátku obsahu vždy <br/> - skrýváme ho pomocí css
            targetElement.innerHTML = targetElement.innerText;
            // data title z innerText, ostatní z data- atributů - zde musí být shoda jmen s html šablonou pro item!
            data['title'] = targetElement.innerText; // innerHTML obsahuje i vložený <br/> tag vzhiklý po stisku enter klávesy
            data['original-title'] = targetElement.getAttribute('data-originaltitle');
            url = targetElement.baseURI + '/red/v1/menu/' + targetElement.getAttribute('data-uid') + '/title';
            // odeslání ajax requestu
            // .ajax vrací Deferred Object - .done a .fail jsou metody Deferred Objectu (a samy vracejí Deferred Object)
            $.ajax({
                    url: url,
                    data: data,
                    type: 'post'
                    })
                    .done(function(data, textStatus, jqXHR) {
                    alert( "Provedeno: " + data );
                    })
                    .fail(function(jqXHR, textStatus, errorThrown){
                    alert( "Selhalo: " + errorThrown );
                });

            log(JSON.stringify(data));

            targetElement.blur();
            event.preventDefault();
        }
    }
}

var navigations = document.getElementsByTagName('nav');
for (var i = 0, len = navigations.length; i < len; i++) {
    navigations[i].addEventListener('keydown', sendOnEnter, true);;
}
//document.addEventListener('keydown', sendOnEnter, true);