<?php
namespace Red\Service\CascadeLoader;

/**
 *
 * @author pes2704
 */
interface CascadeLoaderFactoryInterface {
    public function getRedLoaderElement(string $dataRedApiUri, string $httpCacheControl, string $targetId);
    public function getRedTargetElement(string $id, string $dataRedApiUri, string $httpCacheControl);
    
}
