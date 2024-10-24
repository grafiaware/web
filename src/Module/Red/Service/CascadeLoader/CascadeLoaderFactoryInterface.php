<?php
namespace Red\Service\CascadeLoader;

/**
 *
 * @author pes2704
 */
interface CascadeLoaderFactoryInterface {
    public function getRedLoaderElement(string $dataRedApiUri, bool $httpCacheReloadOnNav, string $dataRedTargetId='');
    public function getRedTargetElement(string $id, string $dataRedApiUri, bool $httpCacheReloadOnNav);
    
}
