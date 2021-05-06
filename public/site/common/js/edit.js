// vyber sablony paperu
//trida .selectTemplate se pouziva u buttonu, ktery slouzi pro vyber sablony paperu
//popisek buttonu je napsany v atributu data-tooltip a je viditelny i pri vyberu sablony z rozeviraciho seznamu
//pomoci tridy .nodatatooltip specifikovanou v author.less atribut skryvam, protoze text v atributu je stejny jako nadpis rozevirajiciho seznamu
$('.selectTemplate').hover(
    function(){
        $(this).parent('.changePaperTemplate').addClass('nodatatooltip');
    },
    function(){
        $(this).parent('.changePaperTemplate').removeClass('nodatatooltip');
    }
);

// kalendář - nastavení češtiny pro všechny kalendáře
$('.ui.calendar').calendar({
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
// konec kalendář

// content buttons
// sada buttonů pro úpravy page content
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
//sada buttonu obsahuje nastroje pro praci s contentem paperu, mimo jine i nastaveni kalendare - odkdy dokdy ma byt content zobrazen
//kalendar ma vlastni sadu buttonu, proto pomoci trid .toolsDate a .toolsContent menim zobrazeni pozadovane sady buttonu
$('.cornerWithTools').hover(showContentButtons,hideContentButtons);
//trida .toolsDate je pouzita u tlacitka s ikonou kalendare v sade buttonu .contentButtons
//na tridu .toolsDate je pripojena take onclick udalost v rendereru
//pri nastavovani polozek kalendare zobrazuji pouze buttony tykajici se kalendare, zabraňuji zobrazení ostatních tlačítek
$('.toolsDate').click(function(){
    $('.cornerWithTools').hover(hideContentButtons);
});
//trida .toolsContent je pripojena u tlacitka kalendare s popisem zrusit upravy
//na tridu .toolsContent je pripojena take onclick udalost v rendereru
//kdyz dokoncim nastaveni kalendare, zobrazuji opet sadu buttonu pro praci s contentem paperu
$('.toolsContent').click(function(){
    $('.cornerWithTools').hover(showContentButtons,hideContentButtons);
});
// přepínání visitelnosti dvou divů s kalendáři
$('.editDate .button.toolsContent').click(function(){
    $(this).parent(".editDate").siblings(".contentButtons").css("display", "block");
    $(this).parent(".editDate").css("display", "none");
    $(this).parent(".editDate").siblings(".editDate").css("display", "none");
});
$('.contentButtons .button.toolsDate').click(function(){
    $(this).parent(".editContent").parent(".contentButtons").siblings(".editDate").css("display", "flex");
    $(this).parent(".editContent").parent(".contentButtons").css("display", "none");
});
// konec content buttons

// ohraničení edotovatelné oblasti - MRAVENCI
//.borderDance - obihajici border kolem editacniho tagu, animace je vytvořena v author.less
//zde prepisuji pouze hodnotu animation-duration podle vysky a sirky tagu
function showHeight(height, width) {
    $('.mce-edit-focus').css("animation-duration", (height+width)/20+"s");
};
$(".borderDance").click(function() {
    showHeight( $( ".mce-edit-focus" ).height(), $( ".mce-edit-focus" ).width() );
});

//semantic-ui accordion (použitý pro nastavení menu) v editačním režimu
$('.accordion.border')
    .accordion()
;
    
//Vyuziti lokalniho uloziste pro menu
//Menu v editacnim rezimu obsahuje moznost Nezavirat menu. Pri kliknuti na tuto volbu se prepina ikona u textu Nezavirat menu, aby bylo poznat, jestli je volba aktivni 
//Kdyz je volba aktivni, uklada se do uloziste trida .open k divu, ve kterem je menu a trida .check k volbe Nezavirat menu
//Trida .open u divu#mySidenav je nastavena ve stylech site-layout.less. Trida .check menici ikonu je vyuzita ze sady ikon semantic-ui
$('.hamburger_dontclose').click(function(){
    $(this).children('i').toggleClass("check");
    var iconCheck = $(this).children('i');
    if(iconCheck.hasClass("check")){
        var hamburgerClose = "open";
        var hamburgerCloseIcon = "check";
    }
    else{
        var hamburgerClose = "";
        var hamburgerCloseIcon = "";
    }
    localStorage.setItem('hamburgerClose', hamburgerClose);
    localStorage.setItem('hamburgerCloseIcon', hamburgerCloseIcon);
});
//Vyuziti lokalniho uloziste pro menu
//ulozeni pozice vertikalni rolovaci listy u menu v editacnim rezimu
$("ul.vertical.menu li").click(function(){
    var itemPosition = $(".vertical.menu").scrollTop();
    localStorage.setItem('itemPosition', itemPosition);
});
$(document).ready(function(){
    //po nacteni stranky se pridaji k menu tridy, ktere byly ulozeny
    $('#mySidenav').addClass(localStorage.getItem('hamburgerClose'));
    $('.hamburger_dontclose').children('i').addClass(localStorage.getItem('hamburgerCloseIcon'));
    //po nacteni stranky menu odroluje na pozici, ktera se ulozila
    $('ul.vertical.menu').scrollTop(
        localStorage.getItem('itemPosition')
    );
});
//konec Vyuziti lokalniho uloziste pro menu


////////////////////
// edit menu items
////////////////////
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
            url = targetElement.baseURI + '/api/v1/menu/' + targetElement.getAttribute('data-uid') + '/title';
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
// pověsí volání sendOnEnter na tagy !nav' (tagy 'nav' nesmí být načítány dodatečně)
var navigations = document.getElementsByTagName('nav');
for (var i = 0, len = navigations.length; i < len; i++) {
    navigations[i].addEventListener('keydown', sendOnEnter, true);;
}
// konec menu edit items