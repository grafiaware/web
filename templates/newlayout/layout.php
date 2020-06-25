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
            
             var map = new ol.Map({
                target: 'map',
                layers: [
                  new ol.layer.Tile({
                    source: new ol.source.OSM()
                  })
                ],
                view: new ol.View({
                  center: ol.proj.fromLonLat([13.3704967, 49.7463531]),
                  zoom: 17
                })
             });
              var layer = new ol.layer.Vector({
                 source: new ol.source.Vector({
                     features: [
                         new ol.Feature({
                             geometry: new ol.geom.Point(ol.proj.fromLonLat([13.3704967, 49.7463531]))
                         })
                     ]
                 })
             });
             map.addLayer(layer);
             var container = document.getElementById('popup');
             var content = document.getElementById('popup-content');
             var closer = document.getElementById('popup-closer');

             var overlay = new ol.Overlay({
                 element: container,
                 autoPan: true,
                 autoPanAnimation: {
                     duration: 250
                 }
             });
             map.addOverlay(overlay);

             closer.onclick = function() {
                 overlay.setPosition(undefined);
                 closer.blur();
                 return false;
             };


        </script>
    </body>
</html>

