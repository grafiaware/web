<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\NajdiSi;

/**
 * Description of Configuration
 *
 * @author pes2704
 */
class ConfigurationBootstrap extends \Site\Common\ConfigurationBootstrap {

    protected static function bootstrapLogsBasePath(): string {
        return ConfigurationConstants::WEB_BOOTSTRAP_LOGS;
    }

    protected static function bootstrapProductionHost(): string {
        return ConfigurationConstants::WEB_BOOTSTRAP_PRODUCTION_HOST;
    }
}
