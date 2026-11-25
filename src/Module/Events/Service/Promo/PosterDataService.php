<?php

namespace Events\Service\Promo;

use Events\Model\Dao\Promo\PromoLinkDao;
use Events\Model\Dao\Promo\PromoQrSvgDao;

/**
 * F3 (#47, docs/vp/zadani/F1.md): QR kódy z QRdirectoru (SVG) u odkazu a data podkladu plakátu pro grafika.
 *
 * Podklad plakátu = samostatné objekty bez designu (PDF skládá prohlížeč, lottery-paper-pdf.js / promo-poster-pdf.js):
 * QR kódy (web vektorově z adresy, QRdirector jako vyčištěné SVG) a textové bloky z DB – text plakátu, krátká adresa
 * k opsání, u odkazu firmy název, adresa a zveřejněné sociální sítě a web (company_to_network + network).
 */
class PosterDataService {

    /** Kanál QR webu v podkladu plakátu – tištěný plakát se počítá jako plakát, ne jako QR na obrazovce. */
    public const POSTER_CHANNEL = 'plakat';
    public const FORMATS = ['A4', 'A3'];

    public function __construct(
        private PromoLinkDao $linkDao,
        private PromoQrSvgDao $svgDao,
        private PromoLinkService $links) {
    }

    // ---------------------------------------------------------------- SVG z QRdirectoru

    /** SVG u odkazu (bez obsahu SVG – jen pro seznam). */
    public function svgList(int $linkId): array {
        return $this->svgDao->rows("SELECT id, link_id_fk, channel, label, decoded_url, created, created_by, LENGTH(svg) AS bytes
                                    FROM promo_qr_svg WHERE link_id_fk = :id ORDER BY id", ['id' => $linkId]);
    }

    /** SVG u odkazu i s obsahem (podklad plakátu, náhledy). */
    public function svgs(int $linkId): array {
        return $this->svgDao->rows("SELECT * FROM promo_qr_svg WHERE link_id_fk = :id ORDER BY id", ['id' => $linkId]);
    }

    /** Všechna nahraná SVG (ověření QR) s kódem odkazu. */
    public function allSvgs(): array {
        return $this->svgDao->rows("SELECT s.*, l.code, l.name AS link_name FROM promo_qr_svg s JOIN promo_link l ON l.id = s.link_id_fk
                                    ORDER BY l.name, s.id");
    }

    /**
     * Nahraje SVG z QRdirectoru: vyčistí, zkontroluje kanál a adresu přečtenou v prohlížeči.
     *
     * @return array{id: ?int, errors: string[], warnings: string[]}
     */
    public function addSvg(int $linkId, string $channel, string $label, string $raw, ?string $decodedUrl, string $by): array {
        $errors = [];
        $warnings = [];
        $channels = $this->links->channels();
        $channel = strtolower(trim($channel));
        if (!isset($channels[$channel])) {
            $errors[] = 'Zvolte kanál QR kódu (např. billboard, leták).';
        }
        $label = trim($label);
        if ($label === '') {
            $label = $channels[$channel]['label'] ?? $channel;
        }
        if (mb_strlen($label) > 100) {
            $errors[] = 'Popisek může mít nejvýš 100 znaků.';
        }
        $clean = SvgQrSanitizer::sanitize($raw);
        if ($clean['error'] !== null) {
            $errors[] = $clean['error'];
        }
        $decodedUrl = self::normalizeDecoded($decodedUrl);
        if ($errors) {
            return ['id' => null, 'errors' => $errors, 'warnings' => []];
        }
        if ($decodedUrl === null) {
            $warnings[] = 'QR kód se v prohlížeči nepodařilo přečíst – ověřte ho skenem telefonem.';
        }
        $this->svgDao->exec("INSERT INTO promo_qr_svg (link_id_fk, channel, label, svg, decoded_url, created, created_by)
                             VALUES (:link, :channel, :label, :svg, :url, :created, :by)",
            ['link' => $linkId, 'channel' => $channel, 'label' => $label, 'svg' => $clean['svg'], 'url' => $decodedUrl,
             'created' => $this->links->config()->now()->format('Y-m-d H:i:s'), 'by' => $by]);
        return ['id' => $this->svgDao->lastId(), 'errors' => [], 'warnings' => $warnings];
    }

    public function deleteSvg(int $linkId, int $svgId): bool {
        return $this->svgDao->exec("DELETE FROM promo_qr_svg WHERE id = :id AND link_id_fk = :link", ['id' => $svgId, 'link' => $linkId]) > 0;
    }

    /** Adresa přečtená z QR v prohlížeči: jen http(s), nejvýš 1000 znaků, jinak null. */
    public static function normalizeDecoded(?string $url): ?string {
        $url = trim((string) $url);
        if ($url === '' || strlen($url) > 1000 || !preg_match('#^https?://[^\s<>"]+$#i', $url)) {
            return null;
        }
        return $url;
    }

    /**
     * Ukazuje QR z QRdirectoru (jeho adresa) na náš odkaz? QRdirector přesměruje sám – ověří se skenem telefonem.
     * Vrací 'link' (adresa je přímo náš odkaz s kanálem), 'external' (jiný web, např. QRdirector), 'unknown' (nepřečteno).
     */
    public static function decodedKind(?string $decodedUrl, string $publicBase, string $code): string {
        if ($decodedUrl === null || $decodedUrl === '') {
            return 'unknown';
        }
        $path = (string) parse_url($decodedUrl, PHP_URL_PATH);
        if (SvgQrSanitizer::isPublicUrl($decodedUrl, $publicBase)
                && preg_match('#/' . preg_quote(PromoLinkService::ROUTE, '#') . preg_quote($code, '#') . '(/[a-z0-9-]*)?/?$#i', $path)) {
            return 'link';
        }
        return 'external';
    }

    // ---------------------------------------------------------------- oprávnění

    /** Je přihlášený zástupcem firmy? (promo kit, QR PNG, podklad plakátu odkazu firmy) */
    public function representsCompany(string $loginName, ?int $companyId): bool {
        if ($loginName === '' || !$companyId) {
            return false;
        }
        return $this->linkDao->row("SELECT 1 AS ok FROM representative WHERE login_login_name = :login AND company_id = :company",
            ['login' => $loginName, 'company' => $companyId]) !== null;
    }

    // ---------------------------------------------------------------- podklad plakátu

    /**
     * Data podkladu plakátu pro PDF v prohlížeči.
     *
     * @param string $absoluteBase veřejná adresa webu končící lomítkem (https://…/)
     * @param bool $withSvg přidat SVG z QRdirectoru (media manager ano, zástupce firmy jen QR webu – SVG jsou pro tisk organizátora)
     */
    public function posterData(array $link, string $absoluteBase, bool $withSvg): array {
        $texts = $this->links->texts($link);
        $linkUrl = $absoluteBase . PromoLinkService::ROUTE . $link['code'];
        $short = preg_replace('#^https?://#i', '', $linkUrl);
        $fill = fn(string $t) => strtr($t, ['{url}' => $short, '{name}' => (string) $link['name']]);
        $objects = [];
        $objects[] = ['kind' => 'qr-web', 'label' => 'QR – web (kanál ' . self::POSTER_CHANNEL . ')',
                      'url' => $linkUrl . '/' . self::POSTER_CHANNEL];
        if ($withSvg) {
            foreach ($this->svgs((int) $link['id']) as $s) {
                $objects[] = ['kind' => 'qr-svg', 'label' => 'QR – ' . $s['label'] . ' (QRdirector, kanál ' . $s['channel'] . ')',
                              'svg' => (string) $s['svg'], 'url' => $s['decoded_url']];
            }
        }
        $objects[] = ['kind' => 'text', 'label' => 'Text plakátu', 'text' => $fill((string) ($texts['poster'] ?? ''))];
        $objects[] = ['kind' => 'text', 'label' => 'Adresa k opsání', 'text' => $short];
        if (!empty($link['company_id_fk'])) {
            foreach ($this->companyBlocks((int) $link['company_id_fk']) as $block) {
                $objects[] = $block;
            }
        }
        return [
            'code' => (string) $link['code'],
            'name' => (string) $link['name'],
            'formats' => self::FORMATS,
            'objects' => array_values(array_filter($objects, fn($o) => $o['kind'] !== 'text' || trim($o['text']) !== '')),
        ];
    }

    /** Textové bloky firmy: název, adresa, zveřejněné sítě a web (tak, jak jsou zadané – kontrola adres je #147). */
    public function companyBlocks(int $companyId): array {
        $company = $this->linkDao->row("SELECT c.name, a.name AS addr_name, a.lokace, a.psc, a.obec
                                        FROM company c LEFT JOIN company_address a ON a.company_id = c.id WHERE c.id = :id",
            ['id' => $companyId]);
        if ($company === null) {
            return [];
        }
        $blocks = [['kind' => 'text', 'label' => 'Název firmy', 'text' => (string) $company['name']]];
        $address = self::address($company);
        if ($address !== '') {
            $blocks[] = ['kind' => 'text', 'label' => 'Adresa firmy', 'text' => $address];
        }
        foreach ($this->networks($companyId) as $n) {
            $blocks[] = ['kind' => 'text', 'label' => $n['title'], 'text' => $n['link']];
        }
        return $blocks;
    }

    /** Zveřejněné sítě a web firmy v pořadí tabulky network. */
    public function networks(int $companyId): array {
        try {
            return $this->linkDao->rows("SELECT n.title, n.icon, n.channel, ctn.link FROM company_to_network ctn
                                         JOIN network n ON n.id = ctn.network_id
                                         WHERE ctn.company_id = :id AND ctn.published = 1 AND ctn.link IS NOT NULL AND ctn.link <> ''
                                         ORDER BY n.id", ['id' => $companyId]);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function address(array $company): string {
        $parts = [trim((string) ($company['addr_name'] ?? '')), trim((string) ($company['lokace'] ?? '')),
                  trim(trim((string) ($company['psc'] ?? '')) . ' ' . trim((string) ($company['obec'] ?? '')))];
        $parts = array_values(array_unique(array_filter($parts, fn($p) => $p !== '')));
        return implode(', ', $parts);
    }
}
