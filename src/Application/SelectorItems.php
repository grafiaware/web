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
use Ping\Middleware\Ping;
use Web\Middleware\Page\Web;
use Red\Middleware\Component\Component;

use Red\Middleware\Redactor\Redactor;
use Transformator\Middleware\Transformator\Transformator;
use Events\Middleware\Events\EventsLoginSync;
use Events\Middleware\Events\Events;
use Sendmail\Middleware\Sendmail\Sendmail;

use Build\Middleware\Build\Build;
use ResponseTime\Middleware\ResponseTime;
use Consent\Middleware\ConsentLogger\ConsentLogger;

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
     * Kostruktor, opbsahuje definice všech middleware stacků.
     *
     * Jako parametr přijímá aplikaci (AppInterface objekt).
     * Položky selektoru jsou v tomto konstruktoru definovány jako asiciativní pole, klíč je prefix položky selektoru a hodnota je middleware stack
     *
     * Pokud definice stacku pro selector item je anonymní funkce, je po vybrání middleware v selectoru podle prefixu tato anonymní funkce zavolána
     * a objekt aplikace předán jako jako parametr této anonymní funkce.
     * K tomu dojde při volání vybrané položky SelectorItem v metodě process() Selectoru.
     *
     * @param AppInterface $app Kontejner pro předání do všech middleware stacků definovaných v konstruktoru.
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

        //
        $this->items = [
            '/web/v1/page' => $default,
            '/ping'=>
            function() {
                return [
                    new ResponseTime(),
                    new Ping(),
                ];},
            '/red'=>
            function() {
                return [
                    new ResponseTime(),
                    new SecurityStatus(),
                    new Login(),
                    new FlashStatus(),
                    new PresentationStatus(),
                    new Transformator(),
                    new Redactor()
                ];},

            '/auth'=>
            function() {
                return [
                    new ResponseTime(),
                    new SecurityStatus(),
                    new FlashStatus(),
//                    new PresentationStatus(),
                    new Login()
                ];},
            '/events'=>
            function() {
                return [
                    new ResponseTime(),
                    new SecurityStatus(),
                    new Login(),
                    new FlashStatus(),
                    new PresentationStatus(),
                    new EventsLoginSync(),
                    new Events()
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
            '/consent'=>
            function() {
                return [
                    new ConsentLogger(),
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
     * Vytvoří objekt Pes\Middleware\Selector a nastaví mu potřebné položky SelectorItem.
     *
     * Objekt Selector je middleware a implementuje Pes\Middleware\ContainerMiddlewareInterface.
     * Proto je schopen přijímat middleware kontejner (metodou setMwContainer rozhraní Pes\Middleware\ContainerMiddlewareInterface).
     * Pokud byl při volání konstruktoru této SelectorFactory nastaven kontejner, je tento kontejner nastaven jako middleware kontejner objektu Selector.
     * Selector svůj middleware kontejner sám nepoužívá, pouze ho předává jako parametr middleware stacku (Closure) vybraného SelectorItem.
     * Pokud je stack při volíní metody addItem() selektoru definován jako anonymní funkce (Closure), která jako parametr přijímá kontejner typu Psr\Container\AppInterface,
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
