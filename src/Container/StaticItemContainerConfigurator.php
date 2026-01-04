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

// template + renderer
use Pes\View\Renderer\PhpTemplateRenderer;

// Access
use Access\AccessPresentation;
use Access\AccessPresentationInterface;
use Access\Enum\AccessPresentationEnum;

//component


// static
use Component\View\StaticItemComponent;
use Component\ViewModel\StaticItemViewModel;

// viewModel
use Component\ViewModel\StatusViewModel;  // jen jméno pro službu delegáta - StatusViewModel definován v app kontejneru

// renderery - pro volání služeb renderer kontejneru renderer::class
use Component\Renderer\Html\StaticItemRenderer;
use Component\Renderer\Html\NoPermittedContentRenderer;

/**
 *
 *
 * @author pes2704
 */
class StaticItemContainerConfigurator extends ContainerConfiguratorAbstract {
    
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
//                    $component->setRendererName(PhpTemplateRenderer::class);
                    $component->setRendererName(StaticItemRenderer::class);
                    $viewModel = $c->get(StaticItemViewModel::class);
                    $component->setData($viewModel);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;                
            },
        ];
    }

    public function getServicesDefinitions(): iterable {
        return [
            StaticItemViewModel::class => function(ContainerInterface $c) {
                return (new StaticItemViewModel(
                            $c->get(StatusViewModel::class))
                        )->injectContainer($c);  // inject component kontejner - pro statické stránky - vznikne automaticky proměnná $container

            },
        ];
    }
}

