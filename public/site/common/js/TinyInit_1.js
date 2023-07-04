/* proměnné jsou definovány v web\local\site\common\templates\js\tinyConfig.js */

/* global tinyConfig */
/* global templates_multipage */
/* global templates_article */
/* global templates_paper */
/* global 'red/v1/templateslist/author' */

/*
 * Přidání/změna selectoru pro Tiny
 * Pokud v Rendereru přidám html prvek, na kterém má být aktivní Tiny,
 * zařadím ho do TinyInit.js - selector může být html tag, id, třída
 * - pokud mám vlastní html tag (např. <mujtag></mujtag>), přidám ještě
 *      1. do TinyInit custom_elements: 'mujtag', extended_valid_elements: 'mujtag'
 *      2. do stylů (layout.less) css vlastnosti mujtag{"display: block; position: relative;"} - Tiny potřebuje blokový prvek
 * - pokud chci selektovat Tiny pomocí třídy/id, ujistím se, že jsem jej přidal/a také v RendererContainerConfigurator.php v classmapách
 */

/**
 * Callback funkce nastavená parametrem v konfiguraci TinyMCE setup: editorFunction. Volá se před inicializací instance TinyMCE.
 *
 * @param {type} editor
 * @returns {undefined}
 */
var editorFunction = function (editor) {

    var form;
    var val;

    editor.on('focus', function(e) {
        val = editor.getContent();
        form = editor.formElement;
    });

    editor.on('blur', function(e) {
        if (editor.isDirty()) {
            if (confirm("Uložit změny?")) {
                editor.save();  // vloží obsah do příslušného hidden inputu
                form.submit();
            } else {
                editor.resetContent();
            }
        }
    });
    editor.on('submit', function(e) {
        var elm = e.element;

    });

    editor.on('NodeChange', function(e) {
      console.log('The ' + e.element.nodeName + ' changed.');
    });

};  // editorFunction

/////////////////////////////////

/**
 * Callback funkce volaná před inicializací TinyMce - použito v editWorkDataConfig.
 *
 * @param {type} ed
 * @returns {undefined}
 */
var maxCharsFunction = function (ed) {
    var allowedKeys = [8, 13, 16, 17, 18, 20, 33, 34, 35, 36, 37, 38, 39, 40, 46];
    ed.on('keydown', function (e) {
        if (allowedKeys.indexOf(e.keyCode) != -1) return true;
        if (tinymce_getContentLength() + 1 > this.settings.max_chars) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        return true;
    });
    ed.on('keyup', function (e) {
        tinymce_updateCharCounter(this, tinymce_getContentLength());
    });
};

/**
 * Callback funkce volaná po inicializaci TinyMce - použito v editWorkDataConfig.
 *
 * @returns {undefined}
 */
var init_instance_callback_function = function () { // initialize counter div
    $('#' + this.id).prev().append('<div class="char_count" style="text-align:right; float: right"></div>');
    tinymce_updateCharCounter(this, tinymce_getContentLength());
};

/**
 * Callback funkce volaná před "paste" vložením obsahu Ctrl+v v TinyMce - použito v editWorkDataConfig.
 * @param {type} plugin
 * @param {type} args
 * @returns {undefined}
 */
var paste_preprocess_function = function (plugin, args) {
    var editor = tinymce.get(tinymce.activeEditor.id);
    var len = editor.contentDocument.body.innerText.length;
    if (len + args.content.length > editor.settings.max_chars) {
        alert('Překročen maximální počet znaků / Maximum number of characters exceeded. Maximum:' + editor.settings.max_chars + '.');
        args.content = '';
    }
    tinymce_updateCharCounter(editor, len + args.content.length);
};

function tinymce_updateCharCounter(el, len) {
    $('#' + el.id).prev().find('.char_count').text(len + '/' + el.settings.max_chars);
}

function tinymce_getContentLength() {
    return tinymce.get(tinymce.activeEditor.id).contentDocument.body.innerText.length;
}


///////////////////////////////////////////////////////////////////////////////////////////

/**
 * Funkce vytvoří v dialogu file picker (Vložit/upravit obrázek) tlačítko pro vyvolání file dialogu, nastaví mu parametry
 * a vyvolá input.click() - tím otevře file dialog.
 * Po vybrání souboru v dialogu (input.onchange) načte obsah souboru do blobCache (proměnná tiny) a vyplní údaje zpět do dialogu file picker.
 * Jméno obrázku složí ze jména souboru vybraného obrázku + "@blob" + timestamp. To zajišťuje, že opakovaně uploadovaný obrázek ze stejného souboru má pokaždé nové jméno
 * a nedojde k zobrazování nového obrázku na místech dříve uploadovaných a vložených obrázků se stejným jménem.
 *
 * @param {type} callback
 * @param {type} value
 * @param {type} meta
 * @returns {undefined}
 */
var file_picker_callback_function = function (callback, value, meta) {
    var input = document.createElement('input');
    input.setAttribute('type', 'file');

    // For the image dialog
    if (meta.filetype === 'image') {
        // IANA MIME types https://www.iana.org/assignments/media-types/media-types.xhtml#image
        // most commonly used web images types https://developer.mozilla.org/en-US/docs/Web/HTML/Element/img
      input.setAttribute('accept', 'image/apng, image/avif, .gif, .jpg, .jpeg, image/png, image/svg+xml, image/webp');
      //input.setAttribute('accept', 'image/*');
    }

    // For the media dialog
    if (meta.filetype === 'media') {
      input.setAttribute('accept', 'video/*');
    }

    /*
      Note: In modern browsers input[type="file"] is functional without
      even adding it to the DOM, but that might not be the case in some older
      or quirky browsers like IE, so you might want to add it to the DOM
      just in case, and visually hide it. And do not forget do remove it
      once you do not need it anymore.
    */

    input.onchange = function () {
        var file = this.files[0];

        var reader = new FileReader();
        reader.onload = function () {
            /*
              Note: Now we need to register the blob in TinyMCEs image blob
              registry. In the next release this part hopefully won't be
              necessary, as we are looking to handle it internally.
            */
            var originalName = file.name.split('.')[0];
            var id = originalName + '@blobid' + (new Date()).getTime();  // timestamp v milisekundách (čas od 1.1.1970)
            var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
            var base64 = reader.result.split(',')[1];  // reader.result konvertuje image na base64 string
            var blobInfo = blobCache.create(id, file, base64);
            blobCache.add(blobInfo);

            /* call the callback and populate the Title field with the file name */
            // zkus  blobInfo.filename()
            callback(blobInfo.blobUri(), { title: file.name.split('.')[0] });  // split - jméno souboru bez přípony
          };
        reader.readAsDataURL(file);
    };

    input.click();
  };
/////////////////////////////////////////

// https://www.tiny.cloud/docs/configure/file-image-upload/#images_upload_handler

function image_upload_handler (blobInfo, success, failure, progress) {
    var xhr, formData;

    xhr = new XMLHttpRequest();
    xhr.withCredentials = false;  // should pass along credentials (such as cookies, authorization headers, or TLS client certificates) for cross-domain uploads
    xhr.open('POST', 'red/v1/upload/editorimages');

    xhr.upload.onprogress = function (e) {
        progress(e.loaded / e.total * 100);
    };

    xhr.onload = function() {
        var json;
        if (xhr.status === 403) {
            failure('HTTP Error: ' + xhr.status, { remove: true });  // remove:true - smaže img element z html
            alert(xhr.statusText);
            console.error('image_upload_handler: failed upload - status: ' + xhr.status + ', message: ' + xhr.statusText);
            return;
        }
        if (xhr.status < 200 || xhr.status >= 300) {
            failure('HTTP Error: ' + xhr.status);
            alert(xhr.statusText);
            console.error('image_upload_handler: failed upload - status: ' + xhr.status + ', message: ' + xhr.statusText);
            return;
        }
        json = JSON.parse(xhr.responseText);

        if (!json || typeof json.location !== 'string') {
            failure('Invalid JSON: ' + xhr.responseText);
            alert(xhr.statusText);
            console.error('image_upload_handler: failed upload - message: ' + xhr.responseText);
            return;
        }
        success(json.location);
    };

    xhr.onerror = function () {
        failure('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
        console.error('image_upload_handler: failed upload - ' + xhr.status);
    };

    formData = new FormData();
    formData.append('file', blobInfo.blob(), blobInfo.filename());  // blobinfo se vytváří v file_picker_callback_function
    //    formData.append('file', blobInfo.blob(), fileName(blobInfo));
    var editedElementId =  tinymce.activeEditor.id;
    var editedElement =  tinymce.activeEditor.getElement();
    var editedMenuItemId =  editedElement.getAttribute('data-red-menuitemid');
    if(null === editedMenuItemId) {
        var msg = 'error image_upload_handler - element id ' + editedElementId + 'has no attribute data-red-menuitemid.';
        console.warn('image_upload_handler: ' + msg);
    } else {
        formData.append('edited_item_id', editedMenuItemId);
    }
    xhr.send(formData);
};



///////////////////////////////////////////////////////////////////////////////////////////

var plugins = [
       'lists  advlist',    // lists - add numbered and bulleted lists, advlist - extends by adding CSS list
       'anchor', // adds an anchor/bookmark button to the toolbar that inserts an anchor at the editor’s cursor
       'autolink', // automatically creates hyperlinks when a user types a valid, complete URL, URLs must include www to be automatically converted
       'autosave', // gives the user a warning if they have unsaved changes in the editor and add a menu item, “Restore last draft” and an optional toolbar button
       'code', //
       'hr',  // Horizontal Rule (hr) plugin allows a user to insert a horizontal rule on the page
       'image', // enables the user to insert an image, also adds a toolbar button and an Insert/edit image menu item under the Insert menu
       //'imagetools', // adds a contextual editing toolbar to the images in the editor
       'link', // allows a user to link external resources, adds two toolbar buttons called link and unlink and three menu items called link, unlink and openlink
       'media ', // adds the ability for users to be able to add HTML5 video and audio elements
       'nonbreaking', // adds a button for inserting nonbreaking space entities &nbsp; , also adds a menu item and a toolbar button
       'noneditable', // enables you to prevent users from being able to edit content within elements assigned the mceNonEditable class
       'paste', // will filter/cleanup content pasted from Microsoft Word
       'save', // adds a save button, which will submit the form that the editor is within.
       'searchreplace', // adds search/replace dialogs, also adds a toolbar button and the menu item
       'table', // adds table management functionality
       'template', // adds support for custom templates. It also adds a menu item and a toolbar button
       //'quickbars',
       'textpattern'
    ];


var toolbarText = 'save cancel | undo redo | fontstyle fontweight | aligment | anchor link';

var toolbar = 'save cancel | undo redo | fontstyle fontweight | aligment | list | template | anchor link image media | code';
var toolbar1 = 'save cancel | undo redo | removeformat | bold italic underline strikethrough nonbreaking | alignleft aligncenter alignright alignjustify | link image media | sablona ';
var toolbar2 = 'styleselect fontsizeselect forecolor | bullist numlist outdent indent | template | code | example';

var linkClassList = [
        {title: 'Vyberte styl odkazu', value: ''},
        {title: 'Výchozí odkaz', value: ''},
        {title: 'Primární tlačítko', value: 'ui primary button'},
        {title: 'Sekundární tlačítko', value: 'ui secondary button'},
        {title: 'Šedé tlačítko', value: 'ui button'}
    ];
var imageClassList = [
        {title: 'Vyberte styl obrázku', value: 'ui image'},
        {title: 'Obrázek není obtékaný', value: 'block-img'},
        {title: 'Obrázek obtékaný zprava', value: 'float-img vlevo'},
        {title: 'Obrázek obtékaný zleva', value: 'float-img vpravo'},
        {title: 'Kruhový obrázek - pro poměr stran 1:1!', value: 'ui circular image'},
        {title: 'Kruhový obrázek obtékaný zprava', value: 'ui circular image float-img vlevo'},
        {title: 'Kruhový obrázek obtékaný zleva', value: 'ui circular image float-img vpravo'},
    ];

var toolbar_groups = {
        fontstyle: {
          icon: 'format',
          tooltip: 'Písmo',
          items: 'styleselect fontsizeselect forecolor | removeformat'
        },
        fontweight: {
          icon: 'bold',
          tooltip: 'Formát',
          items: 'bold italic underline strikethrough | nonbreaking'
        },
        aligment: {
          icon: 'align-left',
          tooltip: 'Zarovnání',
          items: 'alignleft aligncenter alignright alignjustify'
        },
        list: {
          icon: 'ordered-list',
          tooltip: 'Odrážky',
          items: 'bullist numlist outdent indent'
        }
    };
var mobile = {
        menubar: false,
        plugins: [ 'save', 'cancel', 'lists', 'autolink' ],
        toolbar: [ 'save', 'cancel', 'undo', 'bold', 'italic', 'styleselect' ]
    };

var imagetools_toolbar = 'editimage | rotateleft rotateright | flipv fliph | imageoptions';


/////////////////////////////////////////

var editTextConfig = {
    selector: 'form .edit-text',
    schema : 'html5',
    placeholder: 'Nadpis',
    relative_urls : true,
    extended_valid_elements : ['i[*]', 'headline'],
    custom_elements: 'headline',
    hidden_input: true,
    language : tinyConfig.toolbarsLang,
    document_base_url : tinyConfig.basePath,
    content_css: tinyConfig.contentCss,

    menubar: false,
    inline: true,
    plugins: plugins,

    toolbar: toolbarText,
    quickbars_insert_toolbar: '',
    quickbars_selection_toolbar: 'save | undo redo | removeformat italic | link ',

    setup: editorFunction  // callback that will be executed before the TinyMCE editor instance is rendered
};

var editHtmlConfig = {
    selector: 'form .edit-html',
    schema : 'html5',
    placeholder: 'Nový obsah',
    relative_urls: true,
    extended_valid_elements : ['headline[*]', 'perex[*]', 'content[*]', 'i[*]'],
    custom_elements: ['headline', 'perex', 'content'],
    valid_children: '+a[div]',
    link_title: false,
    noneditable_editable_class: 'mceEditable',   // nastavení pro noneditable plugin
    noneditable_noneditable_class: 'mceNonEditable',   // nastavení pro noneditable plugin
    hidden_input: true,
    language : tinyConfig.toolbarsLang,
    document_base_url : tinyConfig.basePath,
    content_css: tinyConfig.contentCss,

    menubar: false,
    inline: true,

    plugins: plugins,
    templates: 'red/v1/templateslist/author',
    toolbar1: toolbar1,
    toolbar2: toolbar2,
    imagetools_toolbar: imagetools_toolbar,
    link_class_list: linkClassList,
    image_class_list: imageClassList,
    image_advtab: true, //přidá do dialogového okna obrázku záložku „Upřesnit“, která umožňuje přidat k obrázkům vlastní styly, mezery a okraje
    /* enable title field in the Image dialog*/
    image_title: true,
    /* enable automatic uploads of images represented by blob or data URIs*/
//    automatic_uploads: true,
    /* URL of our upload handler (for more details check: https://www.tiny.cloud/docs/configure/file-image-upload/#images_upload_url) */
//    na tuto adresu odesílá tiny POST requesty - pro každý obrázek jeden request (tedy request s jedním obrázkem)
// odesílá při každém volání editor.uploadImages() nebo automaticky, pokud je povoleno automatic_uploads option
//    images_upload_url: 'red/v1/upload/editorimages',
    images_reuse_filename: true,
    /* here we add custom filepicker only to Image dialog */
    file_picker_types: 'image media',
    /* and here's our custom image picker*/
    file_picker_callback: file_picker_callback_function,
    images_upload_handler: image_upload_handler,

    setup: editorFunction,  // callback that will be executed before the TinyMCE editor instance is rendered


    /**/
    paste_as_text: true,
    text_patterns: [
        { start: '*', end: '*', format: 'italic' },
        { start: '**', end: '**', format: 'bold' },
    ],
};
var editMceEditableConfig = {
    selector: 'form .edit-mceeditable',
    schema : 'html5',
    placeholder: 'Nový obsah',
    relative_urls: true,
    valid_children: '+a[div]',
    link_title: false,
    noneditable_editable_class: 'mceEditable',   // nastavení pro noneditable plugin
    noneditable_noneditable_class: 'mceNonEditable',   // nastavení pro noneditable plugin
    hidden_input: false,
    language : tinyConfig.toolbarsLang,
    document_base_url : tinyConfig.basePath,
    content_css: tinyConfig.contentCss,

    menubar: false,
    inline: true,

    plugins: plugins,
    templates: 'red/v1/templateslist/author',
    toolbar1: toolbar1,
    toolbar2: toolbar2,
    imagetools_toolbar: imagetools_toolbar,
    link_class_list: linkClassList,
    /* enable title field in the Image dialog*/
    image_title: true,
    /* enable automatic uploads of images represented by blob or data URIs*/
    automatic_uploads: false,  // default value true
    /* URL of our upload handler (for more details check: https://www.tiny.cloud/docs/configure/file-image-upload/#images_upload_url) */
//    na tuto adresu odesílá tiny POST requesty - pro každý obrázek jeden request (tedy request s jedním obrázkem)
// odesílá při každém volání editor.uploadImages() nebo automaticky, pokud je povoleno automatic_uploads option
//    images_upload_url: 'red/v1/upload/editorimages',
    images_reuse_filename: true,
    /* here we add custom filepicker only to Image dialog */
    file_picker_types: 'image media',
    /* and here's our custom image picker*/
    file_picker_callback: file_picker_callback_function,
    images_upload_handler: image_upload_handler,

    setup: editorFunction  // callback that will be executed before the TinyMCE editor instance is rendered
};

var selectTemplateArticleConfig = {
    selector: '.tiny_select_template_article',
    schema : 'html5',
    placeholder: 'Výběr šablony stránky', 
    relative_urls : true,
    extended_valid_elements : ['headline[*]', 'perex[*]', 'content[*]', 'i[*]'],
    custom_elements: ['headline', 'perex', 'content'],
    valid_children: '+a[div] ',
    link_title: false,
    noneditable_editable_class: 'mceEditable',
    noneditable_noneditable_class: 'mceNonEditable',
    language : tinyConfig.toolbarsLang,
    document_base_url : tinyConfig.basePath,
    content_css: tinyConfig.contentCss,
    body_class: "layout preview",

    menubar: false,
    inline: true,
    plugins: [
    'template', 'save', 'noneditable',
    ],
    toolbar: 'template | save',
    templates: 'red/v1/templateslist/article'

};
var selectTemplatePaperConfig = {
    selector: '.tiny_select_template_paper',
    schema : 'html5',
    placeholder: 'Výběr šablony stránky',
    relative_urls : true,
    extended_valid_elements : ['headline[*]', 'perex[*]', 'content[*]', 'i[*]'],
    custom_elements: ['headline', 'perex', 'content'],
    valid_children: '+a[div] ',
    link_title: false,
    noneditable_editable_class: 'mceEditable',
    noneditable_noneditable_class: 'mceNonEditable',
    language : tinyConfig.toolbarsLang,
    document_base_url : tinyConfig.basePath,
    content_css: tinyConfig.contentCss,
    body_class: "layout preview",
    style_formats: [
        { title: 'Vertikálně prohodit perex a contents', selector: 'div.vertikalne-prohodit', classes: [ 'active' ], styles: { 'flex-direction': 'column-reverse' } },
        { title: 'Horizontálně prohodit perex a contents', selector: 'div.horizontalne-prohodit', styles: { 'flex-direction': 'row-reverse' } },
    ],
    style_formats_autohide: true,
    menubar: false,
    inline: true,
    plugins: [
    'template', 'save', 'noneditable',
    ],
    toolbar: 'template | save cancel | styleselect',
    templates: 'red/v1/templateslist/paper'

};

var selectTemplateMultipageConfig = {
    selector: '.tiny_select_template_multipage',
    schema : 'html5',
    placeholder: 'Výběr šablony stránky',
    relative_urls : true,
    extended_valid_elements : ['headline[*]', 'perex[*]', 'content[*]', 'i[*]'],
    custom_elements: ['headline', 'perex', 'content'],
    valid_children: '+a[div] ',
    link_title: false,
    noneditable_editable_class: 'mceEditable',
    noneditable_noneditable_class: 'mceNonEditable',
    language : tinyConfig.toolbarsLang,
    document_base_url : tinyConfig.basePath,
    content_css: tinyConfig.contentCss,
    body_class: "layout preview",

    menubar: false,
    inline: true,
    plugins: [
    'template', 'save', 'noneditable',
    ],
    toolbar: 'template | save',
    templates: 'red/v1/templateslist/multipage'

};

var editWorkDataConfig = {
    selector: '.working-data',
    schema : 'html5',
    relative_urls : false,
    language : tinyConfig.toolbarsLang,
    document_base_url : tinyConfig.basePath,
    menubar: false,
    plugins: [
    'lists', 'paste', 'link'
    ],
    toolbar: 'undo redo | fontsizeselect | bold italic underline | bullist | alignleft aligncenter | link',
    max_chars: 2000,
    setup: maxCharsFunction,
    init_instance_callback: init_instance_callback_function,
    paste_preprocess: paste_preprocess_function
};

tinymce.on('AddEditor', function (e) {
  console.log('Added editor with id: ' + e.editor.id);
});

tinymce.on('RemoveEditor', function (e) {
  console.log('Removed editor with id: ' + e.editor.id);
});