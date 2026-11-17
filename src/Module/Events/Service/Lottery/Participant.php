<?php

namespace Events\Service\Lottery;

/**
 * D1c (#108): klíč účastníka losování – registrovaný návštěvník (login) nebo papírový lístek (id lístku).
 * Klíč se používá v seznamu účastníků kola (klíč => počet losů) a ve vážené volbě.
 */
final class Participant {

    public const VISITOR = 'v';
    public const PAPER = 'p';

    public static function visitor(string $login): string {
        return self::VISITOR . ':' . $login;
    }

    public static function paper(int $paperTicketId): string {
        return self::PAPER . ':' . $paperTicketId;
    }

    /**
     * @return array{type: string, login: ?string, paperId: ?int}
     */
    public static function parse(string $key): array {
        [$type, $id] = explode(':', $key, 2) + [1 => ''];
        if ($type === self::PAPER) {
            return ['type' => self::PAPER, 'login' => null, 'paperId' => (int) $id];
        }
        if ($type !== self::VISITOR) {
            throw new \InvalidArgumentException("Neplatný klíč účastníka losování: {$key}");
        }
        return ['type' => self::VISITOR, 'login' => $id, 'paperId' => null];
    }

    /** Klíč účastníka z řádku výhry nebo přítomnosti (login_login_name_fk / paper_ticket_id_fk). */
    public static function ofRow(array $row): ?string {
        if (($row['paper_ticket_id_fk'] ?? null) !== null) {
            return self::paper((int) $row['paper_ticket_id_fk']);
        }
        if (($row['login_login_name_fk'] ?? null) !== null) {
            return self::visitor((string) $row['login_login_name_fk']);
        }
        return null;
    }
}
