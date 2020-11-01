<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site;

use Pes\Database\Handler\DbTypeEnum;

use Application\WebAppFactory;
use Component\View\Flash\FlashComponent;
use Component\View\Status\{
    LoginComponent,
    LogoutComponent,
    UserActionComponent
};
use Component\Renderer\Html\ClassMap\ClassMap;

/**
 * Description of Configuration
 *
 * @author pes2704
 */
class ConfigurationGr2_grafiacz_20200916 {
    ### bootstrap ###
    #
    public static function bootstrap() {
        return [
            'bootstrap_logs_base_path' => '/_www_gr2_logs/',
        ];
    }

    ### kontejner ###
    #
    public static function api() {
        return [
            #################################
            # Sekce konfigurace účtů databáze pro api kontejner
            # Ostatní parametry konfigurace databáze v kontejneru dbUpgrade
            #
            'api.db.everyone.name' => 'gr2_everyone',
            'api.db.everyone.password' => 'gr2_everyone',
            'api.db.authenticated.name' => 'gr2_auth',
            'api.db.authenticated.password' => 'gr2_auth',
            'api.db.administrator.name' => 'gr2_admin',
            'api.db.administrator.password' => 'gr2_admin',
            #
            ###################################
            #
            'api.logs.view.directory' => 'Logs/App/Web',
            'api.logs.view.file' => 'Render.log',
            #
            ###################################
        ];
    }

    public static function app() {
        return [
            #################################
            # Konfigurace adresáře logů
            #
            'app.logs.directory' => 'Logs/App',
            #
            #################################

            #################################
            # Konfigurace session
            #
            WebAppFactory::SESSION_NAME_SERVICE => 'www_gr2_session',
            'app.logs.session.file' => 'Session.log',
            #
            ##################################

            ##################################
            # Konfigurace session
            #
            'app.logs.router.file' => 'Router.log',
            #
            ##################################
        ];
    }

    public static function build() {
        return [
            #################################
            # Sekce konfigurace databáze
            # Konfigurace databáze může být v aplikačním kontejneru nebo různá v jednotlivých middleware kontejnerech.
            #
            ## konfigurována dvě připojení k databázi - jedno pro vývoj a druhé pro běh na produkčním stroji
            #
            # user s právy drop a create database + crud práva + grant option k nové (upgrade) databázi
            # a také select k staré databázi - reálně nejlépe role DBA
            'build.db.user.name' => PES_DEVELOPMENT ? 'gr2_upgrader' : (PES_PRODUCTION ? 'UPGRADE_BUILD_PRODUCTION_USER' : 'xxxxxxxxxxxxxxxxx'),
            'build.db.user.password' => PES_DEVELOPMENT ? 'gr2_upgrader' : (PES_PRODUCTION ? 'UPGRADE_BUILD_PRODUCTION_HOST' : 'xxxxxxxxxxxxxxxxx'),
            #
            ###################################

            ###################################
            # Konfigurace konverze
            #
            'build.config.createusers' =>
                [
                    'everyone_user' => 'gr2_everyone',
                    'everyone_password' => 'gr2_everyone',
                    'authenticated_user' => 'gr2_auth',
                    'authenticated_password' => 'gr2_auth',
                    'administrator_user' => 'gr2_admin',
                    'administrator_password' => 'gr2_admin',
                ],
            #
            ###################################

            ###################################
            # Konfigurace logů konverze
            'build.db.logs.directory' => 'Logs/Build',
            'build.db.logs.file.drop' => 'Drop.log',
            'build.db.logs.file.create' => 'Create.log',
            'build.db.logs.file.convert' => 'Convert.log',
            #
            ###################################

            ###################################
            # Konfigurace hierarchy tabulek
            #
            'build.hierarchy.table' => 'hierarchy',
            'build.hierarchy.view' => 'hierarchy_view',
            #
            ##################################
            #
        ];
    }

    public static function component() {
        return [
            'component.logs.view.directory' => 'Logs/App/Web',
            'component.logs.view.file' => 'Render.log',
            'component.template.'.FlashComponent::class =>      PROJECT_PATH.'public/web/templates/info/flashMessage.php',
            'component.template.'.LoginComponent::class =>      PROJECT_PATH.'public/web/templates/modal/login.php',
            'component.template.'.LogoutComponent::class =>     PROJECT_PATH.'public/web/templates/modal/logout.php',
            'component.template.'.UserActionComponent::class => PROJECT_PATH.'public/web/templates/modal/user_action.php',
        ];
    }

    public static function dbOld() {
        return [
            #################################
            # Sekce konfigurace databáze
            # Konfigurace databáze může být v aplikačním kontejneru nebo různá v jednotlivých middleware kontejnerech.
            # Služby, které vrací objekty jsou v aplikačním kontejneru a v jednotlivých middleware kontejnerech musí být
            # stejná sada služeb, které vracejí hodnoty konfigurace.
            #
            'dbold.db.type' => DbTypeEnum::MySQL,
            'dbold.db.port' => '3306',
            'dbold.db.charset' => 'utf8',
            'dbold.db.collation' => 'utf8_general_ci',

            'dbold.db.connection.host' => PES_DEVELOPMENT ? 'localhost' : (PES_PRODUCTION ? 'OLD_PRODUCTION_NAME' : 'xxxxxxxxxxxxxxxxx'),
            'dbold.db.connection.name' => PES_DEVELOPMENT ? 'grafiacz_20200916' : (PES_PRODUCTION ? 'OLD_PRODUCTION_HOST' : 'xxxxxxxxxxxxxxxxx'),

            'dbold.logs.directory' => 'Logs/DbOld',
            'dbold.logs.db.file' => 'Database.log',
            #
            # Konec sekce konfigurace databáze
            ###################################
        ];
    }

    public static function dbUpgrade() {
        return [
            #####################################
            # Konfigurace databáze
            #
            # konfigurovány dvě databáze pro Hierarchy a Konverze kontejnery
            # - jedna pro vývoj a druhá pro běh na produkčním stroji
            #
            'dbUpgrade.db.type' => DbTypeEnum::MySQL,
            'dbUpgrade.db.port' => '3306',
            'dbUpgrade.db.charset' => 'utf8',
            'dbUpgrade.db.collation' => 'utf8_general_ci',
            'dbUpgrade.db.connection.host' => PES_DEVELOPMENT ? 'localhost' : (PES_PRODUCTION ? 'UPGRADE_PRODUCTION_HOST' : 'xxxx'),
            'dbUpgrade.db.connection.name' => PES_DEVELOPMENT ? 'gr2_upgrade' : (PES_PRODUCTION ? 'UPGRADE_PRODUCTION_NAME' : 'xxxx'),
            #
            #  Konec sekce konfigurace databáze
            ###################################
            # Konfigurace logu databáze
            #
            'dbUpgrade.logs.db.directory' => 'Logs/Hierarchy',
            'dbUpgrade.logs.db.file' => 'Database.log',
            #
            #################################

        ];
    }

    public static function hierarchy() {
        return  [
            #################################
            # Konfigurace databáze
            # Ostatní parametry konfigurace databáze v kontejneru dbUpgrade
            #
            'dbUpgrade.db.user.name' => PES_DEVELOPMENT ? 'gr2_upgrader' : (PES_PRODUCTION ? 'UPGRADE_PRODUCTION_USER_NAME' : 'xxxx'),
            'dbUpgrade.db.user.password' => PES_DEVELOPMENT ? 'gr2_upgrader' : (PES_PRODUCTION ? 'UPGRADE_PRODUCTION_USER_PASSWORD' : 'xxxx'),
            #
            ###################################
            # Konfigurace hierarchy tabulek
            #
            'hierarchy.table' => 'hierarchy',
            'hierarchy.view' => 'hierarchy_view',
            'hierarchy.menu_item_table' => 'menu_item',
            #
            ##################################
            # konfigurace menu
            #
            'hierarchy.new_title' => 'Nová položka',
            #
            #####################################
        ];
    }

    public static function login() {
        return  [
            #################################
            # Sekce konfigurace účtů databáze
            #
            # user s právem select k databázi s tabulkou uživatelských oprávnění
            # MySQL 5.6: délka jména max 16 znaků

            'login.db.account.everyone.name' => 'gr2_login',  // nelze použít jméno uživatele použité pro db upgrade - došlo by k duplicitě jmen v build create
            'login.db.account.everyone.password' => 'gr2_login',

            'login.logs.database.directory' => 'Logs/Login',
            'login.logs.database.file' => 'Database.log',
            #
            ###################################

        ];
    }

    public static function rendererDefaults() {
        return [
            #
            #  menu classmap
            #
            // default hodnoty
            'menu_edit_items' => [
                            'li' => '',
                            'li.onpath' => 'onpath',
                            'li.leaf' => 'leaf',
                            'li.presented' => 'presented',
                            'li.cut' => 'cut',
                            'li.paste' => 'paste',
                            'li div'=>'semafor polozky_menu',
                            'li div i1.published' => 'circle icon green',
                            'li div i1.notpublished' => 'circle icon red ',
                            'li div i2.published' => 'calendar check icon green',
                            'li div i2.notactive' => 'calendar plus icon grey',
                            'li div i2.notactual' => 'calendar minus icon orange',
                            'li div i2.notactivenotactual' => 'calendar times icon red',
                              //check green icon, times red icon //ui mini green left corner label //vertical green line
                            'li a' => 'item editable',   //nema_pravo //edituje_jiny
                            'li i' => '' //dropdown icon
                        ],
            'menu_edit_buttons' => [
                            'div.name' => 'mini ui basic icon buttons editName',
                            'div.menu' => 'mini ui basic icon buttons editMenu',
                            'div button' => 'ui button',
                            'div button.paste' => 'ui button paste',
                            'div button.name' => 'ui button toolsName',
                            'div button.menu' => 'ui button toolsMenu',
                            'div button0 i' => 'large pen icon',
                            'div button1 i' => 'large trash icon',
                            'div button2 i.on' => 'large green toggle on icon',
                            'div button2 i.off' => 'large red toggle off icon',
                            'div button3 i' => 'large add circle icon',
                            'div button4 i' => 'large arrow circle right icon',
                            'div button5 i' => 'large cut icon', //zmena na paste pri vkladani z vyberu (vybrat k presunuti)
                            'div button6 i' => 'large save icon',
                            'div button7 i' => 'large file icon',
                            'div button8 i' => 'large times circle icon'
                        ],
            'block_edit_buttons' => [ //bloky
                            'div' => 'mini ui basic icon buttons',
                            'div button' => 'ui button',
                            'div button1 i' => 'large trash icon',
                            'div button2 i.on' => 'large green toggle on icon',
                            'div button2 i.off' => 'large red toggle off icon',
                            'div button3 i' => 'large add circle icon',
                        ],
            'trash_edit_buttons' => [
                            'div' => 'mini ui basic icon buttons',
                            'div button' => 'ui button',
                            'div button1 i' => 'large icons',
                            'div button1 i1' => 'trash icon',
                            'div button1 i2' => 'corner red exclamation icon',
                            'div button4 i' => 'large cut icon' //zmena na paste pri vkladani z vyberu (vybrat k presunuti)
                        ],
            'paper_template_edit_buttons' => [
                            'div.paperTemplate' => 'ui mini basic icon dropdown button changePaperTemplate', //'mini ui basic icon buttons changePaperTemplate',
                            'div button' => 'ui button',
                            'div button1 i' => 'large clone outline icon'
                        ],
            'paper_template_select' => [
                            'div button' => 'ui button',
                            'div.menu' => 'menu selectTemplate',
                            'div.header' => 'item header',
                            'div.selection' => 'ui compact selection dropdown',
                            'div.scrollmenu' => 'scrollhint menu',
                            'div.item' => 'item',
                            'div.text' => 'default text',
                            'i.dropdown' => 'dropdown icon',
                        ],
            'paper_edit_buttons' => [
                            'div.page' => 'mini ui basic icon buttons editPage',
                            'div button' => 'ui button',
                            'div button1 i.on' => 'large green toggle on icon',
                            'div button1 i.off' => 'large red toggle off icon',
                            'div button2 i' => 'large sort numeric up icon',
                        ],
            'content_edit_buttons' => [
                            'div.date' => 'mini ui basic icon buttons editDate',
                            'div.date2' => 'editDate',
                            'div' => 'contentButtons',
                            'div div.content' => 'mini ui basic icon buttons editContent',
                            'div div' => 'ui button kalendar',
                            'div div button' => 'ui button',
                            'div div button.date' => 'ui button toolsDate',
                            'div div button1 i.on' => 'large green toggle on icon',
                            'div div button1 i.off' => 'large red toggle off icon',
                            'div div button2 i' => 'large calendar alternate icon',
                            'div div button i.group' => 'icons',
                            'div div button i.arrowup' => 'top right corner arrow up icon',
                            'div div button i.arrowdown' => 'bottom right corner arrow down icon',
                            'div div button i.note' => 'large sticky note outline icon',
                            'div div button i.square' => 'large plus square outline icon',
                            'div div button7 i' => 'large trash icon',
                            'div button' => 'ui button',
                            'div button.content' => 'ui button toolsContent',
                            'div button16 i' => 'large calendar outline icon',
                            'div button17 i' => 'large save icon',
                            'div button18 i' => 'large times circle icon',
                            'div div div' => 'edit_kalendar',
                            'div div div div' => 'ui calendar',
                            'div div div div div' => 'ui input',
                        ],
            'deleted_content_buttons' => [
                            'div' => 'contentButtons trash',
                            'div div.content' => 'mini ui basic icon buttons editContent',
                            'div button' => 'ui button',
                            'div button1 i' => 'large sync icon',
                            'div button2 i' => 'large trash icon',
                        ],
                    ];
    }
    public static function renderer() {
        return [
        ###########################
        # menu classmap
        ###########################
            'menu.presmerovani.classmap' => function() {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui tiny text menu',
                            ],
                        'LevelWrap' => [
                            'ul' => 'menu'
                        ],
                        'Item' => array_merge(Configuration::rendererDefaults()['menu_edit_items'],
                            [
                            'li' => 'item',
                            'li.onpath' => 'item onpath',
                            'li a' => '',
                            ]),
                    ]);
            },
            'menu.presmerovani.classmap.editable' => function() {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui tiny text menu edit',
                            ],
                        'LevelWrap' => [
                            'ul' => 'menu'
                        ],
                        'Item' => array_merge(Configuration::rendererDefaults()['menu_edit_items'],
                            [
                            'li' => 'item',
                            'li.onpath' => 'item onpath',
                            'li a' => '',
                            ]),
                        'Buttons' => Configuration::rendererDefaults()['menu_edit_buttons'],
                    ]
                );
            },

            'menu.vodorovne.classmap' => function() {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui mini text menu left floated',
                            ],
                        'LevelWrap' => [

                        ],
                        'Item' => [
                            'li' => 'item',
                            'li.onpath' => 'item onpath',
                            'li a' => 'ui primary button',
                            ]
                    ]);
            },
            'menu.vodorovne.classmap.editable' => function() {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui mini text menu edit left floated',
                            ],
                        'LevelWrap' => [
                            'ul' => 'menu'
                        ],
                        'Item' => array_merge(Configuration::rendererDefaults()['menu_edit_items'],
                            [
                            'li' => 'item',
                            'li.onpath' => 'item onpath',
                            'li a' => 'ui primary button',
                            ]),
//                        'Item' => Configuration::rendererDefaults()['menu_edit_items'],
                        'Buttons' => Configuration::rendererDefaults()['menu_edit_buttons'],
                    ]
                );
            },
            'menu.svisle.classmap' => function() {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui vertical menu'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu',
                            ],
                        'Item' => Configuration::rendererDefaults()['menu_edit_items'],
                    ]);
            },
            'menu.svisle.classmap.editable' => function() {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui vertical menu edit'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu'
                        ],
                        'Item' => Configuration::rendererDefaults()['menu_edit_items'],
                        'Buttons' => Configuration::rendererDefaults()['menu_edit_buttons'],
                    ]);
            },
            'menu.bloky.classmap.editable' => function() { //bloky
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui vertical menu edit'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu'
                        ],
                        'Item' => Configuration::rendererDefaults()['menu_edit_items'],
                        'Buttons' => Configuration::rendererDefaults()['block_edit_buttons'],
                    ]);
            },
            'menu.kos.classmap' => function() { //kos
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui vertical menu'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu'
                        ],
                        'Item' => Configuration::rendererDefaults()['menu_edit_items'],
                        'Buttons' => Configuration::rendererDefaults()['trash_edit_buttons'],
                    ]);
            },
        ###########################
        # paper classmap
        ###########################
            'paper.classmap' => function() {
                return new ClassMap (
                    ['Segment' => [
                        'div'=>'ui segment',
                        ],
                     'Headline' => [
                        'div'=>'paper',
                        'headline'=>'ui header',
                        ],
                     'Perex' => [
                        'perex'=>'',
                        ],
                     'Content' => [
                        'content'=>'',
                        ]
                    ]
                );
            },
            'paper.editable.classmap' => function() {
                return new ClassMap (
                    ['Segment' => [
                        'div'=>'ui segment',
                        'div.paper'=>'paper editable',
                        ],
                     'Headline' => [
                        'section'=>'',
                        'headline'=>'ui header',
                        ],
                     'Perex' => [
                        'section'=>'',
                        'perex'=>'',
                        ],
                     'Content' => [
                        'section'=>'',
                        'section.trash'=>'trash',
                        'div.semafor'=>'semafor',
                        'div.corner'=>'ui right tiny corner blue label',
                        'i1.published' => 'circle icon green',
                        'i1.notpublished' => 'circle icon red ',
                        'i2.published' => 'calendar check icon green',
                        'i2.notactive' => 'calendar plus icon yellow',
                        'i2.notactual' => 'calendar minus icon orange',
                        'i2.notactivenotactual' => 'calendar times icon red',
                         // verze2
                        'i2.actual' => 'calendar check icon green',
                        'i2.past' => 'calendar plus icon grey',
                        'i2.future' => 'calendar minus icon orange',
                        'i2.invalid' => 'calendar times icon red',

                        'i.trash' => 'trash icon purple',
                        'content'=>'',
                        'div.trash_content'=>'trash_content'
                        ],
                     'PaperTemplateButtons' => Configuration::rendererDefaults()['paper_template_edit_buttons'],
                     'PaperTemplateSelect' => Configuration::rendererDefaults()['paper_template_select'],
                     'PaperButtons' => Configuration::rendererDefaults()['paper_edit_buttons'],
                     'ContentButtons' => Configuration::rendererDefaults()['content_edit_buttons'],
                     'TrashButtons' => Configuration::rendererDefaults()['deleted_content_buttons'],
                    ]
                );
            },

            'block.classmap' => function() {
                return new ClassMap (
                    ['Segment' => [
                        'div'=>'paper',
                        ],
                     'Headline' => [
                        'div'=>'',
                        'headline'=>'',
                        ],
                     'Perex' => [
                        'perex'=>'',
                        ],
                    'Content' => [
                        'content'=>''
                        ],
                    ]
                );
            },
            'block.editable.classmap' => function() {
                return new ClassMap (
                    ['Segment' => [
                        'div'=>'paper editable',
                        ],
                     'Headline' => [
                        'section'=>'',
                        'headline'=>'',
                        ],
                    'Perex' => [
                        'section'=>'',
                        'perex'=>'',
                        ],
                    'Content' => [
                        'section'=>'',
                        'div.semafor'=>'semafor',
                        'section.trash'=>'trash',
                        'div.corner'=>'ui right tiny corner blue label',
                        'i1.published' => 'circle icon green',
                        'i1.notpublished' => 'circle icon red ',
                        'i2.published' => 'calendar check icon green',
                        'i2.notactive' => 'calendar plus icon grey',
                        'i2.notactual' => 'calendar minus icon orange',
                        'i2.notactivenotactual' => 'calendar times icon red',
                        'i.trash' => 'trash icon purple',
                        'content'=>'',
                        'div.trash_content'=>'trash_content'
                        ],
                    'PaperTemplateButtons' => Configuration::rendererDefaults()['paper_template_edit_buttons'],
                    'PaperButtons' => Configuration::rendererDefaults()['paper_edit_buttons'],
                    'ContentButtons' => Configuration::rendererDefaults()['content_edit_buttons'],
                    'TrashButtons' => Configuration::rendererDefaults()['deleted_content_buttons'],
                    ]
                );
            },
        ###########################
        # generated classmap
        ###########################
            'generated.languageselect.classmap' => function() {
                return new ClassMap (
                    ['Item' => [
                        'button'=>'ui basic button',
                        'button.presentedlanguage'=>'ui basic button',
                        'img'=>'jazyk-off',
                        'img.presentedlanguage'=>'jazyk-on',
                        ]
                    ]
                );
            },
        ];
    }

    public static function web() {
        return [
            #################################
            # Sekce konfigurace účtů databáze
            # Konfigurace připojení k databázi je v aplikačním kontejneru, je pro celou aplikaci stejná.
            # Služby, které vrací objekty s informacemi pro připojení k databázi jsou také v aplikačním kontejneru a v jednotlivých middleware
            # kontejnerech se volají jako služby delegate kontejneru.
            #
            # Zde je konfigurace údajů uživatele pro připojení k databázi. Ta je pro každý middleware v jeho kontejneru.
            'web.db.account.everyone.name' => 'gr2_everyone',
            'web.db.account.everyone.password' => 'gr2_everyone',
            'web.db.account.authenticated.name' => 'gr2_auth',
            'web.db.account.authenticated.password' => 'gr2_auth',
            'web.db.account.administrator.name' => 'gr2_admin',
            'web.db.account.administrator.password' => 'gr2_admin',
            #
            ###################################
        ];
    }

    public static function rs() {
        return [

            #################################
            # Sekce konfigurace účtů databáze
            # Konfigurace připojení k databázi je v aplikačním kontejneru, je pro celou apliaci stejná.
            # Služby, které vrací objekty s informacemi pro připojení k databázi jsou také v aplikačním kontejneru a v jednotlivých middleware
            # kontejnerech se volají jako služby delegate kontejneru.
            #
            # Zde je konfigurace údajů uživatele pro připojení k databízi. Ta je pro každý middleware v jeho kontejneru.
            'rs.db.account.everyone.name' => 'gr2_everyone',
            'rs.db.account.everyone.password' => 'gr2_everyone',
            'rs.db.account.authenticated.name' => 'gr2_auth',
            'rs.db.account.authenticated.password' => 'gr2_auth',
            'rs.db.account.administrator.name' => 'gr2_admin',
            'rs.db.account.administrator.password' => 'gr2_admin',
            #
            ###################################

        ];
    }

    ### presentation ###
    #
    public static function statusPresentationManager() {
        return [
            'default_lang_code' => 'cs',
            'default_hierarchy_root_component_name' => 's'
        ];
    }

    public static function layoutControler() {
            $webPublicDir = \Middleware\Web\AppContext::getAppPublicDirectory();
            $webSitePublicDir = \Middleware\Web\AppContext::getAppSitePublicDirectory();
            $commonPublicDir = \Middleware\Web\AppContext::getPublicDirectory();
            $tinyPublicDir = \Middleware\Web\AppContext::getTinyPublicDirectory();

        $theme = 'old';

        switch ($theme) {
            case 'old':
                $templatesLayout['layout'] = PROJECT_PATH.'public/web/site/grafia/layout/layout.php';
                $templatesLayout['linksJs'] = PROJECT_PATH.'public/web/site/grafia/layout/head/linkEditableJs.php';
                $templatesLayout['linksCss'] = PROJECT_PATH.'public/web/site/grafia/layout/head/linkEditableCss.php';
                $templatesLayout['tiny_config'] = PROJECT_PATH.'public/web/site/grafia/layout/head/tiny_config.js';
                break;
            case 'new':
                $templatesLayout['layout'] = PROJECT_PATH.'public/web/site/newlayout/layout/layout.php';
                $templatesLayout['linksJs'] = PROJECT_PATH.'public/web/site/newlayout/layout/head/linkEditableJs.php';
                $templatesLayout['linksCss'] = PROJECT_PATH.'public/web/site/newlayout/layout/head/linkEditableCss.php';
                $templatesLayout['tiny_config'] = PROJECT_PATH.'public/web/site/newlayout/layout/head/tiny_config.js';
                break;
            case 'new1':
                $templatesLayout['layout'] = PROJECT_PATH.'public/web/site/newlayout_1/layout/layout.php';
                $templatesLayout['linksJs'] = PROJECT_PATH.'public/web/site/newlayout_1/layout/head/linkEditableJs.php';
                $templatesLayout['linksCss'] = PROJECT_PATH.'public/web/site/newlayout_1/layout/head/linkEditableCss.php';
                $templatesLayout['tiny_config'] = PROJECT_PATH.'public/web/site/newlayout_1/layout/head/tiny_config.js';
                break;
            case 'new2':
                $templatesLayout['layout'] = PROJECT_PATH.'public/web/site/newlayout_2/layout/layout.php';
                $templatesLayout['linksJs'] = PROJECT_PATH.'public/web/site/newlayout_2/layout/head/linkEditableJs.php';
                $templatesLayout['linksCss'] = PROJECT_PATH.'public/web/site/newlayout_2/layout/head/linkEditableCss.php';
                $templatesLayout['tiny_config'] = PROJECT_PATH.'public/web/site/newlayout_2/layout/head/tiny_config.js';
                break;
            case 'new3':
                $templatesLayout['layout'] = PROJECT_PATH.'public/web/site/newlayout_3/layout/layout.php';
                $templatesLayout['linksJs'] = PROJECT_PATH.'public/web/site/newlayout_3/layout/head/linkEditableJs.php';
                $templatesLayout['linksCss'] = PROJECT_PATH.'public/web/site/newlayout_3/layout/head/linkEditableCss.php';
                $templatesLayout['tiny_config'] = PROJECT_PATH.'public/web/site/newlayout_3/layout/head/tiny_config.js';
                break;
            case 'oa':
                $templatesLayout['layout'] = PROJECT_PATH.'public/web/site/oa/layout/layout.php';
                $templatesLayout['linksJs'] = PROJECT_PATH.'public/web/site/oa/layout/head/linkEditableJs.php';
                $templatesLayout['linksCss'] = PROJECT_PATH.'public/web/site/oa/layout/head/linkEditableCss.php';
                $templatesLayout['tiny_config'] = PROJECT_PATH.'public/web/site/oa/layout/head/tiny_config.js';
                break;
            default:
                $templatesLayout['layout'] = PROJECT_PATH.'public/web/site/grafia/layout/layout.php';
                $templatesLayout['linksJs'] = PROJECT_PATH.'public/web/site/grafia/layout/head/linkEditableJs.php';
                $templatesLayout['linksCss'] = PROJECT_PATH.'public/web/site/grafia/layout/head/linkEditableCss.php';
                $templatesLayout['tiny_config'] = PROJECT_PATH.'public/web/site/grafia/layout/head/tiny_config.js';
                break;
        }


        return [
           'templates.poznamky' => $webPublicDir.'templates/info/poznamky.php',
           'templates.loaderElement' => $webPublicDir.'templates/component-load/loaderElement.php',

           // Language packages tinyMce používají krátké i dlouhé kódy, kód odpovídá jménu souboru např cs.js nebo en_US.js - proto mapování
            // pozn. - popisky šablon pro tiny jsou jen česky (TinyInit.js)
            'tinyLanguage' => [
                'cs' => 'cs',
                'de' => 'de',
                'en' => 'en_US'
            ],
            // title
            'title' => \Middleware\Web\AppContext::getWebTitle(),
            // folders
            'webPublicDir' => $webPublicDir,
            'webSitePublicDir' =>$webSitePublicDir,
            // layout folder
            'layout' => $templatesLayout['layout'],
            'tiny_config' =>    $templatesLayout['tiny_config'],
            // links do head
            'linksJs' =>    $templatesLayout['linksJs'],
            'linksCss' =>    $templatesLayout['linksCss'],
            // js links
           'urlTinyMCE' => $commonPublicDir.'tinymce5_3_1\js\tinymce\tinymce.min.js',
            'urlJqueryTinyMCE' => $commonPublicDir.'tinymce5_3_1\js\tinymce\jquery.tinymce.min.js',
//            'urlTinyMCE' => $commonPublicDir.'tinymce5_4_0\js\tinymce\tinymce.min.js',
//            'urlJqueryTinyMCE' => $commonPublicDir.'tinymce5_4_0\js\tinymce\jquery.tinymce.min.js',

//    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
//    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
//    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/jquery.tinymce.min.js" referrerpolicy="origin"></script>
            'urlTinyInit' => $webPublicDir.'js/TinyInit.js',
            'editScript' => $webPublicDir . 'js/edit.js',
            'kalendarScript' => $webPublicDir . 'js/kalendar.js',
            // css links
            'urlStylesCss' => $webPublicDir."styles/old/styles.css",
            'urlSemanticCss' => $webPublicDir."semantic/dist/semantic.min.css",
            'urlContentTemplatesCss' => $webPublicDir."templates/author/template.css",
            //
            'paperTemplatesUri' =>  $webPublicDir."templates/paper/",  // URI pro Template controler
            'authorTemplatesPath' => $webPublicDir."templates/author/",

        ];
    }

    public static function transformator() {
        return [
            'filesDirectory' => '/_www_gr2_files/',  // relativní cesta vzhledem k DOCUMENT_ROOT (htdocs)
        ];
    }
}
