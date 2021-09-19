<?php
namespace Container;

use Site\Configuration;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

use Component\Renderer\Html\Authored\Paper\SelectTemplateRenderer;
use Component\Renderer\Html\Authored\Paper\ButtonsRenderer;
use Component\Renderer\Html\Authored\Paper\PaperRenderer;
use Component\Renderer\Html\Authored\Paper\PaperRendererEditable;
use Component\Renderer\Html\Authored\Paper\ElementWrapper;
use Component\Renderer\Html\Authored\Paper\ElementEditableWrapper;
use Component\Renderer\Html\Authored\Paper\Buttons;
use Component\Renderer\Html\Authored\Paper\HeadlineRenderer;
use Component\Renderer\Html\Authored\Paper\PerexRenderer;
use Component\Renderer\Html\Authored\Paper\ContentsRenderer;
use Component\Renderer\Html\Authored\Paper\HeadlineRendererEditable;
use Component\Renderer\Html\Authored\Paper\PerexRendererEditable;
use Component\Renderer\Html\Authored\Paper\ContentsRendererEditable;

use Component\Renderer\Html\Authored\Article\ArticleRendererEditable;
use Component\Renderer\Html\Authored\Article\SelectArticleTemplateRenderer;

use Component\Renderer\Html\Authored\Multipage\MultipageRendererEditable;

use Component\Renderer\Html\Status\ButtonEditContentRenderer;

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
        # paper renderer
        ###########################
            SelectTemplateRenderer::class => function(ContainerInterface $c) {
                return new SelectTemplateRenderer($c->get('paper.editable.classmap'));
            },
            ButtonsRenderer::class => function(ContainerInterface $c) {
                return new ButtonsRenderer($c->get('paper.editable.classmap'));
            },
            PaperRenderer::class => function(ContainerInterface $c) {
                return new PaperRenderer($c->get('paper.classmap'));
            },
            PaperRendererEditable::class => function(ContainerInterface $c) {
                return new PaperRendererEditable($c->get('paper.editable.classmap'));
            },
            ElementWrapper::class => function(ContainerInterface $c) {
                return new ElementWrapper($c->get('paper.classmap'));
            },
            ElementEditableWrapper::class => function(ContainerInterface $c) {
                return new ElementEditableWrapper($c->get('paper.editable.classmap'));
            },
            Buttons::class => function(ContainerInterface $c) {
                return new Buttons($c->get('paper.editable.classmap'));
            },
            HeadlineRenderer::class => function(ContainerInterface $c) {
                return new HeadlineRenderer($c->get('paper.classmap'));
            },
            PerexRenderer::class => function(ContainerInterface $c) {
                return new PerexRenderer($c->get('paper.classmap'));
            },
            ContentsRenderer::class => function(ContainerInterface $c) {
                return new ContentsRenderer($c->get('paper.classmap'));
            },
            HeadlineRendererEditable::class => function(ContainerInterface $c) {
                return new HeadlineRendererEditable($c->get('paper.editable.classmap'));
            },
            PerexRendererEditable::class => function(ContainerInterface $c) {
                return new PerexRendererEditable($c->get('paper.editable.classmap'));
            },
            ContentsRendererEditable::class => function(ContainerInterface $c) {
                return new ContentsRendererEditable($c->get('paper.editable.classmap'));
            },
        ###########################
        #  status renderer
        ###########################
            ButtonEditContentRenderer::class => function(ContainerInterface $c) {
                return new ButtonEditContentRenderer($c->get('paper.editable.classmap'));
            },
        ###########################
        #  article renderer
        ###########################
            SelectArticleTemplateRenderer::class => function(ContainerInterface $c) {
                return new SelectArticleTemplateRenderer($c->get('paper.editable.classmap'));   //používá paper classmapu - přejmenovat společnou classmapu??
            },
            ArticleRendererEditable::class => function(ContainerInterface $c) {
                return new ArticleRendererEditable($c->get('paper.editable.classmap'));   //používá paper classmapu - přejmenovat společnou classmapu??
            },
        ###########################
        #  multipage renderer
        ###########################
        MultipageRendererEditable::class => function(ContainerInterface $c) {
                return new MultipageRendererEditable($c->get('paper.editable.classmap'));   //používá paper classmapu - přejmenovat společnou classmapu??
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
