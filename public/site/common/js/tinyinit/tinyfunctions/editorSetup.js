/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */


/* global tinymce */

/**
 * Callback funkce nastavená parametrem v konfiguraci TinyMCE setup: editorFunction. Volá se před inicializací instance TinyMCE.
 *
 * @param {type} editor
 * @returns {undefined}
 */
export const redEditorSetup = (editor) => {
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
//                removeItemAction(editor);  // post request - ukončení editace
                form.submit();
            } else {
                if (confirm("Opravdu chcete ukončit editaci a zahodit změny?")) {
//                    removeItemAction(editor);  // post request - ukončení editace, nerefreshne se stránka, není vidět odhlášení
//                    editor.resetContent();
                    // nadbytečný submit - posílá původní obsah, slouží pouze pro vyncené refreshe obsahu 
                    // (post redirect get) a tím zajistí správné zobrazení tlačítka Zapnout/Vypnout editaci
                    form.submit();
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

//const removeItemAction = (editor) => {
//    const editedElement = editor.getElement();
//    const editedMenuItemId =  editedElement.getAttribute('data-red-menuitemid');
//    fetch("red/v1/itemaction/"+editedMenuItemId+"/remove", {
//    method: "POST", body: ""})  // request je typi text/plain - Pes http body parser vrací obsah body jakoStream
//  .then((response) => {
//            if (response.ok) {
//                console.log(`editorSetup: Remove item action after editor blur.`);
//                return response.text();
//            } else {
//                throw new Error(`HTTP error! Status: ${response.status}`);
//            }
//  })
//  .then(data => {
//    //handle data
//    console.log(data);
//    }
//  )
//  .catch(error => {
//    //handle error
//  });
//}

/**
 * KOPIE z cascade.js
 * @param {type} formElement
 * @returns {undefined}
 */
//function fetchClosestCascadeContent(formElement) {
//    let loaderElemeent = formElement.closest(".cascade");
//    fetchCascadeContent(loaderElemeent);    
//}

/**
 * Callback funkce volaná před inicializací TinyMce - použito v editWorkDataConfig.
 *
 * @param {type} editor
 * @returns {undefined}
 */
export const setupUserInputEditor = (editor) => {
    const allowedKeys = [10, 13, 16, 17, 18, 20];
    const maxChars = editor.getParam('max_chars');
    editor.on('keydown', function (e) {
        let ctrl = e.ctrlKey ? e.ctrlKey : ((e.keyCode === 17) ? true : false); // ctrl detection        
        if (e.keyCode === 86 && ctrl)  return true;   // ctrl + v
        if (allowedKeys.indexOf(e.keyCode) !== -1) return true;
        let editor = activeEditor();
        let max = editor.getParam('max_chars');
        let len = activeEditorContentLength();
        if (len + 1 > max) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        tinymce_updateCharCounter(editor, len+1);
        return true;
    });
    editor.on('keyup', (e) => {
        eventsEnableButtonsOnTinyMCE(activeEditor().formElement);
        tinymce_updateCharCounter(this, activeEditorContentLength());
    });
//    editor.on('Dirty', (e) => {
//        eventsEnableButtonsOnTinyMCE(activeEditor().formElement);
//    });
//  editor.on("change", (e) => {
//    alert("The TinyMCE rich text editor content has changed.");
//  });    
};

function activeEditor() {
    return tinymce.get(tinymce.activeEditor.id);
}

/**
 * Callback funkce volaná po inicializaci TinyMce - použito v editWorkDataConfig.
 *
 * @returns {undefined}
 */
export const initInstanceUserInputEditor = function () { // initialize counter div
    // přidá div před editor
    $('#' + this.id).prev().append('<div class="char_count" style="text-align:right; float: right; color: maroon;"></div>');
    tinymce_updateCharCounter(this, activeEditorContentLength());
};

/**
 * Callback funkce volaná před "paste" vložením obsahu Ctrl+v v TinyMce - použito v editWorkDataConfig.
 * @param {type} plugin
 * @param {type} args
 * @returns {undefined}
 */
export const pastePreprocessUserInput = function (plugin, args) {
    let editor = activeEditor();
    let len = activeEditorContentLength();
    let max = editor.getParam('max_chars');    
    if (len + args.content.length > max) {
        alert('Překročen maximální počet znaků / Maximum number of characters exceeded. Maximum:' + max + '.');
        let shrinked = args.content.substring(0,max-len);
        args.content = shrinked;
    }
    tinymce_updateCharCounter(editor, len + args.content.length);
    eventsEnableButtonsOnTinyMCE(activeEditor().formElement);
    
};

function tinymce_updateCharCounter(editor, len) {
    $('#' + editor.id).prev().find('.char_count').text(len + '/' + activeEditor().getParam('max_chars'));
}

function activeEditorContentLength() {
    return activeEditor().contentDocument.body.innerText.length;
}