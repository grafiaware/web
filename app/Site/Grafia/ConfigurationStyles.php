<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\Grafia;

use \Pes\View\Renderer\ClassMap\ClassMap;
use  Component\Renderer\Html\Menu\{
    MenuWrapRenderer, MenuWrapEditableRenderer, LevelWrapRenderer, ItemRenderer, ItemEditableRenderer, ItemBlockRenderer, ItemTrashRenderer
};
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

/**
 * Description of ConfigurationStyles
 *
 * @author pes2704
 */
class ConfigurationStyles extends ConfigurationRed {

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
                            'li.parent' => 'parent',
                            'li a span' => 'text',
                            'li i.dropdown' => 'dropdown icon',
                            'li i' => '',
                            'li a' => '',
                        ],
            'menu_edit_items' => [
                            'li' => '',
                            'li.item' => 'item',
                            'li.dropdown' => 'item',
                            'li.leaf' => 'item leaf',
                            'li.presented' => 'presented',
                            'li.parent' => 'parent',
                            'li.cut' => 'cut',
                            'li.paste' => 'paste',
                            'li a' => '',
                            'li i.dropdown' => 'dropdown icon',
                            'semafor'=> 'semafor',
                            'semafor.published' => 'circle icon green',
                            'semafor.notpublished' => 'circle icon red ',
                            'semafor.trashed' => 'circle icon inverted purple',
                        ],
            'menu_edit_buttons' => [
                            'div.buttons' => 'mini ui basic icon buttons editMenu',
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
                            'div button' => 'ui huge fade animated button toggleTemplateSelect',
                            'div.hidden' => 'hidden content',
                            'div.visible' => 'visible content',
                            'div i' => 'file alternate teal icon',
                            'div.selectTemplate' => 'select_template',
                            'div.tinySelectTemplatePaper' => 'tiny_select_template_paper borderDance',
                            'div.tinySelectTemplateArticle' => 'tiny_select_template_article borderDance',

                            'div.menu' => 'menu selectTemplate',
                            'div.header' => 'item header',
                            'div.selection' => 'ui compact selection dropdown',
                            'div.scrollmenu' => 'scrollhint menu',
                            'div.item' => 'item',
                            'div.text' => 'default text',
                            'i.dropdown' => 'dropdown icon',
                        ],
            'paper_edit_buttons' => [
                            'div.buttonsWrap' => 'contentButtons page-edit',
                            'div.buttons' => 'small ui basic icon buttons editContent',
                            'div.ribbon' => 'ui right ribbon teal basic label cornerWithTools page-edit',
                            'div.corner' => 'ui right ribbon teal basic label cornerWithTools page-edit',
                            'button' => 'ui button',
                            'button.template' => 'ui button toggleTemplateSelect',
                            'button.arrange' => 'large sort numeric down icon',
                            'button.template i' => 'large file alternate icon',
                            'div.editMode' => 'zapnout_editaci',
                            'div.editMode button' => 'ui small teal icon button',
                            'div.offEditMode button' => 'ui small teal basic icon button',
                            'div.editMode i' => 'pencil alternate icon',

                        ],
            'content_edit_buttons' => [
                            'div.wrapContent' => 'contentButtons',
                            'div.wrapShowDate' => 'calendarWrap editShowDate',
                            'div.wrapEventDate' => 'calendarWrap editEventDate',
                            'div.buttonsEditShowDate' => 'small ui basic icon buttons editDate',
                            'div.buttonsEditEventDate' => 'small ui basic icon buttons editDate',
                            'div.buttonsContent' => 'small ui basic icon buttons editContent',
                            'button' => 'ui button',
                            'button.showDate' => 'ui button toolsShowDate',
                            'button.eventDate' => 'ui button toolsEventDate',
                            'button.content' => 'ui button hideCalendarWrap',
                            'button.notpublish' => 'large green toggle on icon',
                            'button.publish' => 'large red toggle off icon',
                            'button.changedisplaydate' => 'large violet calendar alternate icon',
                            'button.changeeventdate' => 'large yellow money check icon',
                            'button.icons' => 'icons',
                            'button.arrowup' => 'top right corner arrow up icon',
                            'button.arrowdown' => 'bottom right corner arrow down icon',
                            'button.movecontent' => 'large sticky note outline icon',
                            'button.addcontent' => 'large plus square outline icon',
                            'button.movetotrash' => 'large purple trash icon',
//                            'button.event' => 'large sim card icon', //credit card outline ; columns ; certificate; ticket alternate; money check
                            'button.permanently' => 'large calendar outline icon',
                            'button.save' => 'large save icon',
                            'button.cancel' => 'large red times circle icon',
                            //'div.wrapKalendar' => 'edit_kalendar', //'edit_kalendar',   SV
                            'div.grid' => 'ui grid',
                            'div.wholeRow' => 'sixteen wide column',
                            'div.halfRow' => 'eight wide column',
                            'div.calendar' => 'ui calendar',
                            'div.input' => 'ui input',
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
        # menu renderer
        ###########################
            'menu.presmerovani.menuwraprenderer' => function(ContainerInterface $c) {
                return new MenuWrapRenderer($c->get('menu.presmerovani.classmap'), $c->get('menu.presmerovani.classmap.editable'));
            },
            'menu.presmerovani.levelwraprenderer' => function(ContainerInterface $c) {
                return new LevelWrapRenderer($c->get('menu.presmerovani.classmap'), $c->get('menu.presmerovani.classmap.editable'));
            },
            'menu.presmerovani.itemrenderer' => function(ContainerInterface $c) {
                return new ItemRenderer($c->get('menu.presmerovani.classmap'), $c->get('menu.presmerovani.classmap.editable'));
            },

            'menu.vodorovne.menuwraprenderer' => function(ContainerInterface $c) {
                return new MenuWrapRenderer($c->get('menu.vodorovne.classmap'), $c->get('menu.vodorovne.classmap.editable'));
            },
            'menu.vodorovne.levelwraprenderer' => function(ContainerInterface $c) {
                return new LevelWrapRenderer($c->get('menu.vodorovne.classmap'), $c->get('menu.vodorovne.classmap.editable'));
            },
            'menu.vodorovne.itemrenderer' => function(ContainerInterface $c) {
                return new ItemRenderer($c->get('menu.vodorovne.classmap'), $c->get('menu.vodorovne.classmap.editable'));
            },

            'menu.svisle.menuwraprenderer' => function(ContainerInterface $c) {
                return new MenuWrapRenderer($c->get('menu.svisle.classmap'), $c->get('menu.svisle.classmap.editable'));
            },
            'menu.svisle.levelwraprenderer' => function(ContainerInterface $c) {
                return new LevelWrapRenderer($c->get('menu.svisle.classmap'), $c->get('menu.svisle.classmap.editable'));
            },
            'menu.svisle.itemrenderer' => function(ContainerInterface $c) {
                return new ItemRenderer($c->get('menu.svisle.classmap'), $c->get('menu.svisle.classmap.editable'));
            },
            //bloky
            'menu.bloky.menuwraprenderer' => function(ContainerInterface $c) {
                return new MenuWrapRenderer($c->get('menu.svisle.classmap'), $c->get('menu.bloky.classmap.editable'));
            },
            'menu.bloky.levelwraprenderer' => function(ContainerInterface $c) {
                return new LevelWrapRenderer($c->get('menu.svisle.classmap'), $c->get('menu.bloky.classmap.editable'));
            },
            'menu.bloky.itemrenderer' => function(ContainerInterface $c) {
                return new ItemBlockRenderer($c->get('menu.svisle.classmap'), $c->get('menu.bloky.classmap.editable'));
            },
            //kos
            'menu.kos.menuwraprenderer' => function(ContainerInterface $c) {
                return new MenuWrapRenderer($c->get('menu.svisle.classmap'), $c->get('menu.kos.classmap.editable'));
            },
            'menu.kos.levelwraprenderer' => function(ContainerInterface $c) {
                return new LevelWrapRenderer($c->get('menu.svisle.classmap'), $c->get('menu.kos.classmap.editable'));
            },
            'menu.kos.itemrenderer' => function(ContainerInterface $c) {
                return new ItemTrashRenderer($c->get('menu.svisle.classmap'), $c->get('menu.kos.classmap.editable'));
            },


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
                        'Item' => self::rendererDefaults()['menu_items'],
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
                        'Item' => self::rendererDefaults()['menu_edit_items'],
                        'Buttons' => self::rendererDefaults()['menu_edit_buttons'],
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
                        'Item' => array_merge(self::rendererDefaults()['menu_items'],
                            [
                            'li a' => 'ui primary button',
                            ]),
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
                        'Item' => array_merge(self::rendererDefaults()['menu_edit_items'],
                            [
                            'li a' => 'ui primary button',
                            ]),
                        'Buttons' => self::rendererDefaults()['menu_edit_buttons'],
                    ]
                );
            },
            'menu.svisle.classmap' => function() {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui vertical menu' //hidden-submenu pro rozbalení submenu po kliknutí //ui text menu left floated vodorovne_menu
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu',
                            'ul.onpath' => 'menu onpath',
                            ],
                        'Item' => self::rendererDefaults()['menu_items'],
                    ]);
            },
            'menu.svisle.classmap.editable' => function() {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui vertical menu edit'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu',
                            'ul.onpath' => 'menu onpath',
                        ],
                        'Item' => self::rendererDefaults()['menu_edit_items'],
                        'Buttons' => self::rendererDefaults()['menu_edit_buttons'],
                    ]);
            },
            'menu.bloky.classmap.editable' => function() { //bloky
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'hlavni-menu edit'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu',
                            'ul.onpath' => 'menu onpath',
                        ],
                        'Item' => self::rendererDefaults()['menu_edit_items'],
                        'Buttons' => self::rendererDefaults()['block_edit_buttons'],
                    ]);
            },
            'menu.kos.classmap.editable' => function() { //kos
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'hlavni-menu menu'
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
                        ],
                      'PaperButtons' => self::rendererDefaults()['paper_edit_buttons'],
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
                        'headline.edit-text'=>'ui header borderDance edit-text',
                        ],
                     'Perex' => [
                        'section'=>'',
                        'perex.edit-html'=>'borderDance edit-html',
                        ],
                     'Content' => [
                        'section'=>'',
                        'section.trash'=>'trash',
                        'div.semafor'=>'semafor',
                        'div.ribbon'=>'ui right ribbon grey inverted label cornerWithTools',
                        'i1.published' => 'circle icon green',
                        'i1.notpublished' => 'circle icon red ',
                        'i2.published' => 'calendar check icon green',
                        'i2.notactive' => 'calendar plus icon yellow',
                        'i2.notactual' => 'calendar minus icon orange',
                        'i2.notactivenotactual' => 'calendar times icon red',
                         // verze2
                        'i2.actual' => 'calendar check icon green',
                        'i2.past' => 'calendar plus icon brown',
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
                        'headline.edit-text'=>'borderDance edit-text',
                        ],
                    'Perex' => [
                        'section'=>'',
                        'perex.edit-html'=>'borderDance edit-html',
                        ],
                    'Content' => [
                        'section'=>'',
                        'div.semafor'=>'semafor',
                        'section.trash'=>'trash',
                        'div.ribbon'=>'ui right tiny corner blue label cornerWithTools',
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
            'assets' => self::RED_ASSETS.'flags-mini/'
        ];
    }
}
