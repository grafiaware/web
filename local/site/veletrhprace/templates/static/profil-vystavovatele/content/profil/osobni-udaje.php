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
                Osobní údaje
            </div>
            <div class="active content">
                <form class="ui huge form" action="" method="">
                    <div class="fields">
                        <div class="eight wide field">
                            <label>Zastupuji společnost</label>
                            <select>
                                <option value="Akka">AKKA Czech Republic s.r.o.</option>
                                <option value="Daikin">Daikin Industries Czech Republic s.r.o.</option>
                                <option value="DZK">Drůbežářský závod Klatovy a.s.</option>
                                <option value="Grafia" selected>Grafia, s.r.o.</option>
                                <option value="Kermi">Kermi, s.r.o.</option>
                                <option value="Konplan">Konplan s.r.o.</option>
                                <option value="MD">MD Elektronik s.r.o.</option>
                                <option value="Possehl">Possehl Electronics Czech Republic s.r.o.</option>
                                <option value="Stoelzle">STOELZLE UNION s.r.o.</option>
                                <option value="UP">Úřad práce ČR a EURES</option>
                                <option value="Valeo">Valeo Autoklimatizace k.s.</option>
                                <option value="Wienerberger">Wienerberger s.r.o.</option>
                            </select>
                        </div>
                    </div>
                </form>
                <form class="ui huge form" action="" method="">
                    <!--                        <div class="five wide field">
                                                <div style="background-color: peachpuff; width: 200px; height: 200px; margin: 0 auto;">Foto</div>
                                            </div>-->
                    <div class="four fields">
                        <div class="three wide field">
                            <label>Titul před jménem</label>
                            <input type="text" name="titul1" placeholder="Mgr." maxlength="45">
                        </div>
                        <div class="five wide field">
                            <label>Jméno</label>
                            <input type="text" name="first-name" placeholder="Jan" maxlength="90">
                        </div>
                        <div class="five wide field">
                            <label>Příjmení</label>
                            <input type="text" name="last-name" placeholder="Vonásek" maxlength="90">
                        </div>
                        <div class="three wide field">
                            <label>Titul za jménem</label>
                            <input type="text" name="titul2" placeholder="DiS." maxlength="45">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>E-mail</label>
                            <input type="email" name="email" placeholder="vonasek@seznam.cz" maxlength="90">
                        </div>
                        <div class="field">
                            <label>Telefon</label>
                            <input type="tel" name="phone" placeholder="+420 725 896 569" pattern="(\+420)\s[1-9]\d{2}\s\d{3}\s\d{3}" maxlength="45">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field margin">
                            <label>Společnost</label>
                            <select disabled>
                                <option value="Akka">AKKA Czech Republic s.r.o.</option>
                                <option value="Daikin">Daikin Industries Czech Republic s.r.o.</option>
                                <option value="DZK">Drůbežářský závod Klatovy a.s.</option>
                                <option value="Grafia" selected>Grafia, s.r.o.</option>
                                <option value="Kermi">Kermi, s.r.o.</option>
                                <option value="Konplan">Konplan s.r.o.</option>
                                <option value="MD">MD Elektronik s.r.o.</option>
                                <option value="Possehl">Possehl Electronics Czech Republic s.r.o.</option>
                                <option value="Stoelzle">STOELZLE UNION s.r.o.</option>
                                <option value="UP">Úřad práce ČR a EURES</option>
                                <option value="Valeo">Valeo Autoklimatizace k.s.</option>
                                <option value="Wienerberger">Wienerberger s.r.o.</option>
                            </select>
                        </div>
                        <div class="field margin">
                            <button class="ui massive primary button" type="submit">Uložit</button>
                        </div>
                    </div>
                </form>
            </div>

