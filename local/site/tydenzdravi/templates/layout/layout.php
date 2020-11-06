<!doctype html>
<html lang="<?= $langCode; ?>">
    <head>
        <base href="<?= $basePath; ?>">
        <title><?= $title; ?></title>
        <?php include "head/meta.php"; ?>
        <?php include "head/linkCss.php"; ?>
        <?php include "head/linkJs.php"; ?>
    </head>
    <body class="layoutTZ">
        <?php include "body/container.php"; ?>
        <?php include "body/scripts.php"; ?>
        <script>
            $('.ui.dropdown')
              .dropdown()
            ;
            $('.ui.selection.dropdown')
              .dropdown()
            ;
        </script>
    </body>
</html>

