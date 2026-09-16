<?php

declare(strict_types=1);

namespace Sitemap\Contract;

use Sitemap\SitemapSection;

interface SitemapRendererInterface
{
    /**
     * Renders a single section as a <urlset> XML string.
     */
    public function renderSection(SitemapSection $section): string;

    /**
     * Renders the sitemap index as a <sitemapindex> XML string.
     *
     * @param SitemapSection[] $sections
     */
    public function renderIndex(array $sections): string;
}
