<?php
/** G0 (#116): Poradny – program z DB (templates/events/program.php). */
use Site\ConfigurationCache;

$programType = 'poradna';
$programLayout = 'column';
$headline = 'Poradny';
$perex = 'Poradny na veletrhu zdarma.';
include ConfigurationCache::eventTemplates()['templates'] . 'program.php';
