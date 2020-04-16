<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application;

use Pes\Application\AppInterface;
use Pes\Middleware\Selector;

use Middleware\Logged\Service\LoggedAccessor;

/**
 * Description of SelectorFactory
 *
 * @author pes2704
 */
class SelectorFactory {

    /**
     *
     * @var AppInterface
     */
    private $app;

    private $items;

    /**
     * Kontejner pro předání do jrdnotlivých middleware stacků definovaných při přidávání SelectorItem do vytvářeného objektu Selector.
     *
     * @param AppInterface $app
     */
    public function __construct(AppInterface $app=NULL) {
        $this->app = $app;
        // všechny addItem dostávají jako stack anonymní funkci, která přijímá kontejner, vzniklé Item budou připraveny appContainer předaný jim sektorem
        $this->items = [
            '/www/' =>
            function(AppInterface $app) {
                return [
                    new \Middleware\Login\Login(),
                    new \Middleware\Web\Transformator(),
                    new \Middleware\Web\Web()
                ];},
            '/api/'=>
            function(AppInterface $app) {
                return [
                    new \Middleware\Api\Api()
                ];},
            '/auth/'=>
            function(AppInterface $app) {
                return [
                    new \Middleware\Login\Login()
                ];},
            '/rs'=>
            function(AppInterface $app) {
                return [
                    new \Middleware\Logged\LoggedAccess(new LoggedAccessor($app)),
                    new \Middleware\Rs\Transformator(),
                    new \Middleware\Rs\Rs()
                ];},
            '/edun'=>
            function(AppInterface $app) {
                return [
                    new \Middleware\Logged\LoggedAccess(new LoggedAccessor($app)),
                    new \Middleware\Edun\Transformator(),
                    new \Middleware\Edun\Edun()
                ];},
            '/staffer'=>
            function(AppInterface $app) {
                return [
                    new \Middleware\Logged\LoggedAccess(new LoggedAccessor($app)),
                    new \Middleware\Staffer\Transformator(),
                    new \Middleware\Staffer\Staffer()
                ];},
//            '/menu/'=>
//            function(AppInterface $app) {
//                return [
//                    new \Middleware\Logged\LoggedAccess(new LoggedAccessor($app)),
//                    (new \Middleware\Menu\Menu())
//                ];},
            '/konverze'=>       // bez koncového lomítka - je jen tato jedna uri, uri neí delší než prefix
            function(AppInterface $app) {
                return [
                    new \Middleware\Logged\LoggedAccess(new LoggedAccessor($app)),
                    new \Middleware\Konverze\Konverze()
                ];},



// tento item je vybrán vždy - pro libovolné url
            '/' =>
            function(AppInterface $app) {
                return [
                    (new \Middleware\Login\Login()),
                    new \Middleware\Web\Transformator(),
                    (new \Middleware\Web\Web())
                ];},
            ];
    }

    /**
     * Vytvoří objekt Pes\Middleware\Selector a nastaví mu potřebné položky SelectorItem. Objekt Selector je middleware a implementuje Pes\Middleware\ContainerMiddlewareInterface.
     * Proto je schopen přijímat middleware kontejner (metodou setMwContainer rozhraní Pes\Middleware\ContainerMiddlewareInterface).
     * Pokud byl při volání konstruktoru této SelectorFactory nastaven kontejner, je tento kontejner nastaven jako middleware kontejner objektu Selector.
     * Selector svůj middleware kontejnersám nepoužívá, pouze ho předává jako parametr middleware stacku (Closure) vybraného SelectorItem.
     * Pokud je stack při volíní metody addItem() selektoru definován jako anonymní funkce (Closure), kterí jako parametr přijímá kontejner typu Psr\Container\AppInterface,
     * pak této anonymní funkci předán middleware kontejner selektoru. Injektováním aplikačního kontekneru do konstruktoru selektoru a definováním stacků jednotlivých SelectorItem jako anonymní funkce
     * příjímající kontejner typu Psr\Container\AppInterface lze zajistit automatické předávání (propagaci) aplikačního kontejneru do všech SelectorItem a tedy do všech selektovaných middleware stacků.
     *
     * @return Selector
     */
    public function create() {

        ## selector middleware
        $selector = new Selector();

        // kontejner selektoru - selektor je middleware, nastavuji kontejner selektoru setMwContainer(). Selector svůj middleware kontejner pouze předává jako parametr middleware stacku (Closure) vybraného SelectorItem
        if ($this->app) {
            $selector = $selector->setApp($this->app);
        }

        foreach ($this->items as $prefix=>$stack) {
                $selector->addItem($prefix, $stack);
        }
        return $selector;
    }
}
