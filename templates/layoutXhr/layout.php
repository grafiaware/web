<!doctype html>
<html lang="<?= $langCode ?>">
        <head>
            <base href="<?=$basePath;?>">
            <title><?= Middleware\Web\AppContext::getWebTitle() ?></title>
            <?php include "head.php"; ?>
        </head>
        <body class="layout">
            <?php include "body_editacni.php"; ?>
        </body>
</html>

