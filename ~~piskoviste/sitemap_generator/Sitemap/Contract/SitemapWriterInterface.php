<?php

declare(strict_types=1);

namespace Sitemap\Contract;

interface SitemapWriterInterface
{
    /**
     * Persists the given XML content to the given absolute file path.
     * Must create any missing parent directories.
     *
     * @throws \RuntimeException on write failure
     */
    public function write(string $path, string $content): void;
}
