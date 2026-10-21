<?php

declare(strict_types=1);

namespace Sitemap\Contract;

use Sitemap\SitemapSection;

interface SitemapCacheInterface
{
    /**
     * Returns true when the given file path is considered fresh (cache hit).
     */
    public function isValid(string $filepath): bool;

    /**
     * Returns true when ALL section files AND the index file are fresh.
     * When cache is disabled, must always return false.
     *
     * @param SitemapSection[] $sections
     */
    public function allSectionsValid(string $indexPath, SitemapSection ...$sections): bool;
}
