<?php
namespace Sendmail\Middleware\Sendmail\Recipients;

use Sendmail\Middleware\Sendmail\Recipients\RecipientsValidatorInterface;
use Sendmail\Middleware\Sendmail\Recipients\ValidityEnum;
use Pes\Type\Exception\ValueNotInEnumException;
use UnexpectedValueException;

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

    /**
     * Ověří validitu email adresy. Používá tři stupně ověření:
     * - oveření syntaxe - ověřuje pouze syntaktickou správnost email adrwesy
     * - ověření domény - ověří syntaxi a následně ověří reálnou existenci doménového záznamu pro zasílání pošty (MX záznamu 
     *   na DNS serveru) pro doménovou část email adresy (část za zavináčem)
     * - ověření uživatele - oveří syntaxi a doménu a následně ověří existenci uživatelského účtu na cílovém poštovním SMTP serveru
     * 
     * Oveření uživatele metoda provádí navázáním dialogu se SMTP serverem příjemce, kde předstírá, že chce na email adresu doručit mail. 
     * SMTP server obvykle odpoví zda adresa příjemce existuje nebo ne. Metoda však selže u serverů, které přísně hlídají správný obsah DNS 
     * záznamů v doméně odesílatele. Adresu odesílatele metoda jen předstírá a přísná kontrola to odhalí, 
     * 
     * Úspěch kontroly uživatele tak znamená, že email adresa příjemce je opravdu kvalitně ověřena. Naopak neúspěch kontroly uřivatele 
     * nemusí znamenat, že email adresa neexistuje.
     * 
     * 
     * @param string $email email adresa
     * @param type $validationDegree Požadovaný stupeň validace email adresy - hodnota z ValidityEnum
     * @return type Dosažený stupeň validace email adresy -  hodnota z ValidityEnum
     * @throws UnexpectedValueException Požadovaný stupeň validace není hodnota z ValidityEnum
     */
    public function validateEmail($email, $validationDegree) {
        try {
            $validationDegreeEnum = new ValidityEnum();
            $degree = $validationDegreeEnum($validationDegree);            
        } catch (ValueNotInEnumException $exc) {
            throw new UnexpectedValueException("Nedovolená hodnota požadovaného stupně validace.", 0,$exc);
        }

        //   https://www.root.cz/clanky/php-kontrola-e-mail/  (Vrána)
        if ($degree==ValidityEnum::SYNTAX || $degree==ValidityEnum::DOMAIN || $degree==ValidityEnum::USER) {
            $syntaxChecked = $this->checkEmail($email) OR $this->checkEmail2($email);
            if ($syntaxChecked) {
                $validity = ValidityEnum::SYNTAX;
            } else {
                $validity = ValidityEnum::NO_MAIL;
            }
            if ($degree==ValidityEnum::DOMAIN || $degree==ValidityEnum::USER) {
                $domain = preg_replace('~.*@~', '', $email).'.';  //tečka na konci - zabrání doplnění doménového jména o lokální příponu
                $domainChecked = checkdnsrr($domain, 'MX');
                if ($domainChecked) {
                    $validity = ValidityEnum::DOMAIN;
                }
                if ($degree==ValidityEnum::USER) {
                    $from = self::FAKE_MAIL_SENDER;                        
                    $userCheck = $this->tryServerDialog($email, $from, $errorMessage, $communication);
                    if ($userCheck) {
                        $validity = ValidityEnum::USER;
                    }
                }
            }
        }
        return $validity;
    }
    
}
