<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */
// https://wiki.openstreetmap.org/wiki/Google_Maps_Example
//Example Using Google Maps API V3
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Google Maps v3 API Example</title>
        <style>
            html, body, #map {
                height: 100%;
                width: 100%;
                margin: 0;
                padding: 0;
            }
            div#footer {
                position: fixed;
                left: 0;
                right: 0;
                bottom: 0;
                width: 100%;
                height: 18px;
                margin: 0;
                padding: 6px;
                z-index: 2;
                background: WHITE;
            }
        </style>
    </head>
    <body>
        <div id="map" style="float: left;"></div>

        <div id="footer">&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors</div>

        <!-- bring in the google maps library -->
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>

        <script type="text/javascript">
            //Google maps API initialisation
            var element = document.getElementById("map");

            var map = new google.maps.Map(element, {
                center: new google.maps.LatLng(57, 21),
                zoom: 3,
                mapTypeId: "OSM",
                mapTypeControl: false,
                streetViewControl: false
            });

            //Define OSM map type pointing at the OpenStreetMap tile server
            map.mapTypes.set("OSM", new google.maps.ImageMapType({
                getTileUrl: function(coord, zoom) {
                    // "Wrap" x (longitude) at 180th meridian properly
                    // NB: Don't touch coord.x: because coord param is by reference, and changing its x property breaks something in Google's lib
                    var tilesPerGlobe = 1 << zoom;
                    var x = coord.x % tilesPerGlobe;
                    if (x < 0) {
                        x = tilesPerGlobe+x;
                    }
                    // Wrap y (latitude) in a like manner if you want to enable vertical infinite scrolling

                    return "https://tile.openstreetmap.org/" + zoom + "/" + x + "/" + coord.y + ".png";
                },
                tileSize: new google.maps.Size(256, 256),
                name: "OpenStreetMap",
                maxZoom: 18
            }));
        </script>
    </body>
</html>

<!--See it running here
Example - Using Google Maps API v3 setting OSM as a base map layer

This example adds OpenStreetMap as a default Google Maps base layer. Note that you need to set maxZoom in the ImageMapType for it to work as a base layer. The Apple-specific meta tag enables full screen operation when started as a web application on an iPhone or iPad.-->

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <title>OpenStreetMap with Google Maps v3 API</title>
        <style type="text/css">
            html, body, #map {
                height: 100%;
                margin: 0;
                padding: 0;
            }
        </style>
    </head>
    <body>
        <div id="map"></div>

        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
        <script type="text/javascript">
            var element = document.getElementById("map");

            /*
            Build list of map types.
            You can also use var mapTypeIds = ["roadmap", "satellite", "hybrid", "terrain", "OSM"]
            but static lists suck when Google updates the default list of map types.
            */
            var mapTypeIds = [];
            for(var type in google.maps.MapTypeId) {
                mapTypeIds.push(google.maps.MapTypeId[type]);
            }
            mapTypeIds.push("OSM");

            var map = new google.maps.Map(element, {
                center: new google.maps.LatLng(48.1391, 11.5802),
                zoom: 11,
                mapTypeId: "OSM",
                mapTypeControlOptions: {
                    mapTypeIds: mapTypeIds
                }
            });

            map.mapTypes.set("OSM", new google.maps.ImageMapType({
                getTileUrl: function(coord, zoom) {
                    // See above example if you need smooth wrapping at 180th meridian
                    return "https://tile.openstreetmap.org/" + zoom + "/" + coord.x + "/" + coord.y + ".png";
                },
                tileSize: new google.maps.Size(256, 256),
                name: "OpenStreetMap",
                maxZoom: 18
            }));
        </script>
    </body>
</html>
