<?php

declare(strict_types=1);

namespace Sitemap\Contract;

use Sitemap\SitemapSection;

interface SitemapGeneratorInterface
{
    /**
     * Returns (or lazily creates) a named section.
     * Calling with the same name multiple times returns the same instance,
     * allowing URLs to be added incrementally.
     */
    public function section(string $name): SitemapSection;

    /**
     * Persists all section files and the sitemap index to disk.
     *
     * @return bool True when files were (re)written; false when cache was valid and nothing changed.
     * @throws \RuntimeException on write failure
     */
    public function save(): bool;
}
