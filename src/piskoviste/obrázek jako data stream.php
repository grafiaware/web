<?php

/*
 * https://jecas.cz/data-uri
 */

$obrazek = file_get_contents("logo.png");
$dataUrl = "data:image/png;base64," . base64_encode($obrazek);
echo "<img src='" . $dataUrl . "'>";