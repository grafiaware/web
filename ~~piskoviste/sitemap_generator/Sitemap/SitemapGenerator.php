<?php

declare(strict_types=1);

namespace Sitemap;

use Psr\Log\LoggerInterface;
use Sitemap\Contract\SitemapCacheInterface;
use Sitemap\Contract\SitemapGeneratorInterface;
use Sitemap\Contract\SitemapRendererInterface;
use Sitemap\Contract\SitemapWriterInterface;

/**
 * Generates an incremental sitemap index + per-section sitemap files.
 *
 * All dependencies are injected — wire them from your DI container:
 *
 *   new SitemapGenerator(
 *       config:   $container->get(SitemapConfig::class),
 *       cache:    $container->get(SitemapCacheInterface::class),
 *       renderer: $container->get(SitemapRendererInterface::class),
 *       writer:   $container->get(SitemapWriterInterface::class),
 *       logger:   $container->get(LoggerInterface::class),
 *   )
 */
final class SitemapGenerator implements SitemapGeneratorInterface
{
    /** @var array<string, SitemapSection> */
    private array $sections = [];

    public function __construct(
        private readonly SitemapConfig $config,
        private readonly SitemapCacheInterface $cache,
        private readonly SitemapRendererInterface $renderer,
        private readonly SitemapWriterInterface $writer,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * Returns (or lazily creates) a named section.
     * Calling with the same name multiple times returns the same instance,
     * so URLs can be added incrementally from different parts of the application.
     */
    public function section(string $name): SitemapSection
    {
        if (!isset($this->sections[$name])) {
            $this->sections[$name] = new SitemapSection($name, $this->config);
            $this->logger->debug('Sitemap section created.', ['section' => $name]);
        }
        return $this->sections[$name];
    }

    /**
     * Persists all section files + the sitemap index.
     *
     * @return bool True when files were written; false when cache was valid.
     * @throws \RuntimeException on write failure (propagated from writer)
     */
    public function save(): bool
    {
        $sections = array_values($this->sections);

        if ($sections === []) {
            $this->logger->warning('SitemapGenerator::save() called with no sections registered.');
            return false;
        }

        if ($this->cache->allSectionsValid($this->config->indexPath(), ...$sections)) {
            $this->logger->info('Sitemap cache is valid, skipping regeneration.', [
                'index' => $this->config->indexPath(),
            ]);
            return false;
        }

        $this->logger->info('Generating sitemap files.', [
            'sections' => array_keys($this->sections),
            'cacheEnabled' => $this->config->cacheEnabled,
        ]);

        foreach ($sections as $section) {
            $xml = $this->renderer->renderSection($section);
            $this->writer->write($section->getFullPath(), $xml);

            $this->logger->debug('Section sitemap written.', [
                'section' => $section->name,
                'path'    => $section->getFullPath(),
                'urls'    => $section->count(),
            ]);
        }

        $indexXml = $this->renderer->renderIndex($sections);
        $this->writer->write($this->config->indexPath(), $indexXml);

        $this->logger->info('Sitemap index written.', [
            'path'     => $this->config->indexPath(),
            'sections' => count($sections),
        ]);

        return true;
    }
}
