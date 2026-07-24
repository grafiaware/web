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

/**
 * View model static stránky.
 *
 * StaticItem se načítá v pořadí: lokální registry (auth/events) → red DB → session.
 * Session fallback zůstává pro zpětnou kompatibilitu s layout requestem (Web modul).
 * Cascade/menuSwap už na session handoffu nezávisí — menuItemId přijde z URL.
 *
 * @author pes2704
 */
class StaticItemViewModel extends ViewModelAbstract implements StaticItemViewModelInterface {

    protected StatusViewModelInterface $statusViewModel;

    private ?ContainerInterface $container = null;
    /** ID menu položky z route parametru cascade GET (auth/events/red)/v1/static/:menuItemId */
    private ?int $menuItemId = null;
    /** Dostupný v red/web kontejneru — přímý přístup k tabulce static v red DB */
    private ?StaticItemRepoInterface $staticItemRepo = null;
    /** Dostupný v auth/events kontejneru — lokální SQLite registry */
    private ?StaticRegistryRepoInterface $staticRegistryRepo = null;
    /** Volitelně v red — pro select box šablon při editaci */
    private ?StaticRegistryTemplateListClientInterface $templateListClient = null;
    /** Base URL requestu — fallback když staticRegistry.push.moduleBaseUrls nejsou nastaveny */
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

    /**
     * Nastaví menuItemId z route — volá StaticComponentControlerAbstract před renderem.
     */
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
    
    /**
     * Priorita zdrojů StaticItem:
     * 1. lokální SQLite registry (auth/events server — bez red DB)
     * 2. StaticItemRepo (red/web — přímý přístup k red DB)
     * 3. session presentation status (layout request / zpětná kompatibilita)
     */
    private function getStaticItem(): StaticItemInterface {
        // 1. Lokální registry na auth/events serveru
        if ($this->menuItemId !== null && $this->staticRegistryRepo !== null) {
            $entry = $this->staticRegistryRepo->getByMenuItemId($this->menuItemId);
            if ($entry !== null) {
                return $this->staticRegistryRepo->toStaticItemInterface($entry);
            }
        }

        // 2. Red DB (red modul nebo web layout)
        if ($this->menuItemId !== null && $this->staticItemRepo !== null) {
            $item = $this->staticItemRepo->getByMenuItemId($this->menuItemId);
            if ($item !== null) {
                return $item;
            }
        }

        // 3. Fallback: StaticItem uložený LayoutControlerem do session
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

    /**
     * Úplná cesta k adresáři se šablonou: {componentControler.static}{path}{template}/
     * TemplateCompiler pak připojí template.php.
     */
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
     *
     * Volá remote GET /{module}/v1/static/templates — pouze pro events|auth static položky.
     */
    public function getTemplateOptions(): array {
        if ($this->templateListClient === null) {
            return [];
        }
        $apiModule = $this->getApiModuleForTemplates();
        if ($apiModule === null || !in_array($apiModule, ['events', 'auth'], true)) {
            return [];
        }
        $prefixes = ConfigurationCache::staticRegistry()['staticRegistry.templatePrefixes'] ?? [];
        $prefix = $prefixes[$apiModule] ?? ($apiModule . '/');
        try {
            return $this->templateListClient->fetch($apiModule, $prefix, $this->requestBaseUrl);
        } catch (\Throwable) {
            // Selhání remote listu nesmí zablokovat render stránky — editor spadne na textová pole
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
                    'container' => $this->container, // v PHP šablonách dostupný jako $container
                    'templateOptions' => $this->getTemplateOptions(),
                ]
                );
        return parent::getIterator();
    }
}
