<?php

namespace Site\Common;

/**
 * Common bootstrap — site dodá logs path a production host.
 */
abstract class ConfigurationBootstrap {

    abstract protected static function bootstrapLogsBasePath(): string;

    abstract protected static function bootstrapProductionHost(): string;

    public static function bootstrap(): array {
        return ConfigMerge::merge([
            'bootstrap.logs.basepath' => static::bootstrapLogsBasePath(),
            'bootstrap.productionhost' => static::bootstrapProductionHost(),
        ], static::bootstrapOverlay());
    }

    /** @return array<string, mixed> */
    protected static function bootstrapOverlay(): array {
        return [];
    }
}
