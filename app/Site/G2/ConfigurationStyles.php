<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\G2;

use Pes\View\Renderer\ClassMap\ClassMap;
use Red\Component\Renderer\Html\Menu\LevelRenderer;
use Red\Component\Renderer\Html\Menu\MenuWrapEditableRenderer;
use Red\Component\Renderer\Html\Menu\ItemRenderer;
use Red\Component\Renderer\Html\Menu\ItemRendererEditable;
use Red\Component\Renderer\Html\Menu\ItemBlockRenderer;
use Red\Component\Renderer\Html\Menu\ItemBlockRendererEditable;
use Red\Component\Renderer\Html\Menu\ItemTrashRenderer;
use Red\Component\Renderer\Html\Menu\ItemTrashRendererEditable;

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
                            'button.disabled' => 'ui button disabled',
                            'div.buttons' => 'editSize ui basic icon buttons',
                            'div.buttonsRow' => 'ui basic icons',
                            'div.buttonsChangeView' => 'ui button nested',                       //  'line' nebo 'ui button nested'
                            'div.buttonsChangeViewGroup' => 'ui basic icon buttons',             //  'ui basic icon' (pro line) nebo 'ui basic icon buttons' (pro nested)
                            'button.paste' => 'ui button paste',
                            'div.buttonsWrap' => 'contentButtons page-edit',
                            'div.ribbon-article' => 'ui right ribbon basic label cornerWithTools page-edit',
                            'div.editMode' => 'zapnout_editaci',
                            'button.editMode' => 'ui editingButtons_size teal icon button',
                            'button.editMode.disabled' => 'ui editingButtons_size teal icon button disabled',
                            'button.offEditMode' => 'ui editingButtons_size teal basic icon button',
                            'div.wrapTrash' => 'contentButtons trash',
                            'div.wrapContent' => 'contentButtons',
                            'div.wrapShowDate' => 'calendarWrap editShowDate',
                            'div.wrapEventDate' => 'calendarWrap editEventDate',
                            'div.buttonsEditDate' => 'editingButtons_size ui basic icon buttons editDate',
                            'div.buttonsContent' => 'editingButtons_size ui basic icon buttons editContent',
                            'button.showDate' => 'ui button toolsShowDate',
                            'button.eventDate' => 'ui button toolsEventDate',
                            'button.content' => 'ui button hideCalendarWrap',

                        ],
            'icons_buttons' => [
                            'icon.notpublish' => 'green toggle on icon',
                            'icon.publish' => 'red toggle off icon',
                            'icon.cut' => 'cut icon',
                            'icon.cutted' => 'red cut icon',
                            'icon.copy' => 'copy icon',
                            'icon.plus' => 'plus icon',
                            'icon.object' => 'object ungroup icon',
                            'icon.addsiblings' => 'add circle icon',
                            'icon.movetotrash' => 'purple trash icon',
                            'icon.addchildren' => ' arrow circle right icon',
                            'icons' => 'icons',
                            'icon.delete' => 'trash icon',
                            'icon.exclamation' => 'corner red exclamation icon',
                            'icon.templateSelect' => 'clone outline icon',
                            'icon.template' => 'stamp alternate icon',
                            'icon.templateremove' => 'stamp red alternate icon',
                            'icon.arrange' => 'sort numeric down icon',
                            'icon.editMode' => 'pencil alternate icon',
                            'icon.changedisplaydate' => 'violet calendar alternate icon',
                            'icon.changeeventdate' => 'yellow money check icon',
                            'icon.arrowup' => 'top right corner arrow up icon',
                            'icon.arrowdown' => 'bottom right corner arrow down icon',
                            'icon.movecontent' => 'sticky note outline icon',
                            'icon.addcontent' => 'plus square outline icon',
                            'icon.permanently' => 'calendar outline icon',
                            'icon.save' => 'save icon',
                            'icon.cancel' => 'red times circle icon',
                            'icon.restore' => 'sync icon',
                            'icon.clipboard' => 'clipboard outline icon',
                            'semafor.published' => 'circle icon green',
                            'semafor.notpublished' => 'circle icon red',
                            'semafor.actual' => 'clock outline icon green',
                            'semafor.notactual' => 'clock outline icon red',
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
                            'div.hidden' => 'hidden content',
                            'div.visible' => 'visible content',
                            'div i' => 'file alternate teal icon',
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
            'menu.presmerovani.levelRenderer' => function(ContainerInterface $c) {
                return new LevelRenderer($c->get('menu.presmerovani.classmap'));
            },

            'menu.vodorovne.levelRenderer' => function(ContainerInterface $c) {
                return new LevelRenderer($c->get('menu.vodorovne.classmap'));
            },

            'menu.svisle.levelRenderer' => function(ContainerInterface $c) {
                return new LevelRenderer($c->get('menu.svisle.classmap'));
            },

            //bloky
            'menu.bloky.levelRenderer' => function(ContainerInterface $c) {
                return new LevelRenderer($c->get('menu.svisle.classmap'));
            },

            //kos
            'menu.kos.levelRenderer' => function(ContainerInterface $c) {
                return new LevelRenderer($c->get('menu.svisle.classmap'));
            },

        ###########################
        # menu classmap
        ###########################
            'menu.presmerovani.classmap' => function() {
                return new ClassMap (
                    [
                        'Level' => [
                            'ul.lastLevel' => 'ui text menu',
                            'ul' => 'menu'
                        ],
                    ]);
            },
            'menu.presmerovani.classmap.editable' => function() {
                return new ClassMap (
                    [
                        'Level' => [
                            'ul.lastLevel' => 'ui text menu',
                            'ul' => 'menu'
                        ],
                    ]
                );
            },

            'menu.vodorovne.classmap' => function() {
                return new ClassMap (
                    [
                        'Level' => [
                            'ul.lastLevel' => 'ui text menu left floated',
                            'ul' => 'menu'
                        ],
                    ]);
            },
            'menu.vodorovne.classmap.editable' => function() {
                return new ClassMap (
                    [
                        'Level' => [
                            'ul.lastLevel' => 'ui text menu left floated',
                            'ul' => 'menu'
                        ],
                        'Item' => array_merge(self::rendererDefaults()['menu_items'],
                            [
                            'li a' => 'ui primary button',
                            ]),
                    ]
                );
            },
            'menu.svisle.classmap' => function() {
                return new ClassMap (
                    [
                        'Level' => [
                            'ul.lastLevel' => 'ui vertical menu hidden-submenu', //hidden-submenu pro rozbalení submenu po kliknutí //ui text menu left floated vodorovne_menu
                            'ul' => 'menu onpath',
                            ],
                    ]);
            },
            'menu.svisle.classmap.editable' => function() {
                return new ClassMap (
                    [
                        'Level' => [
                            'ul.lastLevel' => 'ui vertical menu',
                            'ul' => 'menu onpath',
                        ],
                    ]);
            },
            'menu.bloky.classmap.editable' => function() { //bloky
                return new ClassMap (
                    [
                        'Level' => [
                            'ul.lastLevel' => 'hlavni-menu',
                            'ul' => 'menu onpath',
                        ],
                    ]);
            },
            'menu.kos.classmap.editable' => function() { //kos
                return new ClassMap (
                    [
                        'Level' => [
                            'ul.lastLevel' => 'hlavni-menu menu',
                            'ul' => 'menu onpath',
                        ],
                    ]);
            },
            'menu.item.classmap' => function() {
                return new ClassMap (
                    [
                        'Item' => self::rendererDefaults()['menu_items'],
                    ]);
            },
            'menu.item.classmap.editable' => function() {
                return new ClassMap (
                    [
                        'Item' => self::rendererDefaults()['menu_items'],
                        'Buttons' => self::rendererDefaults()['buttons'],
                        'Icons' => self::rendererDefaults()['icons_buttons']
                    ]);
            },
            'menu.itembuttons.classmap' => function() {
                return new ClassMap (
                    [
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
                        'section'=>'',
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
                        'div.templateMultipage' => 'template-multipageedit',
                        'div.templateMultipageTrash' => 'template-multipageedit trash',
                        'div.templatePaper' => 'template-paperedit',
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
                        'div.semafor-radek'=>'semafor-radek',
                        'div.ribbon'=>'ui right ribbon grey inverted label cornerWithTools',
                        'ribbon.svg' => 'ribbon-svg',
                        'ribbon.priority' => 'content-priority',
                        'div.nameMenuItem'=> 'nadpis-polozky-menu',
                        'content.edit-html'=>'borderDance edit-html',
                        'div.trash_content'=>'trash_content',
                        'div.grid' => 'ui grid',
                        'div.wholeRow' => 'sixteen wide column',
                        'div.halfRow' => 'eight wide column',
                        'div.calendar' => 'ui calendar',
                        'div.input' => 'ui input',
                        ],
                     'Buttons' => self::rendererDefaults()['buttons'],
                     'Icons' => self::rendererDefaults()['icons_buttons'],
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
