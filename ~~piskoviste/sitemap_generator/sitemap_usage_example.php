<?php

declare(strict_types=1);

/**
 * Example: wiring SitemapGenerator from a DI container.
 *
 * This file shows two patterns:
 *   1. Plain PHP manual wiring (no framework)
 *   2. Registration snippet for Nette DI (config.neon)
 *
 * The generator itself has no knowledge of any framework —
 * swap the DI wiring for Symfony, Laravel, or your own container as needed.
 */

require_once __DIR__ . '/vendor/autoload.php'; // or your framework's bootstrap

use Psr\Log\NullLogger;
use Sitemap\Cache\FilesystemSitemapCache;
use Sitemap\Contract\SitemapCacheInterface;
use Sitemap\Contract\SitemapGeneratorInterface;
use Sitemap\Contract\SitemapRendererInterface;
use Sitemap\Contract\SitemapWriterInterface;
use Sitemap\Renderer\XmlSitemapRenderer;
use Sitemap\SitemapConfig;
use Sitemap\SitemapGenerator;
use Sitemap\Writer\FilesystemSitemapWriter;

// ---------------------------------------------------------------------------
// 1. Manual DI wiring (plain PHP)
// ---------------------------------------------------------------------------

$config = new SitemapConfig(
    baseUrl:      'https://example.com',
    outputDir:    __DIR__ . '/public',
    indexFilename: 'sitemap.xml',

    // false → always regenerate (ideal for deploy hooks or on-edit triggers)
    // true  → skip regeneration when files are younger than cacheTtl seconds
    cacheEnabled: false,
    cacheTtl:     3600,
);

// Replace NullLogger with your PSR-3 compatible logger (Monolog, etc.)
$logger = new NullLogger();

/** @var SitemapGeneratorInterface $generator */
$generator = new SitemapGenerator(
    config:   $config,
    cache:    new FilesystemSitemapCache($config),
    renderer: new XmlSitemapRenderer(),
    writer:   new FilesystemSitemapWriter(),
    logger:   $logger,
);

// ---------------------------------------------------------------------------
// 2. Nette DI — config.neon snippet
// ---------------------------------------------------------------------------
//
// services:
//     sitemap.config:
//         factory: Sitemap\SitemapConfig(
//             baseUrl:      %siteUrl%,
//             outputDir:    %wwwDir%,
//             cacheEnabled: %sitemap.cacheEnabled%,
//             cacheTtl:     %sitemap.cacheTtl%
//         )
//
//     sitemap.cache:
//         factory:    Sitemap\Cache\FilesystemSitemapCache(@sitemap.config)
//         implements: Sitemap\Contract\SitemapCacheInterface
//
//     sitemap.renderer:
//         factory:    Sitemap\Renderer\XmlSitemapRenderer
//         implements: Sitemap\Contract\SitemapRendererInterface
//
//     sitemap.writer:
//         factory:    Sitemap\Writer\FilesystemSitemapWriter
//         implements: Sitemap\Contract\SitemapWriterInterface
//
//     sitemap.generator:
//         factory:    Sitemap\SitemapGenerator(
//             config:   @sitemap.config,
//             cache:    @sitemap.cache,
//             renderer: @sitemap.renderer,
//             writer:   @sitemap.writer,
//             logger:   @logger  # your PSR-3 service
//         )
//         implements: Sitemap\Contract\SitemapGeneratorInterface

// ---------------------------------------------------------------------------
// Usage — identical regardless of how DI is wired
// ---------------------------------------------------------------------------

// Static pages
$generator->section('pages')
    ->add('/',         '2025-06-01', 'daily',   1.0)
    ->add('/about',    '2025-03-10', 'monthly', 0.4)
    ->add('/contact',  '2025-01-20', 'monthly', 0.3);

// Blog — dynamic, lastmod from DB
$articles = $database->fetchAll('SELECT slug, updated_at FROM articles WHERE published = 1');
foreach ($articles as $article) {
    $generator->section('blog')->add(
        '/blog/' . $article['slug'],
        $article['updated_at'],   // real lastmod from DB, NOT date('Y-m-d')
        'weekly',
        0.7,
    );
}

// Products
$products = $database->fetchAll('SELECT slug, updated_at FROM products WHERE active = 1');
foreach ($products as $product) {
    $generator->section('products')->add(
        '/produkty/' . $product['slug'],
        $product['updated_at'],
        'weekly',
        0.8,
    );
}

// ---------------------------------------------------------------------------
// Save — call this once at the end of a deploy hook or after content edit
// ---------------------------------------------------------------------------
$written = $generator->save();

// $written === true  → files regenerated
// $written === false → cache was valid or no sections registered
