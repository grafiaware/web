<?php

namespace Auth\Service;

use Access\Enum\RoleEnum;
use Auth\Model\Dao\CredentialsDao;
use Auth\Model\Dao\LoginDao;
use Auth\Model\Dao\RegistrationDao;
use Auth\Model\Dao\RoleDao;
use Pes\Core\Security\Password\Password;
use Pes\Model\RowData\RowData;

/**
 * A6: založení účtu administrátorem (docs/vp/zadani/A6.md).
 *
 * Účet = login + registration (jen e-mail, bez hesla) + credentials (hash hesla nebo prázdný, role)
 * + volitelně osobní jméno (A8) + zápis do events.login.
 * Všechny kontroly proběhnou před prvním zápisem; když zápis selže, už zapsané řádky se smažou (DAO commitují
 * jednotlivě, jedna transakce přes ně nejde).
 */
class AccountService {

    /** Role, které smí zakládat jednotlivé role administrátorů; null = všechny role z tabulky role. */
    private const CREATABLE_ROLES = [
        RoleEnum::SUPERVISOR => null,
        RoleEnum::EVENTS_ADMINISTRATOR => [RoleEnum::VISITOR, RoleEnum::REPRESENTATIVE],
    ];

    public const LOGIN_NAME_PATTERN = '/^[A-Za-z0-9_]{5,50}$/';

    /** @var callable(string): void */
    private $addToEvents;

    /**
     * @param callable(string): void $addToEvents zápis loginu do events.login
     */
    public function __construct(
        private LoginDao $loginDao,
        private CredentialsDao $credentialsDao,
        private RegistrationDao $registrationDao,
        private RoleDao $roleDao,
        private LoginNameGenerator $loginNameGenerator,
        private DisplayNameService $displayNameService,
        private LoginTokenService $tokens,
        callable $addToEvents,
        private string $passwordPattern,
        private string $passwordInfo,
    ) {
        $this->addToEvents = $addToEvents;
    }

    public static function canCreateAccounts(?string $creatorRole): bool {
        return $creatorRole !== null && array_key_exists($creatorRole, self::CREATABLE_ROLES);
    }

    /**
     * Role, se kterými smí administrátor s rolí $creatorRole zakládat účty.
     *
     * @param string[] $allRoles všechny role z tabulky role
     * @return string[]
     */
    public static function allowedRoles(?string $creatorRole, array $allRoles): array {
        if (!self::canCreateAccounts($creatorRole)) {
            return [];
        }
        $limit = self::CREATABLE_ROLES[$creatorRole];
        return $limit === null ? array_values($allRoles) : array_values(array_intersect($allRoles, $limit));
    }

    /**
     * Zkontroluje vstup a založí účet.
     *
     * @throws AccountException
     */
    public function create(AccountRequest $request, ?string $creatorRole): AccountResult {
        if (!self::canCreateAccounts($creatorRole)) {
            throw AccountException::forbidden('Nemáte oprávnění zakládat účty.');
        }
        $email = trim($request->email);
        if ($email === '' || strlen($email) > 100 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw AccountException::invalid('Zadejte platnou e-mailovou adresu (nejvýš 100 znaků).');
        }
        $role = trim($request->role);
        if ($role === '' || $this->roleDao->get(['role' => $role]) === null) {
            throw AccountException::invalid('Vyberte roli účtu.');
        }
        if (!in_array($role, self::allowedRoles($creatorRole, [$role]), true)) {
            throw AccountException::forbidden("Účet s rolí {$role} nemůžete založit.");
        }
        $loginName = trim($request->loginName);
        if ($loginName === '') {
            $loginName = $this->loginNameGenerator->generate($email);
        } else {
            if (!preg_match(self::LOGIN_NAME_PATTERN, $loginName)) {
                throw AccountException::invalid('Přihlašovací jméno: 5 až 50 znaků, jen písmena bez diakritiky, číslice a podtržítko.');
            }
            if ($this->loginDao->get(['login_name' => $loginName]) !== null) {   // collation ignoruje velikost písmen
                throw AccountException::invalid("Přihlašovací jméno {$loginName} už existuje.");
            }
        }
        if ($request->passwordByMail) {
            $passwordHash = '';
        } else {
            if ($request->password === '') {
                throw AccountException::invalid('Zadejte heslo, nebo zaškrtněte „heslo mailem“.');
            }
            if (!preg_match('/^(?:' . $this->passwordPattern . ')$/u', $request->password)) {
                throw AccountException::invalid($this->passwordInfo);
            }
            $passwordHash = (new Password())->getPasswordHash($request->password);
        }

        $this->insertAccount($loginName, $email, $passwordHash, $role);

        $displayName = $request->displayName !== null ? $this->displayNameService->set($loginName, $request->displayName) : null;
        $eventsError = null;
        try {
            ($this->addToEvents)($loginName);
        } catch (\Throwable $e) {
            $eventsError = 'Účet se nepodařilo zapsat do databáze veletrhu; zapíše se při prvním přihlášení.';
        }
        $token = $request->passwordByMail ? $this->issueSetPasswordToken($loginName, $request->setPasswordTtlHours) : null;
        return new AccountResult($loginName, $email, $role, $displayName, $token, $eventsError);
    }

    /**
     * Nový odkaz na nastavení hesla pro existující účet (prošlý odkaz). Vrací e-mail účtu a token.
     *
     * @return array{email: string, token: string}
     * @throws AccountException
     */
    public function newSetPasswordLink(string $loginName, ?string $creatorRole, int $ttlHours): array {
        $credentials = $this->credentialsDao->get(['login_name_fk' => $loginName]);
        $registration = $this->registrationDao->get(['login_name_fk' => $loginName]);
        if ($credentials === null) {
            throw AccountException::invalid("Účet {$loginName} neexistuje.");
        }
        $role = (string) ($credentials['role_fk'] ?? '');
        if (!self::canCreateAccounts($creatorRole) || !in_array($role, self::allowedRoles($creatorRole, [$role]), true)) {
            throw AccountException::forbidden("U účtu s rolí {$role} nemůžete posílat odkaz na heslo.");
        }
        $email = $registration !== null ? trim((string) $registration['email']) : '';
        if ($email === '') {
            throw AccountException::invalid("Účet {$loginName} nemá e-mail.");
        }
        return ['email' => $email, 'token' => $this->issueSetPasswordToken($loginName, $ttlHours)];
    }

    private function issueSetPasswordToken(string $loginName, int $ttlHours): string {
        return $this->tokens->issue($loginName, LoginTokenService::PURPOSE_SET_PASSWORD, 60 * max(1, $ttlHours));
    }

    private function insertAccount(string $loginName, string $email, string $passwordHash, string $role): void {
        $login = new RowData();
        $login->offsetSet('login_name', $loginName);
        $this->loginDao->insert($login);
        try {
            $registration = new RowData();
            $registration->import(['login_name_fk' => $loginName, 'password_hash' => '', 'email' => $email]);
            $this->registrationDao->insert($registration);
            $credentials = new RowData();
            $credentials->import(['login_name_fk' => $loginName, 'password_hash' => $passwordHash, 'role_fk' => $role]);
            $this->credentialsDao->insert($credentials);
        } catch (\Throwable $e) {
            $this->removeAccountRows($loginName);
            throw AccountException::invalid('Účet se nepodařilo založit, nic nebylo uloženo.', $e);
        }
    }

    private function removeAccountRows(string $loginName): void {
        foreach ([$this->credentialsDao, $this->registrationDao] as $dao) {
            try {
                $row = $dao->get(['login_name_fk' => $loginName]);
                if ($row !== null) {
                    $dao->delete($row);
                }
            } catch (\Throwable) {
            }
        }
        try {
            $row = $this->loginDao->get(['login_name' => $loginName]);
            if ($row !== null) {
                $this->loginDao->delete($row);
            }
        } catch (\Throwable) {
        }
    }
}
