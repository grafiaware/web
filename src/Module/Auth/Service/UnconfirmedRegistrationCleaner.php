<?php

namespace Auth\Service;

use Auth\Model\Dao\RegistrationDao;
use Psr\Log\LoggerInterface;

/**
 * A3: úklid nepotvrzených registrací (docs/vp/zadani/A3.md).
 *
 * Nepotvrzená = bez credentials a bez času potvrzení. Smaže se registrace i login (návštěvník i zástupce firmy);
 * účty založené administrátorem (A6) a ručně dokončení zástupci mají credentials, ty se nesmažou nikdy.
 * Do logu jde jen počet, ne e-maily ani jména.
 */
class UnconfirmedRegistrationCleaner {

    public function __construct(
        private RegistrationDao $registrationDao,
        private ?LoggerInterface $logger = null,
    ) {
    }

    /**
     * Náhled: kolik nepotvrzených registrací starších než N dní by se smazalo.
     *
     * @return array{visitors: int, representatives: int, total: int}
     */
    public function preview(int $days): array {
        $visitors = 0;
        $representatives = 0;
        foreach ($this->registrationDao->findUnconfirmed($days) as $row) {
            self::isRepresentative($row) ? $representatives++ : $visitors++;
        }
        return ['visitors' => $visitors, 'representatives' => $representatives, 'total' => $visitors + $representatives];
    }

    /**
     * Čekající zástupci firem (nepotvrzená registrace s vyplněnou firmou), s počtem dní do smazání.
     *
     * @return array<int, array{loginName: string, company: string, email: string, created: string, daysLeft: int}>
     */
    public function pendingRepresentatives(int $days, ?\DateTimeImmutable $now = null): array {
        $now ??= new \DateTimeImmutable();
        $result = [];
        foreach ($this->registrationDao->findUnconfirmed() as $row) {
            if (!self::isRepresentative($row)) {
                continue;
            }
            $result[] = [
                'loginName' => (string) $row['login_name_fk'],
                'company' => trim((string) $row['info']),
                'email' => (string) ($row['email'] ?? ''),
                'created' => (string) $row['created'],
                'daysLeft' => self::daysLeft((string) $row['created'], $days, $now),
            ];
        }
        return $result;
    }

    /**
     * Smaže nepotvrzené registrace starší než N dní. Vrací počet smazaných.
     */
    public function clean(int $days, string $who = ''): int {
        $deleted = count($this->registrationDao->deleteUnconfirmedOlderThan($days));
        $this->logger?->info("Úklid nepotvrzených registrací: smazáno {$deleted} (starší než {$days} dní)"
            . ($who !== '' ? ", spustil {$who}" : '') . '.');
        return $deleted;
    }

    /**
     * Počet celých dní do smazání (0 = smaže se při nejbližším úklidu).
     */
    public static function daysLeft(string $created, int $days, \DateTimeImmutable $now): int {
        $deleteAt = (new \DateTimeImmutable($created))->modify("+{$days} days");
        if ($deleteAt <= $now) {
            return 0;
        }
        return (int) ceil(($deleteAt->getTimestamp() - $now->getTimestamp()) / 86400);
    }

    private static function isRepresentative(array $row): bool {
        return trim((string) ($row['info'] ?? '')) !== '';
    }
}
