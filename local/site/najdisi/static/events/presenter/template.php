<?php /*$exhibitionStand = 'Agentur_fur_Arbeit_Cham.png';*/ 
use Site\ConfigurationCache;

require 'presenter_data_from_menu_item_title.php';
include ConfigurationCache::eventTemplates()['templates']."presenter/company-archive.php";

