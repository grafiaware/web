<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel;

use Component\ViewModel\ViewModelAbstract;

use Component\ViewModel\StaticItemViewModelInterface;

use Component\ViewModel\StatusViewModelInterface;
use Red\Model\Repository\StaticItemRepoInterface;

use Red\Model\Entity\StaticItemInterface;

use \Site\ConfigurationCache;

use Psr\Container\ContainerInterface;

use UnexpectedValueException;

/**
 * Description of LanguageSelect
 *
 * @author pes2704
 */
class StaticItemViewModel extends ViewModelAbstract implements StaticItemViewModelInterface {

    /**
     * @var StatusViewModelInterface
     */
    protected $statusViewModel;
    
    private $container;

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
     * Vrací StaticItem získaný z presentation statusu.
     * 
     * Očekává ve statusu nastavený StaticItem (při volání statické komponenty a view modelu není k dispozici Red databáze)
     * StaticItem do statusu dává LayoutControler (v té chvíli je Red databáze připojena)
     *  - vždy zkusí načíst StaticItem z repository pro id menuItem 
     *  - když exituje záznam v tabulce static, načte StaticItem jinak null
     * 
     * @return StaticItemInterface
     * @throws UnexpectedValueException
     */
    private function getStaticItem(): StaticItemInterface {
        $staticEntity = $this->statusViewModel->getPresentedStaticItem();
        if (!isset($staticEntity)) {
            throw new UnexpectedValueException("Nenačtena static položka z presentation status view modelu.");
        }
        return $staticEntity;
    }
    
    public function getStaticItemId(): string {
        return $this->getStaticItem()->getId() ?? '';
    }
    
    public function getStaticItemPath(): string {
        return $this->getStaticItem()->getPath() ?? '';
    }
    
    public function getStaticItemTemplate(): string {
        return $this->getStaticItem()->getTemplate() ?? '';
    }

    /**
     * Vrací úplnou cestu k souboru šablony (template) pro StaticIrem získaný z presentation statusu.
     * 
     * @return string
     */
    public function getStaticFullTemplatePath(): string {
        $staticEntity = $this->getStaticItem();
        $basePath = ConfigurationCache::componentControler()['static'] ?? '';
        $path = $staticEntity->getPath() ?? '';
        // template nesmí být null - exception ani prázdný řetězec - vznikly by dvě /
        $template = (null!==$staticEntity->getTemplate() AND $staticEntity->getTemplate()) ?  $staticEntity->getTemplate().'/' : '';
        return $basePath.$path.$template;
    }
    public function isEditable(): bool {
        $editorActions = $this->statusViewModel->getEditorActions();
        return isset($editorActions) ? $editorActions->presentEditableContent() : false;
    }
    public function getIterator(): \Traversable {
        $this->appendData(
                [
                    'staticTemplatePath' => $this->getStaticFullTemplatePath(),
                    'container' => $this->container,        // v proměnné $container ve včech statických stránkách a šablonách je k dispozici kontainer
                ]
                );
        return parent::getIterator();
    }
}
