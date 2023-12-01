<?php
namespace Red\Service\CascadeLoader;

/**
 *
 * @author pes2704
 */
interface CascadeLoaderFactoryInterface {
    public function getRedLoadScript(string $dataRedApiUri, bool $httpReloadOnNavigation);
}
