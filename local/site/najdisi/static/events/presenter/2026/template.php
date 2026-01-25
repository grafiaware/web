<?php
use Site\ConfigurationCache;

$version_fk = '2026';

require __DIR__.'/../company_id_from_item_title.php';

$companyId;
include ConfigurationCache::eventTemplates()['templates']."presenter/company-profile.php";

$companyName;
$path = ConfigurationCache::files()['@presenter']."/smycka/2026/";
$file = "$companyName.pdf";
if (is_readable($path.$file)) {
    include ConfigurationCache::commonTemplates()['templates']."files/pdf/template.php";
}
