<?php

        $select = Html::select("jmeno", "To je label:", ["", "Plzeň-město", "Plzeň-jih", "Plzeň-sever", "Klatovy", "Cheb", "jiné"], [], []);

        $select = Html::select("jmeno", "To je label:", [1=>"", 2=>"Plzeň-město", 3=>"Plzeň-jih", 4=>"Plzeň-sever", 5=>"Klatovy", 6=>"Cheb", 7=>"jiné"], [], []);
        $select = Html::select("jmeno", "To je label:", [1=>"", 2=>"Plzeň-město", 3=>"Plzeň-jih", 4=>"Plzeň-sever", 5=>"Klatovy", 6=>"Cheb", 7=>"jiné"], ["jmeno"=>"Plzeň-sever"], ["data-testovaci"=>"testovaci_atribut"]);

        $select = Html::select("jmeno", "To je label:", [1=>"", 2=>"Plzeň-město", 3=>"Plzeň-jih", 4=>"Plzeň-sever", 5=>"Klatovy", 6=>"Cheb", 7=>"jiné"], ["jmeno"=>"nesmysl"], ["data-testovaci"=>"testovaci_atribut"]);
