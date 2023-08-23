/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */


/**
 * Callback funkce nastavená parametrem v konfiguraci TinyMCE setup: editorFunction. Volá se před inicializací instance TinyMCE.
 *
 * @param {type} editor
 * @returns {undefined}
 */
export const editorHtmlEditSetup = (editor) => {
    var form;
    var val;

    editor.on('focus', function(e) {
        val = editor.getContent();
        form = editor.formElement;
    });
    editor.on('blur', (e) => {
        if (editor.isDirty()) {
            if (confirm("Uložit změny?")) {
                editor.save();  // vloží obsah do příslušného hidden inputu
                form.submit();
            } else {
                if (confirm("Zahodit změny?")) {
                    editor.resetContent();
                } else {
                    editor.focus();
                }
            }
        }
    });
    editor.on('NodeChange', (e) => {
        console.log('The ' + e.element.nodeName + ' changed.');
    });
}; 

/**
 * Callback funkce volaná před inicializací TinyMce - použito v editWorkDataConfig.
 *
 * @param {type} editor
 * @returns {undefined}
 */
export const setupUserInputEditor = function (editor) {
    const allowedKeys = [8, 13, 16, 17, 18, 20, 33, 34, 35, 36, 37, 38, 39, 40, 46];
    editor.on('keydown', function (e) {
        if (allowedKeys.indexOf(e.keyCode) !== -1) return true;
        if (tinymce_getContentLength() + 1 > this.settings.max_chars) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        return true;
    });
    editor.on('keyup', function (e) {
        tinymce_updateCharCounter(this, tinymce_getContentLength());
    });
};

/**
 * Callback funkce volaná po inicializaci TinyMce - použito v editWorkDataConfig.
 *
 * @returns {undefined}
 */
export const initInstanceUserInputEditor = function () { // initialize counter div
    $('#' + this.id).prev().append('<div class="char_count" style="text-align:right; float: right"></div>');
    tinymce_updateCharCounter(this, tinymce_getContentLength());
};

/**
 * Callback funkce volaná před "paste" vložením obsahu Ctrl+v v TinyMce - použito v editWorkDataConfig.
 * @param {type} plugin
 * @param {type} args
 * @returns {undefined}
 */
export const pastePreprocessUserInput = function (plugin, args) {
    var editor = tinymce.get(tinymce.activeEditor.id);
    var len = editor.contentDocument.body.innerText.length;
    if (len + args.content.length > editor.settings.max_chars) {
        alert('Překročen maximální počet znaků / Maximum number of characters exceeded. Maximum:' + editor.settings.max_chars + '.');
        args.content = '';
    }
    tinymce_updateCharCounter(editor, len + args.content.length);
};

function tinymce_updateCharCounter(editor, len) {
    $('#' + editor.id).prev().find('.char_count').text(len + '/' + editor.settings.max_chars);
}

function tinymce_getContentLength() {
    return tinymce.get(tinymce.activeEditor.id).contentDocument.body.innerText.length;
}