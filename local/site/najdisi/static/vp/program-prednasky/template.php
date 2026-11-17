<?php
/** G0 (#116): Přednášky – program z DB (templates/events/program.php). */
use Site\ConfigurationCache;

$programType = 'prednaska';
$programLayout = 'boxes';
$headline = 'Přednášky';
$perex = 'Přednášky veletrhu.';
include ConfigurationCache::eventTemplates()['templates'] . 'program.php';
