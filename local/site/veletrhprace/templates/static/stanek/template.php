<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Html;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
$modalAtributy = [
    "id" => "modal_12",
    "class"=> ["ui tiny longer", "modal"]
    
]

?>

<article class="paper">
    <section>
        <headline>
            <?php include "headline.php" ?>
        </headline>
        <perex>
            <?php include "perex.php" ?>
        </perex>
    </section>
    <section>    
        <content>
            <?php include "content/video.php" ?>
        </content>
        <content>
            <?php include "content/stanek.php" ?>
        </content>
    </section>
</article>

                
                    <!--<form>-->
                    <content id="content_12"> 
                        <div <?= Html::attributes($modalAtributy)?>>                                 
                            <i class="close icon"></i>
                            <div class="header">
                                <p>Přednášky</p>
                                <p>Kliknutím na ikonu vedle času si přednášku zarezervujete.</p>
                            </div>
                            <div class="header">
                                <p>Název firmy</p>
                            </div>
                            <div class="scrolling content">
                                <div class="prednaska-stanku">
                                    <div class="prednasejici">
                                        <img class="img-prednasejici" src="images/woman.jpg" alt="člověk" width="120" height="120"/>
                                        <p class="">Jméno příjmení</p>
                                        <p>pozice</p>
                                    </div>
                                    <div class="vypis-prednasek">
                                        <div class="ui grid">
                                            <div class="row">
                                                <p>Název přednášky</p>
                                            </div>
                                            <div class="stretched row">
                                                <div class="five wide column middle aligned">
                                                    <div class="ui basic segment">
                                                        <p>Den</p>
                                                    </div>
                                                </div>
                                                <div class="ten wide column">
                                                    <div class="ui basic segment">
                                                        <p><a href="">Čas <i class="sign in alternate icon"></i></a></p>
                                                    </div>
                                                    <div class="ui basic segment">
                                                        <p><a href="">Čas <i class="sign in alternate icon"></i></a></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <p class=""></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="actions">
                                <button class="ui button" type="submit">Odeslat</button>
                            </div>
                        </div>
                    </content>
                    <content>
                        <div id="modal_15" class="ui tiny modal">
                            <i class="close icon"></i>
                            <div class="header">
                                Jsme na facebooku!
                            </div>
                            <div class="content">
                                <div class="ui grid centered">
                                    <div class="fb-page" data-href="https://www.facebook.com/veletrhprace/" data-tabs="timeline" data-width="" data-height="500" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/veletrhprace/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/veletrhprace/">Veletrh práce a vzdělávání Plzeň - Klíč k příležitostem</a></blockquote></div>
                                </div>
                            </div>
                            <div class="actions">
                                <a class="ui button" href="https://www.facebook.com/veletrhprace/" target="_blank">Přejít na facebook</a>
                            </div>
                        </div>
                    </content>
                    <content>
                        <div id="modal_16" class="ui modal">
                            <i class="close icon"></i>
                            <div class="header">
                                Letáky ke stažení
                            </div>
                            <div class="content">
                                <div class="ui three column grid centered">
                                    <div class="column">
                                        <div class="letak-stanku">
                                            <img src="images/letak-na-prednasku.jpg" alt="leták" width="250" height="200" />
                                            <p style="text-align: center;"><a href="download/letak-na-prednasku.pdf" download="Leták1"> Stáhnout leták</a></p>
                                        </div>
                                    </div>
                                    <div class="column">
                                        <div class="letak-stanku">
                                            <img src="images/moje-budoucnost-letakA5.jpg" alt="leták" width="250" height="200" />
                                            <p style="text-align: center;"><a href="download/moje-budoucnost-letakA5.pdf" download="Leták1"> Stáhnout leták</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="actions">
                            </div>
                        </div>
                    </content>
                    <!--</form>-->
