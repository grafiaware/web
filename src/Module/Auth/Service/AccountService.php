<?php

namespace Auth\Service;

use Access\Enum\RoleEnum;
use Auth\Model\Dao\CredentialsDao;
use Auth\Model\Dao\LoginDao;
use Auth\Model\Dao\LoginProfileDao;
use Auth\Model\Dao\RegistrationDao;
use Auth\Model\Dao\RoleDao;
use Pes\Core\Security\Password\Password;
use Pes\Model\RowData\RowData;

/**
 * A6: založení účtu administrátorem (docs/vp/zadani/A6.md); A9: účty typu admin, úpravy údajů účtů (docs/vp/zadani/A9.md).
 *
 * Kdo smí co: RoleGrantPolicy (přepínač vp.sprava-roli).
 * S přepínačem (A9): přihlašovací jméno povinné, role nepovinná, heslo nikdy nezadává správce – vždy odkaz e-mailem;
 * účet dostane login_profile.origin = admin.
 *
 * Účet = login + registration (jen e-mail, bez hesla) + credentials (hash hesla nebo prázdný, role)
 * + volitelně osobní jméno (A8) + zápis do events.login.
 * Všechny kontroly proběhnou před prvním zápisem; když zápis selže, už zapsané řádky se smažou (DAO commitují
 * jednotlivě, jedna transakce přes ně nejde).
 */
class AccountService {

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
        private RoleGrantPolicy $policy,
        private ?LoginProfileDao $loginProfileDao = null,
        private ?AccountChangeLog $accountChangeLog = null,
        private ?RoleChangeLog $roleChangeLog = null,
    ) {
        $this->addToEvents = $addToEvents;
    }

    public function policy(): RoleGrantPolicy {
        return $this->policy;
    }

    public function canCreateAccounts(?string $creatorRole): bool {
        return $this->policy->canManageAccounts($creatorRole);
    }

    /**
     * Role, se kterými smí administrátor s rolí $creatorRole zakládat účty.
     *
     * @param string[] $allRoles všechny role z tabulky role
     * @return string[]
     */
    public function allowedRoles(?string $creatorRole, array $allRoles): array {
        return $this->policy->grantableRoles($creatorRole, $allRoles);
    }

    /** @return string[] role z tabulky role */
    public function allRoles(): array {
        return array_map(fn($row) => (string) $row['role'], $this->roleDao->find('1=1', []));
    }

    /**
     * Zkontroluje vstup a založí účet.
     *
     * @throws AccountException
     */
    public function create(AccountRequest $request, ?string $creatorRole, string $creatorLogin = ''): AccountResult {
        $a9 = $this->policy->grantorsEnabled();
        if (!$this->canCreateAccounts($creatorRole)) {
            throw AccountException::forbidden('Nemáte oprávnění zakládat účty.');
        }
        $email = trim($request->email);
        if ($email === '' || strlen($email) > 100 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw AccountException::invalid('Zadejte platnou e-mailovou adresu (nejvýš 100 znaků).');
        }
        $role = trim($request->role);
        if ($role === '' && $a9) {
            // A9: role je nepovinná
        } elseif ($role === '' || $this->roleDao->get(['role' => $role]) === null) {
            throw AccountException::invalid('Vyberte roli účtu.');
        } elseif (!in_array($role, $this->allowedRoles($creatorRole, [$role]), true)) {
            throw AccountException::forbidden("Účet s rolí {$role} nemůžete založit.");
        }
        $loginName = trim($request->loginName);
        if ($loginName === '' && $a9) {
            throw AccountException::invalid('Zadejte přihlašovací jméno.');
        } elseif ($loginName === '') {
            $loginName = $this->loginNameGenerator->generate($email);
        } else {
            if (!preg_match(self::LOGIN_NAME_PATTERN, $loginName)) {
                throw AccountException::invalid('Přihlašovací jméno: 5 až 50 znaků, jen písmena bez diakritiky, číslice a podtržítko.');
            }
            if ($this->loginDao->get(['login_name' => $loginName]) !== null) {   // collation ignoruje velikost písmen
                throw AccountException::invalid("Přihlašovací jméno {$loginName} už existuje.");
            }
        }
        $passwordByMail = $request->passwordByMail || $a9;   // A9: heslo nikdy nezadává správce
        if ($passwordByMail) {
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

        $this->insertAccount($loginName, $email, $passwordHash, $role === '' ? null : $role);
        if ($a9) {
            $this->loginProfileDao?->setOrigin($loginName, AccountOrigin::ADMIN);
            $this->accountChangeLog?->record($creatorLogin, $loginName, 'založení účtu');
            if ($role !== '') {
                $this->roleChangeLog?->record($creatorLogin, $loginName, null, $role);
            }
        }

        $displayName = $request->displayName !== null ? $this->displayNameService->set($loginName, $request->displayName) : null;
        $eventsError = null;
        try {
            ($this->addToEvents)($loginName);
        } catch (\Throwable $e) {
            $eventsError = 'Účet se nepodařilo zapsat do databáze veletrhu; zapíše se při prvním přihlášení.';
        }
        $token = $passwordByMail ? $this->issueSetPasswordToken($loginName, $request->setPasswordTtlHours) : null;
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
        $allowed = $this->canCreateAccounts($creatorRole) && (
            ($role === '' && $this->policy->grantorsEnabled())
            || in_array($role, $this->allowedRoles($creatorRole, [$role]), true));
        if (!$allowed) {
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

    /**
     * A9: úprava údajů účtu v tabulce účtů – e-mail (jen typ admin a legacy, jen RoleGrantPolicy::canEditEmail),
     * osobní jméno, role. null = údaj neměnit. Vrací seznam změněných údajů a případný původní e-mail (oznámení).
     *
     * @return array{changed: string[], oldEmail: ?string, newEmail: ?string}
     * @throws AccountException
     */
    public function updateAccount(string $actorLogin, ?string $actorRole, string $targetLogin,
            ?string $email, ?string $displayName, ?string $role, bool $changeRole): array {
        $credentials = $this->credentialsDao->get(['login_name_fk' => $targetLogin]);
        if ($credentials === null) {
            throw AccountException::invalid("Účet {$targetLogin} neexistuje.");
        }
        $currentRole = ($credentials['role_fk'] ?? '') === '' ? null : (string) $credentials['role_fk'];
        $allRoles = $this->allRoles();
        $denial = $this->policy->roleChangeDenial($actorLogin, $actorRole, $targetLogin, $currentRole, null, $allRoles);
        if ($denial !== null) {
            throw AccountException::forbidden($denial);
        }
        $registration = $this->registrationDao->get(['login_name_fk' => $targetLogin]);
        $oldEmail = $registration !== null ? trim((string) $registration['email']) : '';
        $origin = AccountOrigin::resolve($this->loginProfileDao?->getOrigin($targetLogin), $oldEmail);

        // kontroly před zápisem
        $newEmail = null;
        if ($email !== null && trim($email) !== $oldEmail) {
            if (!$this->policy->canEditEmail($actorRole)) {
                throw AccountException::forbidden('E-mail účtů smí měnit jen správce účtů (auth_role_grantor).');
            }
            if ($origin === AccountOrigin::SELF) {
                throw AccountException::invalid('U účtu z registrace se e-mail nemění – je to jeho identita.');
            }
            $newEmail = trim($email);
            if ($newEmail === '' || strlen($newEmail) > 100 || filter_var($newEmail, FILTER_VALIDATE_EMAIL) === false) {
                throw AccountException::invalid('Zadejte platnou e-mailovou adresu (nejvýš 100 znaků).');
            }
        }
        $newRole = null;
        if ($changeRole) {
            $newRole = ($role === null || $role === '') ? null : $role;
            if ($newRole !== null && !in_array($newRole, $allRoles, true)) {
                throw AccountException::invalid("Role {$newRole} neexistuje.");
            }
            if ($newRole !== $currentRole) {
                $roleDenial = $this->policy->roleChangeDenial($actorLogin, $actorRole, $targetLogin, $currentRole, $newRole, $allRoles);
                if ($roleDenial !== null) {
                    throw AccountException::forbidden($roleDenial);
                }
            } else {
                $changeRole = false;
            }
        }

        $changed = [];
        if ($newEmail !== null) {
            if ($registration !== null) {
                $registration->offsetSet('email', $newEmail);
                $this->registrationDao->update($registration);
            } else {   // legacy účet bez registrace
                $row = new RowData();
                $row->import(['login_name_fk' => $targetLogin, 'password_hash' => '', 'email' => $newEmail]);
                $this->registrationDao->insert($row);
            }
            $changed[] = 'e-mail';
            $this->accountChangeLog?->record($actorLogin, $targetLogin, $oldEmail === '' ? 'doplnění e-mailu' : 'změna e-mailu');
        }
        if ($displayName !== null) {
            $before = $this->displayNameService->get($targetLogin);
            $after = $this->displayNameService->set($targetLogin, $displayName);
            if ($before !== $after) {
                $changed[] = 'osobní jméno';
                $this->accountChangeLog?->record($actorLogin, $targetLogin, 'osobní jméno');
            }
        }
        if ($changeRole) {
            $credentials->offsetSet('role_fk', $newRole);
            $this->credentialsDao->update($credentials);
            $changed[] = 'role';
            $this->roleChangeLog?->record($actorLogin, $targetLogin, $currentRole, $newRole);
        }
        return ['changed' => $changed, 'oldEmail' => ($newEmail !== null && $oldEmail !== '') ? $oldEmail : null, 'newEmail' => $newEmail];
    }

    private function insertAccount(string $loginName, string $email, string $passwordHash, ?string $role): void {
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
