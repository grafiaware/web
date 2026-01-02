



<div class="ui big button red basic btn-letaky" style="background-color: white">Stáhněte si leták</div>

<div id="modal_letaky" class="ui modal">
    <i class="close icon"></i>
    <div class="header">
        Letáky ke stažení
    </div>
    <div class="content">
        <div class="ui three column grid centered stackable">
            <?= $this->repeat(__DIR__.'/letaky/letak.php', $letak) ?>
        </div>
    </div>
    <div class="actions">
    </div>
</div>