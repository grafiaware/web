<?php /*$exhibitionStand = 'Agentur_fur_Arbeit_Cham.png';*/ 
use Site\ConfigurationCache;

$version_fk = '2026';

require __DIR__.'/../company_id_from_item_title.php';
include ConfigurationCache::eventTemplates()['templates']."presenter/company-profile.php";

