<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Sendmail\Middleware\Sendmail\Controler\Contents;

use Sendmail\Middleware\Sendmail\Controler\Contents\RecipientsValidatorInterface;






/**
 * Description of RecipientsValidator
 *
 * @author vlse2610
 */
class RecipientsValidator implements RecipientsValidatorInterface {
    
    public function __construct(           
            ) {
               
    }
    
    

    /** Ověření funkčnosti e-mailu
    * @param string $email adresa příjemce
    * @param string $from adresa odesílatele
    * @return bool na adresu lze doručit zpráva, null pokud nejde ověřit
    */
    private function try_server_dialog($email, $from, &$returnedErrorMessage, &$communication)
    {
        $returnedErrorMessage = '';
        $communication = array();
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
                    $communication[] = "Open socket on $mx: $errorNumber/$errorMessage";
                    $s = fgets($fp);
                    $communication[] = $s;
                    if ($fp && substr($s, 0, 3) == '220') {
                        while ($s{3} == '-') {
                            $s = fgets($fp);
                            $communication[] = $s;
                        }
                        foreach ($commands as $command) {
                            $communication[] = $command;
                            fwrite($fp, "$command\r\n");
                            $s = fgets($fp);
                            $communication[] = $s;
                            if (substr($s, 0, 3) != '250') {
                                $returnedErrorMessage = $s;
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

    private function verifyEmail($email) {
        //   https://www.root.cz/clanky/php-kontrola-e-mail/  (Vrána)
        $from = 'pp@seznam.cz';    
        $test['verified'] = 'error';
        $test['syntax check'] = check_email($email);
        $test['syntax check2'] = check_email2($email);
        $syntaxCheck = $test['syntax check'] OR $test['syntax check2'];
        if ($syntaxCheck) {
            $test['verified'] = 'syntax';
            $domain = preg_replace('~.*@~', '', $email).'.';  //tečka na konci - zabrání doplnění doménového jména o lokální příponu

        //    $test['getdnsrecord '.$domain] = dns_get_record($domain);   // vypnuto - velké
            $test['getdnsrecord MXtype'] = dns_get_record($domain, DNS_MX);
            $test['dnsmx'] = checkdnsrr($domain, 'MX');
            $test['dnsa'] = checkdnsrr($domain, 'A');
            $test['dnsany'] = checkdnsrr($domain, 'ANY');
            $test['gethostbyname'] = gethostbyname($domain);

            $domainCheck = $test['getdnsrecord MXtype'];
            if ($domainCheck) {
                $test['verified'] = 'domain';
                $test['try']['success'] = try_server_dialog($email, $from, $errorMessage, $communication);
                if (isset($errorMessage) AND $errorMessage) {
                    $test['try']['error_message'] = $errorMessage;
                    $userCheck = FALSE;
                } else {
                    $userCheck = TRUE;
                }
                $test['try']['communication'] = $communication;
                if ($userCheck) {
                    $test['verified'] = 'user';
                }
            }
            return $test;
        }    
    }

    private function verifyEmails($emails) {

        foreach ($emails as $email) {
            $test[$email] = verifyEmail($email);
        }
        return $test;
    }

   
    //----------------------------------------------------------------------------------------
    public function validate($data) {        
        
        $verifiedData = [];
        foreach ($data as $dataRow) {
            $email = $dataRow['E-mail:'];
            if (is_string($email)) {
                $test = verifyEmail($email);
                $verified = $test['verified'];
            } else {
                $verified = 'no mail';
            }
            $verifiedData[] = array_merge($dataRow, ['mail verified' => $verified, 'verification time'=> date("Y-m-d H:i:s")]);
        }
        
        
        return    $verifiedData;                
    }    
    
    
}
