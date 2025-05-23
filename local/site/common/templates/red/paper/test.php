<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Component\Renderer\Html\Content\Authored\Paper\ElementWrapper;
use Red\Component\Renderer\Html\Content\Authored\Paper\Buttons;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var ElementWrapper $elementWrapper */
/** @var Buttons $buttons */
/** @var PaperAggregatePaperSectionInterface $paperAggregate */
?>

<div data-component="presented" data-template="test" class="ui segment mceNonEditable">
    <div class="grafia segment headlined editable">
        <form>
        <article class="" >

            <section>
                    <headline class="ui header">

                        <div class="edit-text">
                            <?= $headline ?>
                        </div>
                    </headline>

                    <perex data-template="nazdar" class="edit-html obr-upoutavka borderDance">
                        <div class="ui two column stackable centered grid">
                            <div class="sixteen wide column">
                                <div class="zeleny podklad vlevo nadpis">
                                    <p>Věnujte týden svému zdraví!</p>
                                </div>
                            </div>
                            <div class="ten wide column">
                            </div>
                            <div class="six wide column">
                                <div class="cerveny podklad vpravo nadpis">
                                    <text style="position: relative; display: block;"><p>21.—27. 11. 2020</p></text>
                                </div>
                                <div class="zeleny podklad blok text okraje">
                                    <p class="podnadpis"><b>TÝDEN ZDRAVÍ</b><br/>Zdravá rodina</p>
                                    <text class="edit-text" style="position: relative; display: block;"><p><b>Zúčastněte se nové osvětové akce na podporu prevence a udržení dobrého zdravotního stavu všech generací!</b></p></text>
                                </div>
                            </div>
                        </div>
                    </perex>

            </section>
                <content class="edit-html borderDance">
                    <div class="blok-nadpis-obr-text">
                        <div class="ui stackable centered grid">
                            <div class="sixteen wide column">
                                <div class="cerveny podklad nadpis vlevo">
                                    <p>O akci</p>
                                </div>
                                <div class="velky text okraje vpravo">
                                    <p>V poslední době pod vlivem Covidu-19 lidé zanedbávali preventivní návštěvy lékařů, odsouvali i nutná ošetření a kontroly. Vlivem médií došlo u řady lidí k úzkostným a iracionálním reakcím, vedoucím až ke škodám na zdraví.</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </content>
            <content>
                <div class="zeleny-blok-obr-text">
                    <div class="ui stackable centered grid">
                        <div class="sixteen wide column">
                            <div class="edit-text velky text okraje vlevo">
                                <p>Ve spolupráci s <b>odborníky</b> proto společnost <b>Grafia</b> pořádá akci, jejímž cílem je <b>zvýšit povědomí veřejnosti o zdravém životním stylu, podpoře vlastní imunity a rozumném přístupu k vlastnímu zdraví.</b></p>
                            </div>
                        </div>
                    </div>
                </div>
            </content>
            <div>
                <div class="dva-sloupce-nadpis">
                    <div class="ui two column stackable centered grid">
                        <div class="six wide column middle aligned">
                        </div>
                        <div class="ten wide column">
                            <div class="cerveny podklad nadpis vpravo">
                                <text class="edit-text">Organizátor</text>
                            </div>
                            <div class="edit-text velky text okraje">
                                <p>Autorem myšlenky Týdne zdraví a organizátorem akce je <b>Grafia, s.r.o.</b></p>
                                <p>Umíme efektivně komunikovat akce našich zákazníků i ty vlastní, vzděláváme a zkoušíme dospělé, organizujeme eventy na klíč, vydáváme knihy... Děláme to už od roku 1993 – rádi a dobře! Budeme rádi za Vaše reakce ci připomínky ke zlepšení: pište na <b>slukova@grafia.cz nebo volejte 774 484 855</b></p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </article>
        </form>
    </div>
</div>

