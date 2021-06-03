/* global tinyConfig */

/*
 * Přidání/změna selectoru pro Tiny
 * Pokud v Rendereru přidám html prvek, na kterém má být aktivní Tiny,
 * zařadím ho do TinyInit.js - selector může být html tag, id, třída
 * - pokud mám vlastní html tag (např. <mujtag></mujtag>), přidám ještě
 *      1. do TinyInit custom_elements: 'mujtag', extended_valid_elements: 'mujtag'
 *      2. do stylů (layout.less) css vlastnosti mujtag{"display: block; position: relative;"} - Tiny potřebuje blokový prvek
 * - pokud chci selektovat Tiny pomocí třídy/id, ujistím se, že jsem jej přidal/a také v RendererContainerConfigurator.php v classmapách
 */

var editorFunction = function (editor) {
    editor.ui.registry.addContextToolbar(
        'vyberSablony',
        {
            predicate: function (node) {
                return node.className === 'stretched row';
            },
            items: 'example',
            position: 'node',
            scope: 'node'
        }
    );
    editor.ui.registry.addButton(
        'sablona',
        {
            text: 'Vložit šablonu',
            icon: 'vlastni_icona',
            onAction: function (_) {
                editor.insertContent('<p>lala</p>');
            }
        }
    );
    editor.ui.registry.addContextToolbar(
        'vlozitNadpis',
        {
            predicate: function (node) {
                return node.className === 'ui header';
            },
            items: 'vlastniTlacitkoP',
            //items: 'example',
            position: 'node',
            scope: 'node'
        }
    );
    editor.ui.registry.addIcon(
        'vlastni_icona',
        '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="25px" height="25px" viewBox="0 0 595.279 841.89" enable-background="new 0 0 595.279 841.89" xml:space="preserve"><path d="M422.74,488.949c-5.332-4.775-13.523-4.309-18.272,1.01c-26.995,30.166-65.921,47.455-106.827,47.455     c-40.893,0-79.832-17.289-106.827-47.455c-4.774-5.318-12.94-5.785-18.272-1.01c-5.331,4.764-5.771,12.941-1.009,18.273     c31.899,35.639,77.865,56.072,126.108,56.072c48.257,0,94.223-20.447,126.108-56.072     C428.511,501.89,428.058,493.712,422.74,488.949z"/><path d="M297.64,123.305C133.524,123.305,0,256.829,0,420.945c0,164.117,133.523,297.64,297.64,297.64     s297.64-133.523,297.64-297.64C595.28,256.829,461.756,123.305,297.64,123.305z M297.64,692.703     c-149.855,0-271.758-121.902-271.758-271.757c0-149.855,121.902-271.759,271.758-271.759S569.399,271.09,569.399,420.945     C569.399,570.8,447.495,692.703,297.64,692.703z"/><path d="M401.167,330.359c-35.679,0-64.705,29.026-64.705,64.705c0,7.143,5.798,12.941,12.941,12.941s12.94-5.798,12.94-12.941     c0-21.404,17.419-38.823,38.823-38.823s38.822,17.418,38.822,38.823c0,7.143,5.798,12.941,12.94,12.941     c7.144,0,12.941-5.798,12.941-12.941C465.871,359.385,436.845,330.359,401.167,330.359z"/><path d="M232.936,395.063c0,7.143,5.798,12.941,12.94,12.941c7.144,0,12.941-5.798,12.941-12.941     c0-35.678-29.026-64.705-64.704-64.705c-35.679,0-64.705,29.026-64.705,64.705c0,7.143,5.798,12.941,12.941,12.941     s12.94-5.798,12.94-12.941c0-21.404,17.419-38.823,38.823-38.823C215.517,356.241,232.936,373.659,232.936,395.063z"/></svg>',
    );
    editor.ui.registry.addButton(
        'vlastniTlacitkoP',
        {
            text: 'Vložit nadpis',
            icon: 'vlastni_icona',
            onAction: function (_) {
                editor.insertContent('<h2>Nadpis</h2>');
            }
        }
    );
    editor.ui.registry.addContextToolbar(
        'vlozitOdstavce',
        {
            predicate: function (node) {
                return node.className === 'content';
            },
            items: 'vlastniTlacitkoDiv',
            position: 'node',
            scope: 'node'
        }
    );
    editor.ui.registry.addButton(
        'vlastniTlacitkoDiv',
        {
            text: 'Vložit odstavce',
            icon: 'vlastni_icona',
            onAction: function (_) {
                editor.insertContent('<p>Napište krátký text</p>');
            }
        }
    );

    // ################################

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
    }
            );

    editor.on('NodeChange', function(e) {
      console.log('The ' + e.element.nodeName + ' changed.');
    });

};  // editorFunction
/////////////////////////////////
//maxCharsFunction
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

var init_instance_callback_function = function () { // initialize counter div
    $('#' + this.id).prev().append('<div class="char_count" style="text-align:right; float: right"></div>');
    tinymce_updateCharCounter(this, tinymce_getContentLength());
};

var paste_preprocess_function = function (plugin, args) {
    var editor = tinymce.get(tinymce.activeEditor.id);
    var len = editor.contentDocument.body.innerText.length;
    if (len + args.content.length > editor.settings.max_chars) {
        alert('Pasting this exceeds the maximum allowed number of ' + editor.settings.max_chars + ' characters for the input.');
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

/////////////////////////////////////////
// funkce vytvoří v dialogu file picker (Vložit/upravit obrázek) tlačítko pto vyvolání file dialogu
// po vybrání souboru v dialogu (input.onchange) načte obsah souboru do blobCache (proměnná tiny) a vyplní údaje zpět do dialogu file picker

/**
 *
 * @param {type} cb
 * @param {type} value
 * @param {type} meta
 * @returns {undefined}
 */
var file_picker_callback_function = function (cb, value, meta) {
    var input = document.createElement('input');
    input.setAttribute('type', 'file');

    // For the image dialog
    if (meta.filetype === 'image') {
      input.setAttribute('accept', 'image/*');
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
            var id = originalName + '@blobid' + (new Date()).getTime();
            var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
            var base64 = reader.result.split(',')[1];  // reader.result konvertuje image na base64 string
            var blobInfo = blobCache.create(id, file, base64);
            blobCache.add(blobInfo);

            /* call the callback and populate the Title field with the file name */
            // zkus  blobInfo.filename()
            cb(blobInfo.blobUri(), { title: file.name.split('.')[0] });  // split - jméno souboru bez přípony
          };
        reader.readAsDataURL(file);
    };

    input.click();
  };
/////////////////////////////////////////


function image_upload_handler (blobInfo, success, failure, progress) {
  var xhr, formData;

  xhr = new XMLHttpRequest();
  xhr.withCredentials = false;
  xhr.open('POST', 'red/v1/upload/editorimages');

  xhr.upload.onprogress = function (e) {
    progress(e.loaded / e.total * 100);
  };

  xhr.onload = function() {
    var json;

    if (xhr.status === 403) {
      failure('HTTP Error: ' + xhr.status, { remove: true });
      return;
    }

    if (xhr.status < 200 || xhr.status >= 300) {
      failure('HTTP Error: ' + xhr.status);
      return;
    }

    json = JSON.parse(xhr.responseText);

    if (!json || typeof json.location != 'string') {
      failure('Invalid JSON: ' + xhr.responseText);
      return;
    }

    success(json.location);
  };

  xhr.onerror = function () {
    failure('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
  };

  formData = new FormData();
  formData.append('file', blobInfo.blob(), blobInfo.filename());

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
       'imagetools', // adds a contextual editing toolbar to the images in the editor
       'link', // allows a user to link external resources, adds two toolbar buttons called link and unlink and three menu items called link, unlink and openlink
       'media ', // adds the ability for users to be able to add HTML5 video and audio elements
       'nonbreaking', // adds a button for inserting nonbreaking space entities &nbsp; , also adds a menu item and a toolbar button
       'noneditable', // enables you to prevent users from being able to edit content within elements assigned the mceNonEditable class
       'paste', // will filter/cleanup content pasted from Microsoft Word
       'save', // adds a save button, which will submit the form
       'searchreplace', // adds search/replace dialogs, also adds a toolbar button and the menu item
       'table', // adds table management functionality
       'template', // adds support for custom templates. It also adds a menu item and a toolbar button
    ];

var templates_paper = [
        { title: 'Kontakt', description: 'Grafia web - kontakt',       url: 'red/v1/authortemplate/default/kontakt'}, //vztaženo k rootu RS, tam kde je index redakčního s.
        { title: 'Publikace - novinka', description: 'Grafia web - publikace',   url: 'red/v1/authortemplate/default/eshop_nove'},
        { title: 'Obrázek vlevo a text', description: 'Bez obtékání. Dva sloupce', url: 'red/v1/authortemplate/default/obrazekVlevo_blok'},
        { title: 'Publikace - 2', description: 'Vložení publikací na stránku', url: 'red/v1/authortemplate/default/eshop_radka'},
        { title: 'Menu - 1 položka', description: 'Vložení položky menu na stránku', url: 'red/v1/authortemplate/default/menu_1polozka'},
        { title: 'Menu - 2 položky', description: 'Vložení 2 položek menu na stránku', url: 'red/v1/authortemplate/default/menu_2polozky'},
        { title: 'Menu - 3 položky', description: 'Vložení 3 položek menu na stránku', url: 'red/v1/authortemplate/default/menu_3polozky'},
        { title: '---Tvorba šablon---',    description: 'oddelovac',  url: '' },
        { title: 'Nutné k vytvoření šablon', description: 'Vložte nejprve tuto šablonu a do ní vkládejte ostatní prvky této sekce' , url: 'red/v1/authortemplate/default/grid' },
        { title: 'Menu - 1 položka (bez gridu) verze 1', description: 'Vložení položky menu na stránku', url: 'red/v1/authortemplate/default/menu_1polozka_1'},
        { title: 'Menu - 1 položka (bez gridu) dlouha', description: 'Vložení položky menu na stránku', url: 'red/v1/authortemplate/default/menu_1polozka_1_delsi'},
        { title: 'Menu - 1 položka (bez gridu) dalsi', description: 'Vložení položky menu na stránku', url: 'red/v1/authortemplate/default/menu_1polozka_1_delsi_1'},
        { title: 'Menu - 1 položka (bez gridu) verze 2', description: 'Vložení položky menu na stránku', url: 'red/v1/authortemplate/default/menu_1polozka_2'}
    ];

var toolbar = 'save cancel | undo redo | fontstyle fontweight | aligment | list | template | anchor link image media | code';
var toolbar1 = 'save cancel | undo redo | removeformat | bold italic underline strikethrough nonbreaking | alignleft aligncenter alignright alignjustify | link image ';
var toolbar2 = 'styleselect fontsizeselect forecolor | bullist numlist outdent indent | template | code | example';

var linkClassList = [
        {title: 'Vyberte styl odkazu', value: ''},
        {title: 'Výchozí odkaz', value: ''},
        {title: 'Primární tlačítko', value: 'ui primary button'},
        {title: 'Sekundární tlačítko', value: 'ui secondary button'},
        {title: 'Šedé tlačítko', value: 'ui button'}
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
var blockConfig = {
    selector: 'main form block',
    schema : 'html5',
    placeholder: 'Nový blok',
    relative_urls: true,
    extended_valid_elements : ['block'],
    language : tinyConfig.toolbarsLang,
    document_base_url : tinyConfig.basePath,
    content_css: tinyConfig.contentCss,
    plugins: [
        'quickbars', // adds three context toolbars: Quick Selection - Shown when text is selected, Quick Insert - Shown when a new line is added, Quick Image - Shown when an image is selected
     ],

    setup: editorFunction  // callback that will be executed before the TinyMCE editor instance is rendered
    };

var headlineConfig = {
    selector: 'main form headline',
    schema : 'html5',
    placeholder: 'Nadpis',
    relative_urls : true,
    extended_valid_elements : ['i[*]', 'headline'],
    custom_elements: 'headline',
    language : tinyConfig.toolbarsLang,
    document_base_url : tinyConfig.basePath,
    content_css: tinyConfig.contentCss,

    menubar: false,
    inline: true,
    plugins: plugins,

    toolbar: false,
    quickbars_insert_toolbar: '',
    quickbars_selection_toolbar: 'save | undo redo | removeformat italic | link ',
    toolbar: 'undo redo | bold italic underline | save',

    setup: editorFunction  // callback that will be executed before the TinyMCE editor instance is rendered
};

var contentConfig = {
    selector: 'main form content', //.segment:not(.locked):not(.notpermitted) .grafia.segment...
    schema : 'html5',
    placeholder: 'Nový obsah',
    relative_urls: true,
    extended_valid_elements: ['i[*]', 'content'],
    custom_elements: 'content',
    valid_children: '+a[div]',
    link_title: false,
    noneditable_editable_class: 'mceEditable',
    noneditable_noneditable_class: 'mceNonEditable',
    language : tinyConfig.toolbarsLang,
    document_base_url : tinyConfig.basePath,
    content_css: tinyConfig.contentCss,

    menubar: false,
    inline: true,

    plugins: plugins,
    templates: templates_paper,
    toolbar1: toolbar1,
    toolbar2: toolbar2,
    imagetools_toolbar: imagetools_toolbar,
    link_class_list: linkClassList,
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

    setup: editorFunction  // callback that will be executed before the TinyMCE editor instance is rendered
};

var perexConfig = {
    selector: 'main form perex', //.segment:not(.locked):not(.notpermitted) .grafia.segment...
    schema : 'html5',
    placeholder: 'Vyplňte perex',
    relative_urls: true,
    extended_valid_elements: ['i[*]', 'perex'],
    custom_elements: 'perex',
    valid_children: '+a[div]',
    link_title: false,
    noneditable_editable_class: 'mceEditable',
    noneditable_noneditable_class: 'mceNonEditable',
    language : tinyConfig.toolbarsLang,
    document_base_url : tinyConfig.basePath,
    content_css: tinyConfig.contentCss,

    menubar: false,
    inline: true,

    plugins: plugins,
    templates: templates_paper,
    toolbar1: toolbar1,
    toolbar2: toolbar2,
    imagetools_toolbar: imagetools_toolbar,
    link_class_list: linkClassList,
    /* enable title field in the Image dialog*/
    image_title: true,
    /* enable automatic uploads of images represented by blob or data URIs*/
//    automatic_uploads: true,
    /* URL of our upload handler (for more details check: https://www.tiny.cloud/docs/configure/file-image-upload/#images_upload_url) */
//    images_upload_url: 'red/v1/upload/editorimages',
    images_reuse_filename: true,
    /* here we add custom filepicker only to Image dialog */
    file_picker_types: 'image',
    /* and here's our custom image picker*/
    file_picker_callback: file_picker_callback_function,
    images_upload_handler: image_upload_handler,

    setup: editorFunction
};

var headerFooterConfig = {
    selector: '.kontaktni-udaje, footer p',
    schema : 'html5',
    relative_urls : true,
    extended_valid_elements : 'i[*]',
    language : tinyConfig.toolbarsLang,
    document_base_url : tinyConfig.basePath,
    content_css: tinyConfig.contentCss,

    menubar: false,
    inline: true,
    plugins: [
    'lists', 'paste', 'autolink', 'save'
    ],
    toolbar: 'undo redo | bold italic underline | fontsizeselect | forecolor | save'
};

var selectPaperTemplateConfig = {
    selector: 'main .paper_template_select',
    schema : 'html5',
    placeholder: 'Výběr šablony stránky',
    relative_urls : true,
    extended_valid_elements : ['headline[*]', 'perex[*]', 'content[*]'],
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
    'template', 'save', 'noneditable', 'code'
    ],
    toolbar: 'template | save code',
    templates: [
        { title: 'template default', description: 'Grafia web - článek',       url: 'red/v1/papertemplate/default'},
        { title: 'template contact', description: 'Grafia web - kontakty',       url: 'red/v1/papertemplate/contact'},
        { title: 'template test', description: 'paper_test',       url: 'red/v1/papertemplate/test'},
        { title: 'template course', description: 'Grafia web - kurz',       url: 'red/v1/papertemplate/course'},
        { title: 'Vzor - Úvod', description: 'Vzor - Úvod',       url: 'web/v1/static/uvod'},
        { title: 'Test - presentedpaper s šablonou default', description: 'rendered component',       url: 'red/v1/presenteditem?template=default'},
        { title: 'Test - presentedpaper s šablonou contact', description: 'rendered component',       url: 'red/v1/presenteditem?template=contact'},
        { title: 'Test - presentedpaper s šablonou test', description: 'rendered component',       url: 'red/v1/presenteditem?template=test'},
        { title: 'Test - presentedpaper s šablonou course', description: 'rendered component',       url: 'red/v1/presenteditem?template=course'},
        { title: 'Test - namedpaper a1', description: 'rendered component',       url: 'red/v1/nameditem/a1'},
        { title: 'Test - namedpaper a2', description: 'rendered component',       url: 'red/v1/nameditem/a2'},
        { title: 'Test - namedpaper a3', description: 'rendered component',       url: 'red/v1/nameditem/a3'},

       // { title: 'Publikace', description: 'Grafia web - publikace',   url: tinyConfig.paper_templates_path + 'block/'},
    ]

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


//        tinymce.init(headlineConfig);
//        tinymce.init(contentConfig);
//        tinymce.init(perexConfig);
//        tinymce.init(headerFooterConfig);
        tinymce.init(selectPaperTemplateConfig);
