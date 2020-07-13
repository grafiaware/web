<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Menu\Middleware\Menu\View\Renderer;

use Pes\View\Renderer\RendererInterface;

/**
 * Description of ItemRenderer
 *
 * @author pes2704
 */
class ItemRenderer implements RendererInterface {
    public function render($data = NULL) {
        $item = $data['item'];
        $basePath = $data['basePath'];
        // data- atributy conteteditable div-u používá javascript
        return "<div>"
            . "<a contenteditable=false class=\"editable\" tabindex=0 data-original-title=\"{$item['title']}\" data-uid=\"{$item['uid']}\" href=\"/menu/item/{$item['uid']}/\">{$item['title']}</a>"
                . "<div>"
                    . "<span>{$item['depth']}</span>"
                    . "<button type=\"submit\" name=\"delete\" class=\"\" formmethod=\"post\" formaction=\"/menu/delete/{$item['uid']}/\"  onclick=\"return confirm('Jste si jisti?');\">X</button>"
                    . "<button type=\"submit\" name=\"add\" class=\"\" formmethod=\"post\" formaction=\"/menu/add/{$item['uid']}/\">+</button>"
                    . "<button type=\"submit\" name=\"addchild\" class=\"\" formmethod=\"post\" formaction=\"/menu/addchild/{$item['uid']}/\">+></button>"
                . "</div>"
            . "</div>";
    }
}



/*
## 1
<div id="content1" class="editable">Surprisingly, this text is editable.</div>

<div id="content2" class="editable">And this one, too.</div>
// var div = document.getElementsByClassName('editable')
var list = document.getElementsByClassName("editable");
for (var i = 0; i < list.length; i++) {
    div = list[i];
    div.onclick = function (e) {
        this.contentEditable = true;
        this.focus();
        // this.style.backgroundColor = '#E0E0E0';
        // this.style.border = '1px dotted black';
    }

## 2
    div.onmouseout = function () {
        // this.style.backgroundColor = '#ffffff';
        // this.style.border = '';
        this.contentEditable = false;
    }

        window.onload = function() {
            var div = document.getElementById('editable');
            div.onclick = function(e) {
                this.contentEditable = true;
                this.focus();
                this.style.backgroundColor = '#E0E0E0';
                this.style.border = '1px dotted black';
            }

            div.onmouseout = function() {
                this.style.backgroundColor = '#ffffff';
                this.style.border = '';
                this.contentEditable = false;
            }
        }

        // And for HTML

        <div id="content">
            <span id='editable'>Surprisingly,</span>
            <a href="http://google.com">clicking this link does nothing at all.</a>
        </div>
 */