<?php

namespace Mail\Assembly;

/**
 * Description of SmtpAuth
 *
 * @author pes2704
 */
class SmtpAuth {
    private $smtpAuth;
    private $userName;
    private $password;

    public function getSmtpAuth(): bool {
        return $this->smtpAuth;
    }

    public function getUserName(): string {
        return $this->userName;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function setSmtpAuth(bool $smtpAuth) {
        $this->smtpAuth = $smtpAuth;
        return $this;
    }

    public function setUserName(string $userName) {
        $this->userName = $userName;
        return $this;
    }

    public function setPassword(string $password) {
        $this->password = $password;
        return $this;
    }


}
