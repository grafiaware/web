<?php

namespace Container;

use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;
use Configuration\ComponentConfiguration;
use Configuration\ComponentConfigurationInterface;
use Access\AccessPresentation;
use Access\AccessPresentationInterface;
use Access\Enum\AccessPresentationEnum;
use Component\View\StaticItemComponent;
use Component\ViewModel\StaticItemViewModel;
use Component\ViewModel\StatusViewModel;
use Component\Renderer\Html\StaticItemRenderer;
use Red\Model\Repository\StaticItemRepo;
use Red\Model\Repository\StaticItemRepoInterface;
use Red\Service\StaticRegistry\StaticRegistryTemplateListClientInterface;
use StaticRegistry\Model\Repository\StaticRegistryRepoInterface;

/**
 * Sdílený kontejner pro StaticItemComponent / StaticItemViewModel (web, red, auth, events).
 *
 * View modelu injektuje dostupné repo podle toho, co je v parent kontejneru:
 * - StaticItemRepo → red/web (red DB)
 * - StaticRegistryRepo → auth/events (SQLite)
 * - TemplateListClient → red (remote seznam šablon pro editor)
 *
 * @author pes2704
 */
class StaticItemContainerConfigurator extends ContainerConfiguratorAbstract {
    
    public function getFactoriesDefinitions(): iterable {
        return [
            // Data (view model) se nastavují až v StaticComponentControlerAbstract::static()
            StaticItemComponent::class => function(ContainerInterface $c) {
                /** @var ComponentConfigurationInterface $configuration */
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new StaticItemComponent($configuration);
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class); 
                if($accessPresentation->isAllowed(StaticItemComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component->setRendererName(StaticItemRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;                
            },
        ];
    }

    public function getServicesDefinitions(): iterable {
        return [
            StaticItemViewModel::class => function(ContainerInterface $c) {
                $viewModel = (new StaticItemViewModel(
                            $c->get(StatusViewModel::class))
                        )->injectContainer($c);

                // Volitelná injekce — has() podle stacku modulu (red vs auth/events)
                if ($c->has(StaticItemRepoInterface::class)) {
                    $viewModel->injectStaticItemRepo($c->get(StaticItemRepoInterface::class));
                } elseif ($c->has(StaticItemRepo::class)) {
                    $viewModel->injectStaticItemRepo($c->get(StaticItemRepo::class));
                }

                if ($c->has(StaticRegistryRepoInterface::class)) {
                    $viewModel->injectStaticRegistryRepo($c->get(StaticRegistryRepoInterface::class));
                }

                if ($c->has(StaticRegistryTemplateListClientInterface::class)) {
                    $viewModel->injectTemplateListClient($c->get(StaticRegistryTemplateListClientInterface::class));
                }

                return $viewModel;
            },
        ];
    }
}
