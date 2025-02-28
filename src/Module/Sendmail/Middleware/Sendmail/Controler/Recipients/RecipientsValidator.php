<?php
namespace Sendmail\Middleware\Sendmail\Controler\Recipients;

use Sendmail\Middleware\Sendmail\Controler\Recipients\RecipientsValidatorInterface;
use Sendmail\Middleware\Sendmail\Controler\Recipients\ValidationDegreeEnum;

/**
 * Description of RecipientsValidator
 *
 * @author vlse2610
 */
class RecipientsValidator implements RecipientsValidatorInterface {
    
    const FAKE_MAIL_SENDER  = 'pp@seznam.cz';
    
    /** Kontrola e-mailové adresy
    * @param string $email e-mailová adresa
    * @return bool syntaktická správnost adresy
    */
   private function checkEmail($email)
    {
        $atom = '[-a-z0-9!#$%&\'*+/=?^_`{|}~]'; // znaky tvořící uživatelské jméno
        $domain = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])'; // jedna komponenta domény
        return preg_match("(^$atom+(\\.$atom+)*@($domain?\\.)+$domain\$)i", $email);
    }

    private function checkEmail2($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? TRUE : FALSE;  // vrací email adresu (string) nebo FALSE
    }    

    /** Ověření funkčnosti e-mailu
    * @param string $email adresa příjemce
    * @param string $from adresa odesílatele
    * @return bool na adresu lze doručit zpráva, null pokud nejde ověřit
    */
    private function tryServerDialog($email, $from)
    {
        $domain = preg_replace('~.*@~', '', $email);
        $dnsGetRecords = dns_get_record($domain, DNS_MX);
        foreach ($dnsGetRecords as $record) {
            $mxs[$record['pri']] = $record['target'];
        };
        if (isset($mxs)) {
            ksort($mxs);

            $commands = array(
                "HELO " . preg_replace('~.*@~', '', $from),
                "MAIL FROM: <$from>",
                "RCPT TO: <$email>",
            );
            foreach ($mxs as $mx) {
        //resource fsockopen ( string $hostname [, int $port = -1 [, int &$errno [, string &$errstr [, float $timeout = ini_get("default_socket_timeout") ]]]] )
                $fp = fsockopen($mx, 25, $errorNumber, $errorMessage, 100);
                if (is_resource($fp)) {
                    $s = fgets($fp);
                    if ($fp && substr($s, 0, 3) == '220') {
                        while ($s{3} == '-') {
                            $s = fgets($fp);
                        }
                        foreach ($commands as $command) {
                            fwrite($fp, "$command\r\n");
                            $s = fgets($fp);
                            if (substr($s, 0, 3) != '250') {
                                return false;
                            }
                            while ($s{3} == '-') {
                                $s = fgets($fp);
                            }
                        }
                        return true;
                    }
                }
            }
        }
        return false;
    }

    private function isSequential($value){
        if(is_array($value) || ($value instanceof \Countable && $value instanceof \ArrayAccess)){
            for ($i = count($value) - 1; $i >= 0; $i--) {
                if (!isset($value[$i]) && !array_key_exists($i, $value)) {
                    return FALSE;
                }
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function verifyEmail($email, $validationDegree) {
        $validationDegreeEnum = new ValidationDegreeEnum();
        $degree = $validationDegreeEnum($validationDegree);
        //   https://www.root.cz/clanky/php-kontrola-e-mail/  (Vrána)
        $from = self::FAKE_MAIL_SENDER;    
        if ($degree==ValidationDegreeEnum::SYNTAX || $degree==ValidationDegreeEnum::DOMAIN || $degree==ValidationDegreeEnum::USER) {
            $syntaxChecked = $this->checkEmail($email) OR $this->checkEmail2($email);
            if ($syntaxChecked) {
                $verified = ValidationDegreeEnum::SYNTAX;
            }
            if ($degree==ValidationDegreeEnum::DOMAIN || $degree==ValidationDegreeEnum::USER) {
                $domain = preg_replace('~.*@~', '', $email).'.';  //tečka na konci - zabrání doplnění doménového jména o lokální příponu
                $domainChecked = dns_get_record($domain, DNS_MX);
                if ($domainChecked) {
                    $verified = ValidationDegreeEnum::DOMAIN;
                }
                if ($degree==ValidationDegreeEnum::USER) {
                    $userCheck = $this->tryServerDialog($email, $from, $errorMessage, $communication);
                    if ($userCheck) {
                        $verified = ValidationDegreeEnum::USER;
                    }
                }
            }
        }
        return $verified ?? false;
    }

    public function verifyEmails($emails) {
        foreach ($emails as $email) {
            $test[$email] = verifyEmail($email);
        }
        return $test;
    }   
    
}
