<?php

declare(strict_types=1);

namespace Sitemap;

/**
 * Immutable value object representing a single <url> entry in a sitemap.
 */
final class SitemapUrl
{
    public function __construct(
        /** Absolute URL */
        public readonly string $loc,

        /** ISO 8601 date string (e.g. '2025-06-10') — null = omit from output */
        public readonly ?string $lastmod = null,

        public readonly string $changefreq = 'weekly',

        public readonly float $priority = 0.5,
    ) {}
}
