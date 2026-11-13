<?php

namespace StaticRegistry\Service;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use Site\ConfigurationCache;

/**
 * Projde filesystem pod componentControler['static'] a vrátí adresáře s template.php.
 *
 * Běží na auth/events serveru — red editor si seznam stáhne přes GET /static/templates.
 * PHP šablony se na remote server dostanou git deployem, ne runtime syncem.
 *
 * @author pes2704
 */
class StaticTemplateScanner implements StaticTemplateScannerInterface {

    /**
     * {@inheritdoc}
     *
     * path = nadřazená složka od WEB_STATIC včetně koncového lomítka (events/, events/admin/, nebo '').
     * template = jméno složky, ve které leží template.php (profil-firmy).
     * Stejné rozdělení jako sloupce static.path a static.template v baseline.
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
            $location = StaticTemplateLocation::fromDirectory($this->relativePath($basePath, $fullDir));
            $templates[] = [
                'path' => $location['path'],
                'template' => $location['template'],
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
