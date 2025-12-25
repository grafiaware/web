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

    const DEFAULT_TEMPLATE_FILENAME='template.php';

    /**
     * @var StatusViewModelInterface
     */
    protected $statusViewModel;
    
    /**
     * @var StaticItemRepoInterface
     */
    private $staticRepo;
    
    private $container;

    public function __construct(
            StatusViewModelInterface $status, 
            StaticItemRepoInterface $staticRepo
            ) {
        $this->statusViewModel = $status;
        $this->staticRepo = $staticRepo;
    }
    
    public function injectContainer(ContainerInterface $container): StaticItemViewModelInterface {
        $this->container = $container;
        return $this;
    }
    
    /**
     * Vrací StaticIrem získaný z presentation statusu.
     * 
     * @return StaticItemInterface
     * @throws UnexpectedValueException
     */
    public function getStaticItem(): StaticItemInterface {
        $staticEntity = $this->statusViewModel->getPresentedStaticItem();
        if (!isset($staticEntity)) {
            throw new UnexpectedValueException("Nenačtena static položka z presentation status view modelu.");
        }
        return $staticEntity;
    }

    /**
     * Vrací úplnou cestu k souboru šablony (template) pro StaticIrem získaný z presentation statusu.
     * 
     * @return string
     */
    public function getStaticTemplatePath(): string {
        $staticEntity = $this->getStaticItem();
        $basePath = ConfigurationCache::componentControler()['static'] ?? '';
        $path = null !== $staticEntity->getPath() ? $staticEntity->getPath() : '';

        return $basePath.$path.'/'.$staticEntity->getTemplate().'/'.self::DEFAULT_TEMPLATE_FILENAME;        
    }
    
    public function getIterator(): \Traversable {
        $this->appendData(
                [
                    'staticTemplatePath' => $this->getStaticTemplatePath(),
                    'container' => $this->container,        // v proměnné $container ve včech statických stránkách a šablonách je k dispozici kontainer
                ]
                );
        return parent::getIterator();
    }
}
