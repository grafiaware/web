<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application;

use Pes\Application\AppInterface;
use Pes\Middleware\SelectorInterface;

use Auth\Middleware\Login\Login;
use Auth\Middleware\Logged\LoggedAccess;
use Auth\Middleware\Logged\Service\LoggedAccessor;

use Web\Middleware\Page\Web;
use Red\Middleware\Component\Component;

use Red\Middleware\Redactor\Redactor;
use Red\Middleware\Transformator\Transformator;

use Sendmail\Middleware\Sendmail\Sendmail;

use Build\Middleware\Build\Build;
use ResponseTime\Middleware\ResponseTime;

use Status\Middleware\FlashStatus;
use Status\Middleware\PresentationStatus;
use Status\Middleware\SecurityStatus;

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
                    new Transformator(),
                    new Web()
                ];};

        // všechny položky selektoru dostávají jako stack anonymní funkci, která přijímá AppInterface,
        // ve vzniklých položkách bude dostupná proměnná $app
        $this->items = [
            '/web/v1/page' => $default,
            '/web/v1'=>
            function() {
                return [
                    new ResponseTime(),
                    new SecurityStatus(),
                    new Login(),
                    new FlashStatus(),
                    new PresentationStatus(),
                    new Transformator(),
                    new Component()
                ];},
            '/red'=>
            function() {
                return [
                    new ResponseTime(),
                    new SecurityStatus(),
                    new Login(),
                    new FlashStatus(),
                    new PresentationStatus(),
                    new Redactor()
                ];},

            '/auth'=>
            function() {
                return [
                    new ResponseTime(),
                    new SecurityStatus(),
                    new FlashStatus(),
                    new PresentationStatus(),
                    new Login()
                ];},
            '/event'=>
            function() {
                return [
                    new ResponseTime(),
                    new SecurityStatus(),
                    new Login(),
                    new FlashStatus(),
                    new PresentationStatus(),
                    new \Events\Middleware\Events\Event()
                ];},
            '/sendmail'=>
            function() {
                return [
                    //TODO: doplnit basic autentifikaci pro případ něpřihlášeného uživatele.
                    new SecurityStatus(),
                    new Login(),
                    new LoggedAccess(new LoggedAccessor($this->app)),
                    new Sendmail()
                ];},
            '/build'=>
            function() {
                return [
                    //TODO: doplnit basic autentifikaci pro případ něpřihlášeného uživatele.
                    new SecurityStatus(),
                    new Login(),
                    new LoggedAccess(new LoggedAccessor($this->app)),
                    new Build()
                ];},

            '/' => $default,

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
