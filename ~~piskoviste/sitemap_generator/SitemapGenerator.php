<?php

/**
 * SitemapGenerator
 *
 * Generates sitemap index + incremental section sitemaps.
 * Caching is optional and controlled via config.
 *
 * Usage:
 *   $config = new SitemapConfig(baseUrl: 'https://example.com', outputDir: __DIR__ . '/public');
 *   $generator = new SitemapGenerator($config);
 *   $generator->section('blog')->add('/blog/foo', '2025-01-15', 'weekly', 0.7);
 *   $generator->section('pages')->add('/', '2025-06-01', 'daily', 1.0);
 *   $generator->save(); // writes all section files + sitemap-index.xml
 */

// ---------------------------------------------------------------------------
// Configuration
// ---------------------------------------------------------------------------

class SitemapConfig
{
    public function __construct(
        /** Base URL of the site, no trailing slash */
        public readonly string $baseUrl,

        /** Absolute path to the directory where sitemap files will be written */
        public readonly string $outputDir,

        /** Filename of the sitemap index file */
        public readonly string $indexFilename = 'sitemap.xml',

        /** Enable file-based cache. Set false when generating on every deploy/edit. */
        public readonly bool $cacheEnabled = false,

        /** Cache TTL in seconds (only used when $cacheEnabled = true) */
        public readonly int $cacheTtl = 3600,
    ) {}
}

// ---------------------------------------------------------------------------
// Single URL entry
// ---------------------------------------------------------------------------

class SitemapUrl
{
    public function __construct(
        public readonly string $loc,
        public readonly ?string $lastmod = null,
        public readonly string $changefreq = 'weekly',
        public readonly float $priority = 0.5,
    ) {}
}

// ---------------------------------------------------------------------------
// One section / incremental sitemap
// ---------------------------------------------------------------------------

class SitemapSection
{
    /** @var SitemapUrl[] */
    private array $urls = [];

    public function __construct(
        public readonly string $name,
        private readonly SitemapConfig $config,
    ) {}

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
     * Returns the most recent lastmod among all URLs in this section.
     * Used as the <lastmod> of this section in the sitemap index.
     */
    public function getLastmod(): ?string
    {
        $dates = array_filter(array_map(fn(SitemapUrl $u) => $u->lastmod, $this->urls));
        if (empty($dates)) {
            return null;
        }
        sort($dates);
        return end($dates);
    }

    public function render(): string
    {
        $xml = new \SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?>' .
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>'
        );

        foreach ($this->urls as $url) {
            $item = $xml->addChild('url');
            $item->addChild('loc', htmlspecialchars($url->loc, ENT_XML1));
            if ($url->lastmod !== null) {
                $item->addChild('lastmod', $url->lastmod);
            }
            $item->addChild('changefreq', $url->changefreq);
            $item->addChild('priority', number_format($url->priority, 1));
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        return $dom->saveXML();
    }
}

// ---------------------------------------------------------------------------
// Cache
// ---------------------------------------------------------------------------

class SitemapCache
{
    public function __construct(
        private readonly SitemapConfig $config,
    ) {}

    public function isValid(string $filepath): bool
    {
        if (!$this->config->cacheEnabled) {
            return false; // cache vypnutý → vždy generovat znovu
        }
        if (!file_exists($filepath)) {
            return false;
        }
        return (time() - filemtime($filepath)) < $this->config->cacheTtl;
    }

    /**
     * Checks whether ANY section file is stale.
     * If cache is disabled, always returns false (= regenerate everything).
     */
    public function allSectionsValid(SitemapSection ...$sections): bool
    {
        if (!$this->config->cacheEnabled) {
            return false;
        }
        foreach ($sections as $section) {
            if (!$this->isValid($section->getFullPath())) {
                return false;
            }
        }
        $indexPath = rtrim($this->config->outputDir, '/') . '/' . $this->config->indexFilename;
        return $this->isValid($indexPath);
    }
}

// ---------------------------------------------------------------------------
// Main generator
// ---------------------------------------------------------------------------

class SitemapGenerator
{
    /** @var SitemapSection[] */
    private array $sections = [];

    private readonly SitemapCache $cache;

    public function __construct(
        private readonly SitemapConfig $config,
    ) {
        $this->cache = new SitemapCache($config);
    }

    /**
     * Returns (or creates) a named section.
     * Call repeatedly with the same name to keep adding URLs to the same section.
     */
    public function section(string $name): SitemapSection
    {
        if (!isset($this->sections[$name])) {
            $this->sections[$name] = new SitemapSection($name, $this->config);
        }
        return $this->sections[$name];
    }

    /**
     * Persists all section files + the sitemap index.
     * Skips writing when cache is enabled and all files are still fresh.
     *
     * @return bool True when files were (re)written, false when skipped due to cache.
     */
    public function save(): bool
    {
        $sections = array_values($this->sections);

        if ($this->cache->allSectionsValid(...$sections)) {
            return false; // cache hit, nic nepřepisujeme
        }

        $this->ensureOutputDir();

        foreach ($sections as $section) {
            file_put_contents($section->getFullPath(), $section->render());
        }

        file_put_contents(
            rtrim($this->config->outputDir, '/') . '/' . $this->config->indexFilename,
            $this->renderIndex($sections),
        );

        return true;
    }

    /**
     * Returns the sitemap index XML as a string (without writing to disk).
     * Useful for serving dynamically or for tests.
     */
    public function renderIndex(array $sections = []): string
    {
        if (empty($sections)) {
            $sections = array_values($this->sections);
        }

        $xml = new \SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?>' .
            '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>'
        );

        foreach ($sections as $section) {
            $entry = $xml->addChild('sitemap');
            $entry->addChild('loc', htmlspecialchars($section->getUrl(), ENT_XML1));
            $lastmod = $section->getLastmod();
            if ($lastmod !== null) {
                $entry->addChild('lastmod', $lastmod);
            }
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        return $dom->saveXML();
    }

    // -----------------------------------------------------------------------

    private function ensureOutputDir(): void
    {
        if (!is_dir($this->config->outputDir)) {
            mkdir($this->config->outputDir, 0755, true);
        }
    }
}
