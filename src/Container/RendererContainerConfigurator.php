<?php
namespace Container;

use Site\ConfigurationCache;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

use Pes\View\Renderer\PhpTemplateRenderer;

use Component\Renderer\Html\Menu\ItemRenderer;
use Component\Renderer\Html\Menu\ItemRendererEditable;
use Component\Renderer\Html\Menu\ItemBlockRenderer;
use Component\Renderer\Html\Menu\ItemBlockRendererEditable;
use Component\Renderer\Html\Menu\ItemTrashRenderer;
use Component\Renderer\Html\Menu\ItemTrashRendererEditable;

use Component\Renderer\Html\Authored\Paper\ButtonsRenderer;
use Component\Renderer\Html\Authored\Paper\PaperRenderer;
use Component\Renderer\Html\Authored\Paper\PaperRendererEditable;
use Component\Renderer\Html\Manage\SelectTemplateRenderer;

//use Component\Renderer\Html\Authored\Paper\ElementWrapper;
//use Component\Renderer\Html\Authored\Paper\ElementEditableWrapper;
//use Component\Renderer\Html\Authored\Paper\Buttons;

use Component\Renderer\Html\Authored\Paper\HeadlineRenderer;
use Component\Renderer\Html\Authored\Paper\PerexRenderer;
use Component\Renderer\Html\Authored\Paper\SectionsRenderer;
use Component\Renderer\Html\Authored\Paper\HeadlineRendererEditable;
use Component\Renderer\Html\Authored\Paper\PerexRendererEditable;
use Component\Renderer\Html\Authored\Paper\SectionsRendererEditable;

use Component\Renderer\Html\Authored\Article\ArticleRenderer;
use Component\Renderer\Html\Authored\Article\ArticleRendererEditable;

use Component\Renderer\Html\Authored\Multipage\MultipageRenderer;
use Component\Renderer\Html\Authored\Multipage\MultipageRendererEditable;

use Component\Renderer\Html\Manage\EditContentSwitchRenderer;
use Component\Renderer\Html\Manage\EditContentSwitchOffRenderer;
use Component\Renderer\Html\Manage\EditContentSwitchDisabledRenderer;

use Component\Renderer\Html\Generated\LanguageSelectRenderer;
use Component\Renderer\Html\Generated\SearchPhraseRenderer;
use Component\Renderer\Html\Generated\SearchResultRenderer;
use Component\Renderer\Html\Authored\TypeSelect\ItemTypeSelectRenderer;

use Pes\View\Renderer\ImplodeRenderer;
use Pes\View\Renderer\InterpolateRenderer;
use Component\Renderer\Html\NoPermittedContentRenderer;
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

        ###########################
        # menu item renderer
        ###########################
            ItemRenderer::class => function(ContainerInterface $c) {
                return new ItemRenderer($c->get('menu.item.classmap'));
            },
            ItemRendererEditable::class => function(ContainerInterface $c) {
                return new ItemRendererEditable($c->get('menu.item.classmap.editable'));
            },
            ItemBlockRenderer::class => function(ContainerInterface $c) {
                return new ItemBlockRenderer($c->get('menu.item.classmap'));
            },
            ItemBlockRendererEditable::class => function(ContainerInterface $c) {
                return new ItemBlockRendererEditable($c->get('menu.item.classmap.editable'));
            },
            ItemTrashRenderer::class => function(ContainerInterface $c) {
                return new ItemTrashRenderer($c->get('menu.item.classmap'));
            },
            ItemTrashRendererEditable::class => function(ContainerInterface $c) {
                return new ItemTrashRendererEditable($c->get('menu.item.classmap.editable'));
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
//
//            ElementWrapper::class => function(ContainerInterface $c) {
//                return new ElementWrapper($c->get('authored.classmap'));
//            },
//            ElementEditableWrapper::class => function(ContainerInterface $c) {
//                return new ElementEditableWrapper($c->get('authored.editable.classmap'));
//            },
//            Buttons::class => function(ContainerInterface $c) {
//                return new Buttons($c->get('authored.editable.classmap'));
//            },

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
