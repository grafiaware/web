/* proměnné jsou definovány v web\local\site\common\templates\js\tinyConfig.js */

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

///////////////////////////////////////////////////////////////////////////////////////////

import {editorHtmlEditSetup} from "./tinyfunctions/editorSetup.js";
import {filePickerCallback} from "./tinyfunctions/fileupload.js";
import {imageUploadHandler} from "./tinyfunctions/fileupload.js";

var plugins = [
       'lists',    // lists - add numbered and bulleted lists, 
       'advlist',   // advlist - extends by adding CSS list
       'anchor', // adds an anchor/bookmark button to the toolbar that inserts an anchor at the editor’s cursor
       'autolink', // automatically creates hyperlinks when a user types a valid, complete URL, URLs must include www to be automatically converted
       'autosave', // gives the user a warning if they have unsaved changes in the editor and add a menu item, “Restore last draft” and an optional toolbar button
       'code', //
       'image', // enables the user to insert an image, also adds a toolbar button and an Insert/edit image menu item under the Insert menu
       //'editimage', // adds a contextual editing toolbar to the images in the editor
       'link', // allows a user to link external resources, adds two toolbar buttons called link and unlink and three menu items called link, unlink and openlink
       'media ', // adds the ability for users to be able to add HTML5 video and audio elements
       'nonbreaking', // adds a button for inserting nonbreaking space entities &nbsp; , also adds a menu item and a toolbar button
       'save', // adds a save button, which will submit the form that the editor is within.
       'searchreplace', // adds search/replace dialogs, also adds a toolbar button and the menu item
       'table', // adds table management functionality
       'template', // adds support for custom templates. It also adds a menu item and a toolbar button
       //'quickbars',
       'visualblocks',
       'attachment'
    ];


var toolbarText = 'save cancel | undo redo | fontstyle fontweight | aligment | anchor link';

var toolbar = 'save cancel | undo redo | fontstyle fontweight | aligment | list | template | anchor link image media | code';
var toolbar1 = 'save cancel | undo redo | removeformat | bold italic underline strikethrough nonbreaking | alignleft aligncenter alignright alignjustify | link image media';
var toolbar2 = 'styles fontsize forecolor | bullist numlist outdent indent | template | code | visualblocks | attachment';

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
          items: 'styles fontsize forecolor | removeformat'
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
        toolbar: [ 'save', 'cancel', 'undo', 'bold', 'italic', 'stylesstyles' ]
    };

var editimage_toolbar = 'editimage | rotateleft rotateright | flipv fliph | imageoptions';

/////////////////////////////////////////

var editTextConfig = {
    selector: 'form .edit-text',
    schema : 'html5',
    placeholder: 'Nadpis',
    relative_urls : true,
    extended_valid_elements : 'i[*],headline',
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

    setup: editorHtmlEditSetup  // callback that will be executed before the TinyMCE editor instance is rendered
};

var editHtmlConfig = {
    selector: 'form .edit-html',
    schema : 'html5',
    placeholder: 'Nový obsah',
    relative_urls: true,
    extended_valid_elements : 'headline[*],perex[*],content[*],i[*]',
    custom_elements: 'headline,perex,content',
    valid_children: '+a[div]',
    // link_title: false,
    editable_class: 'mceEditable',   // 
    noneditable_class: 'mceNonEditable',   // 
    hidden_input: true,
    language : tinyConfig.toolbarsLang,
    document_base_url : tinyConfig.basePath,
    content_css: tinyConfig.contentCss,

    menubar: false,
//menubar: 'file edit insert view format table tools help': 'file edit insert view format table tools help',

    inline: true,

    plugins: plugins,
    templates: 'red/v1/templateslist/author',
    toolbar1: toolbar1,
    toolbar2: toolbar2,
    editimage_toolbar: editimage_toolbar,
    link_class_list: linkClassList,
    image_class_list: imageClassList,
    image_advtab: true, //přidá do dialogového okna obrázku záložku „Upřesnit“, která umožňuje přidat k obrázkům vlastní styly, mezery a okraje
    /* enable title field in the Image dialog*/
    image_title: true,
    /* enable automatic uploads of images represented by blob or data URIs*/
    automatic_uploads: true, // default true
    /* URL of our upload handler (for more details check: https://www.tiny.cloud/docs/configure/file-image-upload/#images_upload_url) */
//    na tuto adresu odesílá tiny POST requesty - pro každý obrázek jeden request (tedy request s jedním obrázkem)
// odesílá při každém volání editor.uploadImages() nebo automaticky, pokud je povoleno automatic_uploads option
//    images_upload_url: 'red/v1/upload/image',
    images_reuse_filename: true,
    file_picker_types: 'image media file', // tiny bude volat file_picker_callback v dialogu Přidat/upravit obrázek, media, link
    /* and here's our custom image picker*/
    file_picker_callback: filePickerCallback,
    images_upload_handler: imageUploadHandler,

    setup: editorHtmlEditSetup,  // callback that will be executed before the TinyMCE editor instance is rendered


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
    editable_class: 'mceEditable',   // 
    noneditable_class: 'mceNonEditable',   // 
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
    editimage_toolbar: editimage_toolbar,
    link_class_list: linkClassList,
    /* enable title field in the Image dialog*/
    image_title: true,
    /* enable automatic uploads of images represented by blob or data URIs*/
    automatic_uploads: true, // default true
    /* URL of our upload handler (for more details check: https://www.tiny.cloud/docs/configure/file-image-upload/#images_upload_url) */
//    na tuto adresu odesílá tiny POST requesty - pro každý obrázek jeden request (tedy request s jedním obrázkem)
// odesílá při každém volání editor.uploadImages() nebo automaticky, pokud je povoleno automatic_uploads option
//    images_upload_url: 'red/v1/upload/image',
    images_reuse_filename: true,
    file_picker_types: 'image media file', // tiny bude volat file_picker_callback v dialogu Přidat/upravit obrázek, media, link
    /* and here's our custom image picker*/
    file_picker_callback: filePickerCallback,
    images_upload_handler: imageUploadHandler,
    setup: editorHtmlEditSetup  // callback that will be executed before the TinyMCE editor instance is rendered
};

let selectTemplateCommonConfig = {
    schema : 'html5',
    extended_valid_elements : 'headline[*],perex[*],content[*],i[*]',
    custom_elements: 'headline,perex,content',
    valid_children: '+a[div] ',
    relative_urls : true,
    link_title: false,
    editable_class: 'mceEditable',
    noneditable_class: 'mceNonEditable',
    language : tinyConfig.toolbarsLang,
    document_base_url : tinyConfig.basePath,
    content_css: tinyConfig.contentCss,
    body_class: "layout preview",
    menubar: false,
    inline: true,
    plugins: [
    'template', 'save'
    ]
};

var selectTemplateArticleConfig = {
    ...selectTemplateCommonConfig,
    selector: '.tiny_select_template_article',
    placeholder: 'Výběr šablony article',
    toolbar: 'template | save',
    templates: 'red/v1/templateslist/article'
};

var selectTemplatePaperConfig = {
    ...selectTemplateCommonConfig,
    selector: '.tiny_select_template_paper',
    placeholder: 'Výběr šablony paper',
    style_formats: [
        { title: 'Vertikálně prohodit perex a contents', selector: 'div.vertikalne-prohodit', classes: [ 'active' ], styles: { 'flex-direction': 'column-reverse' } },
        { title: 'Horizontálně prohodit perex a contents', selector: 'div.horizontalne-prohodit', styles: { 'flex-direction': 'row-reverse' } },
    ],
    style_formats_autohide: true,
    toolbar: 'template | save cancel | styles',
    templates: 'red/v1/templateslist/paper'
};

var selectTemplateMultipageConfig = {
    ...selectTemplateCommonConfig,
    selector: '.tiny_select_template_multipage',
    placeholder: 'Výběr šablony multipage',
    toolbar: 'template | save',
    templates: 'red/v1/templateslist/multipage'
};

///////////////////////////////////////////////////////////////////////////////////////////

import {setupUserInputEditor, initInstanceUserInputEditor, pastePreprocessUserInput} from "./tinyfunctions/editorSetup.js";

var editUserInputConfig = {
    selector: '.edit-userinput',
    schema : 'html5',
    relative_urls : false,
    language : tinyConfig.toolbarsLang,
    document_base_url : tinyConfig.basePath,
    menubar: false,
    plugins: [
    'lists', 'link'
    ],
    toolbar: 'undo redo | fontsize | bold italic underline | bullist | alignleft aligncenter | link',
    max_chars: 2000,
    setup: setupUserInputEditor,
    init_instance_callback: initInstanceUserInputEditor,
    paste_preprocess: pastePreprocessUserInput
};
///////////////////////////////////////////////////////////////////////////////////////////
    export const initEditors = () => {
        tinymce.remove();
        tinymce.init(editTextConfig);
        tinymce.init(editHtmlConfig);
        tinymce.init(editMceEditableConfig);
        tinymce.init(selectTemplateArticleConfig);
        tinymce.init(selectTemplatePaperConfig);
        tinymce.init(selectTemplateMultipageConfig);

        //pro editaci pracovního popisu pro přihlášené uživatele
        tinymce.init(editUserInputConfig);
        //rozbalení formuláře osobních údajů pro "chci nazávat kontakt"
            $('.profil-visible').on('click', function(){
            $('.profil.hidden').css('display', 'block');
        });
    }

tinymce.on('AddEditor', function (e) {
  console.log('Added editor with id: ' + e.editor.id);
});

tinymce.on('RemoveEditor', function (e) {
  console.log('Removed editor with id: ' + e.editor.id);
});

import {attachmentPlugin} from "./tinyplugins/plugins.js";

tinymce.PluginManager.add('attachment', attachmentPlugin);
