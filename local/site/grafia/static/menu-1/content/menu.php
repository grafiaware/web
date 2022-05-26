<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

?>

<div class="">
    <div class="ui two column stackable centered grid">
        <div class="eight wide column middle aligned">
            <div class="ui segment">
                <div class="ui two column grid stackable centered">
                    <div class="column">
                        <h2>Název položky</h2>
                        <p><a class="ui primary button" href="index.php">Tlačítko</a></p>
                    </div>
                    <div class="column middle aligned centered">
                        <img class="ui image" src="@commonimages/sablony-tym.jpg" alt="ilustrační obrázek" />
                    </div>
                </div>
            </div>
        </div>
        <div class="eight wide column middle aligned">
           <div class="ui segment">
                <div class="ui grid stackable centered">
                    <div class="sixteen wide column">
                        <img class="" src="@commonimages/sablony-tym.jpg" alt="ilustrační obrázek" 
                             style="height: 100%; width: 100%; z-index: 0; opacity: 0.3;"/>
                        <div class="info"
                             style="position: absolute; z-index: 1; top: 50%; transform: translateY(-50%); left: 30px; width: 80%; text-align: center;"> 
                            <h2 class="text velky">Název položky</h2>
                            <p><a class="ui primary button" href="index.php">Tlačítko</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ui grid centered">
        <div class="sixteen wide column">
            <div class="ui segment">
                <div class="ui two column grid stackable centered">
                    <div class="column">
                        <h2>Název položky</h2>
                        <p><a class="ui primary button" href="index.php">Tlačítko</a></p>
                    </div>
                    <div class="column middle aligned centered">
                        <img class="ui image" src="@commonimages/sablony-tym.jpg" alt="ilustrační obrázek" />
                    </div>
                </div>
            </div>
        </div>
        <div class="sixteen wide column">
            <div class="ui segment">
                <a href="index.php">
                    <div class="ui two column grid stackable centered">
                        <div class="column middle aligned centered">
                            <img class="ui image" src="@commonimages/sablony-tym.jpg" alt="ilustrační obrázek" />
                        </div>
                        <div class="column">
                            <h2>Název položky 2</h2>
                            <p>nějaký text - položka je celá 'klikací'</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="ui grid centered equal width">
        <div class="stretched row">
            <div class="column">
                <div class="ui segment">
                    <div class="ui grid" style="height: 100%;">
                        <div class="sixteen wide column">
                            <div class="" style="height: 100%; display: flex; flex-direction: column; ">
                                <h2>Název položky</h2>
                                <p><a class="ui primary button" href="index.php">Tlačítko</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column">
                <div class="ui segment">
                    <a href="index.php">
                        <div class="ui grid">
                            <div class="sixteen wide column">
                                <img class="ui image" src="@commonimages/sablony-tym.jpg" alt="ilustrační obrázek" />
                            </div>
                            <div class="sixteen wide column">
                                <h2>Název položky</h2>
                                <p>Krátký popis</p>
                            </div>
                        </div>
                    </a> 
                </div>
            </div>
        </div>
    </div>
</div>

