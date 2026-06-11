<?php

declare(strict_types=1);

namespace Sitemap\Cache;

use Sitemap\Contract\SitemapCacheInterface;
use Sitemap\SitemapConfig;
use Sitemap\SitemapSection;

/**
 * File-system based cache.
 * When config->cacheEnabled is false, every check returns false (= always regenerate).
 */
final class FilesystemSitemapCache implements SitemapCacheInterface
{
    public function __construct(
        private readonly SitemapConfig $config,
    ) {}

    public function isValid(string $filepath): bool
    {
        if (!$this->config->cacheEnabled) {
            return false;
        }
        if (!file_exists($filepath)) {
            return false;
        }
        return (time() - filemtime($filepath)) < $this->config->cacheTtl;
    }

    public function allSectionsValid(string $indexPath, SitemapSection ...$sections): bool
    {
        if (!$this->config->cacheEnabled) {
            return false;
        }
        foreach ($sections as $section) {
            if (!$this->isValid($section->getFullPath())) {
                return false;
            }
        }
        return $this->isValid($indexPath);
    }
}
