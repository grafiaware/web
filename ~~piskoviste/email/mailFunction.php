<?php


// , petrova.schranka@gmail.com
$message = new class {
    private $to = 'svoboda@grafia.cz';  //, selnerova@grafia.cz';
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

echo '<p>'.implode('</p><p>', [
    $message->getTo() , $message->getSubject() , $message->getBody() , implode('<br>', $message->getAdditionalHeaders()) , $message->getAdditionalParams()
]);

$success = mail ( $message->getTo() , $message->getSubject() , $message->getBody() , $message->getAdditionalHeaders() , $message->getAdditionalParams() );

