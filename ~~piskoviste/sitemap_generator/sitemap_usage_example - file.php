<?php

require_once 'SitemapGenerator.php';

// ---------------------------------------------------------------------------
// Konfigurace
// ---------------------------------------------------------------------------

$config = new SitemapConfig(
    baseUrl: 'https://example.com',
    outputDir: __DIR__ . '/public',

    // false  → soubory se vždy přepíší (ideální pro generování při deployi / editaci)
    // true   → soubory se přepíší jen pokud jsou starší než $cacheTtl sekund
    cacheEnabled: false,
    cacheTtl: 3600,
);

$generator = new SitemapGenerator($config);

// ---------------------------------------------------------------------------
// Sekce: statické stránky
// ---------------------------------------------------------------------------

$generator->section('pages')
    ->add('/',          '2025-06-01', 'daily',   1.0)
    ->add('/about',     '2025-03-10', 'monthly', 0.4)
    ->add('/contact',   '2025-01-20', 'monthly', 0.3)
    ->add('/services',  '2025-05-01', 'monthly', 0.6);

// ---------------------------------------------------------------------------
// Sekce: blog – dynamicky z databáze
// ---------------------------------------------------------------------------

// Simulace dat z DB (v reálu by bylo: $articles = $db->query('SELECT slug, updated_at FROM articles WHERE published = 1'))
$articles = [
    ['slug' => 'jak-pouzivat-sitemap',   'updated_at' => '2025-06-10'],
    ['slug' => 'php-tipy-pro-framework', 'updated_at' => '2025-05-22'],
    ['slug' => 'rest-api-best-practices','updated_at' => '2025-04-15'],
];

$blogSection = $generator->section('blog');

foreach ($articles as $article) {
    $blogSection->add(
        '/blog/' . $article['slug'],
        $article['updated_at'],  // skutečný lastmod z DB, ne datum generování!
        'weekly',
        0.7,
    );
}

// ---------------------------------------------------------------------------
// Sekce: produkty / e-shop (příklad další sekce)
// ---------------------------------------------------------------------------

$products = [
    ['slug' => 'produkt-alfa', 'updated_at' => '2025-06-08'],
    ['slug' => 'produkt-beta', 'updated_at' => '2025-06-01'],
];

$productSection = $generator->section('products');

foreach ($products as $product) {
    $productSection->add(
        '/produkty/' . $product['slug'],
        $product['updated_at'],
        'weekly',
        0.8,
    );
}

// ---------------------------------------------------------------------------
// Uložení souborů
// ---------------------------------------------------------------------------

$written = $generator->save();

if ($written) {
    echo "Sitemap vygenerována:" . PHP_EOL;
    echo "  public/sitemap.xml          (index)" . PHP_EOL;
    echo "  public/sitemap-pages.xml    (" . count($generator->section('pages')->getUrls()) . " URL)" . PHP_EOL;
    echo "  public/sitemap-blog.xml     (" . count($generator->section('blog')->getUrls()) . " URL)" . PHP_EOL;
    echo "  public/sitemap-products.xml (" . count($generator->section('products')->getUrls()) . " URL)" . PHP_EOL;
} else {
    echo "Cache je platná, soubory nebyly přepsány." . PHP_EOL;
}


// ---------------------------------------------------------------------------
// Alternativa: dynamické servírování bez zápisu na disk
// (pokud nechceš statické soubory, ale endpoint /sitemap.xml)
// ---------------------------------------------------------------------------

// Příklad PHP routy:
//
// if ($request->path === '/sitemap.xml') {
//     header('Content-Type: application/xml; charset=utf-8');
//     echo $generator->renderIndex();
//     exit;
// }
//
// if (preg_match('/^\/sitemap-(\w+)\.xml$/', $request->path, $m)) {
//     $section = $generator->section($m[1]);
//     header('Content-Type: application/xml; charset=utf-8');
//     echo $section->render();
//     exit;
// }
