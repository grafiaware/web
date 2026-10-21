<?php

declare(strict_types=1);

namespace Sitemap\Writer;

use Sitemap\Contract\SitemapWriterInterface;

/**
 * Writes sitemap XML files to the local filesystem.
 * Creates missing parent directories automatically.
 */
final class FilesystemSitemapWriter implements SitemapWriterInterface
{
    public function write(string $path, string $content): void
    {
        $dir = dirname($path);

        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new \RuntimeException(sprintf('Failed to create directory "%s".', $dir));
        }

        if (file_put_contents($path, $content) === false) {
            throw new \RuntimeException(sprintf('Failed to write sitemap file "%s".', $path));
        }
    }
}
