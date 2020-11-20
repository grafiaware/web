<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */


    $prednaska = [
        [
            'jmeno' => 'Anička Černá',
            'funkce' => 'lektorka jógy smíchu',
            'nazevPrednasky' => 'Směje se celá rodina',
            'datumCas' => '21.11.2020, 18:00',
        ],
        [
            'jmeno' => 'JUDr. Jana Lexová',
            'funkce' => 'seniorka, která se dobrovolně věnuje sociálně právnímu poradenství pro seniory a dlouhodobě se podílí  na jejich vzdělávání',
            'nazevPrednasky' => 'Jak dospět ve stáří k jedinečnému poznání, pochopení souvislostí a k moudrosti',
            'datumCas' => '22.11.2020, 17:00',
        ],
        [
            'jmeno' => 'Renata Kurková',
            'funkce' => 'lékárnice, homeopatička',
            'nazevPrednasky' => 'Schüsslerovy soli na podporu imunity',
            'datumCas' => '22.11.2020, 18:00',
        ],
        [
            'jmeno' => 'Konstantin Wiesner',
            'funkce' => 'běžec, ultramaratonec',
            'nazevPrednasky' => 'Běhání pro zdraví',
            'datumCas' => '22.11.2020, 19:00',
        ],
        [
            'jmeno' => 'Vlastimila Faiferlíková',
            'funkce' => 'předsedkyně Správní rady a ředitelka TOTEM, garant a koordinátorka dobrovolnických projektů',
            'nazevPrednasky' => 'Rodinné vztahy a péče o seniory v dnešním světě',
            'datumCas' => '23.11.2020, 18:00',
        ],
        [
            'jmeno' => 'Alena Vrbová,  Marek Rubricius',
            'funkce' => 'krizoví interventi a sociální pracovníci Ledovec',
            'nazevPrednasky' => 'Nezblázni se z covidu',
            'datumCas' => '23.11.2020, 19:30',
        ],
        [
            'jmeno' => 'MUDr. Pavel Tomeš',
            'funkce' => 'sexuolog',
            'nazevPrednasky' => 'Odpovídá na vaše dotazy',
            'datumCas' => '24.11.2020, 18:00',
        ],
        [
            'jmeno' => 'David Brabec',
            'funkce' => 'instruktor sebeobrany, moderátor a konzultant',
            'nazevPrednasky' => 'Sebeobranou ke zdraví',
            'datumCas' => '24.11.2020, 19:30',
        ],
        [
            'jmeno' => 'prof. MUDr. Miloš Pešek, CSc.',
            'funkce' => 'přednosta plicní kliniky FN Plzeň',
            'nazevPrednasky' => 'Odpovídá na dotazy',
            'datumCas' => '25.11.2020, 18:00',
        ],
        [
            'jmeno' => 'Ing. Tomáš Kocánek',
            'funkce' => 'Klub sportovních otužilců Plzeň',
            'nazevPrednasky' => 'Jak se začít otužovat',
            'datumCas' => '25.11.2020, 19:00',
        ],
        [
            'jmeno' => 'Iveta Churavá',
            'funkce' => 'propagátorka regionálních potravin U Lidušky',
            'nazevPrednasky' => 'Opravdové jídlo z regionu',
            'datumCas' => '26.11.2020, 17:00',
        ],
        [
            'jmeno' => 'Jindra Švarcová',
            'funkce' => 'oční optik a prezidentka Lions Clubu Plzeň Bohemia, garant projektu Lví očko',
            'nazevPrednasky' => 'Nezanedbávejte své oči! A prevence očních vad u předškolních dětí',
            'datumCas' => '26.11.2020, 18:30',
        ],
        [
            'jmeno' => 'prof. MUDr. Ondřej Topolčan, CSc.',
            'funkce' => 'náměstek ředitele FN Plzeň pro vědu a výzkum, primář odd. Imunochemické diagnostiky',
            'nazevPrednasky' => 'Vitamínem D proti covidu? Přednáška a odpovědi na otázky',
            'datumCas' => '27.11.2020, 18:00',
        ],
        [
            'jmeno' => 'Anna a Eva Vopalecké',
            'funkce' => 'lektorky a choreografky Studio FITNESSKA',
            'nazevPrednasky' => 'Tančete s námi',
            'datumCas' => '27.11.2020, 19:15',
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
                <?= $this->mono('Anna a Eva Vopalecké ze Studia FITNESSKA pro dobrou náladu posílají antikoronavirový tanec! Zatančete si s nimi!')?>
            </p>
            <video width="500" height="500" controls playsinline> <!--   autoplay muted>-->
                <source src="video/video-fitneska1-500x500-H264.m4v">
                <source src="video/video-fitneska1-500x500.webm">
                <source src="video/video-fitneska1.mov">
                <p>Váš prohlížeč nepodporuje přehrávání videa, video si <a href="video/video-fitneska1-500x500-H264.m4v">stáhněte</a>.</p>
            </video>
        </div>
    </div>
</div>
