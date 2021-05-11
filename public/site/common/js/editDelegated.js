//trida .selectTemplate se pouziva u buttonu, ktery slouzi pro vyber sablony paperu
//popisek buttonu je napsany v atributu data-tooltip a je viditelny i pri vyberu sablony z rozeviraciho seznamu
//pomoci tridy .nodatatooltip specifikovanou v author.less atribut skryvam, protoze text v atributu je stejny jako nadpis rozevirajiciho seznamu
$("container.selectTemplate").on(
        {
            mouseenter: function() {
                $(this).parent('.changePaperTemplate').addClass('nodatatooltip');
            },
            mouseleave: function(){
                $(this).parent('.changePaperTemplate').removeClass('nodatatooltip');
            }
        }
    );

//pri najeti na tag s tridou .cornerWithTools se zobrazi sada buttonu s tridou .contentButtons
//sada buttonu obsahuje nastroje pro praci s contentem paperu, mimo jine i nastaveni kalendare - odkdy dokdy ma byt content zobrazen
//kalendar ma vlastni sadu buttonu, proto pomoci trid .toolsDate a .toolsContent menim zobrazeni pozadovane sady buttonu

//zobrazi sadu buttonu s tridou .contentButtons
var showContentButtons =     function(){
        $(this).parent('section').find('.contentButtons').css("display", "flex");
        $(this).css("z-index", "10");
    };
//skryje sadu buttonu s tridou .contentButtons
var hideContentButtons =     function(){
        $(this).parent('section').find('.contentButtons').css("display", "none");
        $(this).css("z-index", "1");
    };

$("body").on("mouseenter", ".cornerWithTools", showContentButtons);
$("body").on("mouseleave", ".cornerWithTools", hideContentButtons);

//trida .toolsContent je pripojena u tlacitka kalendare s popisem zrusit upravy
//na tuto tridu je pripojena take onclick udalost v rendereru
//kdyz dokoncim nastaveni kalendare, zobrazuji opet sadu buttonu pro praci s contentem paperu
$("container.toolsContent").on(
        {
            mouseenter: function(){
                $(this).parent('section').find('.contentButtons').css("display", "flex");
                $(this).css("z-index", "10");
            },
            mouseleave: function(){
                $(this).parent('section').find('.contentButtons').css("display", "none");
                $(this).css("z-index", "1");
            }
        }
    );

//trida .toolsDate je pouzita u talcitka s ikonou kalendare v sade buttonu .contentButtons
//na tuto tridu je pripojena take onclick udalost v rendereru
//pri nastavovani kalendare zobrazuji pouze buttony tykajici se kalendare
$("body").on("click", ".toolsDate", function(){
                $('.cornerWithTools').hover(hideContentButtons);
            }
    );

//.borderDance - obihajici border kolem editacniho tagu, animace nastavena v author.less
//zde prepisuji vlastnost animation-duration podle vysky a sirky tagu
function showHeight(height, width) {
        $('.mce-edit-focus').css("animation-duration", (height+width)/20+"s");
    };
$("body").on("click", ".borderDance",
        function() {
                showHeight( $( ".mce-edit-focus" ).height(), $( ".mce-edit-focus" ).width() );
            }
    );

$("body").on("click", '.editDate .button.toolsContent',
        function(){
            $(this).parent(".editDate").siblings(".contentButtons").css("display", "block");
            $(this).parent(".editDate").css("display", "none");
            $(this).parent(".editDate").siblings(".editDate").css("display", "none");
        }
    );
$("body").on("click", '.contentButtons .button.toolsDate',
        function(){
            $(this).parent(".editContent").parent(".contentButtons").siblings(".editDate").css("display", "flex");
            $(this).parent(".editContent").parent(".contentButtons").css("display", "none");
        }
    );

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
            data['original-title'] = targetElement.getAttribute('data-oroginaltitle');
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