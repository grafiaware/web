/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$('.pull-out').mouseover(function(){
    $(this).parents('.grafia.segment').find('.contentButtons').css("display", "flex");
});
$('.contentButtons').mouseleave(function(){
    $(this).css("display", "none");
});

function hamburger_open() {
    document.getElementById("mySidenav").style.display = "block";
    document.getElementById("myOverlay").style.display = "block";
}
function hamburger_close() {
    document.getElementById("mySidenav").style.display = "none";
    document.getElementById("myOverlay").style.display = "none";
}

function edit_name() {
    document.getElementById("editName").style.display = "block";
    document.getElementById("editMenu").style.display = "none";
}
function close_edit_name() {
    document.getElementById("editMenu").style.display = "block";
    document.getElementById("editName").style.display = "none";
}

var kolecko = function(e){
   e = e.originalEvent;
   var delta = e.wheelDelta>0||e.detail<0 ? 1 : -1;
   $(".nav-mobile").toggleClass('active', delta === 1, 250);
};

$("ul.vertical.menu.edit li").click(function(){
    var itemPosition = $(".vertical.menu").scrollTop();
    localStorage.setItem('itemPosition', itemPosition);
});

$('ul.vertical.menu.edit').scrollTop(
    localStorage.getItem('itemPosition')
);

function showHeight(height, width) {
    $('.mce-edit-focus').css("animation-duration", (height+width)/20+"s");
};

$("content").click(function() {
  showHeight( $( ".mce-edit-focus" ).height(), $( ".mce-edit-focus" ).width() );
});

$("block").click(function() {
  showHeight( $( ".mce-edit-focus" ).height(), $( ".mce-edit-focus" ).width() );
});

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
var navigations = document.getElementsByTagName('nav');
for (var i = 0, len = navigations.length; i < len; i++) {
    navigations[i].addEventListener('keydown', sendOnEnter, true);;
}
//document.addEventListener('keydown', sendOnEnter, true);