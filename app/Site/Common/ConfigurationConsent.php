<?php

namespace Site\Common;

use Pes\Logger\FileLogger;

/**
 * Common consent — site může přepsat overlay.
 */
abstract class ConfigurationConsent {

    public static function consent(): array {
        return ConfigMerge::merge([
            'consent.logs.directory' => 'PersistentLogs/Consent',
            'consent.logs.file' => 'CookieConsent.log',
            'consent.logs.type' => FileLogger::APPEND_TO_LOG,
        ], static::consentOverlay());
    }

    /** @return array<string, mixed> */
    protected static function consentOverlay(): array {
        return [];
    }
}
