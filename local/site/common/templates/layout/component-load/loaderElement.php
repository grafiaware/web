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

     }
    };
    xhr.open("GET", "<?= $apiUri?>", true);
    xhr.send();
    </script>
</div>
