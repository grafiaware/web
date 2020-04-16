<?php

namespace Middleware\Menu\View;

/**
 * Description of menuView
 *
 * @author pes2704
 */
class MultiMenuView {
    public function render(array $trees) {
        
        assert(FALSE, 'Předělej MultiView!');
        
        $ulClass = 'ui-menu';
        $result[] = <<<HTML
            <!doctype html>
            <html lang="cs">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>menu demo</title>
                <link rel="stylesheet" href="public/menu/jquery/jquery-ui-1.12.1/jquery-ui.theme.css">
                <link rel="stylesheet" href="public/menu/jquery/jquery-ui-1.12.1/jquery-ui.css">
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

                <style>
                    .$ulClass {
                    width: 200px;
                    }
                </style>

                <script src="public/menu/jquery/jquery-3.2.1/jquery-3.2.1.js"></script>
                <script src="public/menu/jquery/jquery-ui-1.12.1/jquery-ui.js"></script>

            </head>
            <body>
HTML;
        
        $listView = new ListView2(); 
        $count = 0;
        foreach ($trees as $header => $tree) {
            $id = 'menu'.++$count;
                $result[] = "<h4>$header</h4>";
                $result[] = "<nav><form action='index.php'>{$listView->render($tree, $id)}</form></nav>";
                $result[] = <<<HTML
            <script>
                $( "#$id" ).menu();
            </script>
HTML;
        }

        $result[] = <<<HTML
            </body>
            </html>
HTML;
                   
        return implode(PHP_EOL, $result);   

    }
}
