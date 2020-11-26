<?= $linkEditorJs ?? '' ?>

<script>
    $('.ui.dropdown')
      .dropdown()
    ;
    $('.ui.selection.dropdown')
      .dropdown()
    ;
    
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
    $(document).ready(function(){
        $('#mySidenav').addClass(localStorage.getItem('hamburgerClose'));
        $('.hamburger_dontclose').children('i').addClass(localStorage.getItem('hamburgerCloseIcon'));
    });
</script>
