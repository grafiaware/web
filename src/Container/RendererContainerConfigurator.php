<?php
namespace Container;

use Site\Configuration;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

use Component\Renderer\Html\Authored\Paper\{
    SelectTemplateRenderer, PaperRenderer,
    ElementWrapper, ElementEditableWrapper, Buttons
};

use Component\Renderer\Html\Generated\{
    LanguageSelectRenderer, SearchPhraseRenderer, SearchResultRenderer, ItemTypeRenderer
};

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
                return new SelectTemplateRenderer($c->get('paper.classmap'), $c->get('paper.editable.classmap'));
            },
            PaperRenderer::class => function(ContainerInterface $c) {
                return new PaperRenderer($c->get('paper.classmap'), $c->get('paper.editable.classmap'));
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
