<?php

namespace Events\Service\Lottery;

use DateTimeImmutable;
use Events\Model\Dao\Lottery\LotteryAnswerDao;
use Events\Model\Dao\Lottery\LotteryDao;
use Events\Model\Dao\Lottery\LotteryPresenceDao;
use Events\Model\Dao\Lottery\LotteryPresenceEventDao;
use Events\Model\Dao\Lottery\LotteryPrizeDao;
use Events\Model\Dao\Lottery\LotteryQuestionDao;
use Events\Model\Dao\Lottery\LotteryRoundDao;
use Events\Model\Dao\Lottery\LotteryTicketDao;
use Events\Model\Dao\Lottery\LotteryWinnerDao;
use Events\Model\Dao\Lottery\VisitorCodeDao;

/**
 * D1 (#46): losování – nastavení, zapojení s anketou, kód návštěvníka, přítomnost (vydání a prodloužení losu),
 * stav pro profil. Losování kola je v LotteryDrawService. Zadání: docs/vp/zadani/D1.md.
 *
 * Časy v DB jsou DATETIME v časové zóně aplikace.
 */
class LotteryService {

    public const STATUS_DRAFT = 'draft';
    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';
    public const STATUSES = [self::STATUS_DRAFT => 'příprava', self::STATUS_OPEN => 'otevřeno', self::STATUS_CLOSED => 'uzavřeno'];

    public const REASON_REGISTRATION = 'registrace';
    public const EVENT_ISSUE = 'vydani';
    public const EVENT_MANUAL = 'rucne';

    // výsledky přítomnosti (vydání / prodloužení)
    public const P_ISSUED = 'issued';
    public const P_REFRESHED = 'refreshed';
    public const P_LOGGED = 'logged';         // akce zaznamenána, platnost se nezměnila
    public const P_NOT_JOINED = 'not_joined';
    public const P_NOT_VALID = 'not_valid';   // prodloužení bez platného losu (mimo vchod)

    private const DT = 'Y-m-d H:i:s';

    /** @var callable():DateTimeImmutable */
    private $clock;

    private \DateTimeZone $tz;

    public function __construct(
        private LotteryDao $lotteryDao,
        private LotteryRoundDao $roundDao,
        private LotteryPrizeDao $prizeDao,
        private LotteryQuestionDao $questionDao,
        private LotteryAnswerDao $answerDao,
        private LotteryTicketDao $ticketDao,
        private VisitorCodeDao $codeDao,
        private LotteryPresenceDao $presenceDao,
        private LotteryPresenceEventDao $presenceEventDao,
        private LotteryWinnerDao $winnerDao,
        private LotteryConfig $config,
        private RandomSourceInterface $random,
        ?callable $clock = null,
    ) {
        $this->tz = new \DateTimeZone($config->timezone);
        $this->clock = $clock ?? fn() => new DateTimeImmutable('now', $this->tz);
    }

    public function now(): DateTimeImmutable {
        return ($this->clock)();
    }

    /** Čas z DB (DATETIME bez zóny) v časové zóně losování. */
    public function time(string $dbValue): DateTimeImmutable {
        return new DateTimeImmutable($dbValue, $this->tz);
    }

    public function config(): LotteryConfig {
        return $this->config;
    }

    ### losování, kola, ceny, anketa (administrace) ###################################

    public function lotteries(): array {
        return $this->lotteryDao->rows("SELECT * FROM lottery ORDER BY created DESC, id DESC");
    }

    public function lottery(int $id): ?array {
        return $this->lotteryDao->row("SELECT * FROM lottery WHERE id = :id", ['id' => $id]);
    }

    /**
     * @param array{name: string, description?: string, status?: string, join_from?: ?string, join_until?: ?string, rules_url?: ?string} $data
     */
    public function saveLottery(?int $id, array $data): int {
        $p = [
            'name' => trim((string) ($data['name'] ?? '')) ?: 'Losování',
            'description' => trim((string) ($data['description'] ?? '')),
            'status' => array_key_exists($data['status'] ?? '', self::STATUSES) ? $data['status'] : self::STATUS_DRAFT,
            'join_from' => self::dtOrNull($data['join_from'] ?? null),
            'join_until' => self::dtOrNull($data['join_until'] ?? null),
            'rules_url' => trim((string) ($data['rules_url'] ?? '')) ?: null,
        ];
        if ($id === null) {
            $this->lotteryDao->exec("INSERT INTO lottery (name, description, status, join_from, join_until, rules_url)
                VALUES (:name, :description, :status, :join_from, :join_until, :rules_url)", $p);
            return $this->lotteryDao->lastId();
        }
        $this->lotteryDao->exec("UPDATE lottery SET name = :name, description = :description, status = :status,
            join_from = :join_from, join_until = :join_until, rules_url = :rules_url WHERE id = :id", $p + ['id' => $id]);
        return $id;
    }

    public function rounds(int $lotteryId): array {
        return $this->roundDao->rows("SELECT * FROM lottery_round WHERE lottery_id_fk = :l ORDER BY draw_at, id", ['l' => $lotteryId]);
    }

    public function round(int $roundId): ?array {
        return $this->roundDao->row("SELECT * FROM lottery_round WHERE id = :id", ['id' => $roundId]);
    }

    public function saveRound(int $lotteryId, ?int $roundId, string $drawAt, bool $substitute): ?int {
        $at = self::dtOrNull($drawAt);
        if ($at === null) {
            return null;
        }
        if ($roundId === null) {
            $this->roundDao->exec("INSERT INTO lottery_round (lottery_id_fk, draw_at, substitute) VALUES (:l, :a, :s)",
                ['l' => $lotteryId, 'a' => $at, 's' => $substitute ? 1 : 0]);
            return $this->roundDao->lastId();
        }
        $this->roundDao->exec("UPDATE lottery_round SET draw_at = :a, substitute = :s WHERE id = :id AND lottery_id_fk = :l",
            ['a' => $at, 's' => $substitute ? 1 : 0, 'id' => $roundId, 'l' => $lotteryId]);
        return $roundId;
    }

    /** Vylosované kolo nejde smazat (výhry by ztratily kolo). */
    public function removeRound(int $roundId): bool {
        return $this->roundDao->exec("DELETE FROM lottery_round WHERE id = :id AND status <> 'drawn'
            AND NOT EXISTS (SELECT 1 FROM lottery_winner w WHERE w.round_id_fk = :id2)", ['id' => $roundId, 'id2' => $roundId]) === 1;
    }

    public function prizes(int $lotteryId): array {
        return $this->prizeDao->rows("SELECT p.*, r.draw_at AS round_draw_at,
                (SELECT COUNT(*) FROM lottery_winner w WHERE w.prize_id_fk = p.id AND w.status <> 'forfeited') AS won
            FROM lottery_prize p LEFT JOIN lottery_round r ON r.id = p.round_id_fk
            WHERE p.lottery_id_fk = :l ORDER BY p.priority, p.id", ['l' => $lotteryId]);
    }

    public function prize(int $prizeId): ?array {
        return $this->prizeDao->row("SELECT * FROM lottery_prize WHERE id = :id", ['id' => $prizeId]);
    }

    public function savePrize(int $lotteryId, ?int $prizeId, array $data): int {
        $roundId = (int) ($data['round_id_fk'] ?? 0);
        $p = [
            'name' => trim((string) ($data['name'] ?? '')) ?: 'Cena',
            'quantity' => max(1, (int) ($data['quantity'] ?? 1)),
            'priority' => (int) ($data['priority'] ?? 0),
            'round' => ($roundId > 0 && $this->roundBelongs($roundId, $lotteryId)) ? $roundId : null,
            'l' => $lotteryId,
        ];
        if ($prizeId === null) {
            $this->prizeDao->exec("INSERT INTO lottery_prize (lottery_id_fk, round_id_fk, name, quantity, priority)
                VALUES (:l, :round, :name, :quantity, :priority)", $p);
            return $this->prizeDao->lastId();
        }
        $this->prizeDao->exec("UPDATE lottery_prize SET round_id_fk = :round, name = :name, quantity = :quantity, priority = :priority
            WHERE id = :id AND lottery_id_fk = :l", $p + ['id' => $prizeId]);
        return $prizeId;
    }

    /** Vylosovanou cenu nejde smazat. */
    public function removePrize(int $prizeId): bool {
        return $this->prizeDao->exec("DELETE FROM lottery_prize WHERE id = :id
            AND NOT EXISTS (SELECT 1 FROM lottery_winner w WHERE w.prize_id_fk = :id2)", ['id' => $prizeId, 'id2' => $prizeId]) === 1;
    }

    public function questions(int $lotteryId): array {
        return $this->questionDao->rows("SELECT q.*, (SELECT COUNT(*) FROM lottery_answer a WHERE a.question_id_fk = q.id) AS answered
            FROM lottery_question q WHERE q.lottery_id_fk = :l ORDER BY q.position, q.id", ['l' => $lotteryId]);
    }

    /**
     * Po prvních odpovědích lze otázku jen přejmenovat a změnit pořadí (ne typ, možnosti, povinnost).
     */
    public function saveQuestion(int $lotteryId, ?int $questionId, array $data): int {
        $type = array_key_exists($data['type'] ?? '', SurveyValidator::TYPES) ? $data['type'] : SurveyValidator::TYPE_SINGLE;
        $p = [
            'text' => trim((string) ($data['text'] ?? '')) ?: 'Otázka',
            'position' => (int) ($data['position'] ?? 0),
            'l' => $lotteryId,
        ];
        $full = [
            'type' => $type,
            'options' => $type === SurveyValidator::TYPE_TEXT ? null : implode("\n", SurveyValidator::options($data['options'] ?? '')),
            'required' => !empty($data['required']) ? 1 : 0,
        ];
        if ($questionId === null) {
            $this->questionDao->exec("INSERT INTO lottery_question (lottery_id_fk, position, text, type, options, required)
                VALUES (:l, :position, :text, :type, :options, :required)", $p + $full);
            return $this->questionDao->lastId();
        }
        $answered = (int) ($this->answerDao->row("SELECT COUNT(*) AS c FROM lottery_answer WHERE question_id_fk = :q", ['q' => $questionId])['c'] ?? 0);
        if ($answered > 0) {
            $this->questionDao->exec("UPDATE lottery_question SET text = :text, position = :position WHERE id = :id AND lottery_id_fk = :l",
                $p + ['id' => $questionId]);
        } else {
            $this->questionDao->exec("UPDATE lottery_question SET text = :text, position = :position, type = :type, options = :options,
                required = :required WHERE id = :id AND lottery_id_fk = :l", $p + $full + ['id' => $questionId]);
        }
        return $questionId;
    }

    public function removeQuestion(int $questionId): bool {
        return $this->questionDao->exec("DELETE FROM lottery_question WHERE id = :id
            AND NOT EXISTS (SELECT 1 FROM lottery_answer a WHERE a.question_id_fk = :id2)", ['id' => $questionId, 'id2' => $questionId]) === 1;
    }

    ### zapojení návštěvníka ##########################################################

    /** Losování, do kterých se lze právě zapojit (otevřená, v období zapojení, na místě). */
    public function joinableLotteries(): array {
        $now = $this->now()->format(self::DT);
        return $this->lotteryDao->rows("SELECT * FROM lottery WHERE status = 'open' AND online = 0
            AND (join_from IS NULL OR join_from <= :n1) AND (join_until IS NULL OR join_until >= :n2) ORDER BY id",
            ['n1' => $now, 'n2' => $now]);
    }

    /** Otevřená losování (pro skener a stav v profilu i mimo období zapojení). */
    public function openLotteries(): array {
        return $this->lotteryDao->rows("SELECT * FROM lottery WHERE status = 'open' AND online = 0 ORDER BY id");
    }

    public function canJoin(array $lottery): bool {
        $now = $this->now();
        return $lottery['status'] === self::STATUS_OPEN
            && ($lottery['join_from'] === null || $this->time($lottery['join_from']) <= $now)
            && ($lottery['join_until'] === null || $this->time($lottery['join_until']) >= $now);
    }

    public function isJoined(int $lotteryId, string $loginName): bool {
        return $this->ticketDao->row("SELECT id FROM lottery_ticket WHERE lottery_id_fk = :l AND login_login_name_fk = :n
            AND reason = :r", ['l' => $lotteryId, 'n' => $loginName, 'r' => self::REASON_REGISTRATION]) !== null;
    }

    public function answers(int $lotteryId, string $loginName): array {
        $ret = [];
        foreach ($this->answerDao->rows("SELECT a.question_id_fk, a.answer FROM lottery_answer a
                JOIN lottery_question q ON q.id = a.question_id_fk WHERE q.lottery_id_fk = :l AND a.login_login_name_fk = :n",
                ['l' => $lotteryId, 'n' => $loginName]) as $r) {
            $ret[(int) $r['question_id_fk']] = (string) $r['answer'];
        }
        return $ret;
    }

    /**
     * Zapojení: souhlas s pravidly + odpovědi ankety (povinné = podmínka). Vrací chyby (prázdné = zapojen).
     * Roli (jen visitor) kontroluje volající.
     *
     * @return array<int|string, string>
     */
    public function join(int $lotteryId, string $loginName, bool $consent, array $input): array {
        $lottery = $this->lottery($lotteryId);
        if ($lottery === null || !$this->canJoin($lottery)) {
            return ['lottery' => 'Do tohoto losování se teď zapojit nelze.'];
        }
        $errors = [];
        if (!$consent) {
            $errors['consent'] = 'Pro zapojení je nutný souhlas s pravidly losování.';
        }
        $result = (new SurveyValidator())->validate($this->questions($lotteryId), $input);
        $errors += $result['errors'];
        if ($errors) {
            return $errors;
        }
        $this->ticketDao->transaction(function () use ($lotteryId, $loginName, $result) {
            foreach ($result['answers'] as $questionId => $answer) {
                $this->answerDao->exec("DELETE FROM lottery_answer WHERE question_id_fk = :q AND login_login_name_fk = :n",
                    ['q' => $questionId, 'n' => $loginName]);
                $this->answerDao->exec("INSERT INTO lottery_answer (question_id_fk, login_login_name_fk, answer) VALUES (:q, :n, :a)",
                    ['q' => $questionId, 'n' => $loginName, 'a' => $answer]);
            }
            if (!$this->isJoined($lotteryId, $loginName)) {
                $this->ticketDao->exec("INSERT INTO lottery_ticket (lottery_id_fk, login_login_name_fk, reason) VALUES (:l, :n, :r)",
                    ['l' => $lotteryId, 'n' => $loginName, 'r' => self::REASON_REGISTRATION]);
            }
        });
        $this->codeFor($loginName);
        return [];
    }

    /** Počet platných (neodebraných) losů účastníka. */
    public function ticketCount(int $lotteryId, string $loginName): int {
        return (int) ($this->ticketDao->row("SELECT COUNT(*) AS c FROM lottery_ticket WHERE lottery_id_fk = :l
            AND login_login_name_fk = :n AND removed IS NULL", ['l' => $lotteryId, 'n' => $loginName])['c'] ?? 0);
    }

    ### kód návštěvníka ###############################################################

    /** Osobní kód (vytvoří se při prvním použití, pak se nemění). */
    public function codeFor(string $loginName): string {
        $row = $this->codeDao->row("SELECT code FROM visitor_code WHERE login_login_name_fk = :n", ['n' => $loginName]);
        if ($row !== null) {
            return $row['code'];
        }
        $generator = new VisitorCodeGenerator($this->random);
        for ($i = 0; $i < 5; $i++) {
            $code = $generator->generate();
            try {
                $this->codeDao->exec("INSERT INTO visitor_code (login_login_name_fk, code) VALUES (:n, :c)", ['n' => $loginName, 'c' => $code]);
                return $code;
            } catch (\Throwable $e) {
                // kolize kódu (nepravděpodobná) nebo souběžné vytvoření – zkusit načíst / znovu
                $row = $this->codeDao->row("SELECT code FROM visitor_code WHERE login_login_name_fk = :n", ['n' => $loginName]);
                if ($row !== null) {
                    return $row['code'];
                }
            }
        }
        throw new \RuntimeException('Kód návštěvníka se nepodařilo vytvořit.');
    }

    public function loginByCode(string $code): ?string {
        $row = $this->codeDao->row("SELECT login_login_name_fk FROM visitor_code WHERE code = :c", ['c' => $code]);
        return $row['login_login_name_fk'] ?? null;
    }

    ### přítomnost na místě ###########################################################

    public function presence(int $lotteryId, string $loginName): ?array {
        return $this->presenceDao->row("SELECT * FROM lottery_presence WHERE lottery_id_fk = :l AND login_login_name_fk = :n",
            ['l' => $lotteryId, 'n' => $loginName]);
    }

    /**
     * Plánované časy kol. $onlyOpen: jen nevylosovaná kola s časem >= $from (pro výpis „hraje v kolech“).
     *
     * @return DateTimeImmutable[]
     */
    public function roundTimes(int $lotteryId, bool $onlyOpen = false, ?DateTimeImmutable $from = null): array {
        $ret = [];
        foreach ($this->rounds($lotteryId) as $r) {
            $t = $this->time($r['draw_at']);
            if (!$onlyOpen || ($r['status'] !== 'drawn' && ($from === null || $t >= $from))) {
                $ret[] = $t;
            }
        }
        return $ret;
    }

    /**
     * Vydání nebo prodloužení losu akcí $event v čase $at.
     *
     * $allowIssue: stanoviště, které los vydává (vchod) – nevydaný nebo propadlý los vydá znovu; ostatní stanoviště
     * a akce jen prodlužují už vydaný los (i propadlý) na čas akce + interval.
     *
     * @return array{result: string, validUntil: ?DateTimeImmutable, eligibleFrom: ?DateTimeImmutable}
     */
    public function presenceAction(int $lotteryId, string $loginName, string $event, DateTimeImmutable $at,
            ?string $by, bool $allowIssue, ?string $station = null): array {
        if (!$this->isJoined($lotteryId, $loginName)) {
            return ['result' => self::P_NOT_JOINED, 'validUntil' => null, 'eligibleFrom' => null];
        }
        return $this->presenceDao->transaction(function () use ($lotteryId, $loginName, $event, $at, $by, $allowIssue, $station) {
            $calc = PresenceCalculator::fromConfig($this->config);
            $presence = $this->presenceDao->row("SELECT * FROM lottery_presence WHERE lottery_id_fk = :l AND login_login_name_fk = :n FOR UPDATE",
                ['l' => $lotteryId, 'n' => $loginName]);
            $valid = $presence !== null && $calc->isValidAt($this->time($presence['valid_until']), $at);
            $log = ['scanned' => $at->format(self::DT), 'by' => $by, 'station' => $station];

            // vchod: nevydaný nebo propadlý los vydá znovu (30 min, N kol); jinde jen prodlouží už vydaný los
            // (i propadlý – akce potvrzuje přítomnost teď, platnost = čas akce + interval)
            if ($presence === null || (!$valid && $allowIssue)) {
                if (!$allowIssue) {
                    return ['result' => self::P_NOT_VALID, 'validUntil' => null, 'eligibleFrom' => null];
                }
                $p = $calc->issue($at, $this->roundTimes($lotteryId));
                $row = ['l' => $lotteryId, 'n' => $loginName, 'i' => $at->format(self::DT), 'b' => $by,
                    'e' => $p['eligibleFrom']->format(self::DT), 'v' => $p['validUntil']->format(self::DT)];
                if ($presence === null) {
                    $this->presenceDao->exec("INSERT INTO lottery_presence (lottery_id_fk, login_login_name_fk, issued, issued_by, eligible_from, valid_until)
                        VALUES (:l, :n, :i, :b, :e, :v)", $row);
                    $presenceId = $this->presenceDao->lastId();
                } else {
                    $presenceId = (int) $presence['id'];
                    $this->presenceDao->exec("UPDATE lottery_presence SET issued = :i, issued_by = :b, eligible_from = :e, valid_until = :v
                        WHERE id = :id", ['i' => $row['i'], 'b' => $by, 'e' => $row['e'], 'v' => $row['v'], 'id' => $presenceId]);
                }
                $this->logEvent($presenceId, self::EVENT_ISSUE, $p['validUntil'], $log);
                return ['result' => self::P_ISSUED, 'validUntil' => $p['validUntil'], 'eligibleFrom' => $p['eligibleFrom']];
            }

            $presenceId = (int) $presence['id'];
            $validUntil = $this->time($presence['valid_until']);
            $minutes = $this->config->refreshMinutes($event);
            if ($minutes === null) {
                $this->logEvent($presenceId, $event, $validUntil, $log);
                return ['result' => self::P_LOGGED, 'validUntil' => $validUntil, 'eligibleFrom' => $this->time($presence['eligible_from'])];
            }
            $new = $calc->refresh($validUntil, $at, $minutes);
            if ($new != $validUntil) {
                $this->presenceDao->exec("UPDATE lottery_presence SET valid_until = :v WHERE id = :id", ['v' => $new->format(self::DT), 'id' => $presenceId]);
            }
            $this->logEvent($presenceId, $event, $new, $log);
            return ['result' => self::P_REFRESHED, 'validUntil' => $new, 'eligibleFrom' => $this->time($presence['eligible_from'])];
        });
    }

    /**
     * Prodloužení z jiných balíků (sken u stánku, přednáška…): jedno volání, akce nastavená v konfiguraci
     * `lottery.presence.refresh`. Prodlouží jen platný los ve všech otevřených losováních na místě.
     * Nenastavená akce nic neprodlouží (jen se zaznamená).
     */
    public function refresh(string $loginName, string $event, ?string $by = null): void {
        foreach ($this->openLotteries() as $lottery) {
            $this->presenceAction((int) $lottery['id'], $loginName, $event, $this->now(), $by, false);
        }
    }

    private function logEvent(int $presenceId, string $event, DateTimeImmutable $validUntil, array $log): void {
        $this->presenceEventDao->exec("INSERT INTO lottery_presence_event (presence_id_fk, event, station, scanned, created_by, valid_until)
            VALUES (:p, :e, :s, :sc, :b, :v)", ['p' => $presenceId, 'e' => $event, 's' => $log['station'],
            'sc' => $log['scanned'], 'b' => $log['by'], 'v' => $validUntil->format(self::DT)]);
    }

    ### stav pro profil a obsluhu #####################################################

    /**
     * Stav návštěvníka v losování (blok Losování v profilu, výsledek skenu).
     */
    public function visitorStatus(int $lotteryId, string $loginName): array {
        $now = $this->now();
        $calc = PresenceCalculator::fromConfig($this->config);
        $presence = $this->presence($lotteryId, $loginName);
        $rounds = $this->rounds($lotteryId);
        $plays = [];
        $valid = false;
        if ($presence !== null) {
            $from = $this->time($presence['eligible_from']);
            $until = $this->time($presence['valid_until']);
            $valid = $calc->isValidAt($until, $now);
            foreach ($rounds as $r) {
                if ($calc->isEligible($from, $until, $this->time($r['draw_at']))) {
                    $plays[] = $r;
                }
            }
        }
        $remaining = array_filter($plays, fn($r) => $r['status'] !== 'drawn' && $this->time($r['draw_at']) >= $now);
        return [
            'joined' => $this->isJoined($lotteryId, $loginName),
            'tickets' => $this->ticketCount($lotteryId, $loginName),
            'presence' => $presence,
            'valid' => $valid && count($remaining) > 0,
            'expired' => $presence !== null && !$valid,   // platnost uplynula (prodloužit / vydat znovu)
            'plays' => $plays,
            'remaining' => array_values($remaining),
            'wins' => $this->winsOf($lotteryId, $loginName),
        ];
    }

    public function winsOf(int $lotteryId, string $loginName): array {
        return $this->winnerDao->rows("SELECT w.*, p.name AS prize_name, r.draw_at AS round_draw_at FROM lottery_winner w
            JOIN lottery_prize p ON p.id = w.prize_id_fk LEFT JOIN lottery_round r ON r.id = w.round_id_fk
            WHERE w.lottery_id_fk = :l AND w.login_login_name_fk = :n ORDER BY w.drawn", ['l' => $lotteryId, 'n' => $loginName]);
    }

    /** Jméno a příjmení z profilu návštěvníka (prázdné = jen přihlašovací jméno). */
    public function displayName(string $loginName): string {
        $row = $this->presenceDao->row("SELECT name, surname FROM visitor_profile WHERE login_login_name = :n", ['n' => $loginName]);
        $name = trim(($row['name'] ?? '') . ' ' . ($row['surname'] ?? ''));
        return $name !== '' ? $name : $loginName;
    }

    public function phoneOf(string $loginName): string {
        if ($loginName === '') {
            return '';
        }
        return (string) ($this->presenceDao->row("SELECT phone FROM visitor_profile WHERE login_login_name = :n", ['n' => $loginName])['phone'] ?? '');
    }

    /**
     * Vyhledání návštěvníků (záloha ke skeneru): přihlašovací jméno, jméno nebo příjmení; e-maily řeší volající
     * (adresy jsou v DB auth) a předá nalezené loginy v $extraLogins.
     *
     * @return array<int, array{login: string, name: string, joined: bool}>
     */
    public function searchVisitors(int $lotteryId, string $q, array $extraLogins = []): array {
        $q = trim($q);
        if (mb_strlen($q) < 2 && !$extraLogins) {
            return [];
        }
        $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q) . '%';
        $rows = $this->presenceDao->rows("SELECT l.login_name FROM login l LEFT JOIN visitor_profile v ON v.login_login_name = l.login_name
            WHERE l.login_name LIKE :q1 OR v.name LIKE :q2 OR v.surname LIKE :q3 OR CONCAT(v.name, ' ', v.surname) LIKE :q4
            ORDER BY l.login_name LIMIT 20", ['q1' => $like, 'q2' => $like, 'q3' => $like, 'q4' => $like]);
        $logins = array_unique(array_merge($extraLogins, array_column($rows, 'login_name')));
        $ret = [];
        foreach (array_slice($logins, 0, 20) as $login) {
            $ret[] = ['login' => $login, 'name' => $this->displayName($login), 'joined' => $this->isJoined($lotteryId, $login)];
        }
        return $ret;
    }

    ### pomocné ######################################################################

    private function roundBelongs(int $roundId, int $lotteryId): bool {
        return $this->roundDao->row("SELECT id FROM lottery_round WHERE id = :id AND lottery_id_fk = :l", ['id' => $roundId, 'l' => $lotteryId]) !== null;
    }

    /** Vstup z formuláře (datetime-local „2027-01-27T11:00“ nebo „2027-01-27 11:00“) → DATETIME, jinak null. */
    public static function dtOrNull(?string $value): ?string {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        $d = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $value)
            ?: \DateTimeImmutable::createFromFormat('Y-m-d H:i', $value)
            ?: \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $value)
            ?: \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', $value);
        return $d ? $d->format(self::DT) : null;
    }
}
