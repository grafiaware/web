<?php

namespace Events\Service\Lottery;

use DateTimeImmutable;
use Events\Model\Dao\Lottery\LotteryWinnerDao;

/**
 * D1 (#46): vylosování kola, náhradník, převzetí výhry, přehled nevyzvednutých výher.
 *
 * Účastník kola = zapojený návštěvník s platnou přítomností pro plánovaný čas kola (eligible_from <= draw_at <= valid_until),
 * bez dosavadních výherců losování (propadlá výhra se nepočítá; o tutéž cenu propadlý výherce už nehraje).
 * Váha = počet neodebraných losů. D1c (#108): papírový lístek s platnou přítomností je účastník s 1 losem.
 * Účastníci se rozlišují klíčem Participant (návštěvník / lístek).
 */
class LotteryDrawService {

    public const W_DRAWN = 'drawn';
    public const W_CLAIMED = 'claimed';
    public const W_CLAIMED_OFFICE = 'claimed_office';
    public const W_FORFEITED = 'forfeited';
    public const W_STATUSES = [
        self::W_DRAWN => 'vylosováno',
        self::W_CLAIMED => 'převzal na místě',
        self::W_CLAIMED_OFFICE => 'převzal v sídle',
        self::W_FORFEITED => 'propadla (nedostavil se)',
    ];

    private const DT = 'Y-m-d H:i:s';

    public function __construct(
        private LotteryService $lotteries,
        private LotteryWinnerDao $winnerDao,
        private RandomSourceInterface $random,
    ) {
    }

    /**
     * Účastníci kola s váhou.
     *
     * @param string[] $alsoExclude další vyloučení účastníci – klíče Participant (např. propadlí výherci ceny při losování náhradníka)
     * @return array<string, int> klíč Participant => počet losů
     */
    public function participants(array $round, array $alsoExclude = []): array {
        $lotteryId = (int) $round['lottery_id_fk'];
        $oneWinAcross = $this->lotteries->config()->oneWinAcrossLotteries;
        $params = ['l' => $lotteryId, 'd1' => $round['draw_at'], 'd2' => $round['draw_at']];
        $visitors = $this->winnerDao->rows("SELECT p.login_login_name_fk AS login, COUNT(t.id) AS tickets
            FROM lottery_presence p
            JOIN lottery_ticket t ON t.lottery_id_fk = p.lottery_id_fk AND t.login_login_name_fk = p.login_login_name_fk AND t.removed IS NULL
            WHERE p.lottery_id_fk = :l AND p.login_login_name_fk IS NOT NULL
              AND p.eligible_from <= :d1 AND p.valid_until >= :d2
              AND NOT EXISTS (SELECT 1 FROM lottery_winner w WHERE w.login_login_name_fk = p.login_login_name_fk
                    AND w.status <> 'forfeited' AND " . ($oneWinAcross ? "1 = 1" : "w.lottery_id_fk = p.lottery_id_fk") . ")
            GROUP BY p.login_login_name_fk", $params);
        // D1c: lístek patří jen k jednomu losování, 1 los
        $papers = $this->winnerDao->rows("SELECT p.paper_ticket_id_fk AS paper
            FROM lottery_presence p
            WHERE p.lottery_id_fk = :l AND p.paper_ticket_id_fk IS NOT NULL
              AND p.eligible_from <= :d1 AND p.valid_until >= :d2
              AND NOT EXISTS (SELECT 1 FROM lottery_winner w WHERE w.paper_ticket_id_fk = p.paper_ticket_id_fk AND w.status <> 'forfeited')",
            $params);
        $ret = [];
        foreach ($visitors as $r) {
            $ret[Participant::visitor((string) $r['login'])] = (int) $r['tickets'];
        }
        foreach ($papers as $r) {
            $ret[Participant::paper((int) $r['paper'])] = 1;
        }
        foreach ($alsoExclude as $key) {
            unset($ret[$key]);
        }
        return $ret;
    }

    /**
     * Ceny, které lze v kole losovat, se zbývajícím počtem kusů: nejdřív ceny přiřazené ke kolu, pak ceny bez kola
     * podle pořadí (priority, id).
     *
     * @return array<int, array> řádky lottery_prize + 'remaining'
     */
    public function availablePrizes(array $round): array {
        $ret = [];
        foreach ($this->lotteries->prizes((int) $round['lottery_id_fk']) as $p) {
            $remaining = (int) $p['quantity'] - (int) $p['won'];
            if ($remaining <= 0) {
                continue;
            }
            if ((int) $p['round_id_fk'] === (int) $round['id']) {
                $ret[] = $p + ['remaining' => $remaining, 'assigned' => true];
            }
        }
        foreach ($this->lotteries->prizes((int) $round['lottery_id_fk']) as $p) {
            $remaining = (int) $p['quantity'] - (int) $p['won'];
            if ($remaining > 0 && $p['round_id_fk'] === null) {
                $ret[] = $p + ['remaining' => $remaining, 'assigned' => false];
            }
        }
        return $ret;
    }

    /**
     * Vylosuje jeden kus ceny $prizeId (null = další cena kola podle pořadí).
     *
     * @return array|null řádek výhry, null když není co nebo z koho losovat
     */
    public function drawOne(int $roundId, ?int $prizeId, string $by): ?array {
        return $this->winnerDao->transaction(function () use ($roundId, $prizeId, $by) {
            // zámek kola: dva telefony obsluhy nevylosují tutéž cenu dvakrát
            $round = $this->winnerDao->row("SELECT * FROM lottery_round WHERE id = :id FOR UPDATE", ['id' => $roundId]);
            if ($round === null) {
                return null;
            }
            $prize = null;
            foreach ($this->availablePrizes($round) as $p) {
                if ($prizeId === null || (int) $p['id'] === $prizeId) {
                    $prize = $p;
                    break;
                }
            }
            if ($prize === null) {
                return null;
            }
            return $this->insertWinner($round, $prize, $by, null);
        });
    }

    /**
     * Vylosuje všechny zbývající kusy cen kola (dokud jsou účastníci).
     *
     * @return array<int, array> nové výhry
     */
    public function drawAll(int $roundId, string $by): array {
        $ret = [];
        for ($i = 0; $i < 1000; $i++) {
            $w = $this->drawOne($roundId, null, $by);
            if ($w === null) {
                break;
            }
            $ret[] = $w;
        }
        return $ret;
    }

    /**
     * Nedostavil se (jen kolo s náhradníkem): výhra propadne a ze stejného kola se vylosuje náhradník.
     *
     * @return array{forfeited: bool, winner: ?array}
     */
    public function substitute(int $winnerId, string $by): array {
        return $this->winnerDao->transaction(function () use ($winnerId, $by) {
            $winner = $this->winnerDao->row("SELECT * FROM lottery_winner WHERE id = :id FOR UPDATE", ['id' => $winnerId]);
            if ($winner === null || $winner['status'] !== self::W_DRAWN || $winner['round_id_fk'] === null) {
                return ['forfeited' => false, 'winner' => null];
            }
            $round = $this->winnerDao->row("SELECT * FROM lottery_round WHERE id = :id FOR UPDATE", ['id' => (int) $winner['round_id_fk']]);
            if ($round === null || (int) $round['substitute'] !== 1) {
                return ['forfeited' => false, 'winner' => null];
            }
            $now = $this->lotteries->now()->format(self::DT);
            $this->winnerDao->exec("UPDATE lottery_winner SET status = 'forfeited', forfeited = :t, forfeited_by = :b WHERE id = :id",
                ['t' => $now, 'b' => $by, 'id' => $winnerId]);
            $prize = $this->lotteries->prize((int) $winner['prize_id_fk']);
            $new = $this->insertWinner($round, $prize, $by, $winnerId);
            return ['forfeited' => true, 'winner' => $new];
        });
    }

    /** Převzal na místě / v sídle. */
    public function claim(int $winnerId, bool $office, string $by): bool {
        return $this->winnerDao->exec("UPDATE lottery_winner SET status = :s, claimed = :t, claimed_by = :b
            WHERE id = :id AND status = 'drawn'", ['s' => $office ? self::W_CLAIMED_OFFICE : self::W_CLAIMED,
            't' => $this->lotteries->now()->format(self::DT), 'b' => $by, 'id' => $winnerId]) === 1;
    }

    public function winner(int $winnerId): ?array {
        $row = $this->winnerDao->row("SELECT w.*, p.name AS prize_name, pt.number AS paper_number FROM lottery_winner w
            JOIN lottery_prize p ON p.id = w.prize_id_fk LEFT JOIN lottery_paper_ticket pt ON pt.id = w.paper_ticket_id_fk
            WHERE w.id = :id", ['id' => $winnerId]);
        return $row !== null ? $this->decorate([$row])[0] : null;
    }

    /** Výhry kola (i propadlé), nejnovější první. */
    public function winnersOfRound(int $roundId): array {
        return $this->decorate($this->winnerDao->rows("SELECT w.*, p.name AS prize_name, pt.number AS paper_number FROM lottery_winner w
            JOIN lottery_prize p ON p.id = w.prize_id_fk LEFT JOIN lottery_paper_ticket pt ON pt.id = w.paper_ticket_id_fk
            WHERE w.round_id_fk = :r ORDER BY w.id DESC", ['r' => $roundId]));
    }

    /** Všechny výhry losování (přehled, export). */
    public function winnersOfLottery(int $lotteryId, bool $onlyUnclaimed = false): array {
        return $this->decorate($this->winnerDao->rows("SELECT w.*, p.name AS prize_name, r.draw_at AS round_draw_at, pt.number AS paper_number FROM lottery_winner w
            JOIN lottery_prize p ON p.id = w.prize_id_fk LEFT JOIN lottery_round r ON r.id = w.round_id_fk
            LEFT JOIN lottery_paper_ticket pt ON pt.id = w.paper_ticket_id_fk
            WHERE w.lottery_id_fk = :l " . ($onlyUnclaimed ? "AND w.status = 'drawn' " : "") . "ORDER BY r.draw_at, w.id", ['l' => $lotteryId]));
    }

    private function decorate(array $rows): array {
        foreach ($rows as &$r) {
            if (($r['paper_number'] ?? null) !== null) {
                $r['display_name'] = LotteryPaperService::label((int) $r['paper_number']);   // D1c: vyvolá se číslem
            } else {
                $r['display_name'] = $r['login_login_name_fk'] !== null ? $this->lotteries->displayName($r['login_login_name_fk']) : '';
            }
        }
        return $rows;
    }

    /** Propadlí výherci ceny (klíče Participant) – o tutéž cenu už nehrají (v dalších kolech o jiné ceny ano). */
    private function forfeitedOfPrize(int $prizeId): array {
        return array_values(array_filter(array_map(fn($r) => Participant::ofRow($r), $this->winnerDao->rows(
            "SELECT login_login_name_fk, paper_ticket_id_fk FROM lottery_winner WHERE prize_id_fk = :p AND status = 'forfeited'",
            ['p' => $prizeId]))));
    }

    private function insertWinner(array $round, array $prize, string $by, ?int $replacesId): ?array {
        $exclude = $this->forfeitedOfPrize((int) $prize['id']);
        $key = (new WeightedDraw($this->random))->pick($this->participants($round, $exclude));
        if ($key === null) {
            return null;
        }
        $who = Participant::parse($key);
        $now = $this->lotteries->now()->format(self::DT);
        $this->winnerDao->exec("INSERT INTO lottery_winner (lottery_id_fk, round_id_fk, prize_id_fk, login_login_name_fk, paper_ticket_id_fk, replaces_id_fk, drawn, drawn_by)
            VALUES (:l, :r, :p, :n, :pt, :rep, :d, :b)", ['l' => (int) $round['lottery_id_fk'], 'r' => (int) $round['id'],
            'p' => (int) $prize['id'], 'n' => $who['login'], 'pt' => $who['paperId'], 'rep' => $replacesId, 'd' => $now, 'b' => $by]);
        $id = $this->winnerDao->lastId();
        $this->winnerDao->exec("UPDATE lottery_round SET status = 'drawn', drawn = COALESCE(drawn, :d), drawn_by = COALESCE(drawn_by, :b) WHERE id = :id",
            ['d' => $now, 'b' => $by, 'id' => (int) $round['id']]);
        $w = $this->winner($id);
        $w['participants'] = count($this->participants($round, $exclude)) + 1;
        return $w;
    }
}
