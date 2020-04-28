<!doctype html>
<html lang="<?= $langCode ?>">
        <head>
            <base href="<?=$basePath;?>">
            <title><?= Middleware\Web\AppContext::getWebTitle() ?></title>
            <?php include "layout/head.php"; ?>
        </head>
        <body class="layout">

            <?php include "layout/body_editacni.php"; ?>
        </body>
</html>

