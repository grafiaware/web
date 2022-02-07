<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\TydenZdravi;

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
            'buttons' => [
                            'button' => 'ui button',
                            'div.buttons' => 'editSize ui basic icon buttons', //sefinovat - používané small
                            'button.paste' => 'ui button paste',
                            'div.buttonsWrap' => 'contentButtons page-edit',
                            'div.ribbon' => 'ui right ribbon teal basic label cornerWithTools page-edit',
                            'div.ribbon-paper' => 'ui right ribbon pink basic label cornerWithTools page-edit',        ///jeden ribbon, barvu měnit v less
                            'div.ribbon-article' => 'ui right ribbon orange basic label cornerWithTools page-edit',
                            'div.ribbon-disabled' => 'ui right ribbon label page-edit black basic',
                            'button.template' => 'ui button toggleTemplateSelect',
                            'div.editMode' => 'zapnout_editaci',
                            'div.editMode button' => 'ui editingButtons_size teal icon button',
                            'div.offEditMode button' => 'ui editingButtons_size teal basic icon button',
                            'div.wrapTrash' => 'contentButtons trash',
                            'div.wrapContent' => 'contentButtons',
                            'div.wrapShowDate' => 'calendarWrap editShowDate',
                            'div.wrapEventDate' => 'calendarWrap editEventDate',
                            'div.buttonsEditDate' => 'editingButtons_size ui basic icon buttons editDate',
                            'div.buttonsContent' => 'editingButtons_size ui basic icon buttons editContent',        ///div.buttons = div.buttonsContent - zkontrolovat!!!
                            'button.showDate' => 'ui button toolsShowDate',
                            'button.eventDate' => 'ui button toolsEventDate',
                            'button.content' => 'ui button hideCalendarWrap',
                
                        ],
            'icons_buttons' => [
                            'button.notpublish' => 'green toggle on icon',
                            'button.publish' => 'red toggle off icon',
                            'button.cut' => 'cut icon',
                            'button.cutted' => 'red cut icon',
                            'button.addsiblings' => 'add circle icon',
                            'button.movetotrash' => 'purple trash icon',
                            'button.addchildren' => ' arrow circle right icon',
                            'button.icons' => 'icons',
                            'button.delete' => 'trash icon',
                            'button.exclamation' => 'corner red exclamation icon',
                            'button.templateSelect' => 'clone outline icon',
                            'button.template i' => 'file alternate icon',
                            'button.arrange' => 'sort numeric down icon',
                            'div.editMode i' => 'pencil alternate icon',
                            'button.changedisplaydate' => 'violet calendar alternate icon',
                            'button.changeeventdate' => 'yellow money check icon',
                            'button.arrowup' => 'top right corner arrow up icon',
                            'button.arrowdown' => 'bottom right corner arrow down icon',
                            'button.movecontent' => 'sticky note outline icon',
                            'button.addcontent' => 'plus square outline icon',
                            'button.permanently' => 'calendar outline icon',
                            'button.save' => 'save icon',
                            'button.cancel' => 'red times circle icon',
                            'button.restore' => 'sync icon',
                            'semafor.published' => 'circle icon green',
                            'semafor.notpublished' => 'circle icon inverted red',
                            'semafor.trashed' => 'circle icon inverted purple',
                        ],
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
                            //'li.isnotleaf icon' => 'chevron-icon', //dropdown icon
                        ],
            'paper_template_select' => [
                            'div button' => 'ui huge fade animated button toggleTemplateSelect', ///vybirani sablon pro article???
                            'div.hidden' => 'hidden content', ///vybirani sablon pro article???
                            'div.visible' => 'visible content', ///vybirani sablon pro article???
                            'div i' => 'file alternate teal icon', ///vybirani sablon pro article???
                
                            
                
                            'div.tinySelectTemplatePaper' => 'tiny_select_template_paper borderDance',   // class tiny_select_template_paper je selektor pro TinyInit - vybere konfiguraci a v té je proměnná se seznameme šablon (jiný seznam pro paper, article, multipage)
                            'div.tinySelectTemplateArticle' => 'tiny_select_template_article borderDance',
                            'div.tinySelectTemplateMultipage' => 'tiny_select_template_multipage borderDance',
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
            'menu.svisle.classmap' => function() {
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui vertical massive menu' //definovat v semantic - použít bez massive
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
                            'ul' => 'ui vertical massive menu edit'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu',
                            'ul.onpath' => 'menu onpath',
                        ],
                        'Item' => self::rendererDefaults()['menu_items'],
                        'Buttons' => self::rendererDefaults()['buttons'],
                        'Icons' => self::rendererDefaults()['icons_buttons']
                    ]);
            },
            'menu.bloky.classmap.editable' => function() { //bloky
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui vertical massive menu edit'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu',
                            'ul.onpath' => 'menu onpath',
                        ],
                        'Item' => self::rendererDefaults()['menu_items'],
                        'Buttons' => self::rendererDefaults()['buttons'],
                        'Icons' => self::rendererDefaults()['icons_buttons']
                    ]);
            },
            'menu.kos.classmap.editable' => function() { //kos
                return new ClassMap (
                    [
                        'MenuWrap' => [
                            'ul' => 'ui vertical massive menu'
                        ],
                        'LevelWrap' => [
                            'ul' => 'menu',
                            'ul.onpath' => 'menu onpath',
                        ],
                        'Item' => self::rendererDefaults()['menu_items'],
                        'Buttons' => self::rendererDefaults()['buttons'],
                        'Icons' => self::rendererDefaults()['icons_buttons']
                    ]);
            },
        ###########################
        # paper classmap
        ###########################
            'authored.classmap' => function() {
                return new ClassMap (
                    [
                     'Template' => [
                        'div.templateMultipage' => 'template-multipage',
                        'div.templatePaper' => 'template-paper',
                        'div.templateArticle' => 'template-article',
                        ],
                     'Headline' => [
                        'headline'=>'',
                        ],
                     'Perex' => [
                        'perex'=>'',
                        ],
                     'Content' => [
                        'content'=>'',
                        ],
                     'Buttons' => self::rendererDefaults()['buttons'],
                     'Icons' => self::rendererDefaults()['icons_buttons'],
                    ]
                );
            },
            'authored.editable.classmap' => function() {
                return new ClassMap (
                    [
                     'Template' => [
                        'div.templateMultipage' => 'template-multipage',
                        'div.templateMultipageTrash' => 'template-multipage trash',
                        'div.templatePaper' => 'template-paper',
                        'div.templatePaperTrash' => 'template-paper trash',
                        'div.templateArticle' => 'template-articleedit',
                        'div.templateArticleTrash' => 'template-articleedit trash',
                        'div.selectTemplate' => 'select_template',
                            // test
                        'div.tinySelectTemplate' => 'tiny_select_template borderDance',
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
                        'section.trash'=>'trash',
                        'div.semafor'=>'semafor',
                        'div.ribbon'=>'ui right ribbon grey inverted label cornerWithTools',
                        'ribbon.svg' => 'ribbon-svg',
                        'ribbon.priority' => 'content-priority',
                        'div.nameMenuItem'=> 'nadpis-polozky-menu',
                        'content'=>'borderDance edit-html',
                        'div.trash_content'=>'trash_content',
                        'div.grid' => 'ui grid',
                        'div.wholeRow' => 'sixteen wide column',
                        'div.halfRow' => 'eight wide column',
                        'div.calendar' => 'ui calendar',
                        'div.input' => 'ui input',
                        ],
                     'Buttons' => self::rendererDefaults()['buttons'],
                     'Icons' => self::rendererDefaults()['icons_buttons'],
                        //////
                     'PaperTemplateSelect' => self::rendererDefaults()['paper_template_select'],
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
