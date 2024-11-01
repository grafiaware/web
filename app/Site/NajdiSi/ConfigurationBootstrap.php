<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\NajdiSi;

use Pes\Logger\FileLogger;

/**
 * Description of Configuration
 *
 * @author pes2704
 */
class ConfigurationBootstrap extends ConfigurationConstants {

    ### bootstrap ###
    #
    public static function bootstrap() {
        return [
            'bootstrap.logs.basepath' => self::WEB_BOOTSTRAP_LOGS,
            'bootstrap.productionhost' => self::WEB_BOOTSTRAP_PRODUCTION_HOST,
        ];
    }
}
