<?php
namespace Replace;

use Psr\Http\Message\ServerRequestInterface;
use Red\Model\Dao\MenuItemDaoInterface;
use Status\Model\Entity\StatusPresentationInterface;

/**
 *
 * @author pes2704
 */
interface ReplaceInterface {
    public function replacePatterns(ServerRequestInterface $request, &$text): void;    
    public function replaceSlots(&$text): void;
    public function replaceRsUrlsInHref(ServerRequestInterface $request, &$text, $key, MenuItemDaoInterface $dao, StatusPresentationInterface $statusPresentation): void;    
}
