<?php
namespace Container;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// renderery
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\View\Renderer\PhpTemplateRenderer;

use Component\Renderer\Html\Menu\{
    MenuWrapRenderer, MenuWrapEditableRenderer, LevelWrapRenderer, ItemRenderer, ItemEditableRenderer, ItemBlockEditableRenderer, ItemTrashRenderer
};

use Component\Renderer\Html\Authored\{
    HeadlinedRenderer, HeadlinedEditableRenderer, BlockRenderer, BlockEditableRenderer
};

use Component\Renderer\Html\Generated\{
    LanguageSelectRenderer, SearchPhraseRenderer, SearchResultRenderer, ItemTypeRenderer
};

use Component\Renderer\Html\ClassMap\ClassMap;

/**
 *
 *
 * @author pes2704
 */
class RendererContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getFactoriesDefinitions() {
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
        ];
    }

    public function getAliases() {
        return [
//            PhpTemplateRendererInterface::class => PhpTemplateRenderer::class,
        ];
    }

    public function getServicesDefinitions() {
        return [
            'menu.presmerovani.classmap' => function(ContainerInterface $c) {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui tiny text menu',
                            ],
                        'LevelWrap' => [
                            'ul' => 'menu'
                        ],
                        'Item' => array_merge($c->get('menu_edit_items'),
                            [
                            'li' => 'item',
                            'li.onpath' => 'item onpath',
                            'li a' => '',
                            ]),
                    ]);
            },
            'menu.presmerovani.classmap.editable' => function(ContainerInterface $c) {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui tiny text menu edit',
                            ],
                        'LevelWrap' => [
                            'ul' => 'menu'
                        ],
                        'Item' => array_merge($c->get('menu_edit_items'),
                            [
                            'li' => 'item',
                            'li.onpath' => 'item onpath',
                            'li a' => '',
                            ]),
                        'Buttons' => $c->get('menu_edit_buttons'),
                    ]
                );
            },

            'menu.vodorovne.classmap' => function(ContainerInterface $c) {
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
            'menu.vodorovne.classmap.editable' => function(ContainerInterface $c) {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui mini text menu edit left floated',
                            ],
                        'LevelWrap' => [
                            'ul' => 'menu'
                        ],
                        'Item' => array_merge($c->get('menu_edit_items'),
                            [
                            'li' => 'item',
                            'li.onpath' => 'item onpath',
                            'li a' => 'ui primary button',
                            ]),
//                        'Item' => $c->get('menu_edit_items'),
                        'Buttons' => $c->get('menu_edit_buttons'),
                    ]
                );
            },
            'menu.svisle.classmap' => function(ContainerInterface $c) {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui vertical menu'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu',
                            ],
                        'Item' => $c->get('menu_edit_items'),
                    ]);
            },
            'menu.svisle.classmap.editable' => function(ContainerInterface $c) {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui vertical menu edit'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu'
                        ],
                        'Item' => $c->get('menu_edit_items'),
                        'Buttons' => $c->get('menu_edit_buttons'),
                    ]);
            },
            'menu.bloky.classmap.editable' => function(ContainerInterface $c) { //bloky
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui vertical menu edit'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu'
                        ],
                        'Item' => $c->get('menu_edit_items'),
                        'Buttons' => $c->get('block_edit_buttons'),
                    ]);
            },
            'menu.kos.classmap' => function(ContainerInterface $c) { //kos
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui vertical menu'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu'
                        ],
                        'Item' => $c->get('menu_edit_items'),
                        'Buttons' => $c->get('trash_edit_buttons'),
                    ]);
            },
        #
        #  menu renderer
        #
            'menu.presmerovani.menuwraprenderer' => function(ContainerInterface $c) {
                return new MenuWrapRenderer($c->get('menu.presmerovani.classmap'));
            },
            'menu.presmerovani.levelwraprenderer' => function(ContainerInterface $c) {
                return new LevelWrapRenderer($c->get('menu.presmerovani.classmap'));
            },
            'menu.presmerovani.itemrenderer' => function(ContainerInterface $c) {
                return new ItemRenderer($c->get('menu.presmerovani.classmap'));
            },
            'menu.presmerovani.menuwraprenderer.editable' => function(ContainerInterface $c) {
                return new MenuWrapEditableRenderer($c->get('menu.presmerovani.classmap.editable'));
            },
            'menu.presmerovani.levelwraprenderer.editable' => function(ContainerInterface $c) {
                return new LevelWrapRenderer($c->get('menu.presmerovani.classmap.editable'));
            },
            'menu.presmerovani.itemrenderer.editable' => function(ContainerInterface $c) {
                return new ItemEditableRenderer($c->get('menu.presmerovani.classmap.editable'));
            },

            'menu.vodorovne.menuwraprenderer' => function(ContainerInterface $c) {
                return new MenuWrapRenderer($c->get('menu.vodorovne.classmap'));
            },
            'menu.vodorovne.levelwraprenderer' => function(ContainerInterface $c) {
                return new LevelWrapRenderer($c->get('menu.vodorovne.classmap'));
            },
            'menu.vodorovne.itemrenderer' => function(ContainerInterface $c) {
                return new ItemRenderer($c->get('menu.vodorovne.classmap'));
            },
            'menu.vodorovne.menuwraprenderer.editable' => function(ContainerInterface $c) {
                return new MenuWrapEditableRenderer($c->get('menu.vodorovne.classmap.editable'));
            },
            'menu.vodorovne.levelwraprenderer.editable' => function(ContainerInterface $c) {
                return new LevelWrapRenderer($c->get('menu.vodorovne.classmap.editable'));
            },
            'menu.vodorovne.itemrenderer.editable' => function(ContainerInterface $c) {
                return new ItemEditableRenderer($c->get('menu.vodorovne.classmap.editable'));
            },

            'menu.svisle.menuwraprenderer' => function(ContainerInterface $c) {
                return new MenuWrapRenderer($c->get('menu.svisle.classmap'));
            },
            'menu.svisle.levelwraprenderer' => function(ContainerInterface $c) {
                return new LevelWrapRenderer($c->get('menu.svisle.classmap'));
            },
            'menu.svisle.itemrenderer' => function(ContainerInterface $c) {
                return new ItemRenderer($c->get('menu.svisle.classmap'));
            },
            'menu.svisle.menuwraprenderer.editable' => function(ContainerInterface $c) {
                return new MenuWrapEditableRenderer($c->get('menu.svisle.classmap.editable'));
            },
            'menu.svisle.levelwraprenderer.editable' => function(ContainerInterface $c) {
                return new LevelWrapRenderer($c->get('menu.svisle.classmap.editable'));
            },
            'menu.svisle.itemrenderer.editable' => function(ContainerInterface $c) {
                return new ItemEditableRenderer($c->get('menu.svisle.classmap.editable'));
            },
                   //bloky
            'menu.bloky.menuwraprenderer.editable' => function(ContainerInterface $c) {
                return new MenuWrapEditableRenderer($c->get('menu.bloky.classmap.editable'));
            },
            'menu.bloky.levelwraprenderer.editable' => function(ContainerInterface $c) {
                return new LevelWrapRenderer($c->get('menu.bloky.classmap.editable'));
            },
            'menu.bloky.itemrenderer.editable' => function(ContainerInterface $c) {
                return new ItemBlockEditableRenderer($c->get('menu.bloky.classmap.editable'));
            },
                    //kos
            'menu.kos.menuwraprenderer' => function(ContainerInterface $c) {
                return new MenuWrapRenderer($c->get('menu.kos.classmap'));
            },
            'menu.kos.levelwraprenderer' => function(ContainerInterface $c) {
                return new LevelWrapRenderer($c->get('menu.kos.classmap'));
            },
            'menu.kos.itemrenderer' => function(ContainerInterface $c) {
                return new ItemTrashRenderer($c->get('menu.kos.classmap'));
            },
            'menu.kos.menuwraprenderer.editable' => function(ContainerInterface $c) {
                return new MenuWrapEditableRenderer($c->get('menu.kos.classmap'));
            },
        #
        #  paper classmap
        #
//            'paper.headlined.classmap' => function(ContainerInterface $c) {
//                return new ClassMap (
//                    ['Component' => [
//                        'div'=>'ui segment',
//                        'div div'=>'grafia segment headlined headline',
//                        'div div headline'=>'ui header',
//                        'div content'=>'grafia segment headlined content',
//                        ]
//                    ]
//                );
//            },
//            'paper.headlined.editable.classmap' => function(ContainerInterface $c) {
//                return new ClassMap (
//                    ['Component' => [
//                        'div'=>'ui segment',
//                        'div div'=>'grafia segment headlined editable',
//                        'div div.notpermitted'=>'grafia segment headlined notpermitted',
//                        'div div.locked'=>'grafia segment headlined locked',
//                        'div div div'=>'',
//                        'div div div headline'=>'ui header',
//                        'div div div i1.published' => 'grafia active green',
//                        'div div div i1.notpublished' => 'grafia active red ',
//                        'div div div i2.published' => 'grafia actual grey',
//                        'div div div i2.notactive' => 'grafia actual yellow',
//                        'div div div i2.notactual' => 'grafia actual orange',
//                        'div div div i2.notactivenotactual' => 'grafia actual red',
//                        'div div div i3'=>'settings icon',
//                        'div div content'=>'',
//                        ],
//                     'Buttons' => $c->get('paper_edit_buttons'),
//                    ]
//                );
//            },
//
//            'paper.block.classmap' => function(ContainerInterface $c) {
//                return new ClassMap (
//                    ['Component' => [
//                        'div'=>'grafia segment block',
//                        'div block'=>'',
//                        ]
//                    ]
//                );
//            },
//            'paper.block.editable.classmap' => function(ContainerInterface $c) {
//                return new ClassMap (
//                    ['Component' => [
//                        'div'=>'grafia segment block editable',
//                        'div block'=>'',
//                        //'block.notpermitted'=>'grafia segment block notpermitted',
//                        //'block.locked'=>'grafia segment block locked',
//                        ],
//                    'Buttons' => $c->get('paper_edit_buttons'),
//                        ]
//                );
//            },
############################
            'paper.headlined.classmap' => function(ContainerInterface $c) {
                return new ClassMap (
                    ['Segment' => [
                        'div'=>'ui segment',
                        ],
                     'Headline' => [
                        'div'=>'grafia segment headlined headline',
                        'headline'=>'ui header',
                        ],
                     'Perex' => [
                        'perex'=>'grafia segment headlined content',
                        ],
                     'Content' => [
                        'content'=>'grafia segment headlined content',
                        ]                    ]
                );
            },
            'paper.headlined.editable.classmap' => function(ContainerInterface $c) {
                return new ClassMap (
                    ['Segment' => [
                        'div'=>'ui segment',
                        'div.grafia'=>'grafia segment headlined editable',
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
                        'i.trash' => 'trash icon purple',
                        'content'=>'',
                        'div.trash_content'=>'trash_content'
                        ],
                     'PaperButtons' => $c->get('paper_edit_buttons'),
                     'ContentButtons' => $c->get('content_edit_buttons'),
                     'TrashButtons' => $c->get('deleted_content_buttons'),
                    ]
                );
            },

            'paper.block.classmap' => function(ContainerInterface $c) {
                return new ClassMap (
                    ['Segment' => [
                        'div'=>'grafia segment block',
                        ],
                    'Perex' => [
                        'perex'=>'',
                        ]
                    ]
                );
            },
            'paper.block.editable.classmap' => function(ContainerInterface $c) {
                return new ClassMap (
                    ['Segment' => [
                        'div'=>'grafia segment block editable',
                        ],
                     'Perex' => [
                        'section'=>'',
                        'perex'=>'',
                        ],
                     'Content' => [
                        'section'=>'',
                        'div.semafor'=>'semafor',
                        'div.corner'=>'ui right tiny corner blue label',
                        'i1.published' => 'circle icon green',
                        'i1.notpublished' => 'circle icon red ',
                        'i2.published' => 'calendar check icon green',
                        'i2.notactive' => 'calendar plus icon grey',
                        'i2.notactual' => 'calendar minus icon orange',
                        'i2.notactivenotactual' => 'calendar times icon red',
                        'content'=>'',
                        ],
                    'ContentButtons' => $c->get('content_edit_buttons'),
                    ]
                );
            },

        #
        #  paper renderer
        #
//            'paper.headlined.renderer' => function(ContainerInterface $c) {
//                return new HeadlinedRenderer($c->get('paper.headlined.classmap'));
//            },
//            'paper.headlined.renderer.editable' => function(ContainerInterface $c) {
//                return new HeadlinedEditableRenderer($c->get('paper.headlined.editable.classmap'));
//            },
//            'paper.block.renderer' => function(ContainerInterface $c) {
//                return new BlockRenderer($c->get('paper.block.classmap'));
//            },
//            'paper.block.renderer.editable' => function(ContainerInterface $c) {
//                return new BlockEditableRenderer($c->get('paper.block.editable.classmap'));
//            },
###########################
            'paper.headlined.renderer' => function(ContainerInterface $c) {
                return new HeadlinedRenderer($c->get('paper.headlined.classmap'));
            },
            'paper.headlined.renderer.editable' => function(ContainerInterface $c) {
                return new HeadlinedEditableRenderer($c->get('paper.headlined.editable.classmap'));
            },
            'paper.block.renderer' => function(ContainerInterface $c) {
                return new BlockRenderer($c->get('paper.block.classmap'));
            },
            'paper.block.renderer.editable' => function(ContainerInterface $c) {
                return new BlockEditableRenderer($c->get('paper.block.editable.classmap'));
            },
        #
        #  generated classmap
        #
            'generated.languageselect.classmap' => function(ContainerInterface $c) {
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

    public function getServicesOverrideDefinitions() {
        return [

        // !! pokud je delegátem zde definovaného kontejneru TemplateRendererContainer
        // TemplateRendererContainer vrací true při volání has(jméno třídy) pro každou eistující třídu
        // => každý renderer, pro který jméno služby je jméno třídy musí být definován zde, v getServicesOverrideDefinitions()
        #
        #  generated renderer
        #
            LanguageSelectRenderer::class => function(ContainerInterface $c) {
                return new LanguageSelectRenderer($c->get('generated.languageselect.classmap'));
            },
            SearchPhraseRenderer::class => function(ContainerInterface $c) {
                return new SearchPhraseRenderer();
            },
            SearchResultRenderer::class => function(ContainerInterface $c) {
                return new SearchResultRenderer();
            },
            ItemTypeRenderer::class => function(ContainerInterface $c) {
                return new ItemTypeRenderer();
            },
        #
        #  default template renderer
        #
//            PhpTemplateRenderer::class => function(ContainerInterface $c) {
//                return new PhpTemplateRenderer();
//            },
        ];
    }
}