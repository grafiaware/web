<?php

use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>


<!--   ZMĚNA OPROTI OSOBNI-UDAJE.PHP V PROFILU         
            <div class="active title">
                <i class="dropdown icon"></i>
                Balíček pracovních údajů
            </div>-->
            <div class="active content">
                <form class="ui huge form" action="" method="">
                    <!--                        <div class="five wide field">
                                                <div style="background-color: peachpuff; width: 200px; height: 200px; margin: 0 auto;">Foto</div>
                                            </div>-->
                    <div class="two fields">
                        <div class="field">
                            <label>Jméno</label>
                            <input type="text" name="first-name" placeholder="Jan">
                        </div>
                        <div class="field">
                            <label>Příjmení</label>
                            <input type="text" name="last-name" placeholder="Vonásek">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>E-mail</label>
                            <input type="email" name="email" placeholder="vonasek@seznam.cz">
                        </div>
                        <div class="field">
                            <label>Telefon</label>
                            <input type="tel" name="phone" placeholder="+420 725 896 569" pattern="(\+420)\s[1-9]\d{2}\s\d{3}\s\d{3}">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>Vzdělání, kurzy</label>
                            <textarea class="working-data"></textarea>
                        </div>
                        <div class="field">
                            <label>Pracovní popis</label>
                            <textarea class="working-data"></textarea>
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field margin">
                            <label>Příloha - životopis</label>
                            <input type="file" name="priloha" size="1">
                            <p class="text"></p>
                            <label>Příloha - motivační dopis</label>
                            <input type="file" name="priloha" size="1">
                        </div>
                        <div class="field margin">
                            <!--   ZMĚNA OPROTI OSOBNI-UDAJE.PHP V PROFILU -->
                            <button class="ui massive primary button" type="submit">Odeslat</button>
                            <!--    
                            <button class="ui massive primary button" type="submit">Uložit</button>
                            -->
                        </div>
                    </div>
                    <label><b>Nahrané soubory</b></label>
                    <div class="fields">
                        <div class="field">
                            <p>Životopis_Malá.pdf </p>
                        </div>
                        <div class="field">
                            <p><a><i class="eye outline icon"></i>Zobrazit soubor</a><a><i class="trash icon"></i>Smazat</a></p>
                        </div>
                        
                    </div>
                </form>
            </div>

