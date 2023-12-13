<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Web\Middleware\Page\Controller;

use Psr\Http\Message\ServerRequestInterface;

use Site\ConfigurationCache;
use Red\Model\Entity\MenuItemInterface;

// komponenty
use Red\Component\View\Generated\SearchResultComponent;
use UnexpectedValueException;

####################

use Red\Model\Repository\MenuItemRepo;
use Red\Model\Repository\BlockRepo;
use Red\Model\Entity\BlockAggregateMenuItemInterface;

/**
 * Description of GetController
 *
 * @author pes2704
 */
class PageController extends LayoutControllerAbstract {

    const HEADER = 'X-RED-PageCtrl-Time';

    ### action metody ###############

    /**
     * Přesměruje na home stránku. Řídí se konfigurací. Home stránka může být definována jménem komponenty nebo jménem statické stránky nebo
     * identifikátorem uid položky menu (položky hierarchie).
     *
     * @param ServerRequestInterface $request
     * @return type
     * @throws \UnexpectedValueException
     */
    public function home(ServerRequestInterface $request) {
        $homePage = ConfigurationCache::layoutController()['home_page'];
        switch ($homePage[0]) {
            case 'block':
                $menuItem = $this->getMenuItemForBlock($homePage[1]);
                if (!isset($menuItem)) {
                    throw new \UnexpectedValueException("Undefined menu item for default page (home page) defined as component with name '$homePage[1]'.");
                }
                break;
            case 'item':
                $menuItem = $this->getMenuItem($homePage[1]);
                if (!isset($menuItem)) {
                    throw new UnexpectedValueException("Undefined default page (home page) defined as static with name '$homePage[1]'.");
                }
                break;
            default:
                throw new UnexpectedValueException("Unknown home page type in configuration. Type: '$homePage[0]'.");
                break;
        }
        return $this->createResponseWithItem($request, $menuItem);
    }

    public function item(ServerRequestInterface $request, $uid) {
        $startTime = microtime(true);

        $menuItem = $this->getMenuItem($uid);
        $getTime = (microtime(true) - $startTime) * 1000;
        $response = $this->createResponseWithItem($request, $menuItem);
        return $response
            ->withHeader('X-RED-PageCtrlGetItem-Time', sprintf('%2.3fms', $getTime))
            ->withHeader(self::HEADER, sprintf('%2.3fms', (microtime(true) - $startTime) * 1000));

    }

    public function searchResult(ServerRequestInterface $request) {
        // TODO tady je nějaký zmatek
        /** @var SearchResultComponent $component */
        $component = $this->container->get(SearchResultComponent::class);
        $key = $request->getQueryParams()['klic'];
        $actionComponents = ["content" => $component->setSearch($key)];
        return $this->createResponseFromView($request, $this->composeLayoutView($request, $this->getComponentViews($actionComponents)));
    }



}
