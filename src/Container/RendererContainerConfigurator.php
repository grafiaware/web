<?php
namespace Container;

use Site\Configuration;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

use Component\Renderer\Html\Authored\Paper\SelectTemplateRendererOld;
use Component\Renderer\Html\Authored\Paper\ButtonsRenderer;
use Component\Renderer\Html\Authored\Paper\PaperRenderer;
use Component\Renderer\Html\Authored\Paper\PaperRendererEditable;
use Component\Renderer\Html\Authored\Paper\ElementWrapper;
use Component\Renderer\Html\Authored\Paper\ElementEditableWrapper;
use Component\Renderer\Html\Authored\Paper\Buttons;
use Component\Renderer\Html\Authored\Paper\HeadlineRenderer;
use Component\Renderer\Html\Authored\Paper\PerexRenderer;
use Component\Renderer\Html\Authored\Paper\SectionsRenderer;
use Component\Renderer\Html\Authored\Paper\HeadlineRendererEditable;
use Component\Renderer\Html\Authored\Paper\PerexRendererEditable;
use Component\Renderer\Html\Authored\Paper\SectionsRendererEditable;

use Component\Renderer\Html\Authored\Article\ArticleRenderer;
use Component\Renderer\Html\Authored\Article\ArticleRendererEditable;
use Component\Renderer\Html\Authored\SelectTemplateRenderer;

use Component\Renderer\Html\Authored\Multipage\MultipageRenderer;
use Component\Renderer\Html\Authored\Multipage\MultipageRendererEditable;

use Component\Renderer\Html\Manage\ToggleEditContentButtomRenderer;

use Component\Renderer\Html\Generated\LanguageSelectRenderer;
use Component\Renderer\Html\Generated\SearchPhraseRenderer;
use Component\Renderer\Html\Generated\SearchResultRenderer;
use Component\Renderer\Html\Generated\ItemTypeRenderer;

/**
 *
 *
 * @author pes2704
 */
class RendererContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams() {
        return Configuration::renderer();
    }

    public function getAliases() {
        return [
//            PhpTemplateRendererInterface::class => PhpTemplateRenderer::class,
        ];
    }

    public function getServicesDefinitions() {
        return [


        ];
    }

    public function getFactoriesDefinitions() {
        return [

        ];
    }

    public function getServicesOverrideDefinitions() {
        return [
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
            ElementWrapper::class => function(ContainerInterface $c) {
                return new ElementWrapper($c->get('authored.classmap'));
            },
            ElementEditableWrapper::class => function(ContainerInterface $c) {
                return new ElementEditableWrapper($c->get('authored.editable.classmap'));
            },
            Buttons::class => function(ContainerInterface $c) {
                return new Buttons($c->get('authored.editable.classmap'));
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
        #  status renderer
        ###########################
            ToggleEditContentButtomRenderer::class => function(ContainerInterface $c) {
                return new ToggleEditContentButtomRenderer($c->get('authored.editable.classmap'));
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
            ItemTypeRenderer::class => function(ContainerInterface $c) {
                return new ItemTypeRenderer();
            },
        ###########################
        #  default template renderer
        ###########################
//            PhpTemplateRenderer::class => function(ContainerInterface $c) {
//                return new PhpTemplateRenderer();
//            },
        ];
    }
}
