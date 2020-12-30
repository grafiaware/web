<div id="loaded_component_<?= $name?>">
    <script>
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
     if (this.readyState == 4 && this.status == 200) {
        // Typical action to be performed when the document is ready:
        document.getElementById("loaded_component_<?= $name?>").innerHTML = xhr.responseText;

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
    xhr.open("GET", "<?= $apiUri?>", true);
    xhr.send();
    </script>
</div>
