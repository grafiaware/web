function hamburger_open() {
    document.getElementById("mySidenav").style.display = "block";
    document.getElementById("myOverlay").style.display = "block";
}
function hamburger_close() {
    document.getElementById("mySidenav").style.display = "none";
    document.getElementById("myOverlay").style.display = "none";
}
var kolecko = function(e){
   e = e.originalEvent;
   var delta = e.wheelDelta>0||e.detail<0 ? 1 : -1;
   $(".nav-mobile").toggleClass('active', delta === 1, 250);
};


