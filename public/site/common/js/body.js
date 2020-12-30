    $('.ui.dropdown')
      .dropdown()
    ;
    $('.ui.selection.dropdown')
      .dropdown()
    ;

    //odeslani prihlasovaciho formulare pri stisku klavesy Enter
    $('.loginEnterKey').keyup(function(event){
        if(event.keyCode === 13){
            $('.positive.button').click();
        }
    });

    //Vyuziti lokalniho uloziste pro menu
    //ulozeni pozice vertikalni rolovaci listy u menu v editacnim rezimu
    $(".vertical.menu.edit li").click(function(){
        var itemPosition = $(".svisle-menu").scrollTop();
        localStorage.setItem('itemPosition', itemPosition);
    });
    $(document).ready(function(){
        //po nacteni stranky menu odroluje na pozici, ktera se ulozila
        $('.svisle-menu').scrollTop(
            localStorage.getItem('itemPosition')
        );
    });

