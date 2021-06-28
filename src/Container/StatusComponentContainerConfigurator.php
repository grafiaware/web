<?php
namespace Container;

use Site\Configuration;

use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}


// logger
use Pes\Logger\FileLogger;

// renderer kontejner
use Pes\Container\Container;
use Container\RendererContainerConfigurator;

// template renderer container
use Pes\View\Renderer\Container\TemplateRendererContainer;

// template
use Pes\View\Template\PhpTemplate;

use Component\View\Flash\FlashComponent;

// viewModel
use Component\ViewModel\Flash\FlashViewModel;

// repo
use Status\Model\Repository\{StatusSecurityRepo, StatusPresentationRepo, StatusFlashRepo};

// controller
use \Status\Middleware\Component\Controller\StatusComponentControler;

// renderery - pro volání služeb renderer kontejneru renderer::class
use Component\Renderer\Html\Flash\FlashRenderer;

// view
use Pes\View\View;
use Pes\View\CompositeView;

// view factory

use \Pes\View\ViewFactory;
/**
 *
 *
 * @author pes2704
 */
class StatusComponentContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams() {
        return Configuration::component();
    }

    public function getAliases() {
        return [];
    }

    public function getServicesDefinitions() {
        return [
            'rendererContainer' => function(ContainerInterface $c) {
                // POZOR - TemplateRendererContainer "má" definovanou službu (metoda ->has() vrací true) pro každé jméno služby, pro které existuje třída!
                // služby RendererContainerConfigurator, které jsou přímo jménem třídy (XxxRender::class) musí být konfigurovány v metodě getServicesOverrideDefinitions()
                return (new RendererContainerConfigurator())->configure(new Container(new TemplateRendererContainer()));
            },
            StatusComponentControler::class => function(ContainerInterface $c) {
                return (new StatusComponentControler(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusFlashRepo::class),
                            $c->get(StatusPresentationRepo::class))
                        )->injectContainer($c);  // inject component kontejner
            },

        ];
    }

    public function getFactoriesDefinitions() {
        return [
        ####
        # view
        #
            View::class => function(ContainerInterface $c) {
                return (new View())->setRendererContainer($c->get('rendererContainer'));
            },
            CompositeView::class => function(ContainerInterface $c) {
                return (new CompositeView())->setRendererContainer($c->get('rendererContainer'));
            },

        ####
        # view factory
        #
            ViewFactory::class => function(ContainerInterface $c) {
                return (new ViewFactory())->setRendererContainer($c->get('rendererContainer'));
            },


            // FlashComponent s vlastním rendererem
//            FlashComponent::class => function(ContainerInterface $c) {
//                $viewModel = new FlashViewModelForRenderer($c->get(StatusFlashRepo::class));
//                return (new FlashComponent($viewModel))->setRendererContainer($c->get('rendererContainer'))->setRendererName(FlashRenderer::class);
//            },

            // komponenty s PHP template
            // - cesty k souboru template jsou definovány v konfiguraci - předány do kontejneru jako parametry setParams()
            FlashComponent::class => function(ContainerInterface $c) {
                $viewModel = new FlashViewModel($c->get(StatusFlashRepo::class));
                return (new FlashComponent($viewModel))->setRendererContainer($c->get('rendererContainer'))->setTemplate(new PhpTemplate($c->get('component.template.'.FlashComponent::class)));
            },

        ];
    }

    public function getServicesOverrideDefinitions() {
        return [];
    }
}
