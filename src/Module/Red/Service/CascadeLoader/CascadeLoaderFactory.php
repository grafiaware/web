<?php
namespace Red\Service\CascadeLoader;

use Site\ConfigurationCache;
use Pes\View\Template\PhpTemplate;
use Pes\View\ViewInterface;

/**
 * Description of CascadeLoaderService
 *
 * @author pes2704
 */
class CascadeLoaderFactory implements CascadeLoaderFactoryInterface {
    
    private $view;
    
    public function __construct(ViewInterface $view) {
        $this->view = $view;
    }    
    
    public function getRedLoadScript(string $dataRedApiUri, bool $httpReloadOnNavigation) {
        if ($httpReloadOnNavigation) {
            $dataRedCacheControl = ConfigurationCache::layoutController()['cascade.cacheReloadOnNav'];
        } else {
            $dataRedCacheControl = ConfigurationCache::layoutController()['cascade.cacheLoadOnce'];
        }
        // prvek data 'loaderWrapperElementId' musí být unikátní - z jeho hodnoty se generuje id načítaného elementu - a id musí být unikátní jinak dojde k opakovanému přepsání obsahu elemntu v DOM
        $uniquid = uniqid();
        $this->view->setData([
                        'class' => ConfigurationCache::layoutController()['cascade.class'],
                        'dataRedCacheControl' => $dataRedCacheControl,
                        'loaderElementId' => "red_loaded_$uniquid",
                        'dataRedApiUri' => $dataRedApiUri,
                        ]);
        $this->view->setTemplate(new PhpTemplate(ConfigurationCache::layoutController()['templates.loaderElement']));
        return $this->view;
    }
}
