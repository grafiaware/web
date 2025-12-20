<?php
namespace Container;

use Site\ConfigurationCache;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Pes\Container\Container;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// controller


// configuration
use Configuration\ComponentConfiguration;
use Configuration\ComponentConfigurationInterface;

// logger
use Pes\Logger\FileLogger;

// renderer kontejner
use Container\RendererContainerConfigurator;

// template renderer container
use Pes\View\Renderer\Container\TemplateRendererContainer;

// template + renderer
use Pes\View\Renderer\PhpTemplateRenderer;

// Access
use Access\AccessPresentation;
use Access\AccessPresentationInterface;
use Access\Enum\AccessPresentationEnum;

//component


// static
use Red\Middleware\Redactor\Controler\StaticControler;
use Component\View\StaticItemComponent;
use Component\ViewModel\StaticItemViewModel;
use Red\Model\Repository\StaticItemRepo;

// viewModel
use Component\ViewModel\StatusViewModel;  // jen jméno pro službu delegáta - StatusViewModel definován v app kontejneru

// renderery - pro volání služeb renderer kontejneru renderer::class
use Component\Renderer\Html\NoPermittedContentRenderer;

// repo
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;
use Red\Model\Repository\MenuItemRepo;

// login aggregate ze session - přihlášený uživatel
use Auth\Model\Entity\LoginAggregateFullInterface; // pro app kontejner

// database
use Pes\Database\Handler\Account;
use Pes\Database\Handler\AccountInterface;

/**
 *
 *
 * @author pes2704
 */
class StaticItemContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams(): iterable {
        return array_merge(
//                ConfigurationCache::web(),  //db
                ConfigurationCache::webComponent(), // hodnoty jsou použity v kontejneru pro službu, která generuje ComponentConfiguration objekt (viz getSrvicecDefinitions)
//                ConfigurationCache::menu(),
//                Configuration::renderer(),
//                ConfigurationCache::redTemplates()
                );
    }
    
    public function getFactoriesDefinitions(): iterable {
        return [
            // pro statické stránky, které nepoužívají data z db
            // pokud statická stránky má používat data z db je nutné 
            // - použít kontejner konfigurátor, ve kterém jsou definice datových modelů (viewModelů)
            // - nastavit také Acocunt pro přístup k db
            StaticItemComponent::class => function(ContainerInterface $c) {
                /** @var ComponentConfigurationInterface $configuration */
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new StaticItemComponent($configuration);
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class); 
                if($accessPresentation->isAllowed(StaticItemComponent::class, AccessPresentationEnum::DISPLAY)) {
                    // StaticItemComponent nemá svůj specifický renderer, používá PhpTemplateRenderer
                    $component->setRendererName(PhpTemplateRenderer::class);
                    $viewModel = $c->get(StaticItemViewModel::class);
                    $component->setData($viewModel);
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;                
            },
        ];
    }

    public function getServicesDefinitions(): iterable {
        return [
            // configuration - používá parametry nastavené metodou getParams()
            ComponentConfiguration::class => function(ContainerInterface $c) {
                return new ComponentConfiguration(
                        $c->get('logs.directory'),
                        $c->get('logs.render'),
                        $c->get('logs.type'),
                        $c->get('templates')
                    );
            },
            StaticItemViewModel::class => function(ContainerInterface $c) {
                return (new StaticItemViewModel(
                            $c->get(StatusViewModel::class),
                            $c->get(MenuItemRepo::class),
                            $c->get(StaticItemRepo::class))
                        )->injectContainer($c);  // inject component kontejner - pro statické stránky - vznikne automaticky proměnná $container

            },

        ####
        # renderer container
        #
            'rendererContainer' => function(ContainerInterface $c) {
                // POZOR - TemplateRendererContainer "má" - (->has() vrací true) - pro každé jméno service, pro které existuje třída!
                // služby RendererContainerConfigurator, které jsou přímo jménem třídy (XxxRender::class) musí být konfigurovány v metodě getServicesOverrideDefinitions()
                return (new RendererContainerConfigurator())->configure(new Container(new TemplateRendererContainer()));
            },
        ];
    }
}

