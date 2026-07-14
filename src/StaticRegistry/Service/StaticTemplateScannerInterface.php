<?php

namespace StaticRegistry\Service;

interface StaticTemplateScannerInterface {

    /**
     * @return array{siteCode: string, prefix: string, basePath: string, templates: array<int, array{path: string, template: string, fullTemplatePath: string}>}
     */
    public function scan(string $prefix, string $siteCode): array;
}
