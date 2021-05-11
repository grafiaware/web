<?php
declare(strict_types=1);
define('PES_FORCE_DEVELOPMENT', 'force_development');
//// nebo
//define('PES_FORCE_PRODUCTION', 'force_production');

include '../vendor/pes/pes/src/Bootstrap/Bootstrap.php';

use Model\Dao\MenuItemDao;
use Piskoviste\HandlerFactory;


/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// Příklad:
// <p> Pro velký zájem je další strategická schůzka projektu <span style="font-size: medium;"><a href="../index.php?list=s01_13&lang=lan1"><strong>Vzdělávejte se!</strong></a> <br />
// href="../index.php?list=s01_13&lang=lan1">

$text = '<p> Pro velký zájem je další strategická schůzka projektu <span style="font-size: medium;"><a href="../index.php?list=s01_13&lang=lan1"><strong>Vzdělávejte se!</strong></a> <br />    '
    .'<p> Pro velký zájem je další strategická schůzka projektu <span style="font-size: medium;"><a href="../index.php?list=s01_15&lang=lan1"><strong>Vzdělávejte se!</strong></a> <br />    '
    .'<p> Pro velký zájem je další strategická schůzka projektu <span style="font-size: medium;"><a href="../index.php?list=s01_16&lang=lan1"><strong>Vzdělávejte se!</strong></a> <br />    ';


$dao = new MenuItemDao((new HandlerFactory())->create());
$transform = [];
$i=0;
$length = strlen('href="');
$end = 0;

do {
    $begin = strpos($text, 'href="', $end);
    if ($begin !== false) {
        $begin = $begin+$length;
        $end = strpos($text, '"', $begin);
        if ($end !== false) {
            $url = substr($text, $begin, $end-$begin);
            $query = parse_url($url, PHP_URL_QUERY);
            parse_str($query, $pairs);
            if (array_key_exists('list', $pairs)) {
                $row = $dao->getByList('cs', $pairs['list']);
                $transform[$url] = "/web/v1/page/item/{$row['uid_fk']}";
            }
        }
    }

} while ($begin!==false);

$newContent = str_replace(array_keys($transform), array_values($transform), $text);

echo $newContent;