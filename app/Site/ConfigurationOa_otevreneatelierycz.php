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
use \Pes\View\Renderer\ClassMap\ClassMap;

/**
 * Description of Configuration
 *
 * @author pes2704
 */
class ConfigurationOa_otevreneatelierycz {

    // site
    const RED_SITE_PATH = 'oa/';

    // local
    const RED_TEMPLATES_COMMON = 'local/site/common/templates/';
    const RED_TEMPLATES_SITE = 'local/site/'.self::RED_SITE_PATH.'templates/';
    // public
    const RED_ASSETS = 'public/assets/';
    const RED_LINKS_COMMON = 'public/site/common/';
    const RED_LINKS_SITE = 'public/site/'.self::RED_SITE_PATH;
    // files
    const RED_FILES = '_www_oa_files/';

    ### bootstrap ###
    #
    public static function bootstrap() {
        return [
            'bootstrap_logs_base_path' => '/_www_oa_logs/',
        ];
    }

    ### kontejner ###
    #

    /**
     * Konfigurace kontejneru - vrací parametry pro ApiContainerConfigurator
     * @return array
     */
    public static function api() {
        return [
            #################################
            # Sekce konfigurace účtů databáze pro api kontejner
            # Ostatní parametry konfigurace databáze v kontejneru dbUpgrade
            #
            'api.db.everyone.name' => 'oa_everyone',
            'api.db.everyone.password' => 'oa_everyone',
            'api.db.authenticated.name' => 'oa_auth',
            'api.db.authenticated.password' => 'oa_auth',
            'api.db.administrator.name' => 'oa_admin',
            'api.db.administrator.password' => 'oa_admin',
            #
            ###################################
            #
            'api.logs.view.directory' => 'Logs/App/Web',
            'api.logs.view.file' => 'Render.log',
            #
            ###################################
        ];
    }

    /**
     * Konfigurace kontejneru - vrací parametry pro AppContainerConfigurator
     * @return array
     */
    public static function app() {
        return [
            #################################
            # Konfigurace adresáře logů
            #
            'app.logs.directory' => 'Logs/App',
            #
            #################################

            #################################
            # Konfigurace session loggeru
            #
            WebAppFactory::SESSION_NAME_SERVICE => 'www_oa_session',
            'app.logs.session.file' => 'Session.log',
            #
            ##################################

            ##################################
            # Konfigurace router loggeru
            #
            'app.logs.router.file' => 'Router.log',
            #
            ##################################

            ##################################
            # Konfigurace selector loggeru
            #
            'app.logs.selector.file' => 'Selector.log',
            #
            ##################################
        ];
    }

    /**
     * Konfigurace kontejneru - vrací parametry pro BuildContainerConfigurator
     * @return array
     */
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
            'build.db.user.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'oa_upgrader' : 'UPGRADE_BUILD_PRODUCTION_USER',
            'build.db.user.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'oa_upgrader' : 'UPGRADE_BUILD_PRODUCTION_HOST',
            #
            ###################################

            ###################################
            # Konfigurace create users - ostatní parametry přidá kontejner
            #
            'build.config.users.everyone' =>
                [
                    'everyone_user' => 'oa_everyone',
                    'everyone_password' => 'oa_everyone',
                ],
            'build.config.users.granted' =>
                [
                    'authenticated_user' => 'oa_auth',
                    'authenticated_password' => 'oa_auth',
                    'administrator_user' => 'oa_admin',
                    'administrator_password' => 'oa_admin',
                ],
            #
            ###################################

            ###################################
            # Konfigurace make - ostatní parametry přidá kontejner
            # pole build.config.make.roots: [type, list, title]
            'build.config.make.roots' => [
                ['root', 'root', 'ROOT'],
                ['trash', 'trash', 'Trash'],
                ['paper', 'blocks', 'Blocks'],
//                ['paper', 'menu_vertical', 'Menu'],      // !! menu menu_vertical je s titulní stranou  -> ve staré db je stránka list=menu_vertical a má titulek
                ['paper', 'menu_horizontal', 'Menu'],
                ['paper', 'menu_redirect', 'Menu'],
            ],
            'build.config.make.roots' => [
                'root',
                'trash',
                'blocks',
                'menu_vertical',
                'menu_horizontal',
                'menu_redirect',
            ],
            'build.config.convert.copy' =>
                [
                    'source' => 'stranky',
                    'target' => 'stranky'
                ],
            'build.config.convert.updatestranky' => [
                ['a0', 's00', -1],        // !! menu menu_vertical je s titulní stranou list=a0 - existující stránku list=a0 ve staré db změním na list='s00', poradi=-1
            ],
            'build.config.convert.home' => [
                'home', 's00',        // titulní stránka s00 (změněná a0) je home page
            ],
            'build.config.convert.repairs' => [
                // smazání chybné stránky v grafia databázích s list='s_01' - chybná syntax list způdobí chyby při vyztváření adjlist - původní stránka nemá žádný obsah
                "DELETE FROM stranky WHERE list = 's_01'",
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

    /**
     * Konfigurace kontejneru - vrací parametry pro ComponentContainerConfigurator
     * @return array
     */
    public static function component() {
        return [
            'component.logs.view.directory' => 'Logs/App/Web',
            'component.logs.view.file' => 'Render.log',
            'component.templatePath.paper' => self::RED_TEMPLATES_COMMON.'paper/',
            'component.templatePath.article' => self::RED_TEMPLATES_COMMON.'article/',
            'component.template.'.FlashComponent::class => self::RED_TEMPLATES_COMMON.'layout/info/flashMessage.php',
            'component.template.'.LoginComponent::class => self::RED_TEMPLATES_COMMON.'layout/modal/login.php',
            'component.template.'.LogoutComponent::class => self::RED_TEMPLATES_COMMON.'layout/modal/logout.php',
            'component.template.'.UserActionComponent::class => self::RED_TEMPLATES_COMMON.'layout/modal/user_action.php',
        ];
    }

    /**
     * Konfigurace kontejneru - vrací parametry pro DbOldContainerConfigurator
     * @return array
     */
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

            'dbold.db.connection.host' => PES_RUNNING_ON_PRODUCTION_HOST ? 'OLD_PRODUCTION_NAME' : 'localhost',
            'dbold.db.connection.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'OLD_PRODUCTION_HOST' : 'wwwgrafia',

            'dbold.logs.directory' => 'Logs/DbOld',
            'dbold.logs.db.file' => 'Database.log',
            #
            # Konec sekce konfigurace databáze
            ###################################
        ];
    }

    /**
     * Konfigurace kontejneru - vrací parametry pro DbUpgradeContainerConfigurator
     * @return array
     */
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
            'dbUpgrade.db.connection.host' => PES_RUNNING_ON_PRODUCTION_HOST ? 'UPGRADE_PRODUCTION_HOST' : 'localhost',
            'dbUpgrade.db.connection.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'UPGRADE_PRODUCTION_NAME' : 'oa_upgrade',
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

    /**
     * Konfigurace kontejneru - vrací parametry pro HierarchyContainerConfigurator
     * @return array
     */
    public static function hierarchy() {
        return  [
            #################################
            # Konfigurace databáze
            # Ostatní parametry konfigurace databáze v kontejneru dbUpgrade
            #
            'dbUpgrade.db.user.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'UPGRADE_PRODUCTION_USER_NAME' : 'oa_upgrader',
            'dbUpgrade.db.user.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'UPGRADE_PRODUCTION_USER_PASSWORD' : 'oa_upgrader',
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

    /**
     * Konfigurace kontejneru - vrací parametry pro LoginContainerConfigurator
     * @return array
     */
    public static function login() {
        return  [
            #################################
            # Sekce konfigurace účtů databáze
            #
            # user s právem select k databázi s tabulkou uživatelských oprávnění
            # MySQL 5.6: délka jména max 16 znaků

            'login.db.account.everyone.name' => 'oa_login',  // nelze použít jméno uživatele použité pro db upgrade - došlo by k duplicitě jmen v build create
            'login.db.account.everyone.password' => 'oa_login',

            'login.logs.database.directory' => 'Logs/Login',
            'login.logs.database.file' => 'Database.log',
            #
            ###################################

        ];
    }

    /**
     * Pomocná metoda pro konfiguraci renderer kontejneru - vrací default hodnoty pro metodu Configuration::renderer()
     * @return array
     */
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
                            'li a' => 'item editable',   //nema_pravo //edituje_jiny
                            'li.isnotleaf icon' => '', //dropdown icon
                            'semafor'=> 'semafor',
                            'semafor.published' => 'circle icon green',
                            'semafor.notpublished' => 'circle icon inverted red ',
                            'semafor.trashed' => 'circle icon inverted purple',
//                            'li div i2.published' => 'calendar check icon green',
//                            'li div i2.notactive' => 'calendar plus icon grey',
//                            'li div i2.notactual' => 'calendar minus icon orange',
//                            'li div i2.notactivenotactual' => 'calendar times icon red',
                        ],
            'menu_edit_buttons' => [
//                            'div.name' => 'mini ui basic icon buttons editName',
//                            'div button.name' => 'ui button toolsName',
//                            'div button.menu' => 'ui button toolsMenu',
//                            'div button0 i' => 'large pen icon',
//                            'div button6 i' => 'large save icon',
//                            'div button7 i' => 'large file icon',
//                            'div button8 i' => 'large times circle icon',
                            'div.buttons' => 'mini ui basic icon buttons',
                            'button' => 'ui button',
                            'button.paste' => 'ui button paste',
                            'button.cut' => 'large cut icon',
                            'button.cutted' => 'large red cut icon',
                            'button.notpublish' => 'large green toggle on icon',
                            'button.publish' => 'large red toggle off icon',
                            'button.addsiblings' => 'large add circle icon',
                            'button.addchildren' => 'large arrow circle right icon',
                            'button.movetotrash' => 'large trash icon',
                        ],
            'block_edit_buttons' => [ //bloky
                            'div.buttons' => 'mini ui basic icon buttons',
                            'button' => 'ui button',
                            'button.notpublish' => 'large green toggle on icon',
                            'button.publish' => 'large red toggle off icon',
                            'button.addsiblings' => 'large add circle icon',
                            'button.movetotrash' => 'large trash icon',
                        ],
            'trash_edit_buttons' => [
                            'div.buttons' => 'mini ui basic icon buttons',
                            'button' => 'ui button',
                            'button.icons' => 'large icons',
                            'button.delete' => 'trash icon',
                            'button.exclamation' => 'corner red exclamation icon',
                            'button.cut' => 'large cut icon',
                            'button.cutted' => 'large red cut icon',
                        ],
            'paper_template_edit_buttons' => [
                            'div.paperTemplate' => 'ui mini basic icon dropdown button changePaperTemplate',
                            'button' => 'ui button',
                            'button.templateSelect' => 'large clone outline icon'
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
                            'div.buttonsPage' => 'mini ui basic icon buttons editPage',
                            'button' => 'ui button',
                            'button.arrange' => 'large sort numeric up icon',
//                            'div button1 i.on' => 'large green toggle on icon',
//                            'div button1 i.off' => 'large red toggle off icon',
                        ],
            'content_edit_buttons' => [
                            'div.wrapContent' => 'contentButtons',
                            'div.wrapDate' => 'editDate',
                            'div.buttonsDate' => 'mini ui basic icon buttons editDate',
                            'div.buttonsContent' => 'mini ui basic icon buttons editContent',
                            'button' => 'ui button',
                            'button.date' => 'ui button toolsDate',
                            'button.content' => 'ui button toolsContent',
                            'button.notpublish' => 'large green toggle on icon',
                            'button.publish' => 'large red toggle off icon',
                            'button.changedate' => 'large calendar alternate icon',
                            'button.icons' => 'icons',
                            'button.arrowup' => 'top right corner arrow up icon',
                            'button.arrowdown' => 'bottom right corner arrow down icon',
                            'button.movecontent' => 'large sticky note outline icon',
                            'button.addcontent' => 'large plus square outline icon',
                            'button.movetotrash' => 'large trash icon',
                            'button.permanently' => 'large calendar outline icon',
                            'button.save' => 'large save icon',
                            'button.cancel' => 'large times circle icon',
                            'div.wrapKalendar' => 'edit_kalendar',
                            'div.calendar' => 'ui calendar',
                            'div.input' => 'ui input',
//                            'button.kalendar' => 'ui button kalendar',
                        ],
            'deleted_content_buttons' => [
                            'div.wrapTrash' => 'contentButtons trash',
                            'div.buttonsContent' => 'mini ui basic icon buttons editContent',
                            'button' => 'ui button',
                            'button.restore' => 'large sync icon',
                            'button.delete' => 'large trash icon',
                        ],
                    ];
    }

    /**
     * Konfigurace kontejneru - vrací parametry pro RendererContainerConfigurator
     * @return array
     */
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
                        'headline'=>'ui header borderDance',
                        ],
                     'Perex' => [
                        'section'=>'',
                        'perex'=>'borderDance',
                        ],
                     'Content' => [
                        'section'=>'',
                        'section.trash'=>'trash',
                        'div.semafor'=>'semafor',
                        'div.corner'=>'ui right tiny corner blue label cornerWithTools',
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
                        'content'=>'borderDance',
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
                        'headline'=>'borderDance',
                        ],
                    'Perex' => [
                        'section'=>'',
                        'perex'=>'borderDance',
                        ],
                    'Content' => [
                        'section'=>'',
                        'div.semafor'=>'semafor',
                        'section.trash'=>'trash',
                        'div.corner'=>'ui right tiny corner blue label cornerWithTools',
                        'i1.published' => 'circle icon green',
                        'i1.notpublished' => 'circle icon red ',
                        'i2.published' => 'calendar check icon green',
                        'i2.notactive' => 'calendar plus icon grey',
                        'i2.notactual' => 'calendar minus icon orange',
                        'i2.notactivenotactual' => 'calendar times icon red',
                        'i.trash' => 'trash icon purple',
                        'content'=>'borderDance',
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

    /**
     * Konfigurace kontejneru - vrací parametry pro WebContainerConfigurator
     * @return array
     */
    public static function web() {
        return [
            #################################
            # Sekce konfigurace účtů databáze
            # Konfigurace připojení k databázi je v aplikačním kontejneru, je pro celou aplikaci stejná.
            # Služby, které vrací objekty s informacemi pro připojení k databázi jsou také v aplikačním kontejneru a v jednotlivých middleware
            # kontejnerech se volají jako služby delegate kontejneru.
            #
            # Zde je konfigurace údajů uživatele pro připojení k databázi. Ta je pro každý middleware v jeho kontejneru.
            'web.db.account.everyone.name' => 'oa_everyone',
            'web.db.account.everyone.password' => 'oa_everyone',
            'web.db.account.authenticated.name' => 'oa_auth',
            'web.db.account.authenticated.password' => 'oa_auth',
            'web.db.account.administrator.name' => 'oa_admin',
            'web.db.account.administrator.password' => 'oa_admin',
            #
            ###################################
        ];
    }

    /**
     * Konfigurace kontejneru - vrací parametry pro RsContainerConfigurator
     * @return array
     */
    public static function rs() {
        return [

            #################################
            # Sekce konfigurace účtů databáze
            # Konfigurace připojení k databázi je v aplikačním kontejneru, je pro celou apliaci stejná.
            # Služby, které vrací objekty s informacemi pro připojení k databázi jsou také v aplikačním kontejneru a v jednotlivých middleware
            # kontejnerech se volají jako služby delegate kontejneru.
            #
            # Zde je konfigurace údajů uživatele pro připojení k databízi. Ta je pro každý middleware v jeho kontejneru.
            'rs.db.account.everyone.name' => 'oa_everyone',
            'rs.db.account.everyone.password' => 'oa_everyone',
            'rs.db.account.authenticated.name' => 'oa_auth',
            'rs.db.account.authenticated.password' => 'oa_auth',
            'rs.db.account.administrator.name' => 'oa_admin',
            'rs.db.account.administrator.password' => 'oa_admin',
            #
            ###################################

        ];
    }

    ### presentation ###
    #

    /**
     * Konfigurace prezentačního objektu - vrací parametry pro statusPresentationManager
     * @return array
     */
    public static function statusPresentationManager() {
        return [
            'default_lang_code' => 'cs',
        ];
    }

    /**
     * Konfigurace prezentačního objektu - vrací parametry pro layoutController
     * @return array
     */
    public static function layoutController() {
        return [
           // Language packages tinyMce používají krátké i dlouhé kódy, kód odpovídá jménu souboru např cs.js nebo en_US.js - proto mapování
            // pozn. - popisky šablon pro tiny jsou jen česky (TinyInit.js)
            'tinyLanguage' => [
                'cs' => 'cs',
                'de' => 'de',
                'en' => 'en_US'
            ],

            // title
            'title' => "Grafia, s.r.o.",

            // folders
            'linksCommon' => self::RED_LINKS_COMMON,
            'linksSite' => self::RED_LINKS_SITE,

            // local templates paths
            'layout' => self::RED_TEMPLATES_SITE.'layout/layout.php',
            'tiny_config' => self::RED_TEMPLATES_SITE.'js/tiny_config.js',
            'linksEditorJs' => self::RED_TEMPLATES_COMMON.'layout/links/linkEditorJs.php',
            'linkEditorCss' => self::RED_TEMPLATES_COMMON.'layout/links/linkEditorCss.php',

            // linksEditorJs links
            'urlTinyMCE' => self::RED_ASSETS.'tinymce5_3_1\js\tinymce\tinymce.min.js',
            'urlJqueryTinyMCE' => self::RED_ASSETS.'tinymce5_3_1\js\tinymce\jquery.tinymce.min.js',
//            'urlTinyMCE' => self::RED_ASSETS.'tinymce5_4_0\js\tinymce\tinymce.min.js',
//            'urlJqueryTinyMCE' => self::RED_ASSETS.'tinymce5_4_0\js\tinymce\jquery.tinymce.min.js',
//    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
//    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
//    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/jquery.tinymce.min.js" referrerpolicy="origin"></script>
            'urlTinyInit' => self::RED_LINKS_COMMON.'js/TinyInit.js',
            'urlEditScript' => self::RED_LINKS_COMMON . 'js/editDelegated.js',

            // linkEditorCss links
            'urlStylesCss' => self::RED_LINKS_COMMON."css/old/styles.css",
            'urlSemanticCss' => self::RED_LINKS_SITE."semantic-ui/semantic.min.css",
            'urlContentTemplatesCss' => self::RED_LINKS_COMMON."css/templates.css",   // KŠ ?????
            //
            'paperTemplatesUri' =>  self::RED_LINKS_SITE."templates/paper/",  // URI pro Template Controller
            'authorTemplatesPath' => self::RED_LINKS_COMMON."templates/author/",

        ];
    }

    /**
     * Konfigurace prezentace - vrací parametry pro pageController
     *
     * Home stránka může být definována jménem komponenty nebo jménem statické stránky nebo identifikátorem uid položky menu (položky hierarchie).
     *
     * @return array
     */
    public static function pageController() {

        return [
               'home_page' => ['component', 'home'],
//               'home_page' => ['static', 'body-pro-zdravi'],
//               'home_page' => ['item', '5fad34398df10'],  // přednášky - pro test

               'templates.poznamky' => self::RED_TEMPLATES_COMMON.'layout/info/poznamky.php',
               'templates.loaderElement' => self::RED_TEMPLATES_COMMON.'layout/component-load/loaderElement.php',
               'templates.loaderElementEditable' => self::RED_TEMPLATES_COMMON.'layout/component-load/loaderElementEditable.php',
            ];
    }

    /**
     * Konfigurace prezentace - vrací parametry pro ComponentController
     * @return array
     */
    public static function componentController() {

        return [
               'static' => self::RED_TEMPLATES_SITE.'static/',

            ];
    }

    /**
     * Konfigurace prezentace - vrací parametry pro templateController
     * @return array
     */
    public static function templateController() {

        return [
               'templates.authorFolder' => self::RED_TEMPLATES_COMMON.'author/',
               'templates.paperFolder' => self::RED_TEMPLATES_COMMON.'paper/',
               'templates.paperContentFolder' => self::RED_TEMPLATES_COMMON.'paper-content/',

            ];
    }

    /**
     * Konfigurace upload files - vrací parametry pro FilesUploadController
     * @return array
     */
    public static function filesUploadController() {

        return [
            'filesDirectory' => PES_RUNNING_ON_PRODUCTION_HOST ? self::RED_FILES : self::RED_FILES,

            ];
    }

    /**
     * Konfigurace prezentačního objektu - vrací parametry pro languageSelectRenderer
     * @return array
     */
    public static function languageSelectRenderer() {
        return [
            'assets' => self::RED_ASSETS.'flags-mini/'
        ];
    }

    /**
     * Konfigurace prezentačního objektu - vrací parametry pro transformator
     * @return array
     */
    public static function transformator() {
        return [
            // relativní cesta vzhledem k DOCUMENT_ROOT (htdocs) -začíná /
            'filesDirectory' => PES_RUNNING_ON_PRODUCTION_HOST ? self::RED_FILES : '/'.self::RED_FILES,
            'public' => self::RED_LINKS_COMMON,
        ];
    }
}
