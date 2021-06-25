    <!--<div class="ui container editable">--> <!--$this->attributes($bodyContainerAttributes) -->
    <div class="ui grid"> 
        <div class="two wide mobile two wide tablet two wide computer two wide large screen two wide widescreen column">
            <div class="fix-bar">
                <?php include "container/telo/svislemenu.php"; ?>
                <?php include "container/hlavicka/prihlaseni.php"; ?>
                <?php include "container/telo/iconmenu.php"; ?>
            </div>
        </div>
        <div class="thirteen wide mobile twelve wide tablet twelve wide computer eleven wide large screen eleven wide widescreen column centered">
            <header>
                <?php include "container/hlavicka.php"; ?>
            </header>
            <main class="page-content">
                <div class="paper_template_select"><div class="mceNonEditable">Výběr šablony pro stránku</div></div>
                <?= $content ?? '' ?>
            </main>
        </div>
        <?= $flash ?? '' ?>
        <?= $poznamky ?? '' ?>
        
        <div class="row">
            <div class="sixteen wide column">
                <footer>
                    <span id="kontakty"></span>
                    <?php include "container/paticka.php"; ?>
                </footer>
            </div>
        </div>
    </div>
    
