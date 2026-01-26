<?php
use Site\ConfigurationCache;

$yearPath = '2026';

// $version_fk - proměnná pro company_id_from_item_title.php - select company z databáze
$version_fk = '2026';

// $version_fk proměnná pro company_id_from_item_title.php - select company z databáze
require __DIR__.'/../company_id_from_item_title.php';

include ConfigurationCache::eventTemplates()['templates']."presenter/company-profile.php";

// proměnné pro files/pdf/template.php
$path = ConfigurationCache::files()['@presenter']."/smycka/$yearPath/";
$file = "$companyName.pdf";

// $path, $file - proměnná pro files/pdf/template.php
if (is_readable($path.$file)) {
    include ConfigurationCache::commonTemplates()['templates']."files/pdf/template.php";
}
