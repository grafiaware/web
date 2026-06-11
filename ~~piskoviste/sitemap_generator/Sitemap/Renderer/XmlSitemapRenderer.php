<?php

declare(strict_types=1);

namespace Sitemap\Renderer;

use Sitemap\Contract\SitemapRendererInterface;
use Sitemap\SitemapSection;

/**
 * Renders sitemap XML using SimpleXMLElement + DOMDocument for pretty-printing.
 */
final class XmlSitemapRenderer implements SitemapRendererInterface
{
    private const URLSET_NS   = 'http://www.sitemaps.org/schemas/sitemap/0.9';
    private const INDEX_NS    = 'http://www.sitemaps.org/schemas/sitemap/0.9';

    public function renderSection(SitemapSection $section): string
    {
        $xml = new \SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?>' .
            '<urlset xmlns="' . self::URLSET_NS . '"/>'
        );

        foreach ($section->getUrls() as $url) {
            $item = $xml->addChild('url');
            $item->addChild('loc', htmlspecialchars($url->loc, ENT_XML1));
            if ($url->lastmod !== null) {
                $item->addChild('lastmod', $url->lastmod);
            }
            $item->addChild('changefreq', $url->changefreq);
            $item->addChild('priority', number_format($url->priority, 1));
        }

        return $this->prettyPrint($xml->asXML());
    }

    public function renderIndex(array $sections): string
    {
        $xml = new \SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?>' .
            '<sitemapindex xmlns="' . self::INDEX_NS . '"/>'
        );

        foreach ($sections as $section) {
            $entry = $xml->addChild('sitemap');
            $entry->addChild('loc', htmlspecialchars($section->getUrl(), ENT_XML1));
            $lastmod = $section->getLastmod();
            if ($lastmod !== null) {
                $entry->addChild('lastmod', $lastmod);
            }
        }

        return $this->prettyPrint($xml->asXML());
    }

    private function prettyPrint(string $rawXml): string
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($rawXml);
        return $dom->saveXML();
    }
}
