<?php

namespace Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use Psr\Log\LoggerInterface;

use Mail\Params;
use Mail\Params\Attachment;
use Mail\Exception\MailException;

/**
 * Description of Mail
 *
 * @author pes2704
 */
class Mail {

    /**
     *
     * @var Params
     */
    private $params;
    private static $logger;

    public function __construct(Params $params = null, LoggerInterface $logger) {
        $this->params = $params;
        self::$logger = $logger;
    }

    /**
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
                $decodedSubject = base64_decode(str_replace("=?utf-8?B?", "", $subject));
                self::$logger->info("Result: '{result}'.", ['result'=>$result]);
                self::$logger->info("Odeslán mail '{subject}' na adresy {to}.", ['subject'=>$decodedSubject, 'to'=>implode(', ', $to)]);
            } else {
                self::$logger->warning("Nepodařilo se odeslat mail '{subject}' na adresy {to}.", ['subject'=>$subject, 'to'=>implode(', ', $to)]);
            }
        }
    }

    public function mail(Params $params = null) {
        $actualParams = $this->params ;
        if (isset($params)) {
            $actualParams->adotpConfugurationParams($params);
        }

        //Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
//            $mail->SMTPDebug = SMTP::DEBUG_CONNECTION;                      //Enable verbose debug output
            $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP

            $mail->Host = $actualParams->getHost()->getHost();                      //Set the SMTP server to send through
            $mail->SMTPSecure = $actualParams->getEncryption()->getSmtpSecure();
            $mail->Port = $actualParams->getEncryption()->getPort();

            //Whether to use SMTP authentication
            $mail->SMTPAuth = $actualParams->getSmtpAuth()->getSmtpAuth();
            //Username to use for SMTP authentication
            $mail->Username = $actualParams->getSmtpAuth()->getUserName();
            //Password to use for SMTP authentication
            $mail->Password = $actualParams->getSmtpAuth()->getPassword();

            $mail->Encoding = '8bit'; 

            //Recipients
            $from = $actualParams->getParty()->getFrom();
            $mail->setFrom($from[0], $from[1]);
            foreach ($actualParams->getParty()->getToArray() as $to) {
                $mail->addAddress($to[0], $to[1]);     //Add a recipient            //Name is optional
            };

            //Attachments
            foreach ($actualParams->getContent()->getAttachments() as $attachment) {
                /** @var Attachment $attachment */
                $mail->addAttachment($attachment->getFileName(), $attachment->getAltText());      //Add attachments  //Optional name
            }

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $actualParams->getContent()->getSubject();
            $mail->msgHTML($actualParams->getContent()->getHtml(), __DIR__);
            $mail->AltBody = $actualParams->getContent()->getAltBody();
            $mail->CharSet = "UTF-8";
            $mail->action_function = Mail::class.'::actionOnSend';

            $mail->send();

        } catch (Exception $e) {
            if (self::$logger) {
                self::$logger->error("Nepodařilo se odeslat mail '{subject}' na adresy {to}.", ['subject'=>$actualParams->getContent()->getSubject(), 'to'=>implode(', ', $actualParams->getParty()->getToArray())]);
                self::$logger->error("PHPmail error info:: '{info}'.", ['info'=>$mail->ErrorInfo]);
                self::$logger->error("PHPmail exception message: '{message}'.", ['message'=>$e->errorMessage()]);
                echo $mail->ErrorInfo;
            }
            throw new MailException("Nepodařilo se odeslat mail", 0, $e);
        }
    }
}
