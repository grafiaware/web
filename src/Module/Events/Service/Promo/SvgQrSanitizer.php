<?php

namespace Events\Service\Promo;

/**
 * F3 (#47, docs/vp/zadani/F1.md): vyčištění SVG s QR kódem z QRdirectoru před uložením.
 *
 * Ponechá jen tvary (svg, g, path, rect, polygon, polyline, circle, ellipse, line) a jejich geometrické a barevné
 * atributy. Barvy zadané stylem (atribut style, nebo třída z <style> ve tvaru .trida{fill:…}) převede na atributy,
 * aby se QR nezměnil (bílé pozadí nezčerná). Vše ostatní zahodí: skripty, foreignObject, obrázky, odkazy (a, use, href), styly, animace, události on*,
 * hodnoty s url( nebo javascript:. Externí entity a DTD se nenačítají. Výsledek je samostatný dokument <svg>
 * s viewBox, který jde bezpečně zobrazit a vložit do PDF.
 */
final class SvgQrSanitizer {

    public const MAX_BYTES = 1048576;        // 1 MB – SVG z QRdirectoru má desítky kB
    private const MAX_ELEMENTS = 20000;

    /** Vlastnosti, které se ze stylu převedou na atributy. */
    private const STYLE_PROPS = ['fill', 'fill-rule', 'fill-opacity', 'stroke', 'stroke-width', 'stroke-opacity', 'opacity', 'clip-rule'];

    private const SHAPES = ['path', 'rect', 'polygon', 'polyline', 'circle', 'ellipse', 'line'];
    private const CONTAINERS = ['svg', 'g'];

    private const COMMON_ATTRS = ['fill', 'fill-rule', 'fill-opacity', 'stroke', 'stroke-width', 'stroke-linecap',
        'stroke-linejoin', 'stroke-opacity', 'opacity', 'transform', 'shape-rendering', 'clip-rule'];
    private const ATTRS = [
        'svg' => ['viewBox', 'width', 'height', 'preserveAspectRatio'],
        'g' => [],
        'path' => ['d'],
        'rect' => ['x', 'y', 'width', 'height', 'rx', 'ry'],
        'polygon' => ['points'],
        'polyline' => ['points'],
        'circle' => ['cx', 'cy', 'r'],
        'ellipse' => ['cx', 'cy', 'rx', 'ry'],
        'line' => ['x1', 'y1', 'x2', 'y2'],
    ];

    /** Barva: #rgb, #rrggbb, slovo (black, white, none, currentColor…), rgb()/rgba() s čísly. */
    private const COLOR = '/^(#[0-9a-fA-F]{3,8}|[a-zA-Z]{1,20}|rgba?\(\s*[\d.%\s,]+\))$/';
    /** Číslo / délka / seznam čísel (viewBox, points, d, transform) – jen čísla, písmena příkazů a oddělovače. */
    private const GEOMETRY = '/^[0-9a-zA-Z.,+\-\s()%]*$/';

    /**
     * @return array{svg: ?string, error: ?string, shapes: int}
     */
    public static function sanitize(string $input): array {
        if (strlen($input) > self::MAX_BYTES) {
            return self::fail('Soubor je větší než 1 MB.');
        }
        $input = preg_replace('/^\xEF\xBB\xBF/', '', $input) ?? $input;
        if (trim($input) === '' || stripos($input, '<svg') === false) {
            return self::fail('Soubor není SVG.');
        }
        if (preg_match('/<!DOCTYPE|<!ENTITY/i', $input)) {
            return self::fail('SVG obsahuje DTD nebo entity – takový soubor nepřijímáme. Exportujte SVG z QRdirectoru znovu.');
        }
        $src = new \DOMDocument();
        $prev = libxml_use_internal_errors(true);
        $ok = $src->loadXML($input, LIBXML_NONET | LIBXML_NOBLANKS | LIBXML_COMPACT);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);
        if (!$ok || $src->documentElement === null || strtolower($src->documentElement->localName) !== 'svg') {
            return self::fail('SVG nejde přečíst (poškozený soubor).');
        }
        $root = $src->documentElement;
        $viewBox = self::viewBox($root);
        if ($viewBox === null) {
            return self::fail('SVG nemá rozměry (viewBox ani width/height).');
        }

        $out = new \DOMDocument('1.0', 'UTF-8');
        $svg = $out->createElementNS('http://www.w3.org/2000/svg', 'svg');
        $svg->setAttribute('viewBox', $viewBox);
        foreach (['width', 'height', 'preserveAspectRatio'] as $a) {
            $v = self::cleanAttr('svg', $a, $root->getAttribute($a));
            if ($v !== null) {
                $svg->setAttribute($a, $v);
            }
        }
        foreach (self::COMMON_ATTRS as $a) {
            $v = self::cleanAttr('svg', $a, $root->getAttribute($a));
            if ($v !== null) {
                $svg->setAttribute($a, $v);
            }
        }
        $out->appendChild($svg);
        $count = ['elements' => 0, 'shapes' => 0];
        $classes = self::classStyles($src);
        self::copyChildren($root, $svg, $out, $count, 0, $classes);
        if ($count['elements'] > self::MAX_ELEMENTS) {
            return self::fail('SVG má příliš mnoho prvků – nevypadá jako QR kód.');
        }
        if ($count['shapes'] === 0) {
            return self::fail('V SVG nejsou žádné tvary QR kódu.');
        }
        return ['svg' => $out->saveXML($svg), 'error' => null, 'shapes' => $count['shapes']];
    }

    private static function fail(string $error): array {
        return ['svg' => null, 'error' => $error, 'shapes' => 0];
    }

    /**
     * Pravidla .trida{vlastnost:hodnota} ze všech <style> (jen jednoduché selektory tříd).
     *
     * @return array<string, array<string, string>> třída => [vlastnost => hodnota]
     */
    private static function classStyles(\DOMDocument $doc): array {
        $map = [];
        foreach ($doc->getElementsByTagName('style') as $style) {
            $css = preg_replace('#/\*.*?\*/#s', '', (string) $style->textContent) ?? '';
            if (preg_match_all('/((?:\.[A-Za-z0-9_-]+\s*,?\s*)+)\{([^}]*)\}/', $css, $m, PREG_SET_ORDER)) {
                foreach ($m as $rule) {
                    $decl = self::declarations($rule[2]);
                    preg_match_all('/\.([A-Za-z0-9_-]+)/', $rule[1], $cls);
                    foreach ($cls[1] as $c) {
                        $map[$c] = array_replace($map[$c] ?? [], $decl);
                    }
                }
            }
        }
        return $map;
    }

    /** Deklarace „fill: #000; stroke: none“ → [vlastnost => hodnota] jen pro STYLE_PROPS. */
    private static function declarations(string $css): array {
        $out = [];
        foreach (explode(';', $css) as $d) {
            $p = explode(':', $d, 2);
            if (count($p) === 2) {
                $k = strtolower(trim($p[0]));
                if (in_array($k, self::STYLE_PROPS, true)) {
                    $out[$k] = trim(preg_replace('/\s*!important\s*$/i', '', $p[1]) ?? '');
                }
            }
        }
        return $out;
    }

    private static function copyChildren(\DOMElement $from, \DOMElement $to, \DOMDocument $out, array &$count, int $depth, array $classes): void {
        if ($depth > 20) {
            return;
        }
        foreach ($from->childNodes as $node) {
            if (!$node instanceof \DOMElement) {
                continue;   // text, komentáře, CDATA, instrukce – pryč
            }
            if (++$count['elements'] > self::MAX_ELEMENTS) {
                return;
            }
            $name = $node->localName;
            if ($node->namespaceURI !== null && $node->namespaceURI !== 'http://www.w3.org/2000/svg') {
                continue;   // cizí jmenné prostory (metadata editorů, xhtml)
            }
            $isShape = in_array($name, self::SHAPES, true);
            if (!$isShape && $name !== 'g') {
                continue;   // script, style, a, use, image, foreignObject, defs, animate, text, title…
            }
            $el = $out->createElementNS('http://www.w3.org/2000/svg', $name);
            // pořadí přednosti jako v CSS: atribut < třída < style=""; nebezpečná hodnota se zahodí a platí nižší úroveň
            $clean = function (array $raw) use ($name): array {
                $ok = [];
                foreach ($raw as $a => $v) {
                    $v = self::cleanAttr($name, $a, (string) $v);
                    if ($v !== null) {
                        $ok[$a] = $v;
                    }
                }
                return $ok;
            };
            $values = [];
            foreach (array_merge(self::ATTRS[$name], self::COMMON_ATTRS) as $a) {
                if ($node->hasAttribute($a)) {
                    $values[$a] = $node->getAttribute($a);
                }
            }
            $values = $clean($values);
            foreach (preg_split('/\s+/', trim($node->getAttribute('class'))) ?: [] as $c) {
                $values = array_replace($values, $clean($classes[$c] ?? []));
            }
            $values = array_replace($values, $clean(self::declarations($node->getAttribute('style'))));
            foreach ($values as $a => $v) {
                $el->setAttribute($a, $v);
            }
            if ($isShape) {
                $count['shapes']++;
                $to->appendChild($el);
            } else {
                self::copyChildren($node, $el, $out, $count, $depth + 1, $classes);
                if ($el->hasChildNodes()) {
                    $to->appendChild($el);
                }
            }
        }
    }

    /** Hodnota atributu, nebo null (zahodit). */
    private static function cleanAttr(string $element, string $attr, string $value): ?string {
        $value = trim($value);
        if ($value === '' || strlen($value) > 200000 || preg_match('/url\s*\(|javascript:|expression\s*\(|[<>"\'\\\\;{}]/i', $value)) {
            return null;
        }
        if (in_array($attr, ['fill', 'stroke'], true)) {
            return preg_match(self::COLOR, $value) ? $value : null;
        }
        if (in_array($attr, ['fill-rule', 'clip-rule', 'stroke-linecap', 'stroke-linejoin', 'shape-rendering', 'preserveAspectRatio'], true)) {
            return preg_match('/^[a-zA-Z\- ]{1,40}$/', $value) ? $value : null;
        }
        return preg_match(self::GEOMETRY, $value) ? $value : null;
    }

    /** viewBox z kořene, nebo z width/height (jen čísla, případně px). */
    private static function viewBox(\DOMElement $root): ?string {
        $vb = trim($root->getAttribute('viewBox'));
        if ($vb !== '' && preg_match('/^-?[\d.]+([\s,]+-?[\d.]+){3}$/', $vb)) {
            $p = preg_split('/[\s,]+/', $vb);
            if ((float) $p[2] > 0 && (float) $p[3] > 0) {
                return implode(' ', $p);
            }
        }
        $w = (float) preg_replace('/px$/', '', trim($root->getAttribute('width')));
        $h = (float) preg_replace('/px$/', '', trim($root->getAttribute('height')));
        return $w > 0 && $h > 0 ? "0 0 {$w} {$h}" : null;
    }

    /** Je adresa veřejná https adresa daného webu? (pro ověření QR) */
    public static function isPublicUrl(?string $url, string $publicBase): bool {
        if ($url === null || $url === '') {
            return false;
        }
        $u = parse_url($url);
        $b = parse_url($publicBase);
        return ($u['scheme'] ?? '') === 'https' && isset($u['host'], $b['host']) && strtolower($u['host']) === strtolower($b['host']);
    }
}
