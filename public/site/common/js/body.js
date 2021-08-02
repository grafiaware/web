    //semantic-ui dropdown (použitý např. pro přihlašování)
    $('.ui.dropdown')
      .dropdown()
    ;
    
    //flash message
    $('#domtoast')
        .toast({
            displayTime: 0 //je zobrazen, dokud se na něj neklikne
        })
    ;

    //menu semantic-ui dropdown reaguje na událost hover
    $('.svisle-menu .ui.dropdown').dropdown({
       on: 'hover'
    });
    
    //výběr šablony stránky, po kliku na tlačítko se objeví div s Tiny
    $('.toogleTemplateSelect').on('click', function(){
        $('.paper_template_select').toggle();
    })

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
    
