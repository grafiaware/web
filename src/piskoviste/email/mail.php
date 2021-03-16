<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require '../../../vendor/autoload.php';

################################################################

// , petrova.schranka@gmail.com
$message = new class {
    private $to = 'svoboda@grafia.cz, selnerova@grafia.cz';
    private $subject = 'Předmět +ěščřžýáíéúůďťň!!!';
    private $body = "
<h3>Kdo je hloupější</h3>

<p>Chalupník se vydal do světa hledat člověka hloupějšího, než je jeho žena. Oklame bohatou paní báchorkou, že spadl z nebe a chce se tam vrátit. Ona mu dala peníze a košile pro svého syna, který před tím zemřel. Když se její muž vrátil a o její hlouposti se dověděl, začal chalupníka pronásledovat. Dostihl ho a ptal se ho, zda tu neviděl někoho utíkat s uzlíkem, on pravil že ano, že mu má pán pohlídat klobouk, že mám pod ním vzácného ptáka, že on toho lstivého chlapíka dohoní. Místo toho však jel domů s tím, že ženu nezbije, neboť potkal lidi hloupější, než byla ona.</p>

<h3>Chytrá horákyně</h3>

<p>Manka je dcerou chudého chalupníka, který se dostane do sporu se svým bohatým bratrem. Díky dceřině důvtipu dostane chalupník to, co mu patří, a ještě vystrojí dceři svatbu. Ta si bere pana soudce, ale musí slíbit, že se mu nebude plést do jeho věcí a soudů. Když však vidí, že její muž rozhodl jednou nespravedlivě, poruší slib a poradí chudému sedlákovi, jak přesvědčit soudce o pravdě. Muž ji za to pošle z domu, ale dovolí ji vzít si s sebou její nejmilejší věc. Chytrá horákyně ho opije a vezme si domů jeho. Soudce ráno musí uznat, že je opravdu chytrá, a spolu se vrací domů.</p>
";
    private $additionalHeaders = [
        'From' => 'webmaster@example.com',
        'Reply-To' => 'svoboda@grafia.cz',
        'X-Mailer' => 'VP'
    ];
    private $additionalParams = '';

    public function getTo() {
        return $this->to;
    }

    public function getSubject() {
        return $this->subject;
    }

    public function getBody() {
        // In case any of our lines are larger than 70 characters, we should use wordwrap()
//        return wordwrap($this->body, 70, PHP_EOL);
        return $this->body;
    }

    public function getAdditionalHeaders() {
        return $this->additionalHeaders;
    }

    public function getAdditionalParams() {
        return $this->additionalParams;
    }
};

//echo '<p>'.implode('</p><p>', [
//    $message->getTo() , $message->getSubject() , $message->getMessage() , implode('<br>', $message->getAdditionalHeaders()) , $message->getAdditionalParams()
//]);
//
//$success = mail ( $message->getTo() , $message->getSubject() , $message->getBody() , $message->getAdditionalHeaders() , $message->getAdditionalParams() );

#######################################################

//Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'posta.grafia.cz';                      //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'user@example.com';                     //SMTP username
    $mail->Password   = 'secret';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 25; //465;                                     //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
    //Whether to use SMTP authentication
    $mail->SMTPAuth = false;
    //Username to use for SMTP authentication
    $mail->Username = 'allmail@grafia.cz';
    //Password to use for SMTP authentication
    $mail->Password = 'Liamlla123';
    //Recipients
    $mail->setFrom('from@example.com', 'Mailer');
    $mail->addAddress('svoboda@grafia.cz', 'Pes');     //Add a recipient
//    $mail->addAddress('selnerova@grafia.cz', 'vlse');     //Name is optional
//    $mail->addAddress('petrova.schranka@gmail.com', 'schranka');     //Name is optional
//    $mail->addReplyTo('info@example.com', 'Information');
//    $mail->addCC('it@grafia.cz');
//    $mail->addBCC('petrova.posta@seznam.cz');

    //Attachments
    $mail->addAttachment('logo_grafia.png', 'Logo Grafia');         //Add attachments  //Optional name

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $message->getSubject();
    $mail->Body    = $message->getBody();
//    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}