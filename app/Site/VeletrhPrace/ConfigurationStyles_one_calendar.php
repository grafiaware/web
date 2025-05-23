<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\VeletrhPrace;

use \Pes\View\Renderer\ClassMap\ClassMap;
use  Red\Component\Renderer\Html\Menu\{
    LevelRenderer, ItemRenderer, ItemEditableRenderer, ItemRenderer
};
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

/**
 * Description of ConfigurationStyles
 *
 * @author pes2704
 */
class ConfigurationStyles_one_calendar extends ConfigurationRed {

    /**
     * Pomocná metoda pro konfiguraci renderer kontejneru - vrací default hodnoty pro metodu self::renderer()
     * @return array
     */
    public static function rendererDefaults() {
        return [
            #
            #  menu classmap
            #
            // default hodnoty
            'menu_items' => [
                            'li' => '',
                            'li.item' => 'item',
                            'li.dropdown' => 'item',
                            'li.leaf' => 'item leaf',
                            'li.presented' => 'presented',
                            'li a span' => '',
                            'li i.dropdown' => 'dropdown icon',
                            'li a' => '',
                            'li i' => '',
                        ],
            'menu_edit_items' => [
                            'li' => '',
                            'li.item' => 'item',
                            'li.dropdown' => 'item',
                            'li.leaf' => 'item leaf',
                            'li.presented' => 'presented',
                            'li.cut' => 'cut',
                            'li.paste' => 'paste',
                            'li a' => '',   //nema_pravo //edituje_jiny
                            'li.isnotleaf icon' => 'chevron-icon', //dropdown icon
                            'semafor'=> 'semafor',
                            'semafor.published' => 'circle icon green',
                            'semafor.notpublished' => 'circle icon inverted red ',
                            'semafor.trashed' => 'circle icon inverted purple',

                        ],
            'menu_edit_buttons' => [
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
        # menu renderer
        ###########################
            'menuVertical.menuwraprenderer' => function(ContainerInterface $c) {
                return new LevelRenderer($c->get('menuVertical.classmap'), $c->get('menuVertical.classmap.editable'));
            },
            'menuVertical.levelwraprenderer' => function(ContainerInterface $c) {
                return new LevelRenderer($c->get('menuVertical.classmap'), $c->get('menuVertical.classmap.editable'));
            },
            'menuVertical.itemrenderer' => function(ContainerInterface $c) {
                return new ItemRenderer($c->get('menuVertical.classmap'), $c->get('menuVertical.classmap.editable'));
            },
            //bloky
            'menuBlocks.menuwraprenderer' => function(ContainerInterface $c) {
                return new LevelRenderer($c->get('menuVertical.classmap'), $c->get('menuBlocks.classmap.editable'));
            },
            'menuBlocks.levelwraprenderer' => function(ContainerInterface $c) {
                return new LevelRenderer($c->get('menuVertical.classmap'), $c->get('menuBlocks.classmap.editable'));
            },
//            'menuBlocks.itemrenderer' => function(ContainerInterface $c) {
//                return new NodeBlockRenderer($c->get('menuVertical.classmap'), $c->get('menuBlocks.classmap.editable'));
//            },
            //kos
            'menuTrash.menuwraprenderer' => function(ContainerInterface $c) {
                return new LevelRenderer($c->get('menuVertical.classmap'), $c->get('menuTrash.classmap.editable'));
            },
            'menuTrash.levelwraprenderer' => function(ContainerInterface $c) {
                return new LevelRenderer($c->get('menuVertical.classmap'), $c->get('menuTrash.classmap.editable'));
            },
//            'menuTrash.itemrenderer' => function(ContainerInterface $c) {
//                return new NodeTrashRenderer($c->get('menuVertical.classmap'), $c->get('menuTrash.classmap.editable'));
//            },


        ###########################
        # menu classmap
        ###########################
            'menuVertical.classmap' => function() {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui vertical massive menu'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu',
                            'ul.onpath' => 'menu onpath',
                            ],
                        'Item' => self::rendererDefaults()['menu_items']
                    ]);
            },
            'menuVertical.classmap.editable' => function() {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui vertical massive menu edit'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu',
                            'ul.onpath' => 'menu onpath',
                        ],
                        'Item' => self::rendererDefaults()['menu_edit_items'],
                        'Buttons' => self::rendererDefaults()['menu_edit_buttons'],
                    ]);
            },
            'menuBlocks.classmap.editable' => function() { //bloky
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui vertical massive menu edit'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu',
                            'ul.onpath' => 'menu onpath',
                        ],
                        'Item' => self::rendererDefaults()['menu_edit_items'],
                        'Buttons' => self::rendererDefaults()['block_edit_buttons'],
                    ]);
            },
            'menuTrash.classmap.editable' => function() { //kos
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui vertical massive menu'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu',
                            'ul.onpath' => 'menu onpath',
                        ],
                        'Item' => self::rendererDefaults()['menu_edit_items'],
                        'Buttons' => self::rendererDefaults()['trash_edit_buttons'],
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
                        'headline'=>'ui header borderDance edit-text',
                        ],
                     'Perex' => [
                        'section'=>'',
                        'perex'=>'borderDance edit-html',
                        ],
                     'Content' => [
                        'section'=>'',
                        'section.trash'=>'trash',
                        'div.semafor'=>'semafor',
                        'div.corner'=>'ui right tiny corner blue label cornerWithTools',
                        'div.corner icon' => 'tools black icon',
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
                        'content'=>'borderDance edit-html',
                        'div.trash_content'=>'trash_content'
                        ],
                     'PaperTemplateButtons' => self::rendererDefaults()['paper_template_edit_buttons'],
                     'PaperTemplateSelect' => self::rendererDefaults()['paper_template_select'],
                     'PaperButtons' => self::rendererDefaults()['paper_edit_buttons'],
                     'ContentButtons' => self::rendererDefaults()['content_edit_buttons'],
                     'TrashButtons' => self::rendererDefaults()['deleted_content_buttons'],
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
                    'PaperTemplateButtons' => self::rendererDefaults()['paper_template_edit_buttons'],
                    'PaperButtons' => self::rendererDefaults()['paper_edit_buttons'],
                    'ContentButtons' => self::rendererDefaults()['content_edit_buttons'],
                    'TrashButtons' => self::rendererDefaults()['deleted_content_buttons'],
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
     * Konfigurace prezentačního objektu - vrací parametry pro languageSelectRenderer
     * @return array
     */
    public static function languageSelectRenderer() {
        return [
            'assets' => self::WEB_ASSETS.'flags-mini/'
        ];
    }
}
