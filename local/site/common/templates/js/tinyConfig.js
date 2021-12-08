var tinyConfig = {
    basePath: '{{basePath}}',
    contentCss: ['{{urlStylesCss}}', '{{urlSemanticCss}}', '{{urlContentTemplatesCss}}'],
    toolbarsLang: '{{toolbarsLang}}'
};
var templates_multipage= [
        { title: 'template article test', description: 'popis',       url: 'web/v1/multipagetemplate/test'},
];

var templates_article = [
       // { title: 'template article test', description: 'popis',       url: 'web/v1/articletemplate/test'},
       // { title: 'Prázdná šablona', description: 'Tato šablona nemá předepsaný styl. Po uložení šablony využijte editační lištu pro formátování.',       url: 'web/v1/articletemplate/empty'},
        { title: 'Šablona pro příspěvek', description: 'Jednoduchá šablona pro vložení textu a obrázku.',       url: 'web/v1/articletemplate/web_post'},
        { title: 'Šablona pro popis knihy', description: 'Popis knihy i autora, obrázky a důležité informace.',       url: 'web/v1/articletemplate/book_description'},
        { title: 'Šablona pro kurz', description: 'Hlavní stránka kurzu. Napište lidem základní informace i recenze od účastníků.',       url: 'web/v1/articletemplate/retraining_course'},
        { title: 'Šablona pro produkt / službu', description: 'Zviditelněte svůj produkt či službu.',       url: 'web/v1/articletemplate/product_page'},

       // { title: 'Vzor - Úvod', description: 'popis',       url: 'web/v1/static/uvod'},
    ];
var templates_paper= [
        { title: 'template paper default', description: 'popis',       url: 'web/v1/papertemplate/default'},
       // { title: 'template paper test', description: 'popis',       url: 'web/v1/papertemplate/test'},
        { title: 'template paper column cards', description: 'popis',       url: 'web/v1/papertemplate/column_cards'},
        { title: 'template paper columns', description: 'popis',       url: 'web/v1/papertemplate/columns'},
        { title: 'template paper divided_rows', description: 'popis',       url: 'web/v1/papertemplate/divided_rows'},
        { title: 'template paper bordered_rows', description: 'popis',       url: 'web/v1/papertemplate/bordered_rows'},
        { title: 'template paper rows', description: 'popis',       url: 'web/v1/papertemplate/rows'},
       // { title: 'Test - namedpaper a1', description: 'rendered component',       url: 'red/v1/nameditem/a1'},
       // { title: 'Test - namedpaper a2', description: 'rendered component',       url: 'red/v1/nameditem/a2'},
       // { title: 'Test - namedpaper a3', description: 'rendered component',       url: 'red/v1/nameditem/a3'}
    ];
var templates_author = [
        { title: 'Kontakt', description: 'Grafia web - kontakt',       url: 'web/v1/authortemplate/kontakt'}, //vztaženo k rootu RS, tam kde je index redakčního s.
        { title: 'Publikace - novinka', description: 'Grafia web - publikace',   url: 'web/v1/authortemplate/eshop_nove'},
        { title: 'Publikace - 2', description: 'Vložení publikací na stránku', url: 'web/v1/authortemplate/eshop_radka'},
        { title: 'Obrázek vlevo a text', description: 'Bez obtékání. Dva sloupce', url: 'web/v1/authortemplate/obrazekVlevo_blok'},
        { title: 'Obrázek vpravo a text', description: 'Bez obtékání. Dva sloupce', url: 'web/v1/authortemplate/obrazekVpravo_blok'},
        { title: 'Ohraničený obsah s odkazem - 1 položka', description: 'Vložení ohraničené položky na stránku. Položka obsahuje odkaz, připojte správnou adresu či odkaz odeberte', url: 'web/v1/authortemplate/menu_1polozka_2'},
        { title: 'Ohraničený obsah s odkazem - 1 položka rozdělená na sloupce', description: 'Vložení ohraničené položky na stránku. Položka obsahuje odkaz, připojte správnou adresu či odkaz odeberte', url: 'web/v1/authortemplate/menu_1polozka'},
        { title: 'Ohraničený obsah s odkazem - 2 položky', description: 'Vložení 2 položek na stránku. Každá položka obsahuje odkaz, připojte správnou adresu či odkaz odeberte', url: 'web/v1/authortemplate/menu_2polozky'},
        { title: 'Ohraničený obsah s odkazem - 3 položky', description: 'Vložení 3 položek menu na stránku. Každá položka obsahuje odkaz, připojte správnou adresu či odkaz odeberte', url: 'web/v1/authortemplate/menu_3polozky'},
        { title: 'Upoutávka na kurz', description: 'popis',       url: 'web/v1/authortemplate/uvod_kurzu'},
        { title: '3 stejně vysoké obrázky  v řádce', description: 'popis',       url: 'web/v1/authortemplate/stretched-images'}, 
        { title: '3 stejně vysoké obsahy v řádce', description: 'popis',       url: 'web/v1/authortemplate/stretched-blocks'},
        { title: 'template two columns divided', description: 'popis',       url: 'web/v1/authortemplate/two_columns_divided'},
        { title: 'template two blocks styled', description: 'popis',       url: 'web/v1/authortemplate/two_blocks_styled'},
        { title: 'template img & text styled', description: 'popis',       url: 'web/v1/authortemplate/img_text_styled'},
        { title: 'template job', description: 'popis',       url: 'web/v1/authortemplate/job'},
        { title: 'Lorem ipsum', description: 'Vložení lorem ipsum', url: 'web/v1/authortemplate/lorem_ipsum'}
    ];