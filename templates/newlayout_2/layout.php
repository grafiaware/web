<!doctype html>
<html lang="<?= $langCode ?>">

    <head>
        <base href="<?=$basePath;?>">
        <?php include "head.php"; ?>
        <script src="https://api.mapy.cz/loader.js"></script>
	<script>Loader.load()</script>
    </head>

    <body class="newlayout_2">
        <?php include "body.php"; ?>
        <script>
            function hamburger_open() {
                document.getElementById("mySidenav").style.display = "block";
                document.getElementById("myOverlay").style.display = "block";
            }
            function hamburger_close() {
                document.getElementById("mySidenav").style.display = "none";
                document.getElementById("myOverlay").style.display = "none";
            }
            
            
            var stred = SMap.Coords.fromWGS84(13.3704967, 49.7463531);
            var mapa = new SMap(JAK.gel("mapa"), stred, 17);
            mapa.addDefaultLayer(SMap.DEF_BASE).enable();
            mapa.addDefaultControls();	  
            
            var layer = new SMap.Layer.Marker();
            mapa.addLayer(layer);
            layer.enable();

            var options = {};
            var marker = new SMap.Marker(stred, "myMarker", options);
            layer.addMarker(marker);
	
        </script>
    </body>
</html>

