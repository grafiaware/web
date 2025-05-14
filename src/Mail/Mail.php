<?php

namespace Mail;

use Mail\MailInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use Psr\Log\LoggerInterface;

use Mail\AssemblyInterface;
use Mail\Params\Attachment;
use Mail\Params\StringAttachment;
use Mail\Exception\MailException;

/**
 * Description of Mail
 *
 * @author pes2704
 */
class Mail implements MailInterface {

    const CHARSET = "UTF-8";
    const ENCODING = "8bit";
    
    /*
     * PHPMailer
     */
    private $phpMailer;
    
    /**
     *
     * @var Assembly
     */
    private $params;
    private static $logger;

    /**
     * Přijímá výchozí sadu parametrů. Tyto parametru mohou být doplněny nebo zaměněny dalšími paramatery zadanými 
     * při odesílání mailu metodou mail(). Do konstuktoru je vhodné zadat sahu parametrů, které budou shodné pro včechny maily 
     * odesílané při běhu skriptu. 
     * 
     * Typicky se jedná a sadu:
     * - smtp host (objekt Mail\Params\Host)
     * - smtp autentikace pro přístup k smtp hostu (objekt Mail\Params\SmtpAuth)
     * - šifrování mailů (objekt Mail\Params\Encryption)
     * - nastavení základní sady hlaviček (objekt Mail\Params\Headers)
     * 
     * @param PHPMailer $phpMailer
     * @param AssemblyInterface $params
     * @param LoggerInterface $logger
     */
    public function __construct(PHPMailer $phpMailer, AssemblyInterface $params = null, LoggerInterface $logger = null) {
        $this->phpMailer = $phpMailer;
        $this->params = $params;
        self::$logger = $logger;
    }

    /**
     * Metoda pro PHPMailer. Metodu volá PHPMailer po pokusu o odeslání mailu. Metoda slouží pro logování a odeslání 
     * informačních mailů např. o úspěchu nebo neúspěchu
     *  
     * Info PHPMailer:
     * Use: [MyClass::class, 'myStaticCallback']
     *
     *
     * @param bool $result
     * @param array $to
     * @param array $cc
     * @param array $bcc
     * @param string $subject
     * @param string $body
     * @param string $from
     * @param string $extra
     *
     */
    public static function actionOnSend(bool $result, array $to, array $cc, array $bcc, string $subject, string $body, string $from, array $extra) {
        /**
         * Callback Action function name.
         *
         * The function that handles the result of the send email action.
         * It is called out by send() for each email sent.
         *
         * Value can be any php callable: http://www.php.net/is_callable
         *
         * Parameters:
         *   bool $result        result of the send action
         *   array   $to            email addresses of the recipients
         *   array   $cc            cc email addresses
         *   array   $bcc           bcc email addresses
         *   string  $subject       the subject
         *   string  $body          the email body
         *   string  $from          email address of sender
         *   array  $extra         extra information of possible use
         *                          "smtp_transaction_id' => last smtp transaction id
         *
         * @var string
         */
        if (self::$logger) {
            if ($result) {
                $toString = implode(', ', array_map(function($pair) {return implode('|', $pair);}, $to));  // $to je pole polí - pole dvojic hodnot 'adresa' a 'jméno adresáta'
                $time = (new \DateTime())->format("Y-m-d H:i:s");
                $decodedSubject = base64_decode(str_replace("=?utf-8?B?", "", $subject));
                self::$logger->info("[$time] Result: '{result}'.", ['result'=>$result]);
                self::$logger->info("[$time] Odeslán mail '{subject}' na adresy {to}.", ['subject'=>$decodedSubject, 'to'=>$toString]);
            } else {
                self::$logger->warning("[$time] Nepodařilo se odeslat mail '{subject}' na adresy {to}.", ['subject'=>$subject, 'to'=>$toString]);
            }
        }
    }

    /**
     * Metoda odešle mail pomocí odesílacího objektu PHPMailer.
     * 
     * Metoda nakonfiguruje odesílací objekt pomocí parametrů předaných do konstruktoru a parametrů předaných metodě. 
     * Parametry předané metodě mají přednost před parametry konstruktoru, použijí se jen pro jedno volání metody mail(), 
     * nepřepisují trvale parametry konstruktoru. 
     * 
     * Pevně zadané parametry mailu jsou: kódování 8 bit, UTF-8.
     *  
     * Parametry metody typicky obsahují:
     * - odesílatele, sadu příjemců, příjemců kopie, příjemců skryté kopie (objekt Mail\Params\Party)
     * - předmět mailu, tělo mailu, přílohy (objekt objekt Mail\Params\Content)
     * 
     * 
     * @param Assembly $params Parametry aktuálně odesílaného mailu
     * @return bool false on error 
     */
    public function mail(AssemblyInterface $params = null): bool {
        $actualParams = $this->params ;
        if (isset($params)) {
            $actualParams->adoptConfigurationParams($params);
        }

        try {
            //Server settings
//            $this->phpMailer->SMTPDebug = SMTP::DEBUG_CONNECTION;                      //Enable verbose debug output
            $this->phpMailer->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
//            
//            zakomentováno $this->phpMailer->isSMTP(); přidáno $this->phpMailer->isMail(); zakomentovány řádky pod $this->phpMailer->Port používající metodu ->getSmtpAuth()
            $this->phpMailer->isSMTP();                                            //Send using SMTP
//            $this->phpMailer->isMail();                                            //Send using SMTP

            $this->phpMailer->Host = $actualParams->getHost()->getHost();                      //Set the SMTP server to send through
            $this->phpMailer->SMTPSecure = $actualParams->getEncryption()->getSmtpSecure();
            $this->phpMailer->Port = $actualParams->getEncryption()->getPort();

            //Whether to use SMTP authentication
            $this->phpMailer->SMTPAuth = $actualParams->getSmtpAuth()->getSmtpAuth();
            //Username to use for SMTP authentication
            $this->phpMailer->Username = $actualParams->getSmtpAuth()->getUserName();
            //Password to use for SMTP authentication
            $this->phpMailer->Password = $actualParams->getSmtpAuth()->getPassword();

            $this->phpMailer->Encoding = self::ENCODING;

            //Recipients
            $from = $actualParams->getParty()->getFrom();
            $this->phpMailer->setFrom($from[0], $from[1]);
            foreach ($actualParams->getParty()->getToArray() as $to) {
                $this->phpMailer->addAddress($to[0], $to[1]);     //Add a recipient            //Name is optional
            };
            foreach ($actualParams->getParty()->getCcArray() as $cc) {
                $this->phpMailer->addCC($cc[0], $cc[1]);     //Add a cc            //Name is optional
            };
            foreach ($actualParams->getParty()->getBccArray() as $bcc) {
                $this->phpMailer->addBCC($bcc[0], $bcc[1]);     //Add a bcc            //Name is optional
            };            
            //Attachments
            foreach ($actualParams->getContent()->getAttachments() as $attachment) {
                if ($attachment instanceof Attachment) {
                    $this->phpMailer->addAttachment($attachment->getFileName(), $attachment->getAltText());      //Add attachments  //Optional name
                }
                if ($attachment instanceof StringAttachment) {
                    $this->phpMailer->addStringAttachment($attachment->getStringAttachment(), $attachment->getAltText());      //Add attachments  //Optional name
                }
            }

            //Content
            $this->phpMailer->isHTML(true);                                  //Set email format to HTML
            $this->phpMailer->Subject = $actualParams->getContent()->getSubject();
            $this->phpMailer->msgHTML($actualParams->getContent()->getHtml(), __DIR__);
            $this->phpMailer->AltBody = $actualParams->getContent()->getAltBody();
            $this->phpMailer->CharSet = self::CHARSET;
            $this->phpMailer->action_function = Mail::class.'::actionOnSend';

            return $this->phpMailer->send();

        } catch (Exception $e) {
            if (self::$logger) {
                $time = (new \DateTime())->format("Y-m-d H:i:s");
                self::$logger->error("[$time] Nepodařilo se odeslat mail '{subject}'.", ['subject'=>$actualParams->getContent()->getSubject()]);
                self::$logger->error("PHPmail error info:: '{info}'.", ['info'=>$this->phpMailer->ErrorInfo]);
                self::$logger->error("PHPmail exception message: '{message}'.", ['message'=>$e->errorMessage()]);
                echo "<h4>Mailerror info:</h4>".PHP_EOL.$this->phpMailer->ErrorInfo;
            }
            throw new MailException("Nepodařilo se odeslat mail", 0, $e);
        }
    }
}
