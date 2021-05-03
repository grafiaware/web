<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application;

use Pes\Application\AppInterface;
use Pes\Middleware\SelectorInterface;

use Module\Auth\Middleware\Login\Login;
use Module\Auth\Middleware\Logged\LoggedAccess;
use Module\Auth\Middleware\Logged\Service\LoggedAccessor;

use Module\ResponseTime\Middleware\ResponseTime;

use Module\Status\Middleware\FlashStatus;
use Module\Status\Middleware\PresentationStatus;
use Module\Status\Middleware\SecurityStatus;

/**
 * Description of SelectorFactory
 *
 * @author pes2704
 */
class SelectorItems {

    /**
     *
     * @var AppInterface
     */
    private $app;

    private $items;

    /**
     * Kontejner pro předání do jednotlivých middleware stacků definovaných při přidávání SelectorItem do vytvářeného objektu Selector.
     *
     * @param AppInterface $app
     */
    public function __construct(AppInterface $app=NULL) {
        $this->app = $app;
        $default = function() {
                return [
                    new ResponseTime(),
                    new SecurityStatus(),
                    new Login(),
                    new FlashStatus(),
                    new PresentationStatus(),
                    new \Middleware\Transformator\Transformator(),
                    new \Middleware\Web\Web()
                ];};

        // všechny položky selektoru dostávají jako stack anonymní funkci, která přijímá AppInterface,
        // ve vzniklých položkách bude dostupná proměnná $app
        $this->items = [
            '/www' => $default,
            '/api'=>
            function() {
                return [
                    new SecurityStatus(),
                    new Login(),
                    new FlashStatus(),
                    new PresentationStatus(),
                    new \Middleware\Api\Api()
                ];},
            '/auth'=>
            function() {
                return [
                    new SecurityStatus(),
                    new FlashStatus(),
                    new PresentationStatus(),
                    new Login()
                ];},
            '/component'=>
            function() {
                return [
                    new ResponseTime(),
                    new SecurityStatus(),
                    new Login(),
                    new FlashStatus(),
                    new \Middleware\Transformator\Transformator(),
                    new \Middleware\Xhr\Component()
                ];},
            '/rs'=>
            function() {
                return [
                    new SecurityStatus(),
                    new LoggedAccess(new LoggedAccessor($this->app)),
                    new \Middleware\Rs\Transformator(),
                    new \Middleware\Rs\Rs()
                ];},
            '/edun'=>
            function() {
                return [
                    new SecurityStatus(),
                    new LoggedAccess(new LoggedAccessor($this->app)),
                    new \Middleware\Edun\Transformator(),
                    new \Middleware\Edun\Edun()
                ];},
            '/staffer'=>
            function() {
                return [
                    new SecurityStatus(),
                    new LoggedAccess(new LoggedAccessor($this->app)),
                    new \Middleware\Staffer\Transformator(),
                    new \Middleware\Staffer\Staffer()
                ];},
//            '/menu/'=>
//            function() {
//                return [
//                    new \Middleware\Logged\LoggedAccess(new LoggedAccessor($this->app)),
//                    (new \Middleware\Menu\Menu())
//                ];},
            '/event'=>
            function() {
                return [
                    new SecurityStatus(),
                    new Login(),
                    new FlashStatus(),
                    new PresentationStatus(),
                    new \Module\Events\Middleware\Api\Api()
                ];},
            '/sendmail'=>
            function() {
                return [
                    //TODO: doplnit basic autentifikaci pro případ něpřihlášeného uživatele.
                    new SecurityStatus(),
                    new Login(),
                    new LoggedAccess(new LoggedAccessor($this->app)),
                    new \Middleware\Sendmail\Sendmail()
                ];},
            '/build'=>
            function() {
                return [
                    //TODO: doplnit basic autentifikaci pro případ něpřihlášeného uživatele.
                    new SecurityStatus(),
                    new Login(),
                    new LoggedAccess(new LoggedAccessor($this->app)),
                    new \Middleware\Build\Build()
                ];},

            '/' => $default,
            ];
    }

    /**
     * Vytvoří objekt Pes\Middleware\Selector a nastaví mu potřebné položky SelectorItem. Objekt Selector je middleware a implementuje Pes\Middleware\ContainerMiddlewareInterface.
     * Proto je schopen přijímat middleware kontejner (metodou setMwContainer rozhraní Pes\Middleware\ContainerMiddlewareInterface).
     * Pokud byl při volání konstruktoru této SelectorFactory nastaven kontejner, je tento kontejner nastaven jako middleware kontejner objektu Selector.
     * Selector svůj middleware kontejner sám nepoužívá, pouze ho předává jako parametr middleware stacku (Closure) vybraného SelectorItem.
     * Pokud je stack při volíní metody addItem() selektoru definován jako anonymní funkce (Closure), kterí jako parametr přijímá kontejner typu Psr\Container\AppInterface,
     * pak této anonymní funkci předán middleware kontejner selektoru. Injektováním aplikačního kontekneru do konstruktoru selektoru a definováním stacků jednotlivých SelectorItem jako anonymní funkce
     * příjímající kontejner typu Psr\Container\AppInterface lze zajistit automatické předávání (propagaci) aplikačního kontejneru do všech SelectorItem a tedy do všech selektovaných middleware stacků.
     *
     * @return Selector
     */
    public function addItems(SelectorInterface $selector) {

        ## selector middleware
        $selector->addItemsArray($this->items);
        return $selector;
    }
}
