<div id="loaded_component_<?= $name?>">
    <script>
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
     if (this.readyState == 4 && this.status == 200) {
        // náhrada innerHtml:
        document.getElementById("loaded_component_<?= $name?>").innerHTML = xhr.responseText;
//$("headline").hover(
//        function() {
//           $( this ).fadeOut( 100 );
//           $( this ).fadeIn( 500 );
//       }
//  );
     }
    };
    xhr.open("GET", "<?= $apiUri?>", true);
    xhr.setRequestHeader("X-Powered-By", "RED Loader");
    xhr.send();
    </script>
</div>
