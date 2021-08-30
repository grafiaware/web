<?php

namespace Menu\Middleware\Menu\View\Renderer;

use Pes\View\Renderer\RendererInterface;

/**
 * Description of PageRenderer
 *
 * @author pes2704
 */
class PageRenderer implements RendererInterface {
    public function render($data = NULL) {
        $result[] = <<<HTML
            <!doctype html>
            <html lang="cs">
            <head>
                <base href="{$data['subDomain']}">
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>menu demo</title>

                <link rel="stylesheet" href="public/menu/jquery/jquery-ui-1.12.1/jquery-ui.theme.css">
                <link rel="stylesheet" href="public/menu/jquery/jquery-ui-1.12.1/jquery-ui.css">
                <link rel="stylesheet" href="public/menu/css/menu.css">



    <link rel="stylesheet" type="text/css" href="public/web/semantic/dist/semantic.min.css">
    <link rel="stylesheet" type="text/css" href="public/web/grafia/css/zkouska_less.css" />
    <link rel="stylesheet" type="text/css" href="public/web/grafia/css/styles.css" />

    <script src="https://code.jquery.com/jquery-3.1.1.min.js" crossorigin="anonymous"></script>
    <script src="public/web/semantic/dist/semantic.min.js"></script>
    <script src="public/web/grafia/js/hamburger.js"></script>


                <script src="public/menu/jquery/jquery-3.2.1/jquery-3.2.1.js"></script>
                <script src="public/menu/jquery/jquery-ui-1.12.1/jquery-ui.js"></script>



            </head>
            <body>
HTML;
        $result[] = $data['nav'];
        $result[] = $data['detail'];

        $result[] = <<<HTML
            </body>
            </html>
HTML;

        return implode(PHP_EOL, $result);
    }
}
