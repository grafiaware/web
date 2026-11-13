<?php

namespace StaticRegistry\Service;

/**
 * Rozdělení adresáře se šablonou na path a template tak, jak jsou uložené v tabulce static.
 *
 * Soubor local/site/{site}/static/events/profil-firmy/template.php
 * odpovídá path "events/" a template "profil-firmy".
 * Šablona přímo pod static/ (zkouska-mailu/template.php) má path "" a template "zkouska-mailu".
 */
final class StaticTemplateLocation {

    /**
     * @return array{path: string, template: string}
     */
    public static function fromDirectory(string $relativeDirectory): array {
        $relativeDirectory = trim(str_replace('\\', '/', $relativeDirectory), '/');
        if ($relativeDirectory === '') {
            return ['path' => '', 'template' => ''];
        }
        $template = basename($relativeDirectory);
        $parent = str_replace('\\', '/', dirname($relativeDirectory));
        $path = ($parent === '.' || $parent === '' || $parent === '/') ? '' : rtrim($parent, '/') . '/';
        return ['path' => $path, 'template' => $template];
    }

    public static function encode(string $path, string $template): string {
        $json = json_encode(
            ['path' => $path, 'template' => $template],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        return $json === false ? '' : $json;
    }

    /**
     * @return array{path: string, template: string}|null
     */
    public static function decode(string $encoded): ?array {
        try {
            $decoded = json_decode($encoded, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }
        if (!is_array($decoded) || !array_key_exists('path', $decoded) || !array_key_exists('template', $decoded)) {
            return null;
        }
        return [
            'path' => (string) $decoded['path'],
            'template' => (string) $decoded['template'],
        ];
    }
}
