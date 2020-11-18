<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>

<div class="prednasejici">
    <div class="ui two column internally celled grid centered">
        <div class="stretched row">
            <div class="eight wide column"><p><b>Přednášející</b></p></div>
            <div class="eight wide column"><p><b>Název přednášky</b></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>prof. MUDr. Miloš Pešek, CSc.</b></p><p><?= $this->mono('přednosta plicní kliniky FN Plzeň')?> </p></div>
            <div class="eight wide column"><p><?= $this->mono('<b>Odpovídá na dotazy</b>')?></p><p>25.11.2020, 18:00</p><p><a class="ui large secondary button" href="https://forms.gle/aUoufuqLyaSjm6rE8" target="_blank">Přihlásit se / Položit dotaz</a></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>prof. MUDr. Ondřej Topolčan, CSc.</b></p><p><?= $this->mono('náměstek ředitele FN Plzeň pro vědu a výzkum, primář odd. Imunochemické diagnostiky')?> </p></div>
            <div class="eight wide column"><p><?= $this->mono('<b>Vitamínem D proti covidu? Přednáška a odpovědi na otázky</b>')?></p><p>27.11.2020, 18:00</p><p><a class="ui large secondary button" href="https://forms.gle/aUoufuqLyaSjm6rE8" target="_blank">Přihlásit se / Položit dotaz</a></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>Konstantin Wiesner</b></p><p><?= $this->mono('běžec, ultramaratonec')?></p></div>
            <div class="eight wide column"><p><?= $this->mono('<b>Běhání pro zdraví</b>')?></p>22.11.2020, 19:00<p></p><p><a class="ui large secondary button" href="https://forms.gle/aUoufuqLyaSjm6rE8" target="_blank">Přihlásit se / Položit dotaz</a></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>Iveta Churavá</b></p><p><?= $this->mono('propagátorka regionálních potravin U Lidušky')?></p></div>
            <div class="eight wide column"><p><?= $this->mono('<b>Opravdové jídlo z regionu</b>')?></p><p>26.11.2020, 17:00</p><p><a class="ui large secondary button" href="https://forms.gle/aUoufuqLyaSjm6rE8" target="_blank">Přihlásit se / Položit dotaz</a></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>Renata Kurková</b></p><p><?= $this->mono('lékárnice, homeopatička')?></p></div>
            <div class="eight wide column"><p><?= $this->mono('<b>Schüsslerovy soli na podporu imunity</b>')?></p><p>22.11.2020, 18:00</p><p><a class="ui large secondary button" href="https://forms.gle/aUoufuqLyaSjm6rE8" target="_blank">Přihlásit se / Položit dotaz</a></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>Alena Vrbová,  Marek Rubricius</b></p><p><?= $this->mono('krizový interventi a sociální pracovníci Ledovec')?></p></div>
            <div class="eight wide column"><p><?= $this->mono('<b>Nezblázni se z covidu</b>')?></p><p>23.11.2020, 19:30</p><p><a class="ui large secondary button" href="https://forms.gle/aUoufuqLyaSjm6rE8" target="_blank">Přihlásit se / Položit dotaz</a></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>Jindra Švarcová</b></p><p><?= $this->mono('oční optik a prezidentka Lions Clubu Plzeň Bohemia, garant projektu Lví očko')?></p></div>
            <div class="eight wide column"><p><?= $this->mono('<b>Nezanedbávejte své oči! A prevence očních vad u předškolních dětí</b>')?></p><p>26.11.2020, 18:30</p><p><a class="ui large secondary button" href="https://forms.gle/aUoufuqLyaSjm6rE8" target="_blank">Přihlásit se / Položit dotaz</a></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>Ing. Tomáš Kocánek</b></p><p><?= $this->mono('Klub sportovních otužilců Plzeň')?></p></div>
            <div class="eight wide column"><p><?= $this->mono('<b>Jak se začít otužovat</b>')?></p><p>25.11.2020, 19:00</p><p><a class="ui large secondary button" href="https://forms.gle/aUoufuqLyaSjm6rE8" target="_blank">Přihlásit se / Položit dotaz</a></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>MUDr. Pavel Tomeš</b></p><p><?= $this->mono('sexuolog')?></p></div>
            <div class="eight wide column"><p><?= $this->mono('<b>Odpovídá na vaše dotazy</b>')?></p><p>24.11.2020, 18:00</p><p><a class="ui large secondary button" href="https://forms.gle/aUoufuqLyaSjm6rE8" target="_blank">Přihlásit se / Položit dotaz</a></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>Anička Černá</b></p><p><?= $this->mono('lektorka jógy smíchu')?></p></div>
            <div class="eight wide column"><p><?= $this->mono('<b>Směje se celá rodina</b>')?></p><p>21.11.2020, 18:00</p><p><a class="ui large secondary button" href="https://forms.gle/aUoufuqLyaSjm6rE8" target="_blank">Přihlásit se / Položit dotaz</a></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>David Brabec</b></p><p><?= $this->mono('instruktor sebeobrany, moderátor a konzultant')?></p></div>
            <div class="eight wide column"><p><?= $this->mono('<b>Sebeobranou ke zdraví</b>')?></p><p>24.11.2020, 19:30</p><p><a class="ui large secondary button" href="https://forms.gle/aUoufuqLyaSjm6rE8" target="_blank">Přihlásit se / Položit dotaz</a></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>Vlastimila Faiferlíková</b></p><p><?= $this->mono('Předsedkyně Správní rady a ředitelka TOTEM, garant a koordinátorka dobrovolnických projektů')?></p></div>
            <div class="eight wide column"><p><?= $this->mono('<b>Rodinné vztahy a péče o seniory v dnešním světě</b>')?></p><p>23.11.2020, 18:00</p><p><a class="ui large secondary button" href="https://forms.gle/aUoufuqLyaSjm6rE8" target="_blank">Přihlásit se / Položit dotaz</a></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>Anna a Eva Vopalecké</b></p><p><?= $this->mono('lektorky ve studiu Fitness KA')?></p></div>
            <div class="eight wide column"><p><?= $this->mono('<b>Tančete s námi</b>')?></p><p><a class="ui large secondary button" href="https://forms.gle/aUoufuqLyaSjm6rE8" target="_blank">Přihlásit se / Položit dotaz</a></p></div>
        </div>
    </div>
    <div class="ui grid centered">
        <div class="sixteen wide column center aligned">
            <p>
                <?= $this->mono('Anna a Eva Vopalecké ze studia Fitness KA pro dobrou náladu posílají antikoronavirový tanec! Zatančete si s nimi!')?>
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
