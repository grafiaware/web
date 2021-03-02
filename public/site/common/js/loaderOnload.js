//funkce se volaji v souboru svislemenu.php
function hamburger_open() {
    document.getElementById("mySidenav").style.display = "block";
    document.getElementById("myOverlay").style.display = "block";
}
function hamburger_close() {
    document.getElementById("mySidenav").style.display = "none";
    document.getElementById("myOverlay").style.display = "none";
}

function replaceElement(id, apiUri){
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        // náhrada innerHtml:
        document.getElementById(id).innerHTML = xhr.responseText;
        
        $('#modal_12').modal('attach events', '.btn-12', 'show');
        $('#modal_15').modal('attach events', '.btn-fb', 'show');
        $('#modal_16').modal('attach events', '.btn-16', 'show');
        $('#modal_56').modal('attach events', '.btn-56', 'show');
        $('#modal_333').modal('attach events', '.btn-333', 'show');    
        
        //semantic-ui popup (použitý např. ikony v online-stáncích)
        $('.popup.icon').popup();
        
        //semantic-ui accordion (použitý např. pro výpis informací v profilu přihlášeného uživatele)
        $('.styled.accordion').accordion();
        
        //a tab is a hidden section of content activated by a menu 
        $('.tabular.menu .item').tab(); 
        
         // Detect request animation frame
        var scroll = window.requestAnimationFrame ||
        // IE Fallback
        function(callback){ window.setTimeout(callback, 1000/60)};
        var elementsToShow = document.querySelectorAll('.show-on-scroll'); 

        function loop() {

          elementsToShow.forEach(function (element) {
            if (isElementInViewport(element)) {
              element.classList.add('is-visible');
            } else {
              element.classList.remove('is-visible');
            }
          });
          scroll(loop);
        }

        // Call the loop for the first time
        loop();

        // Helper function from: http://stackoverflow.com/a/7557433/274826
        function isElementInViewport(el) {
          // special bonus for those using jQuery
          if (typeof jQuery === "function" && el instanceof jQuery) {
            el = el[0];
          }
          var rect = el.getBoundingClientRect();
          return (
            (rect.top <= 0
              && rect.bottom >= 0)
            ||
            (rect.bottom >= (window.innerHeight || document.documentElement.clientHeight) &&
              rect.top <= (window.innerHeight || document.documentElement.clientHeight))
            ||
            (rect.top >= 0 &&
              rect.bottom <= (window.innerHeight || document.documentElement.clientHeight))
          );
        }
        
        
    }
     if (this.readyState == 4 && this.status != 200) {
         console.log("Loader failed for id: "+id+",this.readyState:"+this.readyState+", this.status:"+this.status);
     }
    };
    xhr.open("GET", apiUri, true);
    xhr.setRequestHeader("X-Powered-By", "RED Loader");
    xhr.send();
};

function replaceElementEditable(id, apiUri){
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
     if (this.readyState == 4 && this.status == 200) {
        // Typical action to be performed when the document is ready:
        document.getElementById(id).innerHTML = xhr.responseText;

        tinymce.remove();
        tinymce.init(headlineConfig);
        tinymce.init(contentConfig);
        tinymce.init(perexConfig);
        tinymce.init(headerFooterConfig);
        //semantic-ui dropdown (použitý např. pro přihlašování)
        $('.ui.dropdown').dropdown();
        //menu semantic-ui dropdown reaguje na událost hover
        $('.svisle-menu .ui.dropdown').dropdown({on: 'hover'});
        //výběr šablony pro stránku - vlastní dropdown, protože jsou dva vnořený
        $('.ui.selection.dropdown').dropdown();
        $('.edit_kalendar .ui.calendar').calendar({
            type: 'date',
            today: true,
            firstDayOfWeek: 1,
            text: {
                days: ['Ne', 'Po', 'Út', 'St', 'Čt', 'Pá', 'So'],
                months: ['Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec'],
                today: 'Dnes'
            },
            formatter: {
              date: function (date, settings) {
                if (!date) return '';
                var day = date.getDate();
                var month = date.getMonth() + 1;
                var year = date.getFullYear();
                return day + '. ' + month + '. ' + year;}
            }
        });
     }
    };
    xhr.open("GET", apiUri, true);
    xhr.setRequestHeader("X-Powered-By", "RED Loader");
    xhr.send();
};