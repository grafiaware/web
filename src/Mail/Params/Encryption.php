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

    /**
     * Nasraví šifrováni připojení k SMTP serveru. Je třeba jako hodnotu parametru použít některou z konstant třídy SMTPS, STARTTLS, NONE.
     * Metoda vždy automaticka nystaví odpovídající SMTP port. Default hodnoty jsou NONE (nez šifrování) a port 25.
     * - SMTPS - encryption TLS (dříve nazývano SSL), port 587
     * - STARTTLS - encryption TLS s použitím dialogu STARTTLS, port 465
     * - NONE
     * 
     * @param string $encryption Hodnota některé z konstant třídy SMTPS, STARTTLS, NONE, default NONE
     * @return $this
     */
    public function setEncryption($encryption = self::NONE) {
//Protokol  Port            Zabezpečení spojení Zabezpečená autentizace
//SMTP      25 nebo 625     žádné nebo TLS 	ne
//SMTP      587             TLS                 ne
//SMTPS     465             SSL                 ne
        switch ($encryption) {
            case self::STARTTLS:
                $this->smtpSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $this->port = 587;
                break;
            case self::SMTPS:
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
