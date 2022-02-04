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
                            'li.presented' => 'presented',
                            'li.leaf' => 'item leaf',
                            'li.dropdown' => 'item',
                            'li i.dropdown' => 'dropdown icon',
                            'li.item' => 'item',
                            'li.parent' => 'parent',
                            'li a span' => '',
                            'li a' => '',
                            'li i' => '',
                            //editační prvky
                            'li.cut' => 'cut',
                            'semafor'=> 'semafor',
                            'semafor.published' => 'circle icon green',
                            'semafor.notpublished' => 'circle icon red ',
                            'semafor.trashed' => 'circle icon inverted purple',
                        ],
            'menu_common_edit_buttons' => [
                            'div.buttons' => 'editSize ui basic icon buttons',
                            'button' => 'ui button',
                            'button.notpublish' => 'green toggle on icon',
                            'button.publish' => 'red toggle off icon',
                            'button.cut' => 'cut icon',
                            'button.cutted' => 'red cut icon',
                            'button.addsiblings' => 'add circle icon',
                            'button.movetotrash' => 'purple trash icon',
                        ],
            'menu_edit_buttons' => [
                            'button.paste' => 'ui button paste',
                            'button.addchildren' => ' arrow circle right icon',
                        ],
            'block_edit_buttons' => [
                            
                        ],
            'trash_edit_buttons' => [
                            'button.icons' => 'icons',
                            'button.delete' => 'trash icon',
                            'button.exclamation' => 'corner red exclamation icon',
                        ],
            'paper_template_edit_buttons' => [
                            'div.paperTemplate' => 'ui editSize basic icon dropdown button changePaperTemplate',
                            'button' => 'ui button',
                            'button.templateSelect' => ' clone outline icon'
                        ],
            'paper_template_select' => [
                            'div button' => 'ui huge fade animated button toggleTemplateSelect',
                            'div.hidden' => 'hidden content',
                            'div.visible' => 'visible content',
                            'div i' => 'file alternate teal icon',
                            'div.selectTemplate' => 'select_template',
                            'div.tinySelectTemplatePaper' => 'tiny_select_template_paper borderDance',
                            'div.tinySelectTemplateArticle' => 'tiny_select_template_article borderDance',
                            'div.tinySelectTemplateMultipage' => 'tiny_select_template_multipage borderDance',
                        ],
            'paper_edit_buttons' => [
                            'div.buttonsWrap' => 'contentButtons page-edit',
                            'div.buttons' => 'editingButtons_size ui basic icon buttons editContent',
                            'div.ribbon' => 'ui right ribbon teal basic label cornerWithTools page-edit',
                            'div.ribbon-paper' => 'ui right ribbon pink basic label cornerWithTools page-edit',
                            'div.ribbon-article' => 'ui right ribbon orange basic label cornerWithTools page-edit',
                            'div.ribbon-disabled' => 'ui right ribbon label page-edit black basic',
                            'button' => 'ui button',
                            'button.template' => 'ui button toggleTemplateSelect',
                            'button.template i' => 'file alternate icon',
                            'button.arrange' => 'sort numeric down icon',
                            'div.editMode' => 'zapnout_editaci',
                            'div.editMode i' => 'pencil alternate icon',
                            'div.editMode button' => 'ui editingButtons_size teal icon button',
                            'div.offEditMode button' => 'ui editingButtons_size teal basic icon button',
                        ],
            'content_edit_buttons' => [
                            'div.wrapContent' => 'contentButtons',
                            'div.wrapShowDate' => 'calendarWrap editShowDate',
                            'div.wrapEventDate' => 'calendarWrap editEventDate',
                            'div.buttonsEditDate' => 'editingButtons_size ui basic icon buttons editDate',
                            'div.buttonsContent' => 'editingButtons_size ui basic icon buttons editContent',
                            'button' => 'ui button',
                            'button.showDate' => 'ui button toolsShowDate',
                            'button.eventDate' => 'ui button toolsEventDate',
                            'button.content' => 'ui button hideCalendarWrap',
                            'button.notpublish' => 'green toggle on icon',
                            'button.publish' => 'red toggle off icon',
                            'button.changedisplaydate' => 'violet calendar alternate icon',
                            'button.changeeventdate' => 'yellow money check icon',
                            'button.icons' => 'icons',
                            'button.arrowup' => 'top right corner arrow up icon',
                            'button.arrowdown' => 'bottom right corner arrow down icon',
                            'button.movecontent' => 'sticky note outline icon',
                            'button.addcontent' => 'plus square outline icon',
                            'button.movetotrash' => 'purple trash icon',
                            'button.permanently' => 'calendar outline icon',
                            'button.save' => 'save icon',
                            'button.cancel' => 'red times circle icon',
                            'div.grid' => 'ui grid',
                            'div.wholeRow' => 'sixteen wide column',
                            'div.halfRow' => 'eight wide column',
                            'div.calendar' => 'ui calendar',
                            'div.input' => 'ui input',
                        ],
            'deleted_content_buttons' => [
                            'div.wrapTrash' => 'contentButtons trash',
                            'button.restore' => 'sync icon',
                            'button.delete' => 'trash icon',
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
                            'ul' => 'ui text menu',
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
                            'ul' => 'ui text menu',
                            ],
                        'LevelWrap' => [
                            'ul' => 'menu'
                        ],
                        'Item' => self::rendererDefaults()['menu_items'],
                        'Buttons' => self::rendererDefaults()['menu_edit_buttons'],
                        'CommonButtons' => self::rendererDefaults()['menu_common_edit_buttons'],
                    ]
                );
            },

            'menu.vodorovne.classmap' => function() {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui text menu left floated',
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
                            'ul' => 'ui text menu left floated',
                            ],
                        'LevelWrap' => [
                            'ul' => 'menu'
                        ],
                        'Item' => array_merge(self::rendererDefaults()['menu_items'],
                            [
                            'li a' => 'ui primary button',
                            ]),
                        'Buttons' => self::rendererDefaults()['menu_edit_buttons'],
                        'CommonButtons' => self::rendererDefaults()['menu_common_edit_buttons'],
                    ]
                );
            },
            'menu.svisle.classmap' => function() {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui vertical menu hidden-submenu' //hidden-submenu pro rozbalení submenu po kliknutí //ui text menu left floated vodorovne_menu
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu onpath',
                            ],
                        'Item' => self::rendererDefaults()['menu_items'],
                    ]);
            },
            'menu.svisle.classmap.editable' => function() {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui vertical menu'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu onpath',
                        ],
                        'Item' => self::rendererDefaults()['menu_items'],
                        'Buttons' => self::rendererDefaults()['menu_edit_buttons'],
                        'CommonButtons' => self::rendererDefaults()['menu_common_edit_buttons'],
                    ]);
            },
            'menu.bloky.classmap.editable' => function() { //bloky
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'hlavni-menu'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu onpath',
                        ],
                        'Item' => self::rendererDefaults()['menu_items'],
                        'Buttons' => self::rendererDefaults()['block_edit_buttons'],
                        'CommonButtons' => self::rendererDefaults()['menu_common_edit_buttons'],
                    ]);
            },
            'menu.kos.classmap.editable' => function() { //kos
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'hlavni-menu menu'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu onpath',
                        ],
                        'Item' => self::rendererDefaults()['menu_items'],
                        'Buttons' => self::rendererDefaults()['trash_edit_buttons'],
                        'CommonButtons' => self::rendererDefaults()['menu_common_edit_buttons'],
                    ]);
            },
        ###########################
        # paper classmap
        ###########################
            'authored.classmap' => function() {
                return new ClassMap (
                    [
                     'Headline' => [
                        'headline'=>'',
                        ],
                     'Perex' => [
                        'perex'=>'',
                        ],
                     'Content' => [
                        'content'=>'',
                        'div.templateMultipage' => 'template-multipage',
                        'div.templateMultipage' => 'template-multipage',
                        'div.templatePaper' => 'template-paper',
                        'div.templateArticle' => 'template-article',
                        ],
                      'PaperButtons' => self::rendererDefaults()['paper_edit_buttons'],
                    ]
                );
            },
            'authored.editable.classmap' => function() {
                return new ClassMap (
                    [
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
                        'section.trash'=>'trash',
                        'div.semafor'=>'semafor',
                        'div.ribbon'=>'ui right ribbon grey inverted label cornerWithTools',
                        'ribbon.svg' => 'ribbon-svg',
                        'ribbon.priority' => 'content-priority',
                        'div.nameMenuItem'=> 'nadpis-polozky-menu',
                        'i1.published' => 'circle icon green',
                        'i1.notpublished' => 'circle icon red',
                        'i.trash' => 'trash icon purple',
                        'content'=>'borderDance edit-html',
                        'div.trash_content'=>'trash_content',
                        'div.templateMultipage' => 'template-multipage',
                        'div.templateMultipage' => 'template-multipage',
                        'div.templatePaper' => 'template-paper',
                        'div.templateArticle' => 'template-article',
                        ],
                     'PaperTemplateButtons' => self::rendererDefaults()['paper_template_edit_buttons'],
                     'PaperTemplateSelect' => self::rendererDefaults()['paper_template_select'],
                     'PaperButtons' => self::rendererDefaults()['paper_edit_buttons'],
                     'ContentButtons' => self::rendererDefaults()['content_edit_buttons'],
                     'TrashButtons' => self::rendererDefaults()['deleted_content_buttons'],
                     'CommonButtons' => self::rendererDefaults()['menu_common_edit_buttons'],
                    ]
                );
            },

//            'block.classmap' => function() {
//                return new ClassMap (
//                    [
//                     'Headline' => [
//                        'div'=>'',
//                        'headline'=>'',
//                        ],
//                     'Perex' => [
//                        'perex'=>'',
//                        ],
//                    'Content' => [
//                        'content'=>''
//                        ],
//                    ]
//                );
//            },
//            'block.editable.classmap' => function() {
//                return new ClassMap (
//                    [
//                     'Headline' => [
//                        'section'=>'',
//                        'headline.edit-text'=>'borderDance edit-text',
//                        ],
//                    'Perex' => [
//                        'section'=>'',
//                        'perex.edit-html'=>'borderDance edit-html',
//                        ],
//                    'Content' => [
//                        'section'=>'',
//                        'div.semafor'=>'semafor',
//                        'section.trash'=>'trash',
//                        'div.ribbon'=>'ui right tiny corner blue label cornerWithTools',
//                        'i1.published' => 'circle icon green',
//                        'i1.notpublished' => 'circle icon red',
//                        'i.trash' => 'trash icon purple',
//                        'content'=>'borderDance',
//                        'div.trash_content'=>'trash_content'
//                        ],
//                    'PaperTemplateButtons' => self::rendererDefaults()['paper_template_edit_buttons'],
//                    'PaperButtons' => self::rendererDefaults()['paper_edit_buttons'],
//                    'ContentButtons' => self::rendererDefaults()['content_edit_buttons'],
//                    'TrashButtons' => self::rendererDefaults()['deleted_content_buttons'],
//                    ]
//                );
//            },
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
