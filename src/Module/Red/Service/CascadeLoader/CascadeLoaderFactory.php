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
    
    public function getRedLoadScript(string $dataRedApiUri, $cacheControl='reload') {
        $view = $this->viewFactory->phpTemplateCompositeView(ConfigurationCache::layoutController()['templates.loaderElement']);
        // prvek data 'loaderWrapperElementId' musí být unikátní - z jeho hodnoty se generuje id načítaného elementu - a id musí být unikátní jinak dojde k opakovanému přepsání obsahu elemntu v DOM
        $uniquid = uniqid();
        $view->setData([
                        'class' => ConfigurationCache::layoutController()['cascade.class'],
                        'dataRedCacheControl' => $cacheControl,
                        'loaderElementId' => "cascade_$uniquid",
                        'dataRedApiUri' => $dataRedApiUri,
                        ]);
        return $view;
    }
}
