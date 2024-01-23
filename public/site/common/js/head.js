//funkce se volaji v souboru svislemenu.php
function hamburger_open() {
    document.getElementById("mySidenav").style.display = "block";
    document.getElementById("myOverlay").style.display = "block";
}
function hamburger_close() {
    document.getElementById("mySidenav").style.display = "none";
    document.getElementById("myOverlay").style.display = "none";
}

$(window).scroll(function(){
      // Get number of pixels of scroll.
      var pixel = $(window).scrollTop();
      var header = document.getElementById("header").offsetHeight;
      console.log(pixel);
      // When the scroll exceeds 300px, give the [fixed-menu] class.
      if(pixel > header){
        $('.mobile-menu-bar').addClass('fixed-menu');
      } else {
        $('.mobile-menu-bar').removeClass('fixed-menu');
      }
});

function toggleTemplateSelect(event, id) {
//    $('#'+id).toggle();
    console.log("toggleTemplateSelect: element id "+id+".");
    var elm = document.getElementById(id);
    if (elm === null) {
        console.error("toggleTemplateSelect: Unable toggle template select. There is no element with id '"+id+"'.");
    } else {
        if (elm.style.display==="block") {
            elm.style.display = "none";
        } else {
            elm.style.display = "block";
        }
    }
    event.preventDefault();
    event.stopPropagation();
}

                function removeDisabled(elementId){
                   document.getElementById(elementId).removeAttribute('disabled');
               };

