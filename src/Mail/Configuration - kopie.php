<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mail;

use Mail\Params;
use Mail\Params\{
    Host, Encryption, SmtpAuth, Party, Content, Attachment, Headers
};
/**
 * Description of Configuration
 *
 * @author pes2704
 */
class Configuration {

    private static $paramsArray = [ ];

    /**
     *
     * @param string $name
     * @return Params
     */
    public static function params($name) {
        if (array_key_exists($name, self::$paramsArray)) {
            return self::$paramsArray[$name];
        } else {
            self::$paramsArray[$name] = self::$name();
            return self::$paramsArray[$name];
        }
    }
    //------------------------------------------------------------------------------------------------------------
    private static function body_register() {
        $subject =  'mail pro registraci';
        $body  =  
       "<p>Děkujeme za Vaši registraci. <br/>Na tento mail neodpovídejte.</p>
        <p> Kliknutím na níže uvedený odkaz dokončíte svoji registraci. Odkaz je aktivní po dobu následujících 3 hodin.</p>
                 
         <a href='auth//v1/confirm/ uid '> registrace uid  pro uid</a> 
         " ;
                
        $attachments = [
            (new Attachment())
                    ->setFileName('logo_grafia.png')
                    ->setAltText('Logo Grafia')
        ];

        $params = new Params();
        $params
            ->setHost(
                    (new Host())
                        ->setHost('smtp.cesky-hosting.cz')
                    )
            ->setSmtpAuth(
                    (new SmtpAuth())
                        ->setSmtpAuth(true)
                        ->setUserName('info@najdisi.cz')
                        ->setPassword('KroKF56uJ2pp')
                    )
            ->setEncryption(
                    (new Encryption())->setEncryption(Encryption::NONE)
                    )
            ->setHeaders(
                    (new Headers())
                        ->setHeaders(['X-Mailer' => 'veletrhprace.online'])
                    )
            ->setParty(
                    (new Party())
                        ->setFrom('info@najdisi.cz', 'veletrhprace.online')
                        ->addReplyTo('svoboda@grafia.cz', 'veletrhprace.online')
                  //      ->addTo('svoboda@grafia.cz', 'Pes')
                        ->addTo('selnerova@grafia.cz', 'vlse')
//                        ->addCc($ccAddress, $ccName)
//                        ->addBcc($bccAddress, $bccName)
                    )
            ->setContent(
                    (new Content())
                        ->setSubject($subject)
                        ->setBody($body)
                        ->setAttachments($attachments)
                    )
            ;
        return $params;
    }
    
    //-----------------------------------------------------------------------------------------------------------------------
   
    private static function body_confirm() {
        $subject =  'mail po potvrzení';
        $body  =  
        "Vaše registrace byla dokončena. Děkujeme Vám za spolupráci."   ;
                
        $attachments = [
            (new Attachment())
                    ->setFileName('logo_grafia.png')
                    ->setAltText('Logo Grafia')
        ];

        $params = new Params();
        $params
            ->setHost(
                    (new Host())
                        ->setHost('smtp.cesky-hosting.cz')
                    )
            ->setSmtpAuth(
                    (new SmtpAuth())
                        ->setSmtpAuth(true)
                        ->setUserName('info@najdisi.cz')
                        ->setPassword('KroKF56uJ2pp')
                    )
            ->setEncryption(
                    (new Encryption())->setEncryption(Encryption::NONE)
                    )
            ->setHeaders(
                    (new Headers())
                        ->setHeaders(['X-Mailer' => 'veletrhprace.online'])
                    )
            ->setParty(
                    (new Party())
                        ->setFrom('info@najdisi.cz', 'veletrhprace.online')
                        ->addReplyTo('svoboda@grafia.cz', 'veletrhprace.online')
                      //  ->addTo('svoboda@grafia.cz', 'Pes')
                        ->addTo('selnerova@grafia.cz', 'vlse')
//                        ->addCc($ccAddress, $ccName)
//                        ->addBcc($bccAddress, $bccName)
                    )
            ->setContent(
                    (new Content())
                        ->setSubject($subject)
                        ->setBody($body)
                        ->setAttachments($attachments)
                    )
            ;
        return $params;
    }
    
    //----------------------------------------------------------------------------------------------------------------
    private static function body_forgottenPassword() {
        $subject =  'mail po zapomenuti hesla';
        $body  =  
        " Děkujeme za zaslaný požadavek na vygenerování nového hesla.
          Vaše nové přihlašovací údaje jsou:
          Jméno :  loginJmeno     Heslo: generatedPassword ";
                ;
        $attachments = [
            (new Attachment())
                    ->setFileName('logo_grafia.png')
                    ->setAltText('Logo Grafia')
        ];

        $params = new Params();
        $params
            ->setHost(
                    (new Host())
                        ->setHost('smtp.cesky-hosting.cz')
                    )
            ->setSmtpAuth(
                    (new SmtpAuth())
                        ->setSmtpAuth(true)
                        ->setUserName('info@najdisi.cz')
                        ->setPassword('KroKF56uJ2pp')
                    )
            ->setEncryption(
                    (new Encryption())->setEncryption(Encryption::NONE)
                    )
            ->setHeaders(
                    (new Headers())
                        ->setHeaders(['X-Mailer' => 'veletrhprace.online'])
                    )
            ->setParty(
                    (new Party())
                        ->setFrom('info@najdisi.cz', 'veletrhprace.online')
                        ->addReplyTo('svoboda@grafia.cz', 'veletrhprace.online')
                     //   ->addTo('svoboda@grafia.cz', 'Pes')
                        ->addTo('selnerova@grafia.cz', 'vlse')
//                        ->addCc($ccAddress, $ccName)
//                        ->addBcc($bccAddress, $bccName)
                    )
            ->setContent(
                    (new Content())
                        ->setSubject($subject)
                        ->setBody($body)
                        ->setAttachments($attachments)

                    )
            ;

        return $params;
    }
    
    //-----------------------------------------------------------------------------------------------------------------------

        private static function najdisi() {
        $subject =  'Předmět +ěščřžýáíéúůďťň!!!';
        $body  =
"
<h3>Kdo je hloupější</h3>

<p>Chalupník se vydal do světa hledat člověka hloupějšího, než je jeho žena. Oklame bohatou paní báchorkou, že spadl z nebe a chce se tam vrátit. Ona mu dala peníze a košile pro svého syna, který před tím zemřel. Když se její muž vrátil a o její hlouposti se dověděl, začal chalupníka pronásledovat. Dostihl ho a ptal se ho, zda tu neviděl někoho utíkat s uzlíkem, on pravil že ano, že mu má pán pohlídat klobouk, že mám pod ním vzácného ptáka, že on toho lstivého chlapíka dohoní. Místo toho však jel domů s tím, že ženu nezbije, neboť potkal lidi hloupější, než byla ona.</p>

<h3>Chytrá horákyně</h3>

<p>Manka je dcerou chudého chalupníka, který se dostane do sporu se svým bohatým bratrem. Díky dceřině důvtipu dostane chalupník to, co mu patří, a ještě vystrojí dceři svatbu. Ta si bere pana soudce, ale musí slíbit, že se mu nebude plést do jeho věcí a soudů. Když však vidí, že její muž rozhodl jednou nespravedlivě, poruší slib a poradí chudému sedlákovi, jak přesvědčit soudce o pravdě. Muž ji za to pošle z domu, ale dovolí ji vzít si s sebou její nejmilejší věc. Chytrá horákyně ho opije a vezme si domů jeho. Soudce ráno musí uznat, že je opravdu chytrá, a spolu se vrací domů.</p>
";
        $attachments = [
            (new Attachment())
                    ->setFileName('logo_grafia.png')
                    ->setAltText('Logo Grafia')
        ];

        $params = new Params();
        $params
            ->setHost(
                    (new Host())
                        ->setHost('smtp.cesky-hosting.cz')
                    )
            ->setSmtpAuth(
                    (new SmtpAuth())
                        ->setSmtpAuth(true)
                        ->setUserName('info@najdisi.cz')
                        ->setPassword('KroKF56uJ2pp')
                    )
            ->setEncryption(
                    (new Encryption())->setEncryption(Encryption::NONE)
                    )
            ->setHeaders(
                    (new Headers())
                        ->setHeaders(['X-Mailer' => 'veletrhprace.online'])
                    )
            ->setParty(
                    (new Party())
                        ->setFrom('info@najdisi.cz', 'veletrhprace.online')
                        ->addReplyTo('svoboda@grafia.cz', 'veletrhprace.online')
                        ->addTo('svoboda@grafia.cz', 'Pes')
                        ->addTo('selnerova@grafia.cz', 'vlse')
//                        ->addCc($ccAddress, $ccName)
//                        ->addBcc($bccAddress, $bccName)
                    )
            ->setContent(
                    (new Content())
                        ->setSubject($subject)
                        ->setBody($body)
                        ->setAttachments($attachments)

                    )
            ;

        return $params;
    }
    ##########################################

    private static function vzor() {
        $params = new Params();
        $params
            ->setHost(
                    (new Host())
                        ->setHost('smtp.example.com')
                    )
            ->setSmtpAuth(
                    (new SmtpAuth())
                        ->setSmtpAuth($smtpAuth)
                        ->setUserName($userName)
                        ->setPassword($password)
                    )
            ->setEncryption(
                    (new Encryption())->setEncryption(Encryption::NONE)
                    )
            ->setHeaders(
                    (new Headers())
                        ->setHeaders($headers)
                    )
            ->setParty(
                    (new Party())
                        ->setFrom($from)
                        ->addReplyTo($replyTo)
                        ->addTo($toAddress, $toName)
                        ->addCc($ccAddress, $ccName)
                        ->addBcc($bccAddress, $bccName)
                    )
            ->setContent(
                    (new Content())
                        ->setSubject($subject)
                        ->setBody($body)
                        ->setAttachments(
                            (new Attachment())
                                ->setFileName($filaName)
                                ->setAltText($altText)
                            )
                    )
            ;

        return $params;
    }
}