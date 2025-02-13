                <div class="fourteen wide computer thirteen wide large screen column">
                    <header id="header">
                        <?php include "telo/hlavicka.php"; ?>
                        <?php include "telo/prihlaseni.php"; ?>
                    </header>
                    <div class="ui grid equal width stackable ">
                        <div class="one wide mobile five wide tablet four wide computer column">
                            <?php include "telo/svislemenuNoneditableMode.php"; ?>
                            <?php include "telo/svislemenuPaticka.php"; ?>
                        </div>
                        <div class="column">
                            <main class="page-content">
                                <?= $content ?? '' ?>
                            </main>
                            
                        </div>
                    </div>
                </div>

                <div class="one wide mobile two wide tablet one wide computer column">
                    <div class="fix-bar no-fix"></div>
                    <img class="klic-img" src="layout-images/klicPrilezitosti2.png" alt="Klíč k příležitostem" height="504" width="212" />
                </div>