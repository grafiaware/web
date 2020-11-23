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

var headlineConfig = {
    selector: 'form headline',
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
    plugins: [
    'lists',
    'paste',
    'autolink',
    'quickbars',
    'link',
    'save'
    ],
    toolbar: false,
    quickbars_insert_toolbar: '',
    quickbars_selection_toolbar: 'save | undo redo | removeformat italic | link ',
    toolbar: 'undo redo | bold italic underline | save'
};

var contentConfig = {
    selector: 'form content', //.segment:not(.locked):not(.notpermitted) .grafia.segment...
    schema : 'html5',
    placeholder: 'Nový obsah',
    relative_urls: true,
    extended_valid_elements: 'content',
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
    plugins: [
       'paste advlist autolink lists link  charmap  preview hr anchor pagebreak image code', // codesample print  //
       'searchreplace wordcount visualblocks visualchars code fullscreen',
       'insertdatetime  nonbreaking noneditable save autosave table directionality',
       'template textpattern searchreplace image imagetools save formatpainter cancel'
//       'template textpattern searchreplace image imagetools save example'
    ],
    templates: [
        { title: 'Kontakt', description: 'Grafia web - kontakt',       url: 'component/v1/authortemplate/kontakt/'}, //vztaženo k rootu RS, tam kde je index redakčního s.
        { title: 'Publikace', description: 'Grafia web - publikace',   url: 'component/v1/authortemplate/publikace/'},
        { title: 'Obrázek vlevo a text', description: 'Bez obtékání. Dva sloupce', url: 'component/v1/authortemplate/obrazekVlevo_blok/'},
        { title: 'Publikace - 2', description: 'Vložení publikací na stránku', url: 'component/v1/authortemplate/eshop_radka/'},
        { title: 'Menu - 1 položka', description: 'Vložení položky menu na stránku', url: 'component/v1/authortemplate/menu_1polozka/'},
        { title: 'Menu - 2 položky', description: 'Vložení 2 položek menu na stránku', url: 'component/v1/authortemplate/menu_2polozky/'},
        { title: 'Menu - 3 položky', description: 'Vložení 3 položek menu na stránku', url: 'component/v1/authortemplate/menu_3polozky/'},
        { title: '---Tvorba šablon---',    description: 'oddelovac',  url: '' },
        { title: 'Nutné k vytvoření šablon', description: 'Vložte nejprve tuto šablonu a do ní vkládejte ostatní prvky této sekce' , url: 'component/v1/authortemplate/grid/' },
        { title: 'Menu - 1 položka (bez gridu) verze 1', description: 'Vložení položky menu na stránku', url: 'component/v1/authortemplate/menu_1polozka_1/'},
        { title: 'Menu - 1 položka (bez gridu) dlouha', description: 'Vložení položky menu na stránku', url: 'component/v1/authortemplate/menu_1polozka_1_delsi/'},
        { title: 'Menu - 1 položka (bez gridu) dalsi', description: 'Vložení položky menu na stránku', url: 'component/v1/authortemplate/menu_1polozka_1_delsi_1/'},
        { title: 'Menu - 1 položka (bez gridu) verze 2', description: 'Vložení položky menu na stránku', url: 'component/v1/authortemplate/menu_1polozka_2/'}
    ],
//    toolbar1: 'undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent'
//            + ' | hr | nonbreaking | forecolor backcolor ' + ' | fontsizeselect | code | searchreplace template | link image | save'
//            + ' | example vlozitNadpis vlozitOdstavec'
    toolbar: 'save cancel | undo redo | fontstyle fontweight | aligment | list | template | link image | code',
    toolbar_groups: {
        fontstyle: {
          icon: 'format',
          tooltip: 'Písmo',
          items: 'styleselect fontsizeselect forecolor | formatpainter removeformat'
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
    },
    mobile: {
        menubar: false,
        plugins: [ 'save', 'cancel', 'lists', 'autolink' ],
        toolbar: [ 'save', 'cancel', 'undo', 'bold', 'italic', 'styleselect' ]
    },

    imagetools_toolbar: 'editimage | rotateleft rotateright | flipv fliph | imageoptions',

    setup: editorFunction
};

var perexConfig = {
    selector: 'form perex', //.segment:not(.locked):not(.notpermitted) .grafia.segment...
    schema : 'html5',
    placeholder: 'Vyplňte perex',
    relative_urls: true,
    extended_valid_elements: 'perex',
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

    plugins: [
       'paste advlist autolink lists link  charmap  preview hr anchor pagebreak image code', // codesample print  //
       'searchreplace wordcount visualblocks visualchars code fullscreen',
       'insertdatetime  nonbreaking noneditable save autosave table directionality',
       'template textpattern searchreplace image imagetools save cancel'
//       'template textpattern searchreplace image imagetools save example'
    ],
    templates: [
        { title: 'Kontakt', description: 'Grafia web - kontakt',       url: 'component/v1/authortemplate/kontakt/'}, //vztaženo k rootu RS, tam kde je index redakčního s.
        { title: 'Publikace', description: 'Grafia web - publikace',   url: 'component/v1/authortemplate/publikace/'},
        { title: 'Obrázek vlevo a text', description: 'Bez obtékání. Dva sloupce', url: 'component/v1/authortemplate/obrazekVlevo_blok/'},
        { title: 'Publikace - 2', description: 'Vložení publikací na stránku', url: 'component/v1/authortemplate/eshop_radka/'},
        { title: 'Menu - 1 položka', description: 'Vložení položky menu na stránku', url: 'component/v1/authortemplate/menu_1polozka/'},
        { title: 'Menu - 2 položky', description: 'Vložení 2 položek menu na stránku', url: 'component/v1/authortemplate/menu_2polozky/'},
        { title: 'Menu - 3 položky', description: 'Vložení 3 položek menu na stránku', url: 'component/v1/authortemplate/menu_3polozky/'},
        { title: '---Tvorba šablon---',    description: 'oddelovac',  url: '' },
        { title: 'Nutné k vytvoření šablon', description: 'Vložte nejprve tuto šablonu a do ní vkládejte ostatní prvky této sekce' , url: 'component/v1/authortemplate/grid/' },
        { title: 'Menu - 1 položka (bez gridu) verze 1', description: 'Vložení položky menu na stránku', url: 'component/v1/authortemplate/menu_1polozka_1/'},
        { title: 'Menu - 1 položka (bez gridu) dlouha', description: 'Vložení položky menu na stránku', url: 'component/v1/authortemplate/menu_1polozka_1_delsi/'},
        { title: 'Menu - 1 položka (bez gridu) dalsi', description: 'Vložení položky menu na stránku', url: 'component/v1/authortemplate/menu_1polozka_1_delsi_1/'},
        { title: 'Menu - 1 položka (bez gridu) verze 2', description: 'Vložení položky menu na stránku', url: 'component/v1/authortemplate/menu_1polozka_2/'}
    ],
//    toolbar1: 'undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent'
//            + ' | hr | nonbreaking | forecolor backcolor ' + ' | fontsizeselect | code | searchreplace template | link image | save'
//            + ' | example vlozitNadpis vlozitOdstavec'
    toolbar1: 'save cancel | undo redo | formatpainter removeformat | bold italic underline strikethrough nonbreaking | alignleft aligncenter alignright alignjustify | link image ',
    toolbar2: 'styleselect fontsizeselect forecolor | bullist numlist outdent indent | template | code',

    imagetools_toolbar: 'editimage | rotateleft rotateright | flipv fliph | imageoptions',

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
    selector: '.paper_template_select',
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
        { title: 'template default', description: 'Grafia web - článek',       url: 'component/v1/papertemplate/default'},
        { title: 'template contact', description: 'Grafia web - kontakty',       url: 'component/v1/papertemplate/contact'},
        { title: 'template test', description: 'paper_test',       url: 'component/v1/papertemplate/test'},
        { title: 'template course', description: 'Grafia web - kurz',       url: 'component/v1/papertemplate/course'},
        { title: 'Vzor - Úvod', description: 'Vzor - Úvod',       url: 'component/v1/static/uvod'},
        { title: 'Test - presentedpaper s šablonou default', description: 'rendered component',       url: 'component/v1/presenteditem?template=default'},
        { title: 'Test - presentedpaper s šablonou contact', description: 'rendered component',       url: 'component/v1/presenteditem?template=contact'},
        { title: 'Test - presentedpaper s šablonou test', description: 'rendered component',       url: 'component/v1/presenteditem?template=test'},
        { title: 'Test - presentedpaper s šablonou course', description: 'rendered component',       url: 'component/v1/presenteditem?template=course'},
        { title: 'Test - namedpaper a1', description: 'rendered component',       url: 'component/v1/nameditem/a1'},
        { title: 'Test - namedpaper a2', description: 'rendered component',       url: 'component/v1/nameditem/a2'},
        { title: 'Test - namedpaper a3', description: 'rendered component',       url: 'component/v1/nameditem/a3'},

       // { title: 'Publikace', description: 'Grafia web - publikace',   url: tinyConfig.paper_templates_path + 'block/'},
    ]

};

//        tinymce.init(headlineConfig);
//        tinymce.init(contentConfig);
//        tinymce.init(perexConfig);
//        tinymce.init(headerFooterConfig);
        tinymce.init(selectPaperTemplateConfig);
