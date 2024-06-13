                <div class="fourteen wide mobile fourteen wide tablet eleven wide computer eleven wide large screen eleven wide widescreen column">
                    <header id="header">
                        <?php include "telo/hlavicka.php"; ?>
                        <?php include "telo/prihlaseni.php"; ?>
                    </header>
                    <div class="ui grid">
                        <div class="one wide mobile five wide tablet five wide computer four wide large screen five wide widescreen column">
                            <?php include "telo/svislemenu.php"; ?>
                        </div>
                        <div class="fourteen wide mobile eleven wide tablet eleven wide computer eleven wide large screen eleven wide widescreen column right floated">
                            <main class="page-content">
                                <?= $content ?? '' ?>
                            </main>
                            
                        </div>
                    </div>
                </div>
