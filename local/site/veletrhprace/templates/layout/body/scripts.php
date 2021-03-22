<?= $linkEditorJs ?? '' ?>

<!-- scripty pro socialni site-->
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/cs_CZ/sdk.js#xfbml=1&version=v10.0" nonce="YwCvYlBF"></script> 
<script async src="//www.instagram.com/embed.js"></script>
<script>
    // pro modal elementy
    $('.ui.dropdown').dropdown();
    
    $('.ui.selection.dropdown').dropdown();
    
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
    
    //flash message
    $('#domtoast')
        .toast({
            displayTime: 0 //je zobrazen, dokud se na něj neklikne
        })
    ;
    
    //semantic-ui accordion (použitý pro nastavení menu)
    $('.accordion.border')
        .accordion()
    ;
    
    //odeslani prihlasovaciho formulare pri stisku klavesy Enter
    $('.loginEnterKey').keyup(function(event){
        if(event.keyCode === 13){
            $('.positive.button').click();
        }
    });
    
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
    //ulozeni pozice vertikalni rolovaci listy u menu v editacnim rezimu
    $("ul.vertical.menu.edit li").click(function(){
        var itemPosition = $(".vertical.menu").scrollTop();
        localStorage.setItem('itemPosition', itemPosition);
    });
    $(document).ready(function(){
        //po nacteni stranky se pridaji k menu tridy, ktere byly ulozeny
        $('#mySidenav').addClass(localStorage.getItem('hamburgerClose'));
        $('.hamburger_dontclose').children('i').addClass(localStorage.getItem('hamburgerCloseIcon'));
        //po nacteni stranky menu odroluje na pozici, ktera se ulozila
        $('ul.vertical.menu.edit').scrollTop(
            localStorage.getItem('itemPosition')
        );
    });
</script>
