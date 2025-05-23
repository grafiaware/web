<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site;

use Pes\Database\Handler\DbTypeEnum;

use Application\WebAppFactory;
use Web\Component\View\Flash\FlashComponent;
use Red\Component\View\Manage\{
    LoginComponent,
    LogoutComponent,
    EditorActionComponent
};
use \Pes\View\Renderer\ClassMap\ClassMap;

/**
 * Description of Configuration
 *
 * @author pes2704
 */
class ConfigurationTz_newdb {

    // site
    const WEB_SITE_PATH = 'tydenzdravi/';

    // local
    const WEB_TEMPLATES_COMMON = 'local/site/common/templates/';
    const WEB_TEMPLATES_SITE = 'local/site/'.self::WEB_SITE_PATH.'templates/';
    // public
    const WEB_ASSETS = 'public/assets/';
    const WEB_LINKS_COMMON = 'public/site/common/';
    const WEB_LINKS_SITE = 'public/site/'.self::WEB_SITE_PATH;
    // files
    const WEB_FILES = '_www_tz_files/';

    ### bootstrap ###
    #
    public static function bootstrap() {
        return [
            'bootstrap_logs_base_path' => "/_www_tz_logs/",
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
            'red.db.everyone.name' => 'tydenzdravieu001',
            'red.db.everyone.password' => 'tz_upgrader',
            'red.db.authenticated.name' => 'tydenzdravieu001',
            'red.db.authenticated.password' => 'tz_upgrader',
            'red.db.administrator.name' => 'tydenzdravieu001',
            'red.db.administrator.password' => 'tz_upgrader',
            #
            ###################################
            #
            'red.logs.view.directory' => 'Logs/Web',
            'red.logs.view.file' => 'Render.log',
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
            WebAppFactory::SESSION_NAME_SERVICE => 'www_tz_session',
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
            'build.db.user.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'tydenzdravieu001' : 'tydenzdravieu001',
            'build.db.user.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'tz_upgrader' : 'tz_upgrader',
            #
            ###################################

            ###################################
            # Konfigurace create users - ostatní parametry přidá kontejner
            #
            'build.config.users.everyone' =>
                [
                    'everyone_user' => 'tydenzdravieu001',
                    'everyone_password' => 'tz_upgrader',
                ],
            'build.config.users.granted' =>
                [
                    'authenticated_user' => 'tydenzdravieu001',
                    'authenticated_password' => 'tz_upgrader',
                    'administrator_user' => 'tydenzdravieu001',
                    'administrator_password' => 'tz_upgrader',
                ],
            #
            ###################################

            ###################################
            # Konfigurace make - ostatní parametry přidá kontejner
            # pole build.config.make.roots: [type, list, title]
            'build.config.make.items' => [
                ['root', 'root', 'ROOT'],
                ['trash', 'trash', 'Trash'],
                ['paper', 'blocks', 'Blocks'],
                ['paper', 'menu_vertical', 'Menu'],
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
            'build.config.convert.copy' => [],
            'build.config.convert.updatestranky' => [],
            'build.config.convert.home' => [],
            'build.config.convert.repairs' => [],
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
            'component.logs.directory' => 'Logs/Web',
            'component.logs.render' => 'Render.log',
            'component.templatepath.paper' => self::WEB_TEMPLATES_COMMON.'paper/',
            'component.templatepath.article' => self::WEB_TEMPLATES_COMMON.'article/',
            'component.template.flash' => self::WEB_TEMPLATES_COMMON.'layout/info/flashMessage.php',
            'component.template.login' => self::WEB_TEMPLATES_COMMON.'layout/status/login.php',
            'component.template.logout' => self::WEB_TEMPLATES_COMMON.'layout/status/logout.php',
            'component.template.useraction' => self::WEB_TEMPLATES_COMMON.'layout/status/user_action.php',
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
            'auth.db.type' => DbTypeEnum::MySQL,
            'auth.db.port' => '3306',
            'auth.db.charset' => 'utf8',
            'auth.db.collation' => 'utf8_general_ci',

            'auth.db.connection.host' => PES_RUNNING_ON_PRODUCTION_HOST ? '127.0.0.1' : '127.0.0.1' ,   // 'localhost' zbytečně překládá jméno,
            'auth.db.connection.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'tydenzdravieu01' : 'tydenzdravieu',

            'auth.logs.directory' => 'Logs/Auth',
            'auth.logs.db.file' => 'Database.log',
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
            'red.db.type' => DbTypeEnum::MySQL,
            'red.db.port' => '3306',
            'red.db.charset' => 'utf8',
            'red.db.collation' => 'utf8_general_ci',
            'red.db.connection.host' => PES_RUNNING_ON_PRODUCTION_HOST ? '127.0.0.1' : '127.0.0.1' ,   // 'localhost' zbytečně překládá jméno,
            'red.db.connection.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'tydenzdravieu' : 'tydenzdravieu',
            #
            #  Konec sekce konfigurace databáze
            ###################################
            # Konfigurace logu databáze
            #
            'red.logs.db.directory' => 'Logs/Red',
            'red.logs.db.file' => 'Database.log',
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
            'red.db.user.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'tydenzdravieu001' : 'tydenzdravieu001',
            'red.db.user.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'tz_upgrader' : 'tz_upgrader',
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

            'auth.db.account.everyone.name' => 'tydenzdravieu002',  // nelze použít jméno uživatele použité pro db upgrade - došlo by k duplicitě jmen v build create
            'auth.db.account.everyone.password' => 'tz_opravneni',

            'auth.logs.database.directory' => 'Logs/Auth',
            'auth.logs.database.file' => 'Database.log',
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
                            'li.isnotleaf icon' => 'chevron-icon', //dropdown icon
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
//                            'div.name' => 'small ui basic icon buttons editName',
//                            'div button.name' => 'ui button toolsName',
//                            'div button.menu' => 'ui button toolsMenu',
//                            'button0 i' => 'large pen icon',
//                            'button6 i' => 'large save icon',
//                            'button7 i' => 'large file icon',
//                            'button8 i' => 'large times circle icon'
                            'div.buttons' => 'small ui basic icon buttons',
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
            'block_edit_buttons' => [
                            'div.buttons' => 'small ui basic icon buttons',
                            'button' => 'ui button',
                            'button.notpublish' => 'large green toggle on icon',
                            'button.publish' => 'large red toggle off icon',
                            'button.addsiblings' => 'large add circle icon',
                            'button.movetotrash' => 'large trash icon',
                        ],
            'trash_edit_buttons' => [
                            'div.buttons' => 'small ui basic icon buttons',
                            'button' => 'ui button',
                            'button.icons' => 'large icons',
                            'button.delete' => 'trash icon',
                            'button.exclamation' => 'corner red exclamation icon',
                            'button.cut' => 'large cut icon',
                            'button.cutted' => 'large red cut icon',
                        ],
            'paper_template_edit_buttons' => [
                            'div.paperTemplate' => 'ui small basic icon dropdown button changePaperTemplate',
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
                            'div.buttonsPage' => 'small ui basic icon buttons editPage',
                            'button' => 'ui button',
                            'button.arrange' => 'large sort numeric up icon',
//                            'div button1 i.on' => 'large green toggle on icon',
//                            'div button1 i.off' => 'large red toggle off icon',
                        ],
            'content_edit_buttons' => [
                            'div.wrapContent' => 'contentButtons',
                            'div.wrapDate' => 'editDate',
                            'div.buttonsDate' => 'small ui basic icon buttons editDate',
                            'div.buttonsContent' => 'small ui basic icon buttons editContent',
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
                            'div.buttonsContent' => 'small ui basic icon buttons editContent',
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
            'menuRedirect.classmap' => function() {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui tiny text menu',
                            ],
                        'LevelWrap' => [
                            'ul' => 'menu'
                        ],
                        'Item' => array_merge(ConfigurationCache::rendererDefaults()['menu_edit_items'],
                            [
                            'li' => 'item',
                            'li.onpath' => 'item onpath',
                            'li a' => '',
                            ]),
                    ]);
            },
            'menuRedirect.classmap.editable' => function() {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui tiny text menu edit',
                            ],
                        'LevelWrap' => [
                            'ul' => 'menu'
                        ],
                        'Item' => array_merge(ConfigurationCache::rendererDefaults()['menu_edit_items'],
                            [
                            'li' => 'item',
                            'li.onpath' => 'item onpath',
                            'li a' => '',
                            ]),
                        'Buttons' => ConfigurationCache::rendererDefaults()['menu_edit_buttons'],
                    ]
                );
            },

            'menuHorizontal.classmap' => function() {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui small text menu left floated',
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
            'menuHorizontal.classmap.editable' => function() {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui small text menu edit left floated',
                            ],
                        'LevelWrap' => [
                            'ul' => 'menu'
                        ],
                        'Item' => array_merge(ConfigurationCache::rendererDefaults()['menu_edit_items'],
                            [
                            'li' => 'item',
                            'li.onpath' => 'item onpath',
                            'li a' => 'ui primary button',
                            ]),
//                        'Item' => Configuration::rendererDefaults()['menu_edit_items'],
                        'Buttons' => ConfigurationCache::rendererDefaults()['menu_edit_buttons'],
                    ]
                );
            },
            'menuVertical.classmap' => function() {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui vertical massive menu'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu',
                            ],
                        'Item' => ConfigurationCache::rendererDefaults()['menu_edit_items'],
                    ]);
            },
            'menuVertical.classmap.editable' => function() {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui vertical massive menu edit'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu'
                        ],
                        'Item' => ConfigurationCache::rendererDefaults()['menu_edit_items'],
                        'Buttons' => ConfigurationCache::rendererDefaults()['menu_edit_buttons'],
                    ]);
            },
            'menuBlocks.classmap.editable' => function() { //bloky
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui vertical massive menu edit'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu'
                        ],
                        'Item' => ConfigurationCache::rendererDefaults()['menu_edit_items'],
                        'Buttons' => ConfigurationCache::rendererDefaults()['block_edit_buttons'],
                    ]);
            },
            'menuTrash.classmap' => function() { //kos
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui vertical massive menu'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu'
                        ],
                        'Item' => ConfigurationCache::rendererDefaults()['menu_edit_items'],
                        'Buttons' => ConfigurationCache::rendererDefaults()['trash_edit_buttons'],
                    ]);
            },
        ###########################
        # paper classmap
        ###########################
            'authored.classmap' => function() {
                return new ClassMap (
                    ['Segment' => [
                        'div'=>'ui basic segment',
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
            'authored.editable.classmap' => function() {
                return new ClassMap (
                    ['Segment' => [
                        'div'=>'ui basic segment',
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
                     'PaperTemplateButtons' => ConfigurationCache::rendererDefaults()['paper_template_edit_buttons'],
                     'PaperTemplateSelect' => ConfigurationCache::rendererDefaults()['paper_template_select'],
                     'PaperButtons' => ConfigurationCache::rendererDefaults()['paper_edit_buttons'],
                     'ContentButtons' => ConfigurationCache::rendererDefaults()['content_edit_buttons'],
                     'TrashButtons' => ConfigurationCache::rendererDefaults()['deleted_content_buttons'],
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
                    'PaperTemplateButtons' => ConfigurationCache::rendererDefaults()['paper_template_edit_buttons'],
                    'PaperButtons' => ConfigurationCache::rendererDefaults()['paper_edit_buttons'],
                    'ContentButtons' => ConfigurationCache::rendererDefaults()['content_edit_buttons'],
                    'TrashButtons' => ConfigurationCache::rendererDefaults()['deleted_content_buttons'],
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
            'web.db.account.everyone.name' => 'tydenzdravieu001',
            'web.db.account.everyone.password' => 'tz_upgrader',
            'web.db.account.authenticated.name' => 'tydenzdravieu001',
            'web.db.account.authenticated.password' => 'tz_upgrader',
            'web.db.account.administrator.name' => 'tydenzdravieu001',
            'web.db.account.administrator.password' => 'tz_upgrader',
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
            'rs.db.account.everyone.name' => 'tydenzdravieu001',
            'rs.db.account.everyone.password' => 'tz_upgrader',
            'rs.db.account.authenticated.name' => 'tydenzdravieu001',
            'rs.db.account.authenticated.password' => 'tz_upgrader',
            'rs.db.account.administrator.name' => 'tydenzdravieu001',
            'rs.db.account.administrator.password' => 'tz_upgrader',
            #
            ###################################

        ];
    }

    ### presentation ###
    #

    /**
     * Konfigurace prezentace - vrací parametry pro statusPresentationManager
     * @return array
     */
    public static function statusPresentationManager() {
        return [
            'default_lang_code' => 'cs',
        ];
    }

    /**
     * Konfigurace prezentace - vrací parametry pro layoutControler
     * @return array
     */
    public static function layoutControler() {
        return [
           // Language packages tinyMce používají krátké i dlouhé kódy, kód odpovídá jménu souboru např cs.js nebo en_US.js - proto mapování
            // pozn. - popisky šablon pro tiny jsou jen česky (TinyInit.js)
            'tinyLanguage' => [
                    'cs' => 'cs',
                    'de' => 'de',
                    'en' => 'en_US'
                ],

            // title
            'title' => "Týden zdraví",

            // folders
            'linksCommon' => self::WEB_LINKS_COMMON,
            'linksSite' => self::WEB_LINKS_SITE,

            // local templates paths
            'layout' => self::WEB_TEMPLATES_SITE.'layout/layout.php',
            'tiny_config' => self::WEB_TEMPLATES_SITE.'js/tiny_config.js',
            'linksEditorJs' => self::WEB_TEMPLATES_COMMON.'layout/links/linkEditorJs.php',
            'linkEditorCss' => self::WEB_TEMPLATES_COMMON.'layout/links/linkEditorCss.php',

            // linksEditorJs links
            'urlTinyMCE' => self::WEB_ASSETS.'tinymce5_3_1\js\tinymce\tinymce.min.js',
            'urlJqueryTinyMCE' => self::WEB_ASSETS.'tinymce5_3_1\js\tinymce\jquery.tinymce.min.js',
//            'urlTinyMCE' => self::WEB_ASSETS.'tinymce5_4_0\js\tinymce\tinymce.min.js',
//            'urlJqueryTinyMCE' => self::WEB_ASSETS.'tinymce5_4_0\js\tinymce\jquery.tinymce.min.js',
//    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
//    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
//    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/jquery.tinymce.min.js" referrerpolicy="origin"></script>
            'urlTinyInit' => self::WEB_LINKS_COMMON.'js/TinyInit.js',
            'urlEditScript' => self::WEB_LINKS_COMMON . 'js/editDelegated.js',

            // linkEditorCss links
            'urlStylesCss' => self::WEB_LINKS_COMMON."css/old/styles.css",
            'urlSemanticCss' => self::WEB_LINKS_SITE."semantic-ui/semantic.min.css",
            'urlContentTemplatesCss' => self::WEB_LINKS_COMMON."css/templates.css",   // KŠ ?????
            //
            'paperTemplatesUri' =>  self::WEB_LINKS_SITE."templates/paper/",  // URI pro Template Controler
            'authorTemplatesPath' => self::WEB_LINKS_COMMON."templates/author/",

        ];
    }

    /**
     * Konfigurace prezentace - vrací parametry pro pageControler
     *
     * Home stránka může být definována jménem komponenty nebo jménem statické stránky nebo identifikátorem uid položky menu (položky hierarchie).
     *
     * @return array
     */
    public static function pageControler() {

        return [
               'home_page' => ['component', 'home'],
//               'home_page' => ['static', 'body-pro-zdravi'],
//               'home_page' => ['item', '5fad34398df10'],  // přednášky - pro test

               'templates.poznamky' => self::WEB_TEMPLATES_COMMON.'layout/info/poznamky.php',
               'templates.loaderElement' => self::WEB_TEMPLATES_COMMON.'layout/cascade/loaderElement.php',
               'templates.loaderElementEditable' => self::WEB_TEMPLATES_COMMON.'layout/cascade/loaderElementEditable.php',
            ];
    }

    /**
     * Konfigurace prezentace - vrací parametry pro ComponentControler
     * @return array
     */
    public static function componentControler() {

        return [
               'static' => self::WEB_TEMPLATES_SITE.'static/',

            ];
    }

    /**
     * Konfigurace prezentace - vrací parametry pro templateControler
     * @return array
     */
    public static function redTemplates() {

        return [
               'templates.authorFolder' => self::WEB_TEMPLATES_COMMON.'author/',
               'templates.paperFolder' => self::WEB_TEMPLATES_COMMON.'paper/',
               'templates.paperContentFolder' => self::WEB_TEMPLATES_COMMON.'paper-content/',

            ];
    }

    /**
     * Konfigurace upload files - vrací parametry pro FilesUploadControler
     * @return array
     */
    public static function redUploads() {

        return [
            'filesDirectory' => PES_RUNNING_ON_PRODUCTION_HOST ? self::WEB_FILES_SITE : self::WEB_FILES_SITE,

            ];
    }

    /**
     * Konfigurace prezentačního objektu - vrací parametry pro languageSelectRenderer
     * @return array
     */
    public static function languageSelectRenderer() {
        return [
            'assets' => self::WEB_ASSETS.'flags-mini/'
        ];
    }

    /**
     * Konfigurace prezentačního objektu - vrací parametry pro transformator
     * @return array
     */
    public static function transformator() {
        return [
            // relativní cesta vzhledem k DOCUMENT_ROOT (htdocs) -začíná /
            'filesDirectory' => PES_RUNNING_ON_PRODUCTION_HOST ? self::WEB_FILES_SITE : '/'.self::WEB_FILES_SITE,
            'public' => self::WEB_LINKS_COMMON,
        ];
    }
}
