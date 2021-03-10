<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>

        
            <div class="active title">
                <i class="dropdown icon"></i>
                Balíček pracovních údajů
            </div>
            <div class="active content">
                <form class="ui huge form">
                    <div class="fields">
                        <div class="five wide field">
                            <div style="background-color: peachpuff; width: 200px; height: 200px; margin: 0 auto;">Foto</div>
                        </div>
                        <div class="eleven wide field">
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
                        </div>
                    </div>
                </form>
            </div>

