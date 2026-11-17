<?php
/** G0 (#116): Program veletrhu – program z DB (templates/events/program.php). */
use Site\ConfigurationCache;

$programType = null;
$programLayout = 'boxes';
$headline = 'Program veletrhu';
$perex = 'Přednášky, poradny a další akce veletrhu.';
include ConfigurationCache::eventTemplates()['templates'] . 'program.php';
