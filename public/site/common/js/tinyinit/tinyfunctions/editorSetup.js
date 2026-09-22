/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */


/* global tinymce */

/** true během fetch uložení — zabrání dvojímu POST z blur + tlačítka Save */
let savingEditor = false;

/**
 * Uloží obsah TinyMCE POST-em přes fetch (ne nativní form.submit).
 * Plugin Save i blur handler používají tuto funkci.
 *
 * Server u perex/headline/article vrací 204 No Content. Firefox u navigace
 * formuláře s 204 hlásí NS_BINDING_ABORTED a v DevTools není vidět odpověď,
 * i když se data uložila. fetch 204 zpracuje bez navigace.
 *
 * @param {object} editor TinyMCE editor
 * @returns {Promise<void>}
 */
export const saveRedEditor = (editor) => {
    if (!editor || savingEditor) {
        return Promise.resolve();
    }
    const form = editor.formElement;
    if (!form) {
        console.error('saveRedEditor: editor has no formElement');
        return Promise.resolve();
    }
    savingEditor = true;
    editor.save();
    const formData = new FormData(form);
    formData.set(editor.id, editor.getContent());
    const action = form.getAttribute('action');
    return fetch(action, {
        method: (form.getAttribute('method') || 'POST').toUpperCase(),
        body: formData,
        credentials: 'same-origin'
    }).then((response) => {
        if (response.status === 204 || response.ok) {
            editor.setDirty(false);
            return;
        }
        throw new Error('HTTP ' + response.status + ' ' + response.statusText);
    }).catch((error) => {
        console.error('saveRedEditor: ' + error.message);
        editor.notificationManager.open({
            text: 'Uložení selhalo: ' + error.message,
            type: 'error'
        });
    }).finally(() => {
        savingEditor = false;
    });
};

/**
 * Callback funkce nastavená parametrem v konfiguraci TinyMCE setup: editorFunction. Volá se před inicializací instance TinyMCE.
 *
 * @param {type} editor
 * @returns {undefined}
 */
export const redEditorSetup = (editor) => {
    editor.on('blur', (e) => {
        if (savingEditor) {
            return;
        }
        if (editor.isDirty()) {
            if (confirm("Uložit změny?")) {
                saveRedEditor(editor);
            } else {
                if (confirm("Opravdu chcete ukončit editaci a zahodit změny?")) {
                    editor.resetContent();
                    editor.setDirty(false);
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
        const ctrl = e.ctrlKey ? e.ctrlKey : ((e.keyCode === 17) ? true : false); // ctrl detection        
        if (e.keyCode === 86 && ctrl)  return true;   // ctrl + v
        if (allowedKeys.indexOf(e.keyCode) !== -1) return true;
        const activeEditorInstance = activeEditor();
        const max = activeEditorInstance.getParam('max_chars');
        const len = activeEditorContentLength();
        if (len + 1 > max) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        tinymce_updateCharCounter(activeEditorInstance, len+1);
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
    const editor = activeEditor();
    const len = activeEditorContentLength();
    const max = editor.getParam('max_chars');    
    if (len + args.content.length > max) {
        alert('Překročen maximální počet znaků / Maximum number of characters exceeded. Maximum:' + max + '.');
        const shrinked = args.content.substring(0,max-len);
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