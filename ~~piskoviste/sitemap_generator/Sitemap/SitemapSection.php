<?php

declare(strict_types=1);

namespace Sitemap;

/**
 * Represents one incremental section sitemap (e.g. blog, products, pages).
 * Generates a file named  sitemap-{name}.xml  relative to the output directory.
 */
final class SitemapSection
{
    /** @var SitemapUrl[] */
    private array $urls = [];

    public function __construct(
        public readonly string $name,
        private readonly SitemapConfig $config,
    ) {}

    /**
     * Adds a URL to this section.
     *
     * @param string      $path       Relative path, e.g. '/blog/my-article'
     * @param string|null $lastmod    ISO 8601 date from DB (not generated date!)
     * @param string      $changefreq 'always'|'hourly'|'daily'|'weekly'|'monthly'|'yearly'|'never'
     * @param float       $priority   0.0 – 1.0
     */
    public function add(
        string $path,
        ?string $lastmod = null,
        string $changefreq = 'weekly',
        float $priority = 0.5,
    ): self {
        $loc = rtrim($this->config->baseUrl, '/') . '/' . ltrim($path, '/');
        $this->urls[] = new SitemapUrl($loc, $lastmod, $changefreq, $priority);
        return $this;
    }

    /** @return SitemapUrl[] */
    public function getUrls(): array
    {
        return $this->urls;
    }

    public function count(): int
    {
        return count($this->urls);
    }

    public function getFilename(): string
    {
        return 'sitemap-' . $this->name . '.xml';
    }

    public function getFullPath(): string
    {
        return rtrim($this->config->outputDir, '/') . '/' . $this->getFilename();
    }

    public function getUrl(): string
    {
        return rtrim($this->config->baseUrl, '/') . '/' . $this->getFilename();
    }

    /**
     * The most recent lastmod date among all URLs in this section.
     * Used as the section's <lastmod> in the sitemap index.
     */
    public function getLastmod(): ?string
    {
        $dates = array_filter(array_map(static fn(SitemapUrl $u) => $u->lastmod, $this->urls));
        if ($dates === []) {
            return null;
        }
        sort($dates);
        return end($dates);
    }
}
