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
        $('.ui.dropdown').dropdown();
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