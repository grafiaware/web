tinymce.PluginManager.add('example', function(editor, url) {
    var openDialog = function () {
        return editor.windowManager.open({
            title: 'Šablona menu',
            body: {
                type: 'tabpanel',
                tabs: [
                    { 
                        name: 'sablona',
                        title: 'Výběr šablony',
                        items: [
                            {
                                type: 'selectbox',
                                name: 'vyberSablonu',
                                label: 'Vyberte vzhled položky',
                                items: [
                                    {value: 'menu1',text: 'Možnost 1'},
                                    {value: 'menu2',text: 'Možnost 2'},
                                ],
                            }

                        ]
                    },
                    {
                        name: 'vlozitData',
                        title: 'Vložení dat',
                        items: [
                            {
                                type: 'selectbox',
                                name: 'prirazeniUrl',
                                label: 'Vyberte název položky menu',
                                items: [
                                    {value: 'index.php?list=s01', text: 'Vzdělávání'},
                                    {value: 'index.php?list=s08', text: 'Vydavatelství'},
                                ]
                            },
                            {
                                type: 'input',
                                name: 'header',
                                label: 'Nadpis'
                            },
                            {
                                type: 'input',
                                name: 'text',
                                label: 'Krátký popis'
                            },
                            {
                                type: 'dropzone',
                                name: 'image',
                                label: 'Vložte obrázek',
                            }
                        ],
                    },
                ]
            },
            buttons: [
                {
                  type: 'cancel',
                  text: 'Zavřít'
                },
                {
                  type: 'submit',
                  text: 'Vložit',
                  primary: true
                }
            ],
            onChange: (dialogApi, details) => {
                if (dialogApi.getData().vyberSablonu === 'menu1'){
                var data = dialogApi.getData();
                // Insert content when the window form is submitted 

                api.close();
                }
            },
            onSubmit: function (api) {
                var data = api.getData();
                // Insert content when the window form is submitted 
//                editor.insertContent( 
//                    '<div class="column mceNonEditable"><div class="ui segment"><a href="' + data.prirazeniUrl + '">' +
//                        '<div class="ui two column grid stackable centered"><div class="column"><h2>' + data.header + '</h2><p>' + data.text + '</p></div>'+
//                        '<div class="column middle aligned centered"><p>' + data.image + '</p></div></div></a></div>' +
//                    '</div>'
//                );
                editor.insertContent( 
        '<div class="column mceNonEditable">'+
            '<div class="ui segment">'+
                    '<a href="' + data.prirazeniUrl + '">'+
                        '<div class="ui two column grid stackable centered">'+
                            '<div class="column">'+
                                '<div class="ui header" style="background-color: lightcyan">' + data.header + '</div>'+
                                '<div class="content" style="background-color: lightpink">' + data.header + '</div>'+
                            '</div>'+
                            '<div class="column middle aligned centered">'+
                                '<p>' + data.image + '</p>'+
                            '</div>'+
                        '</div>'+
                    '</a>'+
                '</div>'+
            '</div>'
                );
                api.close();
            }
        });
    };
  
    // Add a button that opens a window
    editor.ui.registry.addButton('example', {
        text: 'Sablona',
        onAction: function () {
          // Open window
          openDialog();
        }
    });

    return {
        getMetadata: function () {
          return  {
            name: "Example plugin",
            url: "http://exampleplugindocsurl.com"
          };
        }
    };
});