<?= $linkEditorJs ?? '' ?>

<script>
    $('.ui.dropdown')
      .dropdown()
    ;
    $('.ui.selection.dropdown')
      .dropdown()
    ;
    
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
</script>
