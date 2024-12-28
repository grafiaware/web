
/* skript je načítán v body.js - document.onreadystatechange - import("tinyinit/TinyInit.js")
 * proměnné jsou definovány v web\local\site\common\templates\js\tinyConfig.js
 */

/* global tinyConfig */

/*
 * Přidání/změna selectoru pro Tiny
 * Pokud v Rendereru přidám html prvek, na kterém má být aktivní Tiny,
 * zařadím ho do 
 *  - selector může být html tag, id, třída
 * - pokud mám vlastní html tag (např. <mujtag></mujtag>), přidám ještě
 *      1. do TinyInit custom_elements: 'mujtag', extended_valid_elements: 'mujtag'
 *      2. do stylů (layout.less) css vlastnosti mujtag{"display: block; position: relative;"} - Tiny potřebuje blokový prvek
 * - pokud chci selektovat Tiny pomocí třídy/id, ujistím se, že jsem jej přidal/a také v RendererContainerConfigurator.php v classmapách
 */

//
// inicializace editorů
//
export const initEditors = () => {
    tinymce.remove();
    tinymce.init(editFormRepresentative);
    tinymce.init(editTextConfig);
    tinymce.init(editHtmlConfig);
    tinymce.init(editMceEditableConfig);
    tinymce.init(selectTemplateArticleConfig);
    tinymce.init(selectTemplatePaperConfig);
    tinymce.init(selectTemplateMultipageConfig);

    //pro editaci pracovního popisu pro přihlášené uživatele
    tinymce.init(editUserInputConfig);
}
    
///////////////////////////////////////////////////////////////////////////////////////////

import {redEditorSetup} from "./tinyfunctions/editorSetup.js";
import {filePickerCallback} from "./tinyfunctions/fileupload.js";
import {redImageUploadHandler} from "./tinyfunctions/fileupload.js";

//open source plugins:
//    Accordion
//    Anchor
//    Autolink
//    Autoresize
//    Autosave
//    Character Map
//    Code
//    Code Sample
//    Directionality
//    Emoticons
//    Full Screen
//    Help
//    Image
//    Import CSS
//    Insert Date/Time
//    Link
//    Lists
//    List Styles
//    Media
//    Nonbreaking Space
//    Page Break
//    Preview
//    Quick Toolbars
//    Save
//    Search and Replace
//    Table
//    Visual Blocks
//    Visual Characters
//    Word Count


var editCommonPlugins = [
       'lists',    // lists - add numbered and bulleted lists, 
       'advlist',   // advlist - extends by adding CSS list
       'anchor', // adds an anchor/bookmark button to the toolbar that inserts an anchor at the editor’s cursor
       'autolink', // automatically creates hyperlinks when a user types a valid, complete URL, URLs must include www to be automatically converted
       'autosave', // gives the user a warning if they have unsaved changes in the editor and add a menu item, “Restore last draft” and an optional toolbar button
       'code', //
       'image', // enables the user to insert an image, also adds a toolbar button and an Insert/edit image menu item under the Insert menu
//       'editimage', // není obsažen v aktuální verzi timy // adds a contextual editing toolbar to the images in the editor
       'link', // allows a user to link external resources, adds two toolbar buttons called link and unlink and three menu items called link, unlink and openlink
       'media ', // adds the ability for users to be able to add HTML5 video and audio elements
       'nonbreaking', // adds a button for inserting nonbreaking space entities &nbsp; , also adds a menu item and a toolbar button
       'save', // adds a save button, which will submit the form that the editor is within.
       'searchreplace', // adds search/replace dialogs, also adds a toolbar button and the menu item
       'table', // adds table management functionality
       'template', // adds support for custom templates. It also adds a menu item and a toolbar button
//       'quickbars',
       'visualchars',  // adds the ability to see invisible characters like &nbsp; displayed in the editable area
       'visualblocks',  // allows a user to see block level elements in the editable area
       'attachment'
    ];

var menubarfull = 'file edit view insert format tools table help';  //TODO: help
var toolbarfull = "undo redo | accordion accordionremove | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link image | table media | lineheight outdent indent| forecolor backcolor removeformat | charmap emoticons | code fullscreen preview | save print | pagebreak anchor codesample | ltr rtl";

var toolbarText = 'save cancel | undo redo | styles | anchor'; //| fontstyle fontweight | aligment ';

var toolbarHtml = 'save cancel | undo redo | fontstyle fontweight | aligment | list | template | anchor link image | code'; 
var toolbarHtmlRow1 = 'save cancel | undo redo | removeformat | bold italic underline nonbreaking | alignleft aligncenter alignright alignjustify | link';
var toolbarHtmlRow2 = 'styles fontsize forecolor | bullist numlist outdent indent | code visualchars visualblocks | template | image media attachment';

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
        toolbar: [ 'save', 'cancel', 'undo', 'bold', 'italic', 'styles' ]
    };

var editimage_toolbar = 'editimage | rotateleft rotateright | flipv fliph | imageoptions';

/////////////////////////////////////////
let editCommonConfig = {
    schema : 'html5',
    promotion: false,   // vypíná tlačítko upgrade - Premium upgrade promotion option
    relative_urls : true,  // true — All URLs created in TinyMCE will be converted to a link relative to the document_base_url (false - absolute url's)
    inline: true, // Inline editing mode does not replace the selected element with an iframe, but instead edits the element’s content in-place
                  // Sticky toolbars are always enabled in inline mode and cannot be disabled.
                  // By default all inline editors have a hidden input element in which content gets saved 
                  // This option is not supported on mobile devices. 
//  toolbar_mode: 'sliding',    
//event_root: 'main',                  
    editable_class: 'mceEditable',
    noneditable_class: 'mceNonEditable',
 
    language : tinyConfig.toolbarsLang,
    document_base_url : tinyConfig.basePath,
    content_css: tinyConfig.contentCss,    
};

let editRedConfig = {
    extended_valid_elements : 'headline[*],perex[*],content[*],i[*]',
    custom_elements: 'headline,perex,content',
    valid_children: '+a[div]',
    fixed_toolbar_container: '.item_action', //'.ribbon'
};

let editFullConfig = {
    link_class_list: linkClassList,
    image_class_list: imageClassList,  
    smart_paste: true,   // Detect text that resembles a URL and change the text to a hyperlink.
                         //Detect text that resembles the URL for an image and will try to replace the text with the image.       
};

var editFormRepresentative = {
    selector: 'form .edit-representative',
    placeholder: 'Napište text',
    paste_as_text: true,  // default false, true - převede vkládaný obsah na holý text
    menubar: false,  // bez vypnutí se zobeazí default menu
    plugins: ['save', 'cancel', 'lists', 'autolink'], //var mobile
    toolbar: ['save', 'cancel', 'undo', 'bold', 'italic', 'styles'], //var mobile
    setup: redEditorSetup   
    //plugin wordcount
    
};

var editTextConfig = {
    ...editCommonConfig,
    ...editRedConfig,
    selector: 'form .edit-text',
    placeholder: 'Nadpis',
    extended_valid_elements : 'i[*],headline',
    custom_elements: 'headline',
    paste_as_text: true,  // default false, true - převede vkládaný obsah na holý text
    
    menubar: false,  // bez vypnutí se zobeazí default menu
    plugins: editCommonPlugins,  
    toolbar: toolbarText,
//    quickbars_insert_toolbar: false,
//    quickbars_selection_toolbar: 'save | undo redo | removeformat italic | link ',    
    style_formats: [
        {title: 'Styly nadpisu'},  // group
        {title: 'Výchozí styl nadpisu webu', selector: 'p', classes: 'nadpis'},
        {title: 'Animovaný, podtržený zap/vyp', selector: 'p', classes: 'nadpis podtrzeny nastred nadpis-scroll show-on-scroll is-visible'}
    ],
    /* callback that will be executed before the TinyMCE editor instance is rendered */
    setup: redEditorSetup    
};

var editHtmlConfig = {
    ...editCommonConfig,
    ...editRedConfig,
    ...editFullConfig,
    selector: 'form .edit-html',
    placeholder: 'Nový obsah',
    
    menubar: menubarfull, //    menubar: false,
    plugins: editCommonPlugins,
    templates: 'red/v1/templateslist/author',

//    toolbar: toolbar_groups,
//    toolbar: toolbarfull,
    toolbar1: toolbarHtmlRow1,
    toolbar2: toolbarHtmlRow2,
    editimage_toolbar: editimage_toolbar,

    /* přidá do dialogového okna obrázku záložku „Upřesnit“, která umožňuje přidat k obrázkům vlastní styly, mezery a okraje */
    image_advtab: true,
    /* enable title field in the Image dialog*/
    image_title: true,
    /* enable automatic uploads of images represented by blob or data URIs */
    automatic_uploads: true, // default true
    /* URL of our upload handler (for more details check: https://www.tiny.cloud/docs/configure/file-image-upload/#images_upload_url) */
//    na tuto adresu odesílá tiny POST requesty - pro každý obrázek jeden request (tedy request s jedním obrázkem)
//    odesílá při každém volání editor.uploadImages() nebo automaticky, pokud je povoleno automatic_uploads option
//    images_upload_url: 'red/v1/upload/image',
    images_reuse_filename: true,
    file_picker_types: 'image media file', // tiny bude volat file_picker_callback v dialogu Přidat/upravit obrázek, media, link
    /* and here's our custom image picker */
    file_picker_callback: filePickerCallback,
    images_upload_handler: redImageUploadHandler,
    
    video_template_callback: (data) =>
    `<video width="${data.width}" height="${data.height}"${data.poster ? ` poster="${data.poster}"` : ''} controls="controls">\n` +
    `<source src="${data.source}"${data.sourcemime ? ` type="${data.sourcemime}"` : ''} />\n` +
    (data.altsource ? `<source src="${data.altsource}"${data.altsourcemime ? ` type="${data.altsourcemime}"` : ''} />\n` : '') +
    '</video>',
    
    
    
    /* callback that will be executed before the TinyMCE editor instance is rendered */
    setup: redEditorSetup
};

var editMceEditableConfig = {
    ...editCommonConfig,
    ...editFullConfig,
    selector: 'form .edit-mceeditable',
    placeholder: 'Nový obsah',
    valid_children: '+a[div]',

    menubar: false,

    plugins: editCommonPlugins,
    templates: 'red/v1/templateslist/author',
    toolbar1: toolbarHtmlRow1,
    toolbar2: toolbarHtmlRow2,
    editimage_toolbar: editimage_toolbar,
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
    images_upload_handler: redImageUploadHandler,
    /* callback that will be executed before the TinyMCE editor instance is rendered */
    setup: redEditorSetup
};

let selectTemplateCommonConfig = {
    ...editCommonConfig,
    ...editRedConfig,
    body_class: "layout preview",
//    menubar: false,
    plugins: [
    'template', 'save' 
    ],
    
//  menu: {
//    custom: { title: 'Custom Menu', items: 'undo redo myCustomMenuItem' }
//  },
//  menubar: 'file edit custom',
//  setup: (editor) => {
//    editor.ui.registry.addMenuItem('myCustomMenuItem', {
//      text: 'My Custom Menu Item',
//      onAction: () => alert('Menu item clicked')
//    });
//  }    
};

var selectTemplateArticleConfig = {
    ...selectTemplateCommonConfig,
    selector: '.tiny_select_template_article',
    placeholder: 'Výběr stylu zobrazení pro article',
    toolbar: 'template | save',
    templates: 'red/v1/templateslist/article'
};

var selectTemplatePaperConfig = {
    ...selectTemplateCommonConfig,
    selector: '.tiny_select_template_paper',
    placeholder: 'Výběr stylu zobrazení pro paper',
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
    placeholder: 'Výběr stylu zobrazení pro multipage',
    toolbar: 'template | save', 
    templates: 'red/v1/templateslist/multipage'
};

///////////////////////////////////////////////////////////////////////////////////////////

import {setupUserInputEditor, initInstanceUserInputEditor, pastePreprocessUserInput} from "./tinyfunctions/editorSetup.js";

var editUserInputConfig = {
    selector: '.edit-userinput',
    schema : 'html5',
    promotion: false,   // vypíná tlačítko upgrade - Premium upgrade promotion option
    relative_urls : true,
    hidden_input: true,
//    inline: true,
    paste_as_text: true,  // default false, true - převede vkládaný obsah na holý text

    menubar: false,
    plugins: [
    'lists', 'link'
    ],
    toolbar: 'undo redo | bold italic underline | bullist | alignleft aligncenter | link',
//    toolbar: 'undo redo | fontsize | bold italic underline | bullist | alignleft aligncenter | link',
    max_chars: 2000,
    language : tinyConfig.toolbarsLang,
    document_base_url : tinyConfig.basePath,    
    setup: setupUserInputEditor,
    init_instance_callback: initInstanceUserInputEditor,
    paste_preprocess: pastePreprocessUserInput
};

////////////////////////////////////////

// https://codepen.io/maibaduy/pen/KrGPve
var editStickyConfig = {
  selector: 'textarea',
  height: 1100,
  menubar: false,
  plugins: [
    'advlist autolink lists link image charmap print preview anchor',
    'searchreplace visualblocks code fullscreen',
    'insertdatetime media table contextmenu paste code',
    'stickytoolbar autoresize'
  ],
  toolbar1: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
  toolbar2: 'link image',
  content_css: [
    '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
    '//www.tinymce.com/css/codepen.min.css']
};

import {attachmentPlugin} from "./tinyplugins/plugins.js";
tinymce.PluginManager.add('attachment', attachmentPlugin);

tinymce.on('AddEditor', function (e) {
  console.log('Added editor with id: ' + e.editor.id);
});

tinymce.on('RemoveEditor', function (e) {
  console.log('Removed editor with id: ' + e.editor.id);
});
