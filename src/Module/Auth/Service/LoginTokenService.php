<?php

namespace Auth\Service;

use Auth\Model\Dao\LoginTokenDao;
use Pes\Model\RowData\RowData;

/**
 * Jednorázové tokeny pro přihlašovací odkaz (A2) a nastavení hesla (A6).
 *
 * Token: 32 náhodných bajtů, v odkazu base64url (43 znaků), v DB jen hash('sha256', token).
 */
class LoginTokenService {

    public const PURPOSE_LOGIN = 'login';
    public const PURPOSE_SET_PASSWORD = 'setpassword';

    private LoginTokenDao $dao;

    /** @var callable():\DateTimeImmutable */
    private $clock;

    public function __construct(LoginTokenDao $dao, ?callable $clock = null) {
        $this->dao = $dao;
        $this->clock = $clock ?? fn() => new \DateTimeImmutable();
    }

    public static function hash(string $token): string {
        return hash('sha256', $token);
    }

    public static function newToken(): string {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    /** Token má tvar base64url o délce 43 znaků (ochrana před nesmysly v URL). */
    public static function isWellFormed(string $token): bool {
        return (bool) preg_match('/^[A-Za-z0-9_-]{43}$/', $token);
    }

    /**
     * Smí se pro login vytvořit další token? (nejvýš $limitCount za posledních $limitMinutes)
     */
    public function canIssue(string $loginName, string $purpose, int $limitCount, int $limitMinutes): bool {
        $since = $this->now()->modify("-{$limitMinutes} minutes");
        return $this->dao->countCreatedSince($loginName, $purpose, $since) < $limitCount;
    }

    /**
     * Vytvoří token a uloží jeho hash. Vrací token v čitelné podobě – patří jen do odkazu v e-mailu.
     */
    public function issue(string $loginName, string $purpose, int $ttlMinutes): string {
        $token = self::newToken();
        $now = $this->now();
        $row = new RowData();
        $row->import([
            'token_hash' => self::hash($token),
            'login_name_fk' => $loginName,
            'purpose' => $purpose,
            'created' => $now->format('Y-m-d H:i:s'),
            'expires' => $now->modify("+{$ttlMinutes} minutes")->format('Y-m-d H:i:s'),
        ]);
        $this->dao->insert($row);
        return $token;
    }

    /**
     * Login, kterému patří platný (nepoužitý, neprošlý) token daného účelu; jinak null. Token nespotřebuje.
     */
    public function peek(string $token, string $purpose): ?string {
        if (!self::isWellFormed($token)) {
            return null;
        }
        $row = $this->dao->get(['token_hash' => self::hash($token)]);
        if (!$row || $row['purpose'] !== $purpose || $row['used'] !== null) {
            return null;
        }
        if (new \DateTimeImmutable($row['expires']) <= $this->now()) {
            return null;
        }
        return $row['login_name_fk'];
    }

    /**
     * Spotřebuje token: vrátí login a token označí jako použitý; neplatný, prošlý nebo už použitý token → null.
     */
    public function consume(string $token, string $purpose): ?string {
        $loginName = $this->peek($token, $purpose);
        if ($loginName === null) {
            return null;
        }
        return $this->dao->markUsed(self::hash($token), $purpose, $this->now()) ? $loginName : null;
    }

    private function now(): \DateTimeImmutable {
        return ($this->clock)();
    }
}
