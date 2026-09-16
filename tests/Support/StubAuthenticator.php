<?php
declare(strict_types=1);

namespace Test\Support;

use Auth\Authenticator\AuthenticatorInterface;
use Auth\Model\Entity\LoginAggregateCredentialsInterface;

final class StubAuthenticator implements AuthenticatorInterface
{
    public function __construct(
        private readonly bool $accept = true,
        private readonly ?string $expectedPassword = null,
    ) {
    }

    public function authenticate(LoginAggregateCredentialsInterface $loginAggregateEntity, $password): bool
    {
        if (!$this->accept) {
            return false;
        }
        if ($this->expectedPassword !== null) {
            return $password === $this->expectedPassword;
        }

        return true;
    }
}
