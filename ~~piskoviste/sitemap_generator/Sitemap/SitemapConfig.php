<?php

declare(strict_types=1);

namespace Sitemap;

/**
 * Immutable configuration value object.
 * Intended to be constructed once and injected via DI container.
 *
 * Example DI registration (Nette, Symfony, or plain PHP):
 *
 *   new SitemapConfig(
 *       baseUrl:        'https://example.com',
 *       outputDir:      __DIR__ . '/public',
 *       indexFilename:  'sitemap.xml',
 *       cacheEnabled:   false,   // true = skip regeneration within cacheTtl
 *       cacheTtl:       3600,
 *   )
 */
final class SitemapConfig
{
    public function __construct(
        /** Base URL of the site — no trailing slash */
        public readonly string $baseUrl,

        /** Absolute path to the directory where sitemap files will be written */
        public readonly string $outputDir,

        /** Filename of the top-level sitemap index file */
        public readonly string $indexFilename = 'sitemap.xml',

        /**
         * When false (default), files are always regenerated on save().
         * Ideal for deploy-time or on-edit generation hooks.
         *
         * When true, existing files younger than $cacheTtl seconds are kept as-is.
         */
        public readonly bool $cacheEnabled = false,

        /** Cache TTL in seconds — only effective when $cacheEnabled = true */
        public readonly int $cacheTtl = 3600,
    ) {}

    public function indexPath(): string
    {
        return rtrim($this->outputDir, '/') . '/' . $this->indexFilename;
    }

    public function indexUrl(): string
    {
        return rtrim($this->baseUrl, '/') . '/' . $this->indexFilename;
    }
}
