<?php

namespace StaticRegistry\Service;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use Site\ConfigurationCache;

/**
 * Vrací seznam dostupných static šablon pro editor v red modulu.
 */
class StaticTemplateScanner implements StaticTemplateScannerInterface {

    /**
     * {@inheritdoc}
     */
    public function scan(string $prefix, string $siteCode): array {
        $basePath = ConfigurationCache::componentControler()['static'] ?? '';
        $scanRoot = rtrim($basePath, '/\\') . '/' . ltrim($prefix, '/');
        if (!is_dir($scanRoot)) {
            return [
                'siteCode' => $siteCode,
                'prefix' => $prefix,
                'basePath' => $basePath,
                'templates' => [],
            ];
        }

        $templates = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($scanRoot, FilesystemIterator::SKIP_DOTS)
        );
        $regex = new RegexIterator($iterator, '/template\.php$/i');
        foreach ($regex as $fileInfo) {
            /** @var \SplFileInfo $fileInfo */
            $fullDir = str_replace('\\', '/', $fileInfo->getPath());
            $relativeFromStatic = rtrim($this->relativePath($basePath, $fullDir), '/') . '/';
            $templates[] = [
                'path' => $relativeFromStatic,
                'template' => '',
                'fullTemplatePath' => str_replace('\\', '/', $fileInfo->getPathname()),
            ];
        }

        usort($templates, static function (array $a, array $b): int {
            return strcmp($a['path'] . $a['template'], $b['path'] . $b['template']);
        });

        return [
            'siteCode' => $siteCode,
            'prefix' => $prefix,
            'basePath' => $basePath,
            'templates' => $templates,
        ];
    }

    private function relativePath(string $basePath, string $fullDir): string {
        $base = rtrim(str_replace('\\', '/', $basePath), '/') . '/';
        $full = rtrim(str_replace('\\', '/', $fullDir), '/') . '/';
        if (str_starts_with($full, $base)) {
            return substr($full, strlen($base));
        }
        return $full;
    }

}
