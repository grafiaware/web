<?php

namespace Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use Mail\Params;
use Mail\Params\Attachment;

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

    public function __construct(Params $params = null) {
        $this->params = $params;
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
        echo '!action!';
        $a = 1;
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
            $mail->Body    = $actualParams->getContent()->getBody();
            $mail->AltBody = $actualParams->getContent()->getAltBody();

            $mail->action_function = Mail::class.'::actionOnSend';


            $mail->send();
            $message =  'Message has been sent';
        } catch (Exception $e) {
            $message =  "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
        return $message;
        }
}
