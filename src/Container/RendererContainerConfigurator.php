<?php
namespace Container;

use Site\ConfigurationCache;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

use Pes\View\Renderer\PhpTemplateRenderer;


use Red\Component\Renderer\Html\Menu\MenuRenderer;
use Red\Component\Renderer\Html\Menu\LevelRenderer;
use Red\Component\Renderer\Html\Menu\LevelRendererEditable;

use Red\Component\Renderer\Html\Menu\ItemRenderer;
use Red\Component\Renderer\Html\Menu\DriverRenderer;
use Red\Component\Renderer\Html\Menu\DriverRendererEditable;
use Red\Component\Renderer\Html\Menu\DriverPresentedRenderer;
use Red\Component\Renderer\Html\Menu\DriverPresentedRendererEditable;

use Red\Component\Renderer\Html\Content\Authored\Paper\ButtonsRenderer;
use Red\Component\Renderer\Html\Content\Authored\Paper\PaperRenderer;
use Red\Component\Renderer\Html\Content\Authored\Paper\PaperRendererEditable;
use Red\Component\Renderer\Html\Manage\SelectTemplateRenderer;

use Red\Component\Renderer\Html\Content\Authored\Paper\HeadlineRenderer;
use Red\Component\Renderer\Html\Content\Authored\Paper\PerexRenderer;
use Red\Component\Renderer\Html\Content\Authored\Paper\SectionsRenderer;
use Red\Component\Renderer\Html\Content\Authored\Paper\HeadlineRendererEditable;
use Red\Component\Renderer\Html\Content\Authored\Paper\PerexRendererEditable;
use Red\Component\Renderer\Html\Content\Authored\Paper\SectionsRendererEditable;

use Red\Component\Renderer\Html\Content\Authored\Article\ArticleRenderer;
use Red\Component\Renderer\Html\Content\Authored\Article\ArticleRendererEditable;

use Red\Component\Renderer\Html\Content\Authored\Multipage\MultipageRenderer;
use Red\Component\Renderer\Html\Content\Authored\Multipage\MultipageRendererEditable;

use Red\Component\Renderer\Html\Manage\EditContentSwitchRenderer;
use Red\Component\Renderer\Html\Manage\EditContentSwitchOffRenderer;
use Red\Component\Renderer\Html\Manage\EditContentSwitchDisabledRenderer;

use Red\Component\Renderer\Html\Manage\ButtonsItemManipulationRenderer;
use Red\Component\Renderer\Html\Manage\ButtonsMenuAddRenderer;
use Red\Component\Renderer\Html\Manage\ButtonsMenuAddOnelevelRenderer;
use Red\Component\Renderer\Html\Manage\ButtonsMenuPasteRenderer;
use Red\Component\Renderer\Html\Manage\ButtonsMenuPasteOnelevelRenderer;
use Red\Component\Renderer\Html\Manage\ButtonsMenuCutCopyRenderer;
use Red\Component\Renderer\Html\Manage\ButtonsMenuCutCopyEscapeRenderer;
use Red\Component\Renderer\Html\Manage\ButtonsMenuDeleteRenderer;

use Red\Component\Renderer\Html\Generated\LanguageSelectRenderer;
use Red\Component\Renderer\Html\Generated\SearchPhraseRenderer;
use Red\Component\Renderer\Html\Generated\SearchResultRenderer;
use Red\Component\Renderer\Html\Content\TypeSelect\ItemTypeSelectRenderer;

use Pes\View\Renderer\ImplodeRenderer;
use Pes\View\Renderer\InterpolateRenderer;
use Component\Renderer\Html\NoPermittedContentRenderer;
use Component\Renderer\Html\NoContentForStatusRenderer;

/**
 *
 *
 * @author pes2704
 */
class RendererContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams(): iterable {
        return [];
    }

    public function getAliases(): iterable {
        return [
            PhpTemplateRendererInterface::class => PhpTemplateRenderer::class,
        ];
    }

    /**
     * ###############  POZOR! ###############
     * Všechny renderery jsou vyráběny jako service (singleton). Ve skutečnosti většina z nich dědí HtmlRendererAbstract, který má konstruktor a přijímá classMap.
     * To znamená, že render jednoho typu vznikne jen jeden a používá se stéle tentýž renderer se stejnou class mapou
     * #######################################
     *
     * @return iterable
     */
    public function getServicesDefinitions(): iterable {
        return array_merge(ConfigurationCache::renderer(),
                [
        ImplodeRenderer::class => function(ContainerInterface $c) {
            return new ImplodeRenderer();
        },
        InterpolateRenderer::class => function(ContainerInterface $c) {
            return new InterpolateRenderer();
        },
        NoPermittedContentRenderer::class => function(ContainerInterface $c) {
            return new NoPermittedContentRenderer();
        },
        NoContentForStatusRenderer::class => function(ContainerInterface $c) {
            return new NoContentForStatusRenderer();
        },

        // menu

        ButtonsItemManipulationRenderer::class => function(ContainerInterface $c) {
            return new ButtonsItemManipulationRenderer($c->get('menu.itembuttons.classmap'));
        },
        ButtonsMenuAddRenderer::class => function(ContainerInterface $c) {
            return new ButtonsMenuAddRenderer($c->get('menu.itembuttons.classmap'));
        },
        ButtonsMenuAddOnelevelRenderer::class => function(ContainerInterface $c) {
            return new ButtonsMenuAddOnelevelRenderer($c->get('menu.itembuttons.classmap'));
        },
        ButtonsMenuPasteRenderer::class => function(ContainerInterface $c) {
            return new ButtonsMenuPasteRenderer($c->get('menu.itembuttons.classmap'));
        },
        ButtonsMenuPasteOnelevelRenderer::class => function(ContainerInterface $c) {
            return new ButtonsMenuPasteOnelevelRenderer($c->get('menu.itembuttons.classmap'));
        },
        ButtonsMenuCutCopyRenderer::class => function(ContainerInterface $c) {
            return new ButtonsMenuCutCopyRenderer($c->get('menu.itembuttons.classmap'));
        },
        ButtonsMenuCutCopyEscapeRenderer::class => function(ContainerInterface $c) {
            return new ButtonsMenuCutCopyEscapeRenderer($c->get('menu.itembuttons.classmap'));
        },
        ButtonsMenuDeleteRenderer::class => function(ContainerInterface $c) {
            return new ButtonsMenuDeleteRenderer($c->get('menu.itembuttons.classmap'));
        },

        MenuRenderer::class => function(ContainerInterface $c) {
            return new MenuRenderer();
        },
        ###########################
        # menu renderer
        ###########################
            'menu.svisle.levelRenderer' => function(ContainerInterface $c) {
                return new LevelRenderer($c->get('menu.svisle.classmap'));
            },
            'menu.svisle.levelRenderer.editable' => function(ContainerInterface $c) {
                return new LevelRenderer($c->get('menu.svisle.classmap.editable'));
            },
            'menu.presmerovani.levelRenderer' => function(ContainerInterface $c) {
                return new LevelRenderer($c->get('menu.presmerovani.classmap'));
            },

            'menu.vodorovne.levelRenderer' => function(ContainerInterface $c) {
                return new LevelRenderer($c->get('menu.vodorovne.classmap'));
            },                    
            //bloky
            'menu.bloky.levelRenderer' => function(ContainerInterface $c) {
                return new LevelRenderer($c->get('menu.bloky.classmap'));
            },
            'menu.bloky.levelRenderer.editable' => function(ContainerInterface $c) {
                return new LevelRenderer($c->get('menu.bloky.classmap.editable'));
            },
            //kos
            'menu.kos.levelRenderer' => function(ContainerInterface $c) {
                return new LevelRenderer($c->get('menu.kos.classmap'));
            },
            'menu.kos.levelRenderer.editable' => function(ContainerInterface $c) {
                return new LevelRenderer($c->get('menu.kos.classmap.editable'));
            },                          
        ###########################
        # menu item renderer
        ###########################
            ItemRenderer::class => function(ContainerInterface $c) {
                return new ItemRenderer($c->get('menu.item.classmap'));
            },
//            ItemRendererEditable::class => function(ContainerInterface $c) {
//                return new ItemRendererEditable($c->get('menu.item.classmap.editable'));
//            },
//            NodeBlockRenderer::class => function(ContainerInterface $c) {
//                return new NodeBlockRenderer($c->get('menu.item.classmap'));
//            },
//            NodeBlockRendererEditable::class => function(ContainerInterface $c) {
//                return new NodeBlockRendererEditable($c->get('menu.item.classmap.editable'));
//            },
//            NodeTrashRenderer::class => function(ContainerInterface $c) {
//                return new NodeTrashRenderer($c->get('menu.item.classmap'));
//            },
//            NodeTrashRendererEditable::class => function(ContainerInterface $c) {
//                return new NodeTrashRendererEditable($c->get('menu.item.classmap.editable'));
//            },
        ###########################
        # menu driver renderer
        ###########################
            DriverRenderer::class => function(ContainerInterface $c) {
                return new DriverRenderer($c->get('menu.item.classmap'));
            },
            DriverRendererEditable::class => function(ContainerInterface $c) {
                return new DriverRendererEditable($c->get('menu.item.classmap.editable'));
            },
            DriverPresentedRenderer::class => function(ContainerInterface $c) {
                return new DriverPresentedRenderer($c->get('menu.item.classmap'));
            },
            DriverPresentedRendererEditable::class => function(ContainerInterface $c) {
                return new DriverPresentedRendererEditable($c->get('menu.item.classmap.editable'));
            },
        ###########################
        # select template renderer
        ###########################
            SelectTemplateRenderer::class => function(ContainerInterface $c) {
                return new SelectTemplateRenderer($c->get('authored.editable.classmap'));  // -> selecttemplate.editable.classmap
            },
        ###########################
        # paper renderer
        ###########################
            ButtonsRenderer::class => function(ContainerInterface $c) {
                return new ButtonsRenderer($c->get('authored.editable.classmap'));
            },
            PaperRenderer::class => function(ContainerInterface $c) {
                return new PaperRenderer($c->get('authored.classmap'));
            },
            PaperRendererEditable::class => function(ContainerInterface $c) {
                return new PaperRendererEditable($c->get('authored.editable.classmap'));
            },

            HeadlineRenderer::class => function(ContainerInterface $c) {
                return new HeadlineRenderer($c->get('authored.classmap'));
            },
            PerexRenderer::class => function(ContainerInterface $c) {
                return new PerexRenderer($c->get('authored.classmap'));
            },
            SectionsRenderer::class => function(ContainerInterface $c) {
                return new SectionsRenderer($c->get('authored.classmap'));
            },
            HeadlineRendererEditable::class => function(ContainerInterface $c) {
                return new HeadlineRendererEditable($c->get('authored.editable.classmap'));
            },
            PerexRendererEditable::class => function(ContainerInterface $c) {
                return new PerexRendererEditable($c->get('authored.editable.classmap'));
            },
            SectionsRendererEditable::class => function(ContainerInterface $c) {
                return new SectionsRendererEditable($c->get('authored.editable.classmap'));
            },
        ###########################
        #  edit content renderer
        ###########################
            EditContentSwitchRenderer::class => function(ContainerInterface $c) {
                return new EditContentSwitchRenderer($c->get('authored.editable.classmap'));
            },
            EditContentSwitchOffRenderer::class => function(ContainerInterface $c) {
                return new EditContentSwitchOffRenderer($c->get('authored.editable.classmap'));
            },
            EditContentSwitchDisabledRenderer::class => function(ContainerInterface $c) {
                return new EditContentSwitchDisabledRenderer($c->get('authored.editable.classmap'));
            },
        ###########################
        #  article renderer
        ###########################
            ArticleRenderer::class => function(ContainerInterface $c) {
                return new ArticleRenderer($c->get('authored.classmap'));   //používá paper classmapu - přejmenovat společnou classmapu??
            },
            ArticleRendererEditable::class => function(ContainerInterface $c) {
                return new ArticleRendererEditable($c->get('authored.editable.classmap'));   //používá paper classmapu - přejmenovat společnou classmapu??
            },
        ###########################
        #  multipage renderer
        ###########################
            MultipageRenderer::class => function(ContainerInterface $c) {
                return new MultipageRenderer($c->get('authored.classmap'));   //používá paper classmapu - přejmenovat společnou classmapu??
            },
            MultipageRendererEditable::class => function(ContainerInterface $c) {
                return new MultipageRendererEditable($c->get('authored.editable.classmap'));   //používá paper classmapu - přejmenovat společnou classmapu??
            },
        ###########################
        #  generated renderer
        ###########################
            LanguageSelectRenderer::class => function(ContainerInterface $c) {
                return new LanguageSelectRenderer($c->get('generated.languageselect.classmap'));
            },
            SearchPhraseRenderer::class => function(ContainerInterface $c) {
                return new SearchPhraseRenderer();
            },
            SearchResultRenderer::class => function(ContainerInterface $c) {
                return new SearchResultRenderer();
            },
            ItemTypeSelectRenderer::class => function(ContainerInterface $c) {
                return new ItemTypeSelectRenderer();
            },
        ###########################
        #  default template renderer
        ###########################
            PhpTemplateRenderer::class => function(ContainerInterface $c) {
                return new PhpTemplateRenderer();
            },
        ]);

    }

    public function getFactoriesDefinitions(): iterable {
        return [

        ];
    }
}
