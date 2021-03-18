<?php

namespace Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use Mail\Configuration;
use Mail\Params\Attachment;

/**
 * Description of Mail
 *
 * @author pes2704
 */
class Mail {

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
    public static function actionOnSend(bool $result, array   $to, array   $cc, array   $bcc, string  $subject, string  $body, string  $from, string  $extra) {
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
         *   string  $extra         extra information of possible use
         *                          "smtp_transaction_id' => last smtp transaction id
         *
         * @var string
         */
        $a = 1;
    }

    public function mail() {
        $configuration = Configuration::params('najdisi');

        //Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_LOWLEVEL;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP

            $mail->Host = $configuration->getHost()->getHost();                      //Set the SMTP server to send through
            $mail->SMTPSecure = $configuration->getEncryption()->getSmtpSecure();
            $mail->Port = $configuration->getEncryption()->getPort();

            //Whether to use SMTP authentication
            $mail->SMTPAuth = $configuration->getSmtpAuth()->getSmtpAuth();
            //Username to use for SMTP authentication
            $mail->Username = $configuration->getSmtpAuth()->getUserName();
            //Password to use for SMTP authentication
            $mail->Password = $configuration->getSmtpAuth()->getPassword();

            //Recipients
            $from = $configuration->getParty()->getFrom();
            $mail->setFrom($from[0], $from[1]);
            foreach ($configuration->getParty()->getToArray() as $to) {
                $mail->addAddress($to[0], $to[1]);     //Add a recipient            //Name is optional
            };

            //Attachments
            foreach ($configuration->getContent()->getAttachments() as $attachment) {
                /** @var Attachment $attachment */
                $mail->addAttachment($attachment->getFileName(), $attachment->getAltText());      //Add attachments  //Optional name
            }

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $configuration->getContent()->getSubject();
            $mail->Body    = $configuration->getContent()->getBody();
            $mail->AltBody = $configuration->getContent()->getAltBody();

            $mail->action_function =[Mail::class, 'actionOnSend'];


            $mail->send();
            $message =  'Message has been sent';
        } catch (Exception $e) {
            $message =  "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
        return $message;
        }
}
