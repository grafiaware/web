
        <form class="ui form" method="POST" action="">
            <div class="ui icon top left pointing dropdown button">
              Akce
              <div class="menu">
                <div class="item header"><p><i class="user icon"></i><?= $userName ?></p></div>
<!--                <button class="fluid ui primary labeled icon button" type="submit" name="app" value="rs" formtarget="_blank"
                            formmethod="GET" formaction="api/v1/useraction/app/rs">
                        <i class="edit outline large icon"></i>
                        Redakční systém
                </button>
                <button class="fluid ui yellow labeled icon button" type="submit" name="app" value="edun" formtarget="_blank"
                            formmethod="GET" formaction="api/v1/useraction/app/edun">
                        <i class="file alternate outline large icon"></i>
                        Katalog kurzů
                </button>-->
                <button class="fluid ui olive labeled icon button" type="submit" name="edit_article" value="<?= empty($editArticle) ? 1 : 0 ?>" formtarget="_self"
                            formaction="api/v1/presentation/edit_article">
                        <i class="pencil alternate large icon"></i>
                        <?= empty($editArticle) ? "Zapnout inline editaci článků" : "Vypnout inline editaci článků" ?>
                </button>
                <button class="fluid ui olive labeled icon button" type="submit" name="edit_layout" value="<?= empty($editLayout) ? 1 : 0 ?>" formtarget="_self"
                            formaction="api/v1/presentation/edit_layout">
                        <i class="pencil alternate large icon"></i>
                        <?= empty($editLayout) ? "Zapnout inline editaci rozvržení" : "Vypnout inline editaci rozvržení" ?>
                </button>
                <button class="fluid ui yellow labeled icon button" type="submit" name="edit_menu" value="<?= empty($editMenu) ? 1 : 0 ?>" formtarget="_self"
                            formaction="api/v1/presentation/edit_menu">
                        <i class="file alternate large icon"></i>
                        <?= empty($editMenu) ? "Zapnout inline editaci menu" : "Vypnout inline editaci menu" ?>
                </button>
              </div>
            </div>
        </form>




