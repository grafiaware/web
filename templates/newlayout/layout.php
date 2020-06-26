<!doctype html>
<html lang="<?= $langCode ?>">

    <head>
        <base href="<?=$basePath;?>">
        <?php include "head.php"; ?>
    </head>

    <body class="newlayout">
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

    
            map = new OpenLayers.Map("map");
            map.addLayer(new OpenLayers.Layer.OSM());

            var lonLat = new OpenLayers.LonLat([13.3704967, 49.7463531])
            .transform(
                new OpenLayers.Projection("EPSG:4326"),
                map.getProjectionObject() 
            );
            var zoom = 17;
            
            var markers = new OpenLayers.Layer.Markers( "Markers" );
            map.addLayer(markers);
            markers.addMarker(new OpenLayers.Marker(lonLat));

            map.setCenter (lonLat, zoom);


        </script>
    </body>
</html>

