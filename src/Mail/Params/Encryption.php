<?php

namespace Mail\Params;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Description of Encryption
 *
 * @author pes2704
 */
class Encryption {

    const SMTPS = "SMTPS";
    const STARTTLS = "STARTTLS";
    const NONE = "NONE";

    private $smtpSecure = '';
    private $port = 25;

    public function setEncryption($encryption = self::NONE) {

        switch ($encryption) {
            case self::SMTPS:
                $this->smtpSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $this->port = 587;
                break;
            case self::STARTTLS:
                $this->smtpSecure = PHPMailer::ENCRYPTION_SMTPS;
                $this->port = 465;
                break;
            case self::NONE:
            default:
                $this->smtpSecure = '';
                $this->port = 25;
                break;
        }

        return $this;
    }

    public function getSmtpSecure() {
        return $this->smtpSecure;
    }

    public function getPort() {
        return $this->port;
    }

}
