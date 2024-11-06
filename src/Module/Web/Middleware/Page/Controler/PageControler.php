<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Web\Middleware\Page\Controler;

use Psr\Http\Message\ServerRequestInterface;

use Site\ConfigurationCache;

// komponenty
use Red\Component\View\Generated\SearchResultComponent;
use UnexpectedValueException;

/**
 * Description of GetControler
 *
 * @author pes2704
 */
class PageControler extends LayoutControlerAbstract {

    const HEADER = 'X-WEB-PageCtrl-Time';

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
        try {
            $menuItem = $this->getHomeMenuItem();
        } catch (Exception $exc) {
            $trace = $exc->getTraceAsString();
            return $this->createStringOKResponse($trace);
        }
        return $this->createResponseRedirectSeeOther($request, "web/v1/page/item/{$menuItem->getUidFk()}");
//        return $this->createResponseWithItem($request, $menuItem);
    }

    public function item(ServerRequestInterface $request, $uid) {
//        $startTime = microtime(true);

        $menuItem = $this->getMenuItem($uid);
//        $getTime = (microtime(true) - $startTime) * 1000;
        $response = $this->createResponseWithItem($request, $menuItem);
        return $response;
//            ->withHeader('X-WEB-PageCtrlGetItem-Time', sprintf('%2.3fms', $getTime))
//            ->withHeader(self::HEADER, sprintf('%2.3fms', (microtime(true) - $startTime) * 1000));

    }

    public function searchResult(ServerRequestInterface $request) {
        // TODO tady je nějaký zmatek
        /** @var SearchResultComponent $component */
        $component = $this->container->get(SearchResultComponent::class);
        $key = $request->getQueryParams()['klic'];
        $actionComponents = ["content" => $component->setSearch($key)];
        return $this->createStringOKResponseFromView($this->composeLayoutView($request, $this->getComponentViews($actionComponents)));
    }



}
