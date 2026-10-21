<?php

namespace Site\VeletrhPrace;

/**
 * Site bootstrap — logs path a production host z ConfigurationConstants.
 */
class ConfigurationBootstrap extends \Site\Common\ConfigurationBootstrap {

    protected static function bootstrapLogsBasePath(): string {
        return ConfigurationConstants::WEB_BOOTSTRAP_LOGS;
    }

    protected static function bootstrapProductionHost(): string {
        return ConfigurationConstants::WEB_BOOTSTRAP_PRODUCTION_HOST;
    }
}
