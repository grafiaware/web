<?php

require '../../../vendor/autoload.php';

use Mail\Mail;
use Mail\ParamsContainer;
use Mail\Params;
use Mail\Params\{Content, Attachment, Party};


$configuration = ParamsContainer::params('najdisi');

$mail = new Mail($configuration);

$subject =  'Předmět mailu.';

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

$content = (new Content())
            ->setSubject($subject)
            ->setHtml($body)
            ->setAttachments($attachments);

$party = (new Party())
            ->setFrom('info@najdisi.cz', 'veletrhprace.online')
            ->addReplyTo('svoboda@grafia.cz', 'veletrhprace.online')
            ->addTo('svoboda@grafia.cz', 'návštěvník')
//            ->addTo('selnerova@grafia.cz', 'vlse')
//            ->addCc($ccAddress, $ccName)
//            ->addBcc($bccAddress, $bccName)
        ;

$params = (new Params())->setContent($content)->setParty($party);

$mail->mail($params);
