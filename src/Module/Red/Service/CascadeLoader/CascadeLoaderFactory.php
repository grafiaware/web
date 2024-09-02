<?php
namespace Red\Service\CascadeLoader;

use Site\ConfigurationCache;
use Pes\View\Template\PhpTemplate;
use Pes\View\ViewFactoryInterface;

use Pes\View\CompositeView;

/**
 * Description of CascadeLoaderService
 *
 * @author pes2704
 */
class CascadeLoaderFactory implements CascadeLoaderFactoryInterface {
    
    private $viewFactory;
    
    public function __construct(ViewFactoryInterface $viewFactory) {
        $this->viewFactory = $viewFactory;
    }    
    
    public function getRedLoaderElement(string $dataRedApiUri, string $httpCacheControl, string $targetId='') {
        // prvek data 'loaderWrapperElementId' musí být unikátní - z jeho hodnoty se generuje id načítaného elementu - a id musí být unikátní jinak dojde k opakovanému přepsání obsahu elemntu v DOM
        $uniquid = uniqid();
        $id = "red_loader_$uniquid";
        return $this->getLoader($id, $dataRedApiUri, $httpCacheControl, $targetId);
    }
    
    public function getRedTargetElement(string $id, string $dataRedApiUri, string $httpCacheControl) {
        return $this->getLoader($id, $dataRedApiUri, $httpCacheControl);
    }
    
    private function getLoader(string $id, string $dataRedApiUri, string $httpCacheControl, string $targetId='') {
        $view = $this->viewFactory->phpTemplateCompositeView(ConfigurationCache::layoutController()['templates.loaderElement']);
        if ($httpCacheControl) {
            $dataRedCacheControl = ConfigurationCache::layoutController()['cascade.cacheReloadOnNav'];
        } else {
            $dataRedCacheControl = ConfigurationCache::layoutController()['cascade.cacheLoadOnce'];
        }
        $view->setData([
                        'class' => ConfigurationCache::layoutController()['cascade.class'],   // cascade.js 
                        'dataRedCacheControl' => $dataRedCacheControl,
                        'id' => $id,
                        'dataRedApiUri' => $dataRedApiUri,
                        'dataRedTargetId'=> ($targetId ? $targetId : '')
                        ]);
        return $view;
        
    }
}
