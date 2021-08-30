<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=$soubor");
header("Content-Length: " . filesize($soubor));
readfile($soubor);