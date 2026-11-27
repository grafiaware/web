<?php

namespace Events\Service\Promo;

use Auth\Service\Source\VisitSource;
use Events\Model\Dao\Promo\PromoLinkDao;
use Events\Model\Dao\Promo\PromoLinkVisitDao;

/**
 * F2 (#47, docs/vp/zadani/F1.md): trvalé odkazy /events/v1/r/<kód>[/<kanál>] – kód se nemění, cíl ano (historie),
 * počítadlo návštěv po dnech a kanálech, odkaz firmy pro promo kit, podklady ke zkopírování, přehled.
 *
 * Kanály: sociální sítě a web z tabulky network (sloupec channel), ostatní z konfigurace.
 * Neuvedený kanál se počítá jako „-“, neznámý jako „jiny“.
 */
class PromoLinkService {

    public const OWNER_TYPES = [
        'organizer' => 'Organizátor',
        'exhibitor' => 'Vystavovatel',
        'school' => 'Škola',
        'labour_office' => 'Úřad práce',
        'partner' => 'Partner',
    ];

    public const CHANNEL_NONE = '-';
    public const CHANNEL_OTHER = 'jiny';
    public const GROUPS = ['print' => 'Tisk', 'web' => 'Web', 'mail' => 'E-mail', 'social' => 'Sociální sítě', 'qr' => 'QR kód'];
    public const ROUTE = 'events/v1/r/';

    /** Roboti náhledů odkazů a vyhledávačů – přesměrují se, ale nepočítají. */
    private const BOT_PATTERN = '/bot|crawl|spider|slurp|facebookexternalhit|facebot|embedly|preview|whatsapp|telegram|skype|discord|'
        . 'pinterest|vkshare|quora link|outbrain|bitly|tumblr|nuzzel|redditbot|lighthouse|headless|curl|wget|python-requests|httpclient/i';

    private ?array $channels = null;

    public function __construct(
        private PromoLinkDao $linkDao,
        private PromoLinkVisitDao $visitDao,
        private PromoConfig $config) {
    }

    public function config(): PromoConfig {
        return $this->config;
    }

    // ---------------------------------------------------------------- pravidla (bez DB)

    public static function normalizeCode(?string $code): string {
        return strtolower(trim((string) $code));
    }

    public static function isValidCode(string $code): bool {
        return (bool) preg_match(VisitSource::CODE_PATTERN, $code);
    }

    /** Kód z názvu: bez diakritiky, malá písmena, pomlčky, 2–30 znaků, začíná písmenem. */
    public static function slug(string $name): string {
        $map = ['á' => 'a', 'č' => 'c', 'ď' => 'd', 'é' => 'e', 'ě' => 'e', 'í' => 'i', 'ň' => 'n', 'ó' => 'o', 'ř' => 'r',
            'š' => 's', 'ť' => 't', 'ú' => 'u', 'ů' => 'u', 'ý' => 'y', 'ž' => 'z', 'ä' => 'a', 'ö' => 'o', 'ü' => 'u',
            'ß' => 'ss', 'ł' => 'l', 'ľ' => 'l', 'ĺ' => 'l', 'ŕ' => 'r', 'ô' => 'o'];
        $s = strtr(mb_strtolower($name, 'UTF-8'), $map);
        if (function_exists('iconv')) {
            $t = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
            if ($t !== false) {
                $s = $t;
            }
        }
        // právní formy na konci nic neříkají (s.r.o., a.s., spol. s r.o.…)
        $s = preg_replace('/[\s,]+(spol\.?\s*s\s*r\.?\s*o\.?|s\.?\s*r\.?\s*o\.?|a\.?\s*s\.?|s\.?\s*p\.?|z\.?\s*u\.?|v\.?\s*o\.?\s*s\.?|o\.?\s*p\.?\s*s\.?)\s*$/', '', $s) ?? $s;
        $s = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($s)) ?? '', '-');
        $s = substr($s, 0, 30);
        $s = rtrim($s, '-');
        if ($s === '') {
            return 'odkaz';
        }
        if (!ctype_alpha($s[0])) {
            $s = rtrim(substr('f-' . $s, 0, 30), '-');
        }
        return strlen($s) < 2 ? 'odkaz' : $s;
    }

    /** Volný kód z názvu: slug, při kolizi slug-2, slug-3… */
    public static function codeFromName(string $name, callable $exists): string {
        $base = self::slug($name);
        $code = $base;
        for ($i = 2; $exists($code); $i++) {
            $suffix = '-' . $i;
            $code = rtrim(substr($base, 0, 30 - strlen($suffix)), '-') . $suffix;
        }
        return $code;
    }

    /** Kód kanálu z adresy: prázdný → „-“, známý → kód, jinak „jiny“. */
    public static function channelFor(?string $raw, array $knownChannels): string {
        $c = strtolower(trim((string) $raw));
        if ($c === '') {
            return self::CHANNEL_NONE;
        }
        return isset($knownChannels[$c]) ? $c : self::CHANNEL_OTHER;
    }

    public static function isBot(?string $userAgent): bool {
        $ua = trim((string) $userAgent);
        return $ua === '' || (bool) preg_match(self::BOT_PATTERN, $ua);
    }

    /**
     * Adresa stránky webu zadaná media managerem → cesta v rámci webu (bez úvodního lomítka), nebo null.
     * Přijme celou adresu webu (https://<host>/<base>/…), cestu od kořene (/<base>/…) i relativní cestu.
     * Odmítne cizí doménu (open redirect), jiné schéma, přesměrování na odkaz samotný a řídicí znaky.
     *
     * @param string[] $hosts povolené hostitele (aktuální a veřejný)
     * @param string $basePath kořen aplikace, např. '/' nebo '/web/'
     */
    public static function normalizeTargetPath(string $input, array $hosts, string $basePath): ?string {
        $v = trim($input);
        if ($v === '' || strlen($v) > 500 || preg_match('/[\x00-\x20\x7f\\\\]/', $v)) {
            return null;
        }
        $base = '/' . trim($basePath, '/');
        $base = $base === '/' ? '/' : $base . '/';
        if (preg_match('#^[a-z][a-z0-9+.-]*:#i', $v)) {
            $parts = parse_url($v);
            if ($parts === false || !in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)
                    || !in_array(strtolower($parts['host'] ?? ''), array_map('strtolower', $hosts), true)
                    || isset($parts['user']) || isset($parts['pass'])) {
                return null;
            }
            $path = ($parts['path'] ?? '/') === '' ? '/' : ($parts['path'] ?? '/');
            $v = $path . (isset($parts['query']) ? '?' . $parts['query'] : '') . (isset($parts['fragment']) ? '#' . $parts['fragment'] : '');
        }
        if (str_starts_with($v, '//')) {
            return null;
        }
        if (str_starts_with($v, '/')) {
            if ($v . '/' === $base) {
                $v = '';
            } elseif (!str_starts_with($v, $base)) {
                return null;
            } else {
                $v = substr($v, strlen($base));
            }
        }
        $v = ltrim($v, '/');
        if (str_starts_with(strtolower($v), self::ROUTE)) {
            return null;
        }
        return $v;
    }

    /** Cíl odkazu jako cesta v rámci webu (bez úvodního lomítka, s query). */
    public function targetUri(array $link): string {
        if (!(int) $link['active']) {
            return '';
        }
        if ($link['target'] === PromoConfig::TARGET_PAGE) {
            return (string) ($link['target_path'] ?? '');
        }
        $t = $this->config->targets[$link['target']] ?? null;
        if ($t === null) {
            return '';
        }
        $query = (string) ($t['query'] ?? '');
        return ltrim((string) ($t['path'] ?? ''), '/') . ($query !== '' ? '?' . $query : '');
    }

    public function targetLabel(array $link): string {
        if ($link['target'] === PromoConfig::TARGET_PAGE) {
            return (string) ($link['target_path'] ?? '') === '' ? 'Úvod' : (string) $link['target_path'];
        }
        return (string) ($this->config->targets[$link['target']]['label'] ?? $link['target']);
    }

    // ---------------------------------------------------------------- kanály

    /**
     * Kanály: kód => [label, group, icon]. Sociální sítě a web z tabulky network, ostatní z konfigurace.
     * Pořadí: tisk, web, e-mail, sociální sítě, QR.
     */
    public function channels(): array {
        if ($this->channels !== null) {
            return $this->channels;
        }
        $all = [];
        foreach ($this->config->channels as $code => $c) {
            $all[(string) $code] = ['label' => (string) ($c['label'] ?? $code), 'group' => (string) ($c['group'] ?? 'print'), 'icon' => null];
        }
        try {
            $rows = $this->linkDao->rows("SELECT icon, title, channel FROM network WHERE channel IS NOT NULL AND channel <> '' ORDER BY id");
        } catch (\Throwable $e) {
            $rows = [];   // před migrací F1
        }
        foreach ($rows as $r) {
            $code = strtolower((string) $r['channel']);
            if (!preg_match(VisitSource::CHANNEL_PATTERN, $code) || $code === self::CHANNEL_OTHER) {
                continue;
            }
            $all[$code] = ['label' => $code === 'web' ? 'Web' : mb_strtoupper(mb_substr((string) $r['title'], 0, 1)) . mb_substr((string) $r['title'], 1), 'group' => $code === 'web' ? 'web' : 'social',
                'icon' => (string) $r['icon']];
        }
        $order = array_flip(array_keys(self::GROUPS));
        $keys = array_keys($all);
        usort($keys, function ($a, $b) use ($all, $order, $keys) {
            $ga = $order[$all[$a]['group']] ?? 99;
            $gb = $order[$all[$b]['group']] ?? 99;
            if ($ga !== $gb) {
                return $ga <=> $gb;
            }
            if ($a === 'soc' || $b === 'soc') {      // „jiná sociální síť“ na konec skupiny
                return $a === 'soc' ? 1 : -1;
            }
            return array_search($a, $keys, true) <=> array_search($b, $keys, true);
        });
        $sorted = [];
        foreach ($keys as $k) {
            $sorted[$k] = $all[$k];
        }
        return $this->channels = $sorted;
    }

    public function channelLabel(string $code): string {
        return match ($code) {
            self::CHANNEL_NONE => 'neuveden',
            self::CHANNEL_OTHER => 'jiný (neznámý kód)',
            default => $this->channels()[$code]['label'] ?? $code,
        };
    }

    // ---------------------------------------------------------------- texty ke kopírování

    /** Texty odkazu: výchozí z konfigurace přepsané texty u odkazu. */
    public function texts(array $link): array {
        $own = json_decode((string) ($link['texts'] ?? ''), true);
        $own = is_array($own) ? array_filter($own, fn($v) => is_string($v) && trim($v) !== '') : [];
        return array_replace($this->config->texts, array_intersect_key($own, $this->config->texts));
    }

    /**
     * Podklady ke zkopírování po kanálech (ctrl+c / ctrl+v).
     *
     * @param string $absoluteBase veřejná adresa webu končící lomítkem (https://…/)
     * @return array<int, array{channel: string, label: string, group: string, url: string, text: ?string, html: ?string}>
     */
    public function copyRows(array $link, string $absoluteBase): array {
        $texts = $this->texts($link);
        $name = (string) $link['name'];
        $rows = [];
        foreach ($this->channels() as $code => $c) {
            $url = $absoluteBase . self::ROUTE . $link['code'] . '/' . $code;
            $fill = fn(string $t) => strtr($t, ['{url}' => $url, '{name}' => $name]);
            $text = match ($c['group']) {
                'social' => $fill($texts['social']),
                'mail' => $fill($texts['mail']),
                default => null,
            };
            $html = $c['group'] === 'web'
                ? '<a href="' . htmlspecialchars($url, ENT_QUOTES) . '">' . htmlspecialchars($fill($texts['web']), ENT_QUOTES) . '</a>'
                : null;
            $rows[] = ['channel' => $code, 'label' => $c['label'], 'group' => $c['group'], 'url' => $url, 'text' => $text, 'html' => $html];
        }
        return $rows;
    }

    // ---------------------------------------------------------------- odkazy

    public function links(): array {
        return $this->linkDao->rows(
            "SELECT l.*, c.name AS company_name, COALESCE(SUM(v.visits), 0) AS visits
             FROM promo_link l LEFT JOIN company c ON c.id = l.company_id_fk LEFT JOIN promo_link_visit v ON v.link_id_fk = l.id
             GROUP BY l.id ORDER BY l.active DESC, l.name, l.code");
    }

    public function link(int $id): ?array {
        return $this->linkDao->row("SELECT l.*, c.name AS company_name FROM promo_link l LEFT JOIN company c ON c.id = l.company_id_fk
                                    WHERE l.id = :id", ['id' => $id]);
    }

    public function linkByCode(string $code): ?array {
        return $this->linkDao->row("SELECT * FROM promo_link WHERE code = :code", ['code' => self::normalizeCode($code)]);
    }

    public function codeExists(string $code): bool {
        return $this->linkByCode($code) !== null;
    }

    public function companies(): array {
        return $this->linkDao->rows("SELECT id, name, version_fk FROM company ORDER BY version_fk DESC, name");
    }

    public function history(int $linkId): array {
        return $this->linkDao->rows("SELECT * FROM promo_link_target_log WHERE link_id_fk = :id ORDER BY changed DESC, id DESC", ['id' => $linkId]);
    }

    public function visitCount(int $linkId): int {
        return (int) ($this->linkDao->row("SELECT COALESCE(SUM(visits), 0) AS n FROM promo_link_visit WHERE link_id_fk = :id", ['id' => $linkId])['n'] ?? 0);
    }

    /**
     * Založí nebo upraví odkaz. $data: code, name, owner_type, company_id, target, target_path (už normalizovaná cesta
     * nebo null = neplatná adresa), note, active, texts (pole social/mail/web).
     *
     * @return array{id: ?int, errors: string[]}
     */
    public function save(?int $id, array $data, string $by): array {
        $errors = [];
        $before = $id !== null ? $this->link($id) : null;
        if ($id !== null && $before === null) {
            return ['id' => null, 'errors' => ['Odkaz neexistuje.']];
        }
        $code = self::normalizeCode($data['code'] ?? ($before['code'] ?? ''));
        if ($before !== null && $code !== $before['code'] && $this->visitCount($id) > 0) {
            $errors[] = 'Kód odkazu, který už má návštěvy, nejde změnit (může být vytištěný nebo zveřejněný).';
            $code = $before['code'];
        }
        if (!self::isValidCode($code)) {
            $errors[] = 'Kód: 2–30 znaků, malá písmena bez diakritiky, číslice a pomlčka, na začátku písmeno.';
        } elseif (($other = $this->linkByCode($code)) !== null && (int) $other['id'] !== (int) ($id ?? 0)) {
            $errors[] = "Kód „{$code}“ už má jiný odkaz.";
        }
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '' || mb_strlen($name) > 200) {
            $errors[] = 'Vyplňte název odkazu (nejvýš 200 znaků).';
        }
        $owner = (string) ($data['owner_type'] ?? '');
        if (!isset(self::OWNER_TYPES[$owner])) {
            $errors[] = 'Vyberte, od koho odkaz je.';
        }
        $companyId = (int) ($data['company_id'] ?? 0) ?: null;
        if ($companyId !== null && $this->linkDao->row("SELECT id FROM company WHERE id = :id", ['id' => $companyId]) === null) {
            $errors[] = 'Vybraná firma neexistuje.';
        }
        $target = (string) ($data['target'] ?? '');
        $targetPath = null;
        if (!isset($this->config->targets[$target])) {
            $errors[] = 'Vyberte cíl odkazu.';
        } elseif ($target === PromoConfig::TARGET_PAGE) {
            $targetPath = $data['target_path'] ?? null;
            if ($targetPath === null) {
                $errors[] = 'Cíl musí být stránka tohoto webu – vložte její adresu (z adresního řádku prohlížeče).';
            }
        }
        $note = trim((string) ($data['note'] ?? ''));
        if (mb_strlen($note) > 1000) {
            $errors[] = 'Poznámka je delší než 1000 znaků.';
        }
        $texts = [];
        foreach (array_keys($this->config->texts) as $k) {
            $t = trim(str_replace("\r\n", "\n", (string) ($data['texts'][$k] ?? '')));
            if ($t !== '' && $t !== $this->config->texts[$k]) {
                $texts[$k] = mb_substr($t, 0, 2000);
            }
        }
        if ($errors) {
            return ['id' => $id, 'errors' => $errors];
        }
        $row = [
            'code' => $code, 'name' => $name, 'owner_type' => $owner, 'company_id_fk' => $companyId, 'target' => $target,
            'target_path' => $targetPath, 'note' => $note === '' ? null : $note,
            'texts' => $texts ? json_encode($texts, JSON_UNESCAPED_UNICODE) : null, 'active' => !empty($data['active']) ? 1 : 0,
        ];
        $now = $this->config->now()->format('Y-m-d H:i:s');
        return $this->linkDao->transaction(function () use ($id, $before, $row, $by, $now) {
            if ($id === null) {
                $this->linkDao->exec("INSERT INTO promo_link (code, name, owner_type, company_id_fk, target, target_path, note, texts, active, created, created_by)
                    VALUES (:code, :name, :owner_type, :company_id_fk, :target, :target_path, :note, :texts, :active, :created, :created_by)",
                    $row + ['created' => $now, 'created_by' => $by]);
                $id = $this->linkDao->lastId();
            } else {
                $this->linkDao->exec("UPDATE promo_link SET code = :code, name = :name, owner_type = :owner_type, company_id_fk = :company_id_fk,
                    target = :target, target_path = :target_path, note = :note, texts = :texts, active = :active, updated = :updated WHERE id = :id",
                    $row + ['updated' => $now, 'id' => $id]);
            }
            if ($before === null || $before['target'] !== $row['target'] || (string) $before['target_path'] !== (string) $row['target_path']) {
                $this->logTarget($id, $row['target'], $row['target_path'], $by, $now);
            }
            return ['id' => $id, 'errors' => []];
        });
    }

    private function logTarget(int $id, string $target, ?string $path, string $by, string $now): void {
        $this->linkDao->exec("INSERT INTO promo_link_target_log (link_id_fk, target, target_path, changed, changed_by)
            VALUES (:id, :target, :path, :changed, :by)", ['id' => $id, 'target' => $target, 'path' => $path, 'changed' => $now, 'by' => $by]);
    }

    /**
     * Odkaz firmy pro promo kit – založí se při prvním zobrazení (kód z názvu firmy, výchozí cíl z konfigurace).
     */
    public function companyLink(int $companyId, string $companyName, string $by): array {
        $existing = $this->linkDao->row("SELECT * FROM promo_link WHERE company_id_fk = :id AND owner_type = 'exhibitor' ORDER BY id LIMIT 1",
            ['id' => $companyId]);
        if ($existing !== null) {
            return $existing;
        }
        for ($attempt = 0; $attempt < 3; $attempt++) {
            $code = self::codeFromName($companyName, fn($c) => $this->codeExists($c));
            try {
                $result = $this->save(null, ['code' => $code, 'name' => mb_substr(trim($companyName), 0, 200) ?: $code,
                    'owner_type' => 'exhibitor', 'company_id' => $companyId, 'target' => $this->config->companyLinkTarget, 'active' => 1], $by);
            } catch (\PDOException $e) {
                continue;   // souběh – kód mezitím obsadil jiný
            }
            if (!$result['errors']) {
                return $this->link($result['id']);
            }
        }
        throw new \RuntimeException('Odkaz firmy se nepodařilo založit.');
    }

    // ---------------------------------------------------------------- návštěva

    /**
     * Návštěva odkazu: započítá (ne roboty) a vrátí cíl a zdroj návštěvy.
     * Vypnutý nebo neznámý odkaz: cíl úvod, nic se nepočítá, zdroj null.
     *
     * @return array{target: string, counted: bool, source: ?VisitSource}
     */
    public function visit(string $code, ?string $rawChannel, bool $isBot): array {
        $link = self::isValidCode(self::normalizeCode($code)) ? $this->linkByCode($code) : null;
        if ($link === null || !(int) $link['active']) {
            return ['target' => '', 'counted' => false, 'source' => null];
        }
        $channel = self::channelFor($rawChannel, $this->channels());
        $now = $this->config->now();
        if (!$isBot) {
            $this->visitDao->increment((int) $link['id'], $now->format('Y-m-d'), $channel);
        }
        $source = VisitSource::fromLink($link['code'], $channel === self::CHANNEL_NONE ? null : $channel, $now);
        return ['target' => $this->targetUri($link), 'counted' => !$isBot, 'source' => $isBot ? null : $source];
    }

    // ---------------------------------------------------------------- přehled

    /** Návštěvy po odkazech a kanálech za období dnů [od, do] ('Y-m-d' nebo null). */
    public function visitsByChannel(?string $from, ?string $to, ?int $linkId = null): array {
        return $this->linkDao->rows(
            "SELECT link_id_fk, channel, SUM(visits) AS visits FROM promo_link_visit
             WHERE (:f IS NULL OR day >= :f2) AND (:t IS NULL OR day <= :t2) AND (:l IS NULL OR link_id_fk = :l2)
             GROUP BY link_id_fk, channel",
            ['f' => $from, 'f2' => $from, 't' => $to, 't2' => $to, 'l' => $linkId, 'l2' => $linkId]);
    }

    /** Návštěvy odkazu po dnech: den => [kanál => počet]. */
    public function daily(int $linkId, ?string $from, ?string $to): array {
        $ret = [];
        foreach ($this->linkDao->rows("SELECT day, channel, visits FROM promo_link_visit WHERE link_id_fk = :l
                AND (:f IS NULL OR day >= :f2) AND (:t IS NULL OR day <= :t2) ORDER BY day DESC",
                ['l' => $linkId, 'f' => $from, 'f2' => $from, 't' => $to, 't2' => $to]) as $r) {
            $ret[$r['day']][$r['channel']] = (int) $r['visits'];
        }
        return $ret;
    }

    /**
     * Řádky přehledu: odkaz × kanál s návštěvami, registracemi a potvrzenými registracemi; navíc řádky UTM bez odkazu.
     *
     * @param array $registrations řádky RegistrationSourceService::counts()
     * @return array{links: array, utm: array, totals: array}
     */
    public static function overview(array $links, array $visits, array $registrations, ?string $ownerType = null): array {
        $byId = [];
        $byCode = [];
        foreach ($links as $l) {
            if ($ownerType !== null && $ownerType !== '' && $l['owner_type'] !== $ownerType) {
                continue;
            }
            $byId[(int) $l['id']] = $l['code'];
            $byCode[$l['code']] = ['link' => $l, 'channels' => [], 'visits' => 0, 'registered' => 0, 'confirmed' => 0];
        }
        $cell = fn() => ['visits' => 0, 'registered' => 0, 'confirmed' => 0];
        foreach ($visits as $v) {
            $code = $byId[(int) $v['link_id_fk']] ?? null;
            if ($code === null) {
                continue;
            }
            $ch = (string) $v['channel'];
            $byCode[$code]['channels'][$ch] ??= $cell();
            $byCode[$code]['channels'][$ch]['visits'] += (int) $v['visits'];
            $byCode[$code]['visits'] += (int) $v['visits'];
        }
        $utm = [];
        $totals = $cell();
        foreach ($registrations as $r) {
            $code = $r['link_code'] ?? null;
            if ($code !== null && $code !== '') {
                if (!isset($byCode[$code])) {
                    continue;   // odkaz mimo filtr (nebo smazaný)
                }
                $ch = (string) ($r['channel'] ?? '') === '' ? self::CHANNEL_NONE : (string) $r['channel'];
                $byCode[$code]['channels'][$ch] ??= $cell();
                $byCode[$code]['channels'][$ch]['registered'] += (int) $r['registered'];
                $byCode[$code]['channels'][$ch]['confirmed'] += (int) $r['confirmed'];
                $byCode[$code]['registered'] += (int) $r['registered'];
                $byCode[$code]['confirmed'] += (int) $r['confirmed'];
            } elseif ($ownerType === null || $ownerType === '') {
                $key = ($r['utm_source'] ?? '–') . ' / ' . ($r['utm_campaign'] ?? '–');
                $utm[$key] ??= ['label' => $key, 'registered' => 0, 'confirmed' => 0];
                $utm[$key]['registered'] += (int) $r['registered'];
                $utm[$key]['confirmed'] += (int) $r['confirmed'];
                $totals['registered'] += (int) $r['registered'];
                $totals['confirmed'] += (int) $r['confirmed'];
            }
        }
        foreach ($byCode as $row) {
            $totals['visits'] += $row['visits'];
            $totals['registered'] += $row['registered'];
            $totals['confirmed'] += $row['confirmed'];
        }
        ksort($utm);
        return ['links' => array_values($byCode), 'utm' => array_values($utm), 'totals' => $totals];
    }

    /** CSV přehledu: řádek na odkaz × kanál a řádky UTM. */
    public function csvRows(array $overview, ?string $from, ?string $to): array {
        $rows = [['Kód', 'Název', 'Od koho', 'Firma', 'Kanál', 'Návštěvy', 'Registrace', 'Potvrzené registrace', 'Období od', 'Období do']];
        foreach ($overview['links'] as $l) {
            $link = $l['link'];
            $channels = $l['channels'] ?: [self::CHANNEL_NONE => ['visits' => 0, 'registered' => 0, 'confirmed' => 0]];
            foreach ($channels as $ch => $n) {
                $rows[] = [$link['code'], $link['name'], self::OWNER_TYPES[$link['owner_type']] ?? $link['owner_type'],
                    (string) ($link['company_name'] ?? ''), $this->channelLabel((string) $ch), $n['visits'], $n['registered'], $n['confirmed'],
                    (string) $from, (string) $to];
            }
        }
        foreach ($overview['utm'] as $u) {
            $rows[] = ['UTM', $u['label'], '', '', '', '', $u['registered'], $u['confirmed'], (string) $from, (string) $to];
        }
        return $rows;
    }
}
