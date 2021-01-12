    $('.ui.dropdown')
      .dropdown()
    ;
    
//    $('.hlavni-menu > li').removeClass('item').addClass('ui icon dropdown');
//    $('.svisle-menu .ui.dropdown').dropdown({
//       on: 'hover'
//    });
//    $('.hlavni-menu > .ui.dropdown, .menu > .item:not(.leaf)').dropdown('show',{
//        on: 'hover'
//    });
    $('.right.sidebar').first()
      .sidebar('attach events', '.toggle.button')
    ;
    $('.toggle.button')
      .removeClass('disabled')
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

