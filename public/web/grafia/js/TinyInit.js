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

// line 25436

var headlineConfig = {
    selector: '.editable headline',
    schema : 'html5',

    language : tinyConfig.toolbarsLang,
    document_base_url : tinyConfig.basePath,
    content_css: tinyConfig.contentCss,

    menubar: false,
    inline: true,
    plugins: [
    'lists',
    'paste',
    'autolink', 'save'
    ],
    toolbar: 'undo redo | bold italic underline | save',
    // valid_elements: 'strong,em,span[style],a[href]',
    // valid_styles: {
    //  '*': 'font-size,font-family,color,text-decoration,text-align'
    // },
    relative_urls : true,
    extended_valid_elements : 'i[*], headline',
    custom_elements: 'headline'
};

var editorFunction = function (editor) {

    editor.ui.registry.addContextToolbar('vyberSablony', {
      predicate: function (node) {
        return node.className === 'stretched row';
      },
      items: 'example',
      position: 'node',
      scope: 'node'

    });
    //
    editor.ui.registry.addButton('sablona', {
      text: 'Vložit šablonu',
      icon: 'vlastni_icona',

      onAction: function (_) {
        editor.insertContent('<p>lala</p>');
      }
    });

    editor.ui.registry.addContextToolbar('vlozitNadpis', {
      predicate: function (node) {
        return node.className === 'ui header';
      },
      items: 'vlastniTlacitkoP',
      //items: 'example',
      position: 'node',
      scope: 'node'
    });
    editor.ui.registry.addIcon('vlastni_icona',
     '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="25px" height="25px" viewBox="0 0 595.279 841.89" enable-background="new 0 0 595.279 841.89" xml:space="preserve"><path d="M422.74,488.949c-5.332-4.775-13.523-4.309-18.272,1.01c-26.995,30.166-65.921,47.455-106.827,47.455     c-40.893,0-79.832-17.289-106.827-47.455c-4.774-5.318-12.94-5.785-18.272-1.01c-5.331,4.764-5.771,12.941-1.009,18.273     c31.899,35.639,77.865,56.072,126.108,56.072c48.257,0,94.223-20.447,126.108-56.072     C428.511,501.89,428.058,493.712,422.74,488.949z"/><path d="M297.64,123.305C133.524,123.305,0,256.829,0,420.945c0,164.117,133.523,297.64,297.64,297.64     s297.64-133.523,297.64-297.64C595.28,256.829,461.756,123.305,297.64,123.305z M297.64,692.703     c-149.855,0-271.758-121.902-271.758-271.757c0-149.855,121.902-271.759,271.758-271.759S569.399,271.09,569.399,420.945     C569.399,570.8,447.495,692.703,297.64,692.703z"/><path d="M401.167,330.359c-35.679,0-64.705,29.026-64.705,64.705c0,7.143,5.798,12.941,12.941,12.941s12.94-5.798,12.94-12.941     c0-21.404,17.419-38.823,38.823-38.823s38.822,17.418,38.822,38.823c0,7.143,5.798,12.941,12.94,12.941     c7.144,0,12.941-5.798,12.941-12.941C465.871,359.385,436.845,330.359,401.167,330.359z"/><path d="M232.936,395.063c0,7.143,5.798,12.941,12.94,12.941c7.144,0,12.941-5.798,12.941-12.941     c0-35.678-29.026-64.705-64.704-64.705c-35.679,0-64.705,29.026-64.705,64.705c0,7.143,5.798,12.941,12.941,12.941     s12.94-5.798,12.94-12.941c0-21.404,17.419-38.823,38.823-38.823C215.517,356.241,232.936,373.659,232.936,395.063z"/></svg>',
        );
    editor.ui.registry.addButton('vlastniTlacitkoP', {
      text: 'Vložit nadpis',
      icon: 'vlastni_icona',
      onAction: function (_) {
        editor.insertContent('<h2>Nadpis</h2>');
      }
    });


    //
    editor.ui.registry.addContextToolbar('vlozitOdstavce', {
      predicate: function (node) {
        return node.className === 'content';
      },
      items: 'vlastniTlacitkoDiv',
      position: 'node',
      scope: 'node'
    });

    editor.ui.registry.addButton('vlastniTlacitkoDiv', {
      text: 'Vložit odstavce',
      icon: 'vlastni_icona',
      onAction: function (_) {
        editor.insertContent('<p>Napište krátký text</p>');
      }
    });

    // ################################

    var form;
    //    var val;

    editor.on('focus', function(e) {
        val = editor.getContent();
        form = editor.formElement;
    });

    editor.on('blur', function(e) {
        if (editor.isDirty()) {
            if (confirm("Zahodit změny?")) {
                editor.reset();
    //                editor.setContent(val);
                editor.save();
            } else {
                editor.save();
                form.submit();
            }
        }
    //        if(val!=editor.getContent()){
    //            form.submit();
    //        }
    });

    };

var segmentConfig = {
  selector: '.headlined.editable content', //.segment:not(.locked):not(.notpermitted) .grafia.segment...
    schema : 'html5',

    language : tinyConfig.toolbarsLang,
    document_base_url : tinyConfig.basePath,
    content_css: tinyConfig.contentCss,

    menubar: false,
    inline: true,
    plugins: [
       'paste advlist autolink lists link  charmap  preview hr anchor pagebreak image code', // codesample print  //
       'searchreplace wordcount visualblocks visualchars code fullscreen',
       'insertdatetime  nonbreaking noneditable save autosave table directionality',
       'template textpattern searchreplace image imagetools save'
//       'template textpattern searchreplace image imagetools save example'
    ],
    templates: [
        { title: 'Kontakt', description: 'Grafia web - kontakt',       url: tinyConfig.content_templates_path + 'kontakt.html'}, //vztaženo k rootu RS, tam kde je index redakčního s.
        { title: 'Publikace', description: 'Grafia web - publikace',   url: tinyConfig.content_templates_path + 'publikace.html'},
        { title: 'Obrázek vlevo a text', description: 'Bez obtékání. Dva sloupce', url: tinyConfig.content_templates_path + 'obrazekVlevo_blok.html'},
        { title: 'Publikace - 2', description: 'Vložení publikací na stránku', url: tinyConfig.content_templates_path + 'eshop_radka.html'},
        { title: 'Menu - 1 položka', description: 'Vložení položky menu na stránku', url: tinyConfig.content_templates_path + 'menu_1polozka.html'},
        { title: 'Menu - 2 položky', description: 'Vložení 2 položek menu na stránku', url: tinyConfig.content_templates_path + 'menu_2polozky.html'},
        { title: 'Menu - 3 položky', description: 'Vložení 3 položek menu na stránku', url: tinyConfig.content_templates_path + 'menu_3polozky.html'},
        { title: '---Tvorba šablon---',    description: 'oddelovac',  url: '' },
        { title: 'Nutné k vytvoření šablon', description: 'Vložte nejprve tuto šablonu a do ní vkládejte ostatní prvky této sekce' , url: tinyConfig.content_templates_path + 'grid.html' },
        { title: 'Menu - 1 položka (bez gridu) verze 1', description: 'Vložení položky menu na stránku', url: tinyConfig.content_templates_path + 'menu_1polozka_1.html'},
        { title: 'Menu - 1 položka (bez gridu) dlouha', description: 'Vložení položky menu na stránku', url: tinyConfig.content_templates_path + 'menu_1polozka_1_delsi.html'},
        { title: 'Menu - 1 položka (bez gridu) dalsi', description: 'Vložení položky menu na stránku', url: tinyConfig.content_templates_path + 'menu_1polozka_1_delsi_1.html'},
        { title: 'Menu - 1 položka (bez gridu) verze 2', description: 'Vložení položky menu na stránku', url: tinyConfig.content_templates_path + 'menu_1polozka_2.html'}
    ],
    toolbar1: 'undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent'
            + ' | hr | nonbreaking | forecolor backcolor ' + ' | fontsizeselect | code | searchreplace template | link image | save'
//            + ' | example vlozitNadpis vlozitOdstavec'
            ,

    imagetools_toolbar: 'editimage | rotateleft rotateright | flipv fliph | imageoptions',
    relative_urls: true,
    extended_valid_elements: 'content, perex',
    custom_elements: 'content, perex',
    valid_children: '+a[div]',
    link_title: false,
    noneditable_editable_class: 'mceEditable',
    noneditable_noneditable_class: 'mceNonEditable',

    setup: editorFunction
};

var perexConfig = {
  selector: '.editable perex', //.segment:not(.locked):not(.notpermitted) .grafia.segment...
    schema : 'html5',

    language : tinyConfig.toolbarsLang,
    document_base_url : tinyConfig.basePath,
    content_css: tinyConfig.contentCss,

    menubar: false,
    inline: true,
    plugins: [
       'paste advlist autolink lists link  charmap  preview hr anchor pagebreak image code', // codesample print  //
       'searchreplace wordcount visualblocks visualchars code fullscreen',
       'insertdatetime  nonbreaking noneditable save autosave table directionality',
       'template textpattern searchreplace image imagetools save'
//       'template textpattern searchreplace image imagetools save example'
    ],
    templates: [
        { title: 'Kontakt', description: 'Grafia web - kontakt',       url: tinyConfig.content_templates_path + 'kontakt.html'}, //vztaženo k rootu RS, tam kde je index redakčního s.
        { title: 'Publikace', description: 'Grafia web - publikace',   url: tinyConfig.content_templates_path + 'publikace.html'},
        { title: 'Obrázek vlevo a text', description: 'Bez obtékání. Dva sloupce', url: tinyConfig.content_templates_path + 'obrazekVlevo_blok.html'},
        { title: 'Publikace - 2', description: 'Vložení publikací na stránku', url: tinyConfig.content_templates_path + 'eshop_radka.html'},
        { title: 'Menu - 1 položka', description: 'Vložení položky menu na stránku', url: tinyConfig.content_templates_path + 'menu_1polozka.html'},
        { title: 'Menu - 2 položky', description: 'Vložení 2 položek menu na stránku', url: tinyConfig.content_templates_path + 'menu_2polozky.html'},
        { title: 'Menu - 3 položky', description: 'Vložení 3 položek menu na stránku', url: tinyConfig.content_templates_path + 'menu_3polozky.html'},
        { title: '---Tvorba šablon---',    description: 'oddelovac',  url: '' },
        { title: 'Nutné k vytvoření šablon', description: 'Vložte nejprve tuto šablonu a do ní vkládejte ostatní prvky této sekce' , url: tinyConfig.content_templates_path + 'grid.html' },
        { title: 'Menu - 1 položka (bez gridu) verze 1', description: 'Vložení položky menu na stránku', url: tinyConfig.content_templates_path + 'menu_1polozka_1.html'},
        { title: 'Menu - 1 položka (bez gridu) dlouha', description: 'Vložení položky menu na stránku', url: tinyConfig.content_templates_path + 'menu_1polozka_1_delsi.html'},
        { title: 'Menu - 1 položka (bez gridu) dalsi', description: 'Vložení položky menu na stránku', url: tinyConfig.content_templates_path + 'menu_1polozka_1_delsi_1.html'},
        { title: 'Menu - 1 položka (bez gridu) verze 2', description: 'Vložení položky menu na stránku', url: tinyConfig.content_templates_path + 'menu_1polozka_2.html'}
    ],
    toolbar1: 'undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent'
            + ' | hr | nonbreaking | forecolor backcolor ' + ' | fontsizeselect | code | searchreplace template | link image | save'
//            + ' | example vlozitNadpis vlozitOdstavec'
            ,

    imagetools_toolbar: 'editimage | rotateleft rotateright | flipv fliph | imageoptions',
    relative_urls: true,
    extended_valid_elements: 'content, perex',
    custom_elements: 'content, perex',
    valid_children: '+a[div]',
    link_title: false,
    noneditable_editable_class: 'mceEditable',
    noneditable_noneditable_class: 'mceNonEditable',

    setup: editorFunction
};

var blockConfig = {
    selector: '.block.editable content',
    schema : 'html5',

    language : tinyConfig.toolbarsLang,
    document_base_url : tinyConfig.basePath,
    content_css: tinyConfig.contentCss,

    menubar: false,
    inline: true,
    plugins: [
    'lists paste autolink link image save'
    ],
    toolbar: 'undo redo | alignleft aligncenter alignright | link image | save',
    relative_urls : true,
    extended_valid_elements : 'i[*], block',
    custom_elements: 'block'
};

var headerFooterConfig = {
    selector: '.kontaktni-udaje, footer p',
    schema : 'html5',

    language : tinyConfig.toolbarsLang,
    document_base_url : tinyConfig.basePath,
    content_css: tinyConfig.contentCss,

    menubar: false,
    inline: true,
    plugins: [
    'lists', 'paste', 'autolink', 'save'
    ],
    toolbar: 'undo redo | bold italic underline | fontsizeselect | forecolor | save',
    relative_urls : true,
    extended_valid_elements : 'i[*]'
};

var selectPaperTemplateConfig = {
    selector: '.paper_template_select',
    schema : 'html5',

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
    relative_urls : true,
    extended_valid_elements : 'i[*], headline, content, perex',
    custom_elements: 'headline, content, perex',
    valid_children: '+a[div] ',
    link_title: false,
    noneditable_editable_class: 'mceEditable',
    noneditable_noneditable_class: 'mceNonEditable',
    templates: [
        { title: 'Článek', description: 'Grafia web - článek',       url: tinyConfig.paper_templates_path + 'paper.html'},
        { title: 'Kontakty', description: 'Grafia web - kontakty',       url: tinyConfig.paper_templates_path + 'paper-contact.html'},
        { title: 'Test - nový paper ze šablony', description: 'paper_test',       url: tinyConfig.paper_templates_path + 'paper_test.html'},
       // { title: 'Publikace', description: 'Grafia web - publikace',   url: tinyConfig.paper_templates_path + 'block.html'},
    ]
};
var vyberSablony = {
    selector: '.vyber_sablony',
    schema : 'html5',

    language : tinyConfig.toolbarsLang,
    document_base_url : tinyConfig.basePath,
    content_css: tinyConfig.contentCss,
    body_class: "layout preview",

    menubar: false,
    inline: true,
    plugins: [
    'template', 'save', 'noneditable', 'code'
    ],
    toolbar: 'template | save | styleselect code',
    relative_urls : true,
    extended_valid_elements : 'i[*], headline, content, perex',
    custom_elements: 'headline, content, perex',
    valid_children: '+a[div], form[perex], form[content]',
    link_title: false,
    noneditable_editable_class: 'mceEditable',
    noneditable_noneditable_class: 'mceNonEditable',
    templates: [
        { title: 'Kontakt', description: 'Grafia web - kontakt',  url: tinyConfig.content_templates_path + 'ohraniceny_blok.html'},
    ],
    style_formats: [
        { title: 'Headers', items: [
          { title: 'Nadpis h2', block: 'h2' },
          { title: 'Nadpis h3', block: 'h3' }
        ] },

        { title: 'Blocks', items: [
          { title: 'Odstavec', block: 'p' },
          { title: 'Blok', block: 'div' }
        ] },

        { title: 'Containers', items: [
          { title: 'sekce', block: 'section', merge_siblings: false },
          { title: 'článek', block: 'article',  merge_siblings: false },
          { title: 'citace', block: 'blockquote' },
          { title: 'doplněk', block: 'aside' }
        ] }
  ],
};



tinymce.init(headlineConfig);
tinymce.init(segmentConfig);
tinymce.init(perexConfig);
tinymce.init(blockConfig);
tinymce.init(headerFooterConfig);
tinymce.init(selectPaperTemplateConfig);
tinymce.init(vyberSablony);
