<?php
namespace Container;

use Site\Configuration;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

use Component\Renderer\Html\Menu\{
    MenuWrapRenderer, MenuWrapEditableRenderer, LevelWrapRenderer, ItemRenderer, ItemEditableRenderer, ItemBlockEditableRenderer, ItemTrashRenderer
};

use Component\Renderer\Html\Authored\{
    PaperRenderer, PaperEditableRenderer
};

use Component\Renderer\Html\Generated\{
    LanguageSelectRenderer, SearchPhraseRenderer, SearchResultRenderer, ItemTypeRenderer
};

use \Pes\View\Renderer\NodeRenderer;

/**
 *
 *
 * @author pes2704
 */
class RendererContainerConfigurator_Nav extends ContainerConfiguratorAbstract {

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

//        ###########################
//        # menu renderer
//        ###########################
//            'menu.presmerovani.menuwraprenderer' => function(ContainerInterface $c) {
//                return new MenuWrapRenderer($c->get('menu.presmerovani.classmap'));
//            },
//            'menu.presmerovani.levelwraprenderer' => function(ContainerInterface $c) {
//                return new LevelWrapRenderer($c->get('menu.presmerovani.classmap'));
//            },
//            'menu.presmerovani.itemrenderer' => function(ContainerInterface $c) {
//                return new ItemRenderer($c->get('menu.presmerovani.classmap'));
//            },
//            'menu.presmerovani.menuwraprenderer.editable' => function(ContainerInterface $c) {
//                return new MenuWrapEditableRenderer($c->get('menu.presmerovani.classmap.editable'));
//            },
//            'menu.presmerovani.levelwraprenderer.editable' => function(ContainerInterface $c) {
//                return new LevelWrapRenderer($c->get('menu.presmerovani.classmap.editable'));
//            },
//            'menu.presmerovani.itemrenderer.editable' => function(ContainerInterface $c) {
//                return new ItemEditableRenderer($c->get('menu.presmerovani.classmap.editable'));
//            },
//
//            'menu.vodorovne.menuwraprenderer' => function(ContainerInterface $c) {
//                return new MenuWrapRenderer($c->get('menu.vodorovne.classmap'));
//            },
//            'menu.vodorovne.levelwraprenderer' => function(ContainerInterface $c) {
//                return new LevelWrapRenderer($c->get('menu.vodorovne.classmap'));
//            },
//            'menu.vodorovne.itemrenderer' => function(ContainerInterface $c) {
//                return new ItemRenderer($c->get('menu.vodorovne.classmap'));
//            },
//            'menu.vodorovne.menuwraprenderer.editable' => function(ContainerInterface $c) {
//                return new MenuWrapEditableRenderer($c->get('menu.vodorovne.classmap.editable'));
//            },
//            'menu.vodorovne.levelwraprenderer.editable' => function(ContainerInterface $c) {
//                return new LevelWrapRenderer($c->get('menu.vodorovne.classmap.editable'));
//            },
//            'menu.vodorovne.itemrenderer.editable' => function(ContainerInterface $c) {
//                return new ItemEditableRenderer($c->get('menu.vodorovne.classmap.editable'));
//            },
//
//            'menu.svisle.menuwraprenderer' => function(ContainerInterface $c) {
//                return new MenuWrapRenderer($c->get('menu.svisle.classmap'));
//            },
//            'menu.svisle.levelwraprenderer' => function(ContainerInterface $c) {
//                return new LevelWrapRenderer($c->get('menu.svisle.classmap'));
//            },
//            'menu.svisle.itemrenderer' => function(ContainerInterface $c) {
//                return new ItemRenderer($c->get('menu.svisle.classmap'));
//            },
//            'menu.svisle.menuwraprenderer.editable' => function(ContainerInterface $c) {
//                return new MenuWrapEditableRenderer($c->get('menu.svisle.classmap.editable'));
//            },
//            'menu.svisle.levelwraprenderer.editable' => function(ContainerInterface $c) {
//                return new LevelWrapRenderer($c->get('menu.svisle.classmap.editable'));
//            },
//            'menu.svisle.itemrenderer.editable' => function(ContainerInterface $c) {
//                return new ItemEditableRenderer($c->get('menu.svisle.classmap.editable'));
//            },
//                   //bloky
//            'menu.bloky.menuwraprenderer.editable' => function(ContainerInterface $c) {
//                return new MenuWrapEditableRenderer($c->get('menu.bloky.classmap.editable'));
//            },
//            'menu.bloky.levelwraprenderer.editable' => function(ContainerInterface $c) {
//                return new LevelWrapRenderer($c->get('menu.bloky.classmap.editable'));
//            },
//            'menu.bloky.itemrenderer.editable' => function(ContainerInterface $c) {
//                return new ItemBlockEditableRenderer($c->get('menu.bloky.classmap.editable'));
//            },
//                    //kos
//            'menu.kos.menuwraprenderer' => function(ContainerInterface $c) {
//                return new MenuWrapRenderer($c->get('menu.kos.classmap'));
//            },
//            'menu.kos.levelwraprenderer' => function(ContainerInterface $c) {
//                return new LevelWrapRenderer($c->get('menu.kos.classmap'));
//            },
//            'menu.kos.itemrenderer' => function(ContainerInterface $c) {
//                return new ItemTrashRenderer($c->get('menu.kos.classmap'));
//            },
//            'menu.kos.menuwraprenderer.editable' => function(ContainerInterface $c) {
//                return new MenuWrapEditableRenderer($c->get('menu.kos.classmap'));
//            },
        ###########################
        # nav renderer
        ###########################
            'nav.presmerovani.menuwraprenderer' => function(ContainerInterface $c) {
                return new NodeRenderer($c->get('menu.presmerovani.classmap'));
            },
            'nav.presmerovani.menuwraprenderer.editable' => function(ContainerInterface $c) {
                return new NodeRenderer($c->get('menu.presmerovani.classmap.editable'));
            },

            'nav.vodorovne.menuwraprenderer' => function(ContainerInterface $c) {
                return new NodeRenderer($c->get('menu.vodorovne.classmap'));
            },
            'nav.vodorovne.menuwraprenderer.editable' => function(ContainerInterface $c) {
                return new NodeRenderer($c->get('menu.vodorovne.classmap.editable'));
            },

            'nav.svisle.menuwraprenderer' => function(ContainerInterface $c) {
                return new NodeRenderer($c->get('menu.svisle.classmap'));
            },
            'nav.svisle.menuwraprenderer.editable' => function(ContainerInterface $c) {
                return new NodeRenderer($c->get('menu.svisle.classmap.editable'));
            },
                   //bloky
            'nav.bloky.menuwraprenderer.editable' => function(ContainerInterface $c) {
                return new NodeRenderer($c->get('menu.bloky.classmap.editable'));
            },
                    //kos
            'nav.kos.menuwraprenderer' => function(ContainerInterface $c) {
                return new NodeRenderer($c->get('menu.kos.classmap'));
            },
            'nav.kos.menuwraprenderer.editable' => function(ContainerInterface $c) {
                return new NodeRenderer($c->get('menu.kos.classmap'));
            },
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
            PaperRenderer::class => function(ContainerInterface $c) {
                return new PaperRenderer($c->get('paper.classmap'));
            },
            PaperEditableRenderer::class => function(ContainerInterface $c) {
                return new PaperEditableRenderer($c->get('paper.editable.classmap'));
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
