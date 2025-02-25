<?php

include 'csv.php';

/** Kontrola e-mailové adresy
* @param string $email e-mailová adresa
* @return bool syntaktická správnost adresy
*/
function check_email($email)
{
    $atom = '[-a-z0-9!#$%&\'*+/=?^_`{|}~]'; // znaky tvořící uživatelské jméno
    $domain = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])'; // jedna komponenta domény
    return preg_match("(^$atom+(\\.$atom+)*@($domain?\\.)+$domain\$)i", $email);
}

function check_email2($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? TRUE : FALSE;  // vrací email adresu (string) nebo FALSE
}

/** Ověření funkčnosti e-mailu
* @param string $email adresa příjemce
* @param string $from adresa odesílatele
* @return bool na adresu lze doručit zpráva, null pokud nejde ověřit
*/
function try_server_dialog($email, $from, &$returnedErrorMessage, &$communication)
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

function isSequential($value){
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

function verifyEmail($email) {
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

function verifyEmails($emails) {

    foreach ($emails as $email) {
        $test[$email] = verifyEmail($email);
    }
    return $test;
}


echo "<p>Ankety</p>";
$data = importCsv("VPV_ankety_test.csv");
echo "<pre>";
print_r($data);
echo "</pre>";

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

echo "<p>Verifikace</p>";

echo "<pre>";
print_r($verifiedData);
echo "</pre>";

exportCsv("VPV_ankety_test_verified.csv", $verifiedData);