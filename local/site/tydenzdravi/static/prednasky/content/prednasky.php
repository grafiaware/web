<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */


    $prednaska = [
        [
            'jmeno' => 'Anička Černá',
            'funkce' => 'lektorka jógy smíchu',
            'nazevPrednasky' => 'Směje se celá rodina',
            'datumCas' => '21.11.2020, 18:00',
            'odkazPrednaskyAttributes' => [
                'class' => 'ui large secondary button',
                'href' => 'https://www.youtube.com/watch?v=ZMsy1JSXGNM',
                'target' => '_blank'
            ],
            'odkazPrednaskyText' => 'Zhlédnout záznam'
        ],
        [
            'jmeno' => 'JUDr. Jana Lexová',
            'funkce' => 'seniorka, která se dobrovolně věnuje sociálně právnímu poradenství pro seniory a dlouhodobě se podílí  na jejich vzdělávání',
            'nazevPrednasky' => 'Jak dospět ve stáří k jedinečnému poznání, pochopení souvislostí a k moudrosti',
            'datumCas' => '22.11.2020, 17:00',
            'odkazPrednaskyAttributes' => [
                'class' => 'ui large secondary button',
                'href' => 'https://www.youtube.com/watch?v=j51qncvBQ2M',
                'target' => '_blank'
            ],
            'odkazPrednaskyText' => 'Zhlédnout záznam'
        ],
        [
            'jmeno' => 'Renata Kurková',
            'funkce' => 'lékárnice, homeopatička',
            'nazevPrednasky' => 'Schüsslerovy soli na podporu imunity',
            'datumCas' => '22.11.2020, 18:00',
            'odkazPrednaskyAttributes' => [
                'class' => 'ui large secondary button',
                'href' => 'https://www.youtube.com/watch?v=MhsHyekfg0I',
                'target' => '_blank'
            ],
            'odkazPrednaskyText' => 'Zhlédnout záznam'
        ],
        [
            'jmeno' => 'Konstantin Wiesner',
            'funkce' => 'běžec, ultramaratonec',
            'nazevPrednasky' => 'Běhání pro zdraví',
            'datumCas' => '22.11.2020, 19:00',
            'odkazPrednaskyAttributes' => [
                'class' => 'ui large secondary button',
                'href' => 'https://www.youtube.com/watch?v=qqwjsTFjsdk',
                'target' => '_blank'
            ],
            'odkazPrednaskyText' => 'Zhlédnout záznam'
        ],
        [
            'jmeno' => 'Vlastimila Faiferlíková',
            'funkce' => 'předsedkyně Správní rady a ředitelka TOTEM, garant a koordinátorka dobrovolnických projektů',
            'nazevPrednasky' => 'Rodinné vztahy a péče o seniory v dnešním světě',
            'datumCas' => '23.11.2020, 18:00',
            'odkazPrednaskyAttributes' => [
                'class' => 'ui large secondary button',
                'href' => 'https://www.youtube.com/watch?v=DqXlW0ceNfk',
                'target' => '_blank'
            ],
            'odkazPrednaskyText' => 'Zhlédnout záznam'
        ],
        [
            'jmeno' => 'Alena Vrbová,  Marek Rubricius',
            'funkce' => 'krizoví interventi a sociální pracovníci Ledovec',
            'nazevPrednasky' => 'Nezblázni se z covidu',
            'datumCas' => '23.11.2020, 19:30',
            'odkazPrednaskyAttributes' => [
                'class' => 'ui large secondary button',
                'href' => 'https://www.youtube.com/watch?v=x3GbQ0GsKac',
                'target' => '_blank'
            ],
            'odkazPrednaskyText' => 'Zhlédnout záznam'
        ],
        [
            'jmeno' => 'MUDr. Pavel Tomeš',
            'funkce' => 'sexuolog',
            'nazevPrednasky' => 'Odpovídá na vaše dotazy',
            'datumCas' => '24.11.2020, 18:00',
            'odkazPrednaskyAttributes' => [
                'class' => 'ui large secondary button',
                'href' => 'https://www.youtube.com/watch?v=eDktwkP6QlU',
                'target' => '_blank'
            ],
            'odkazPrednaskyText' => 'Zhlédnout záznam'
        ],
        [
            'jmeno' => 'David Brabec',
            'funkce' => 'instruktor sebeobrany, moderátor a konzultant',
            'nazevPrednasky' => 'Sebeobranou ke zdraví',
            'datumCas' => '24.11.2020, 19:30',
            'odkazPrednaskyAttributes' => [
                'class' => 'ui large secondary button',
                'href' => 'https://www.youtube.com/watch?v=AZ5oWT_WeBU',
                'target' => '_blank'
            ],
            'odkazPrednaskyText' => 'Zhlédnout záznam'
        ],
        [
            'jmeno' => 'prof. MUDr. Miloš Pešek, CSc.',
            'funkce' => 'přednosta plicní kliniky FN Plzeň',
            'nazevPrednasky' => 'Odpovídá na dotazy',
            'datumCas' => '25.11.2020, 18:00',
            'odkazPrednaskyAttributes' => [
                'class' => 'ui large secondary button',
                'href' => 'https://www.youtube.com/watch?v=FkwRhmQyoB4',
                'target' => '_blank'
            ],
            'odkazPrednaskyText' => 'Zhlédnout záznam'
        ],
        [
            'jmeno' => 'Ing. Tomáš Kocánek',
            'funkce' => 'Klub sportovních otužilců Plzeň',
            'nazevPrednasky' => 'Jak se začít otužovat',
            'datumCas' => '25.11.2020, 19:00',
            'odkazPrednaskyAttributes' => [
                'class' => 'ui large secondary button',
                'href' => 'https://www.youtube.com/watch?v=Zhl5HFH-GAw',
                'target' => '_blank'
            ],
            'odkazPrednaskyText' => 'Zhlédnout záznam'
        ],
        [
            'jmeno' => 'Iveta Churavá',
            'funkce' => 'propagátorka regionálních potravin U Lidušky',
            'nazevPrednasky' => 'Opravdové jídlo z regionu',
            'datumCas' => '26.11.2020, 17:00',
            'odkazPrednaskyAttributes' => [
                'class' => 'ui large secondary button',
                'href' => 'https://www.youtube.com/watch?v=7kxxnn7_x-o',
                'target' => '_blank'
            ],
            'odkazPrednaskyText' => 'Zhlédnout záznam'
        ],
        [
            'jmeno' => 'Jindra Švarcová',
            'funkce' => 'oční optik a prezidentka Lions Clubu Plzeň Bohemia, garant projektu Lví očko',
            'nazevPrednasky' => 'Nezanedbávejte své oči! A prevence očních vad u předškolních dětí',
            'datumCas' => '26.11.2020, 18:30',
            'odkazPrednaskyAttributes' => [
                'class' => 'ui large secondary button',
                'href' => 'https://www.youtube.com/watch?v=ako7UOkyFRY',
                'target' => '_blank'
            ],
            'odkazPrednaskyText' => 'Zhlédnout záznam'
        ],
        [
            'jmeno' => 'prof. MUDr. Ondřej Topolčan, CSc.',
            'funkce' => 'náměstek ředitele FN Plzeň pro vědu a výzkum, primář odd. Imunochemické diagnostiky',
            'nazevPrednasky' => 'Vitamínem D proti covidu? Přednáška a odpovědi na otázky',
            'datumCas' => '27.11.2020, 18:00',
            'odkazPrednaskyAttributes' => [
                'class' => 'ui large secondary button',
                'href' => 'https://www.youtube.com/watch?v=Tt-9i3HaaKM',
                'target' => '_blank'
            ],
            'odkazPrednaskyText' => 'Zhlédnout záznam'
        ],
        [
            'jmeno' => 'Anna a Eva Vopalecké',
            'funkce' => 'lektorky a choreografky Studio FITNESSKA',
            'nazevPrednasky' => 'Tančete s námi',
            'datumCas' => '27.11.2020, 19:15',
            'odkazPrednaskyAttributes' => [
                'class' => 'ui large secondary button',
                'href' => 'https://www.youtube.com/watch?v=4_T0CvnqMsM',
                'target' => '_blank'
            ],
            'odkazPrednaskyText' => 'Zhlédnout záznam'
        ]
    ]
?>

<div class="prednasejici">
    <div class="ui two column internally celled grid centered">
        <div class="stretched row">
            <div class="eight wide column"><p><b>Přednášející</b></p></div>
            <div class="eight wide column"><p><b>Název přednášky</b></p></div>
        </div>
        <?= $this->repeat(__DIR__.'/prednasky/prednaska.php', $prednaska) ?>
    </div>
    <div class="ui grid centered">
        <div class="sixteen wide column center aligned">
            <p>
                <?= Text::mono('Anna a Eva Vopalecké ze Studia FITNESSKA pro dobrou náladu posílají antikoronavirový tanec! Zatančete si s nimi!')?>
            </p>
            <video width="500" height="500" controls playsinline> <!--   autoplay muted>-->
                <source src="@movies/video-fitneska1-500x500-H264.m4v">
                <source src="@movies/video-fitneska1-500x500.webm">
                <source src="@movies/video-fitneska1.mov">
                <p>Váš prohlížeč nepodporuje přehrávání videa, video si <a href="video/video-fitneska1-500x500-H264.m4v">stáhněte</a>.</p>
            </video>
        </div>
    </div>
</div>
