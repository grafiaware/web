//funkce se volaji v souboru svislemenu.php
function hamburger_open() {
    document.getElementById("mySidenav").style.display = "block";
    document.getElementById("myOverlay").style.display = "block";
}
function hamburger_close() {
    document.getElementById("mySidenav").style.display = "none";
    document.getElementById("myOverlay").style.display = "none";
}

function togleTemplateSelect(event, id) {
//    $('#'+id).toggle();
    console.log("togleTemplateSelect: element id "+id+".");
    var elm = document.getElementById(id);
    if (elm.style.display=="block") {
        elm.style.display = "none";
    } else {
        elm.style.display = "block";
    }
    event.preventDefault();
    event.stopPropagation();
}



