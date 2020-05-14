
        <form class="ui form" method="POST" action="">
            <div class="ui icon top left pointing dropdown button">
              Akce
              <div class="menu">
                <div class="item header"><p><i class="user icon"></i><?= $userName ?></p></div>
                <button class="fluid ui primary labeled icon button" type="submit" name="app" value="rs" formtarget="_blank"
                            formmethod="GET" formaction="api/v1/useraction/app/rs">
                        <i class="edit outline large icon"></i>
                        Redakční systém
                </button>
                <button class="fluid ui yellow labeled icon button" type="submit" name="app" value="edun" formtarget="_blank"
                            formmethod="GET" formaction="api/v1/useraction/app/edun">
                        <i class="file alternate outline large icon"></i>
                        Katalog kurzů
                </button>
                <button class="fluid ui olive labeled icon button" type="submit" name="edit_article" value="<?= $editArticle ? 0 : 1 ?>" formtarget="_self"
                            formaction="api/v1/useraction/edit_article">
                        <i class="pencil alternate large icon"></i>
                        <?= $editArticle ? "Vypnout inline editaci článků" : "Zapnout inline editaci článků" ?>
                </button>
                <button class="fluid ui olive labeled icon button" type="submit" name="edit_layout" value="<?= $editLayout ? 0 : 1 ?>" formtarget="_self"
                            formaction="api/v1/useraction/edit_layout">
                        <i class="pencil alternate large icon"></i>
                        <?= $editLayout ? "Vypnout inline editaci rozvržení" : "Zapnout inline editaci rozvržení" ?>
                </button>
              </div>
            </div>
        </form>


    <script>
        $('.ui.dropdown')
          .dropdown()
        ;
    </script>


