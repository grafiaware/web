<!doctype html>
<html lang="<?= $langCode ?>">

    <head>
        <base href="<?= $basePath; ?>">
        <title><?= $title; ?></title>
        <?php include "head/meta.php"; ?>
        <?php include "head/linkCss.php"; ?>
        <?php include "head/linkJs.php"; ?>
    </head>

    <body class="newlayout_1">
        <?php include "body.php"; ?>
        <script>
            function hamburger_open() {
                document.getElementById("mySidenav").style.display = "block";
                document.getElementById("myOverlay").style.display = "block";
            }
            function hamburger_close() {
                document.getElementById("mySidenav").style.display = "none";
                document.getElementById("myOverlay").style.display = "none";
            }
        </script>
    </body>
</html>

