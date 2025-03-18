<?php
namespace Replace;

use Psr\Http\Message\ServerRequestInterface;
use Red\Model\Dao\MenuItemDaoInterface;
use Status\Model\Entity\PresentationInterface;

/**
 *
 * @author pes2704
 */
interface ReplaceInterface {
    public function replaceTemplateStrings(ServerRequestInterface $request, &$text): void;    
    public function replaceRSStrings(ServerRequestInterface $request, &$text): void;    
    public function replaceSlots(&$text): void;
    public function replaceRsUrlsInHref(ServerRequestInterface $request, &$text, $key, MenuItemDaoInterface $dao, PresentationInterface $statusPresentation): void;    
}
