<div id="loaded_component_<?= $name?>">
    <script>
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
     if (this.readyState == 4 && this.status == 200) {
        // Typical action to be performed when the document is ready:
        document.getElementById("loaded_component_<?= $name?>").innerHTML = xhttp.responseText;

        tinymce.init(headlineConfig);
        tinymce.init(contentConfig);
        tinymce.init(perexConfig);
        tinymce.init(headerFooterConfig);

     }
    };
    xhttp.open("GET", "<?= $apiUri?>", true);
    xhttp.send();
    </script>
</div>
