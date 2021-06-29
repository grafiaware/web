
<div data-component="presented" data-template="<?= $paperAggregate->getTemplate() ?>" class="">
    <form>
        <article class="edit-html borderDance">
            <div class="title active">
                <p class="podnadpis"><i class="dropdown icon"></i><span class="edit-text borderDance">Název pozice, Místo výkonu</span>                
                    <span class="ui big red tag label edit-text borderDance">kategorie</span>                            </p>
            </div>
            <div class="content active">
                <div class="ui grid stackable visible" style="display: flex !important;">
                    <div class="row">
                        <div class="four wide column"><b>Místo výkonu práce:</b></div>
                        <div class="six wide column edit-text borderDance">Plzeň</div>
                    </div>
                    <div class="row">
                        <div class="four wide column"><b>Požadované vzdělání:</b></div>
                        <div class="six wide column edit-text borderDance">SOU s maturitou</div>
                    </div>
                    <div class="row">
                        <div class="four wide column">
                            <p><b>Popis pracovní pozice:</b></p>
                        </div>
                        <div class="twelve wide column edit-text borderDance"><p>Napište popis</p></div>
                    </div>
                    <div class="row">
                        <div class="eight wide column">
                            <p><b>Požadujeme:</b></p>
                            <div class="edit-text borderDance">
                                <ul>
                                    <li>Požadavek 1</li>
                                    <li>Požadavek 2</li>
                                </ul>
                            </div>
                        </div>
                        <div class="eight wide column">
                            <p><b>Nabízíme:</b></p>
                            <div class="edit-text borderDance">
                                <ul>
                                    <li>Benefit 1</li>
                                    <li>Benefit 2</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </form>
</div>