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
    
    /**
     * Připraví view s PHP šablonou definovanou v layoutController()['templates.loaderElement'].
     * Předá mu data, která budou použita pro atributy v loader elementu - informace pro cascade.js.
     * Hodnota pro atribut id loader elementu je generována jako unikátní, na stránce se hodnota id nesmí opakovat.
     * Ostatní data jsou vytvořena z parametrů a konfigurace - např. class a data-red-xxx .
     *  
     * @param string $dataRedApiUri
     * @param bool $httpCacheReloadOnNav
     * @param string $dataNavTargetId
     * @return type
     */
    public function getRedLoaderElement(string $dataRedApiUri, bool $httpCacheReloadOnNav, string $dataNavTargetId='') {
        // prvek data 'loaderWrapperElementId' musí být unikátní - z jeho hodnoty se generuje id načítaného elementu - a id musí být unikátní jinak dojde k opakovanému přepsání obsahu elemntu v DOM
        $uniquid = uniqid();
        $id = "red_loader_$uniquid";
        return $this->getLoader($id, $dataRedApiUri, $httpCacheReloadOnNav, $dataNavTargetId);
    }
    /**
     * 
     * @param string $id
     * @param string $dataRedApiUri
     * @param bool $httpCacheReloadOnNav
     * @return type
     */
    public function getRedTargetElement(string $id, string $dataRedApiUri, bool $httpCacheReloadOnNav) {
        return $this->getLoader($id, $dataRedApiUri, $httpCacheReloadOnNav);
    }
    
    /**
     * Připraví view s PHP šablonou definovanou v layoutController()['templates.loaderElement'].
     * Předá mu data vytvořená z parametrů a konfigurace - např. id, class a data-red-xxx atributy pro loader element.
     * 
     * @param string $id
     * @param string $dataRedApiUri
     * @param bool $httpCacheReloadOnNav nastaví hodnotu pro atribut data-red-cache-control podle konfigurace pro reload obsahu při navigaci nebo jednorázové načtení obsahu
     * @param string $dataNavTargetId
     * @return type
     */
    private function getLoader(string $id, string $dataRedApiUri, bool $httpCacheReloadOnNav, string $dataNavTargetId='') {
        $view = $this->viewFactory->phpTemplateCompositeView(ConfigurationCache::layoutController()['templates.loaderElement']);
        if ($httpCacheReloadOnNav) {
            $dataRedCacheControl = ConfigurationCache::layoutController()['cascade.cacheReloadOnNav'];
        } else {
            $dataRedCacheControl = ConfigurationCache::layoutController()['cascade.cacheLoadOnce'];
        }
        $view->setData([
                        'class' => ConfigurationCache::layoutController()['cascade.class'],   // cascade.js 
                        'dataRedCacheControl' => $dataRedCacheControl,
                        'id' => $id,
                        'dataRedApiUri' => $dataRedApiUri,
                        'dataNavTargetId'=> ($dataNavTargetId ? $dataNavTargetId : '')
                        ]);
        return $view;
        
    }
}
