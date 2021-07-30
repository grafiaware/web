    <!--<div class="ui container editable">--> <!--$this->attributes($bodyContainerAttributes) -->
    <div class="ui grid">
        <div class="two wide mobile two wide tablet two wide computer two wide large screen two wide widescreen column">
            <div class="fix-bar"> <!-- editMenu změnit třídu editMenu -->
                <?php include "container/telo/svislemenu.php"; ?>
                <?php include "container/hlavicka/prihlaseni.php"; ?>
                <?php include "container/telo/iconmenu.php"; ?>
            </div>
        </div>
        <div class="thirteen wide mobile twelve wide tablet twelve wide computer eleven wide large screen eleven wide widescreen column centered"> <!-- editMenu změnit třídu ten wide tablet ten wide computer ten wide large screen eleven wide widescreen column right floated-->
            <header>
                <?php include "container/hlavicka.php"; ?>
            </header>
            <main class="page-content">
                <div class="zapnout_editaci" style="text-align:right;">
                    <form class="ui form" method="POST" action="">
                        <button class="ui huge fade animated button" type="submit" name="edit_article" value="<?= empty($editArticle) ? 1 : 0 ?>" formtarget="_self" tabindex="0"
                                formaction="red/v1/presentation/edit_article">
                            <div class="hidden content" style="font-size: 0.7em;top: 30%; ">
                                <?= empty($editArticle) ? "Editavat článek" : "Vypnout editaci" ?>
                            </div>
                            <div class="visible content">
                                <i class="pencil teal alternate icon"></i>
                            </div>
                        </button>
                    </form>
                    <button class="ui huge fade animated button toogleTemplateSelect" formtarget="_self" tabindex="0">
                        <div class="hidden content" style="font-size: 0.7em; top: 30%;">
                            Šablony pro stránku
                        </div>
                        <div class="visible content">
                            <i class="file alternate teal icon"></i>
                        </div>
                    </button>
                </div>
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

