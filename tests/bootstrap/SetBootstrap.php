<?php

include 'app/Site/ConfigurationCache.php';  // SetBootstrap se volá před autoload
require_once __DIR__ . '/../Support/TestLogPaths.php';

use Site\ConfigurationCache;
use Test\Support\TestLogPaths;

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

// Base path FileLoggeru pro testy: {projectRoot}/tests/
// Relativní cesty Logs/... (včetně bootstrap Logs/Bootstrap/) jdou do tests/Logs/...
define('PES_BOOTSTRAP_LOGS_BASE_PATH', TestLogPaths::logsBasePath());
// define('PES_BOOTSTRAP_LOGS_PATH', 'Mojelogy/Bootstrap/');  // cesta ke složce, do které budou zapisovány soubory s logy vytvářené skripty v průběhu bootstrapu
// define('PES_BOOTSTRAP_ERROR_LOGS_PATH', 'Mojelogy/Errors/');  // Cesta ke složce, do které budou zapisovány soubory s chybovými logy vytvářené skripty v bootstrapu včetně error a exception handlerů

###
# Příklad dalších vhodných možností - pokud tyto položky potřebujete v aplikaci, definujte je v souboru BootstrapSet.php umístěném ve kořenovém adresáři aplikace
###

/*
 * Automaticky nastaví prostředí na produkční, pokud je skript spuštěn na stroji (host) se zadaným jménem
 * TOTO NASTAVENÍ MÁ PŘEDNOST PŘED NASTAVENÍM PROMĚNNÝCH PROSTŘEDÍ I NASTAVENÍMI FORCE_PRODUCTION NEBO FORCE_DEVELOPMENT
 */
define('PES_PRODUCTION_MACHINE_HOST_NAME', ConfigurationCache::bootstrap()['bootstrap.productionhost']);  //vp
/*
 * Vynutí nastevení prostředí na produkční nebo vývojové bez ohledu na nastavení proměnných prostředí
 * Hodnota konstanty se vyhodnocuje jako bool, tedy jestli je TRUE nebo FALSE.
 */
define('PES_FORCE_DEVELOPMENT', 'force_development');
//// nebo
//define('PES_FORCE_PRODUCTION', 'force_production');
