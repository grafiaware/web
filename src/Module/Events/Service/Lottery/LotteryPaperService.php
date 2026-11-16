<?php

namespace Events\Service\Lottery;

use Events\Model\Dao\Lottery\LotteryPaperTicketDao;

/**
 * D1c (#108): papírové anketní lístky – sada lístků s pořadovým číslem a náhodným kódem, vyhledání podle kódu a čísla,
 * přehled (vytištěno / vydáno / platné). Kód je identifikátor, ne tajný údaj (AGENTS.md). Zadání docs/vp/zadani/D1c.md.
 *
 * QR lístku obsahuje adresu …/events/v1/scan/L<kód>; prefix L odliší lístek od kódu návštěvníka (D1).
 */
class LotteryPaperService {

    public const CODE_PREFIX = 'L';
    public const MAX_SET = 2000;

    private const DT = 'Y-m-d H:i:s';

    public function __construct(
        private LotteryService $lotteries,
        private LotteryPaperTicketDao $paperDao,
        private RandomSourceInterface $random,
    ) {
    }

    /** Číslo lístku pro tisk a zobrazení: 0427. */
    public static function formatNumber(int $number): string {
        return sprintf('%04d', $number);
    }

    /** Zobrazení lístku místo jména výherce / návštěvníka. */
    public static function label(int $number): string {
        return 'Lístek č. ' . self::formatNumber($number);
    }

    /** Text QR / skenu lístku: L + kód. */
    public static function scanCode(string $code): string {
        return self::CODE_PREFIX . $code;
    }

    /**
     * Z naskenovaného textu (adresa …/scan/L<kód> nebo samotné L<kód>) vrátí kód lístku (bez L), jinak null.
     */
    public static function codeFromScannedText(string $text): ?string {
        $text = trim($text);
        if (preg_match('~/scan/([A-Za-z0-9]+)/?(?:[?#].*)?$~', $text, $m)) {
            $text = $m[1];
        }
        $text = strtoupper($text);
        if (strlen($text) !== VisitorCodeGenerator::LENGTH + 1 || $text[0] !== self::CODE_PREFIX) {
            return null;
        }
        $code = substr($text, 1);
        return VisitorCodeGenerator::isWellFormed($code) ? $code : null;
    }

    /** Ruční zadání čísla lístku ve skeneru (0427, 427) → číslo, jinak null. */
    public static function numberFromText(string $text): ?int {
        $text = trim($text);
        return preg_match('/^\d{1,6}$/', $text) && (int) $text > 0 ? (int) $text : null;
    }

    /**
     * Vytvoří další sadu $count lístků; čísla navazují na poslední lístek losování.
     *
     * @return array{from: int, to: int}
     */
    public function createSet(int $lotteryId, int $count): array {
        if ($count < 1 || $count > self::MAX_SET) {
            throw new \InvalidArgumentException('Počet lístků musí být 1 až ' . self::MAX_SET . '.');
        }
        return $this->paperDao->transaction(function () use ($lotteryId, $count) {
            // zámek losování: dvě souběžné sady nedostanou stejná čísla
            $this->paperDao->row("SELECT id FROM lottery WHERE id = :l FOR UPDATE", ['l' => $lotteryId]);
            $last = (int) ($this->paperDao->row("SELECT MAX(number) AS n FROM lottery_paper_ticket WHERE lottery_id_fk = :l",
                ['l' => $lotteryId])['n'] ?? 0);
            $generator = new VisitorCodeGenerator($this->random);
            $now = $this->lotteries->now()->format(self::DT);
            for ($n = $last + 1; $n <= $last + $count; $n++) {
                for ($attempt = 0; ; $attempt++) {
                    $code = $generator->generate();
                    if ($this->codeExists($code)) {
                        if ($attempt >= 5) {
                            throw new \RuntimeException('Kód lístku se nepodařilo vytvořit.');
                        }
                        continue;
                    }
                    $this->paperDao->exec("INSERT INTO lottery_paper_ticket (lottery_id_fk, number, code, created) VALUES (:l, :n, :c, :t)",
                        ['l' => $lotteryId, 'n' => $n, 'c' => $code, 't' => $now]);
                    break;
                }
            }
            return ['from' => $last + 1, 'to' => $last + $count];
        });
    }

    public function byCode(string $code): ?array {
        return $this->paperDao->row("SELECT * FROM lottery_paper_ticket WHERE code = :c", ['c' => $code]);
    }

    public function byNumber(int $lotteryId, int $number): ?array {
        return $this->paperDao->row("SELECT * FROM lottery_paper_ticket WHERE lottery_id_fk = :l AND number = :n",
            ['l' => $lotteryId, 'n' => $number]);
    }

    public function ticket(int $id): ?array {
        return $this->paperDao->row("SELECT * FROM lottery_paper_ticket WHERE id = :id", ['id' => $id]);
    }

    /** Lístky losování v rozsahu čísel (tisk, export). */
    public function tickets(int $lotteryId, ?int $from = null, ?int $to = null): array {
        return $this->paperDao->rows("SELECT * FROM lottery_paper_ticket WHERE lottery_id_fk = :l AND number >= :f AND number <= :t
            ORDER BY number", ['l' => $lotteryId, 'f' => $from ?? 1, 't' => $to ?? PHP_INT_MAX]);
    }

    /**
     * @return array{printed: int, issued: int, valid: int, last: int}
     */
    public function stats(int $lotteryId): array {
        $row = $this->paperDao->row("SELECT COUNT(*) AS printed, COALESCE(MAX(number), 0) AS last FROM lottery_paper_ticket WHERE lottery_id_fk = :l",
            ['l' => $lotteryId]);
        $p = $this->paperDao->row("SELECT COUNT(*) AS issued, COALESCE(SUM(valid_until >= :now), 0) AS valid FROM lottery_presence
            WHERE lottery_id_fk = :l AND paper_ticket_id_fk IS NOT NULL",
            ['l' => $lotteryId, 'now' => $this->lotteries->now()->format(self::DT)]);
        return ['printed' => (int) $row['printed'], 'issued' => (int) $p['issued'], 'valid' => (int) $p['valid'], 'last' => (int) $row['last']];
    }

    /**
     * Stav lístku pro přehled obsluhy: lístek, přítomnost (vydáno, platí do), výhry.
     */
    public function status(array $ticket): array {
        $lotteryId = (int) $ticket['lottery_id_fk'];
        $presence = $this->lotteries->paperPresence($lotteryId, (int) $ticket['id']);
        $calc = PresenceCalculator::fromConfig($this->lotteries->config());
        $valid = $presence !== null && $calc->isValidAt($this->lotteries->time($presence['valid_until']), $this->lotteries->now());
        $wins = $this->paperDao->rows("SELECT w.*, p.name AS prize_name, r.draw_at AS round_draw_at FROM lottery_winner w
            JOIN lottery_prize p ON p.id = w.prize_id_fk LEFT JOIN lottery_round r ON r.id = w.round_id_fk
            WHERE w.paper_ticket_id_fk = :t ORDER BY w.drawn", ['t' => (int) $ticket['id']]);
        return ['ticket' => $ticket, 'label' => self::label((int) $ticket['number']), 'presence' => $presence, 'valid' => $valid, 'wins' => $wins];
    }

    private function codeExists(string $code): bool {
        return $this->paperDao->row("SELECT code FROM lottery_paper_ticket WHERE code = :c", ['c' => $code]) !== null
            || $this->paperDao->row("SELECT code FROM visitor_code WHERE code = :c", ['c' => $code]) !== null;
    }
}
