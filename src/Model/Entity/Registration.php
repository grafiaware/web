<?php
namespace Model\Entity;


class Registration extends EntityAbstract implements RegistrationInterface {

    /**
     * @var string
     */
    private $loginNameFk;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $email;

    /**
     * @var \DateTime
     */
    private $emailTime;

    private $keyAttribute = 'login_name_fk';

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    /**
     *
     * @return string|null
     */
    public function getLoginNameFk(): string {
        return $this->loginNameFk;
    }


    /**
     *
     * @param string $loginNameFk
     * @return RegistrationInterface
     */
    public function setLoginNameFk(string $loginNameFk): RegistrationInterface {
        $this->loginNameFk = $loginNameFk;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getPassword(): ?string {
        return $this->password;
    }

    /**
     *
     * @param string $password
     * @return RegistrationInterface
     */
    public function setPassword(string $password): RegistrationInterface {
        $this->password = $password;
        return $this;
    }

    /**
     *
     * @return string|null
     */
    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(string $email):RegistrationInterface {
        $this->email = $email;
        return $this;
    }

    /**
     *
     * @return \DateTime|null
     */
    public function getEmailTime(): ?\DateTime {
        return $this->emailTime;
    }

    /**
     *
     * @param \DateTime $emailTime
     * @return RegistrationInterface
     */
    public function setEmailTime(\DateTime $emailTime): RegistrationInterface {
        $this->emailTime = $emailTime;
        return $this;
    }

}
