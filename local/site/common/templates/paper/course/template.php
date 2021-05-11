<div data-templates="paper-contact" data-component="presented" class="ui segment mceNonEditable">
    <form method="POST" action="">
        <div class="mini ui basic icon buttons editComponent">
            <button class="ui button" data-tooltip="Smazat" data-position="top right" type="submit" name="delete" formmethod="post" formaction="" onclick="return confirm('Jste si jisti?');">
                <i class="large trash icon"></i>
            </button>
        </div>
    </form>
    <div class="grafia segment headlined editable">
        <form method="POST" action="">
            <div class="mini ui basic icon buttons editPage">
                <button class="ui button" data-tooltip="Přidat obsah" type="submit" name="button" value="" formmethod="post" formaction="red/v1/paper/616/contents">
                    <i class="icons">
                        <i class="large plus square outline icon"></i>
                        <i class="bottom right corner arrow down icon"></i>
                    </i>
                </button> 
            </div> 
        </form> 
        <section class="">
            <form method="POST" action="red/v1/paper/616/headline/">
                <headline class="ui header"><p>Nadpis</p></headline>
            </form>
        </section>
        <section class="">
            <div class="ui right tiny corner blue label">
                <form method="POST" action="">
                    <div class="contentButtons">
                        <div class="mini ui basic icon buttons editContent">
                            <button class="ui button" data-tooltip="Aktivní/neaktivní stránka" type="submit" name="button" value="toggle" formmethod="post" formaction="red/v1/paper/73/contents/4/toggle">
                                <i class="large green toggle on icon">

                                </i>
                            </button><button class="ui button toolsDate" data-tooltip="Zobrazeno od 14.01.2008  do 31.12.2008" data-position="top right" onclick="event.preventDefault();">
                                <i class="large calendar alternate icon">

                                </i>
                            </button>
                        </div><div class="mini ui basic icon buttons editContent">
                            <button class="ui button" data-tooltip="Posunout o jednu výš" type="submit" name="button" value="" formmethod="post" formaction="red/v1/paper/73/contents/4/up">
                                <i class="icons">
                                    <i class="large sticky note outline icon">

                                    </i><i class="top right corner arrow up icon">

                                    </i>
                                </i>
                            </button><button class="ui button" data-tooltip="Posunout o jednu níž" type="submit" name="button" value="" formmethod="post" formaction="red/v1/paper/73/contents/4/down">
                                <i class="icons">
                                    <i class="large sticky note outline icon">

                                    </i><i class="bottom right corner arrow down icon">

                                    </i>
                                </i>
                            </button>
                        </div><div class="mini ui basic icon buttons editContent">
                            <button class="ui button" data-tooltip="Přidat další obsah před" type="submit" name="button" value="" formmethod="post" formaction="red/v1/paper/73/contents/4/add_above">
                                <i class="icons">
                                    <i class="large plus square outline icon">

                                    </i><i class="top right corner arrow up icon">

                                    </i>
                                </i>
                            </button><button class="ui button" data-tooltip="Přidat další obsah za" type="submit" name="button" value="" formmethod="post" formaction="red/v1/paper/73/contents/4/add_below">
                                <i class="icons">
                                    <i class="large plus square outline icon">

                                    </i><i class="bottom right corner arrow down icon">

                                    </i>
                                </i>
                            </button>
                        </div><div class="mini ui basic icon buttons editContent">
                            <button class="ui button" data-tooltip="Smazat" type="submit" name="button" value="" formmethod="post" formaction="red/v1/paper/73/contents/4/trash">
                                <i class="large trash icon">

                                </i>
                            </button>
                        </div>
                    </div><div class="mini ui basic icon buttons editDate">
                        <button class="ui button" data-tooltip="Trvale" data-position="top right" type="submit" name="button" value="permanent" formmethod="post" formaction="red/v1/paper/73/contents/4/actual">
                            <i class="large calendar outline icon">

                            </i>
                        </button><button class="ui button" data-tooltip="Uložit" data-position="top right" type="submit" name="button" value="calendar" formmethod="post" formaction="red/v1/paper/73/contents/4/actual">
                            <i class="large save icon">

                            </i>
                        </button><button class="ui button toolsContent" data-tooltip="Zrušit úpravy" data-position="top right" onclick="event.preventDefault(); this.form.reset();">
                            <i class="large times circle icon">

                            </i>
                        </button><div class="ui button" data-position="top right">
                            <i class="large calendar alternate icon">

                            </i>
                        </div>
                    </div><div class="editDate">
                        <div class="edit_kalendar">
                            <p class="">
                                Uveřejnit od
                            </p><div class="ui calendar">
                                <div class="ui input"><div class="ui popup calendar"><table class="ui celled center aligned unstackable table day"><thead><tr><th colspan="7"><span class="link">Leden 2008</span><span class="prev link"><i class="chevron left icon"></i></span><span class="next link"><i class="chevron right icon"></i></span></th></tr><tr><th>Po</th><th>Út</th><th>St</th><th>Čt</th><th>Pá</th><th>So</th><th>Ne</th></tr></thead><tbody><tr><td class="link adjacent disabled">31</td><td class="link">1</td><td class="link">2</td><td class="link">3</td><td class="link">4</td><td class="link">5</td><td class="link">6</td></tr><tr><td class="link">7</td><td class="link">8</td><td class="link">9</td><td class="link">10</td><td class="link">11</td><td class="link">12</td><td class="link">13</td></tr><tr><td class="link active focus">14</td><td class="link">15</td><td class="link">16</td><td class="link">17</td><td class="link">18</td><td class="link">19</td><td class="link">20</td></tr><tr><td class="link">21</td><td class="link">22</td><td class="link">23</td><td class="link">24</td><td class="link">25</td><td class="link">26</td><td class="link">27</td></tr><tr><td class="link">28</td><td class="link">29</td><td class="link">30</td><td class="link">31</td><td class="link adjacent disabled">1</td><td class="link adjacent disabled">2</td><td class="link adjacent disabled">3</td></tr><tr><td class="link adjacent disabled">4</td><td class="link adjacent disabled">5</td><td class="link adjacent disabled">6</td><td class="link adjacent disabled">7</td><td class="link adjacent disabled">8</td><td class="link adjacent disabled">9</td><td class="link adjacent disabled">10</td></tr><tr><td colspan="7" class="today link">Dnes</td></tr></tbody></table></div>
                                    <input type="text" name="show" placeholder="Klikněte pro výběr data" value="14.01.2008">
                                </div>
                            </div><p class="">
                                Uveřejnit do
                            </p><div class="ui calendar">
                                <div class="ui input"><div class="ui popup calendar"><table class="ui celled center aligned unstackable table day"><thead><tr><th colspan="7"><span class="link">Prosinec 2008</span><span class="prev link"><i class="chevron left icon"></i></span><span class="next link"><i class="chevron right icon"></i></span></th></tr><tr><th>Po</th><th>Út</th><th>St</th><th>Čt</th><th>Pá</th><th>So</th><th>Ne</th></tr></thead><tbody><tr><td class="link">1</td><td class="link">2</td><td class="link">3</td><td class="link">4</td><td class="link">5</td><td class="link">6</td><td class="link">7</td></tr><tr><td class="link">8</td><td class="link">9</td><td class="link">10</td><td class="link">11</td><td class="link">12</td><td class="link">13</td><td class="link">14</td></tr><tr><td class="link">15</td><td class="link">16</td><td class="link">17</td><td class="link">18</td><td class="link">19</td><td class="link">20</td><td class="link">21</td></tr><tr><td class="link">22</td><td class="link">23</td><td class="link">24</td><td class="link">25</td><td class="link">26</td><td class="link">27</td><td class="link">28</td></tr><tr><td class="link">29</td><td class="link">30</td><td class="link active focus">31</td><td class="link adjacent disabled">1</td><td class="link adjacent disabled">2</td><td class="link adjacent disabled">3</td><td class="link adjacent disabled">4</td></tr><tr><td class="link adjacent disabled">5</td><td class="link adjacent disabled">6</td><td class="link adjacent disabled">7</td><td class="link adjacent disabled">8</td><td class="link adjacent disabled">9</td><td class="link adjacent disabled">10</td><td class="link adjacent disabled">11</td></tr><tr><td colspan="7" class="today link">Dnes</td></tr></tbody></table></div>
                                    <input type="text" name="hide" placeholder="Klikněte pro výběr data" value="31.12.2008">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div><div class="semafor">
                <i class="circle icon green" title="published">

                </i><i class="calendar plus icon grey" role="presentation" title="past">

                </i>1
            </div><form method="POST" action="red/v1/paper/73/contents/4/">
                <content id="content_4" class="" data-paperowner="anta2208" data-owner="anta2208">
                    <div class="column">
                        <div class="ui segment">
                            <h3 class="ui header">Pozice (Jednatelka)</h3>
                            <div class="content">
                                <p><b>Ing. Valdemar Novák</b></p>
                                <p>Tel.: +420 377 543 345</p>
                                <p>Mobil: +420 774 484 850</p>
                                <p>E-mail: <a href="mailto:info@grafia.cz">info@grafia.cz</a></p>
                                <p>Fax: 378 771 211</p>
                                <p>Kancelář: Budilova 4, Plzeň</p>
                            </div>
                        </div>
                    </div>
                </content>
            </form>
        </section>
    </div>
</div>