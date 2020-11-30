<?php
########## AUTOLOAD ###################################
require "../Pes/Pes/src/Autoloader/Autoloader.php";

use Pes\Autoloader\Autoloader;

$pesAutoloader = new Autoloader();
$pesAutoloader->register();
$pesAutoloader->addNamespace('Pes', '../Pes/Pes/src/'); //autoload pro namespace Pes
$pesAutoloader->addNamespace('Menu', 'Menu/'); //autoload pro namespace 
$pesAutoloader->addNamespace('Konverze', 'Konverze/'); //autoload pro namespace 
$pesAutoloader->addNamespace('Database', 'Database/'); //autoload pro namespace 
$pesAutoloader->addNamespace('Psr', 'vendor/Psr'); //autoload pro namespace Psr

