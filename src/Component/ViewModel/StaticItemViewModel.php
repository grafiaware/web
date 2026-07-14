<?php

namespace Component\ViewModel;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StaticItemViewModelInterface;
use Component\ViewModel\StatusViewModelInterface;
use Red\Model\Repository\StaticItemRepoInterface;
use Red\Model\Entity\StaticItemInterface;
use Red\Service\StaticRegistry\StaticRegistryTemplateListClientInterface;
use Site\ConfigurationCache;
use StaticRegistry\Model\Repository\StaticRegistryRepoInterface;
use Psr\Container\ContainerInterface;
use ArrayIterator;
use UnexpectedValueException;

class StaticItemViewModel extends ViewModelAbstract implements StaticItemViewModelInterface {

    protected StatusViewModelInterface $statusViewModel;

    private ?ContainerInterface $container = null;
    private ?int $menuItemId = null;
    private ?StaticItemRepoInterface $staticItemRepo = null;
    private ?StaticRegistryRepoInterface $staticRegistryRepo = null;
    private ?StaticRegistryTemplateListClientInterface $templateListClient = null;
    private ?string $requestBaseUrl = null;

    public function __construct(
            StatusViewModelInterface $status
            ) {
        $this->statusViewModel = $status;
    }
    
    public function injectContainer(ContainerInterface $container): StaticItemViewModelInterface {
        $this->container = $container;
        return $this;
    }

    public function setMenuItemId(int $menuItemId): StaticItemViewModelInterface {
        $this->menuItemId = $menuItemId;
        return $this;
    }

    public function injectStaticItemRepo(?StaticItemRepoInterface $repo): StaticItemViewModelInterface {
        $this->staticItemRepo = $repo;
        return $this;
    }

    public function injectStaticRegistryRepo(?StaticRegistryRepoInterface $repo): StaticItemViewModelInterface {
        $this->staticRegistryRepo = $repo;
        return $this;
    }

    public function injectTemplateListClient(?StaticRegistryTemplateListClientInterface $client): StaticItemViewModelInterface {
        $this->templateListClient = $client;
        return $this;
    }

    public function setRequestBaseUrl(?string $baseUrl): StaticItemViewModelInterface {
        $this->requestBaseUrl = $baseUrl;
        return $this;
    }
    
    private function getStaticItem(): StaticItemInterface {
        if ($this->menuItemId !== null && $this->staticRegistryRepo !== null) {
            $entry = $this->staticRegistryRepo->getByMenuItemId($this->menuItemId);
            if ($entry !== null) {
                return $this->staticRegistryRepo->toStaticItemInterface($entry);
            }
        }

        if ($this->menuItemId !== null && $this->staticItemRepo !== null) {
            $item = $this->staticItemRepo->getByMenuItemId($this->menuItemId);
            if ($item !== null) {
                return $item;
            }
        }

        $staticEntity = $this->statusViewModel->getPresentedStaticItem();
        if ($staticEntity !== null) {
            return $staticEntity;
        }

        throw new UnexpectedValueException(
            'Static položka nenalezena pro menuItemId=' . ($this->menuItemId ?? 'null') . '.'
        );
    }
    
    public function getStaticItemId(): string {
        return (string) ($this->getStaticItem()->getId() ?? '');
    }
    
    public function getStaticItemPath(): string {
        return $this->getStaticItem()->getPath() ?? '';
    }
    
    public function getStaticItemTemplate(): string {
        return $this->getStaticItem()->getTemplate() ?? '';
    }

    public function getStaticFullTemplatePath(): string {
        $staticEntity = $this->getStaticItem();
        $basePath = ConfigurationCache::componentControler()['static'] ?? '';
        $path = $staticEntity->getPath() ?? '';
        $template = (null !== $staticEntity->getTemplate() && $staticEntity->getTemplate())
            ? $staticEntity->getTemplate() . '/'
            : '';
        return $basePath . $path . $template;
    }

    public function isEditable(): bool {
        $editorActions = $this->statusViewModel->getEditorActions();
        return isset($editorActions) ? $editorActions->presentEditableContent() : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateOptions(): array {
        if ($this->templateListClient === null) {
            return [];
        }
        $apiModule = $this->getApiModuleForTemplates();
        if ($apiModule === null || !in_array($apiModule, ['events', 'auth'], true)) {
            return [];
        }
        $prefixes = ConfigurationCache::staticRegistry()['templatePrefixes'] ?? [];
        $prefix = $prefixes[$apiModule] ?? ($apiModule . '/');
        try {
            return $this->templateListClient->fetch($apiModule, $prefix, $this->requestBaseUrl);
        } catch (\Throwable) {
            return [];
        }
    }

    private function getApiModuleForTemplates(): ?string {
        $menuItem = $this->statusViewModel->getPresentedMenuItem();
        if ($menuItem !== null && $menuItem->getApiModuleFk()) {
            return $menuItem->getApiModuleFk();
        }
        return null;
    }
    
    public function getIterator(): ArrayIterator {
        $this->appendData(
                [
                    'staticTemplatePath' => $this->getStaticFullTemplatePath(),
                    'container' => $this->container,
                    'templateOptions' => $this->getTemplateOptions(),
                ]
                );
        return parent::getIterator();
    }
}
