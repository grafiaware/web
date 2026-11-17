<?php

namespace Events\Service\Lottery;

use Auth\Model\Dao\RegistrationDao;
use Container\AuthContainerConfigurator;
use Container\AuthDbContainerConfigurator;
use Pes\Container\Container;

/**
 * D1: e-maily účtů jsou jen v DB auth (registration.email). Vyhledání podle e-mailu (záloha ke skeneru,
 * vyzvednutí v sídle) a e-mail výherce pro export / zprávu po zapojení.
 * Až auth a events poběží na různých doménách (Z12, #68), nahradí se voláním API auth.
 */
class AuthLookup {

    private ?RegistrationDao $dao = null;

    private function dao(): RegistrationDao {
        return $this->dao ??= (new AuthContainerConfigurator())->configure(
                (new AuthDbContainerConfigurator())->configure(new Container())
            )->get(RegistrationDao::class);
    }

    public function emailOf(string $loginName): ?string {
        $row = $this->dao()->get(['login_name_fk' => $loginName]);
        $email = $row !== null ? trim((string) $row['email']) : '';
        return $email !== '' ? $email : null;
    }

    /** @return string[] */
    public function loginsByEmail(string $email): array {
        if (filter_var(trim($email), FILTER_VALIDATE_EMAIL) === false) {
            return [];
        }
        return $this->dao()->findLoginNamesByEmail($email, false);
    }
}
