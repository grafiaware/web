<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application;

use Pes\Application\AppInterface;
use Pes\Application\Middleware\SelectorInterface;

use Auth\Middleware\Login\Login;
use Firewall\Middleware\Firewall;
use Firewall\Middleware\Rule\IsLogged;
use Firewall\Middleware\Rule\HasRole;

use Ping\Middleware\Ping;
use Web\Middleware\Page\Web;

use Red\Middleware\Redactor\Redactor;
use Transformator\Middleware\Transformator\Transformator;
use Events\Middleware\Events\ValidateUser;
use Events\Middleware\Events\Events;
use Sendmail\Middleware\Sendmail\Sendmail;

use Build\Middleware\Build\Build;
use ResponseTime\Middleware\ResponseTime;
use Consent\Middleware\ConsentLogger\ConsentLogger;

use Status\Middleware\FlashStatus;
use Status\Middleware\PresentationStatus;
use Status\Middleware\SecurityStatus;
use Status\Middleware\UnlockStatus;

use Access\Enum\RoleEnum;

/**
 * Middleware stacky selektoru — jen prefixy zapnuté v {@see DeployComposition}.
 *
 * @author pes2704
 */
class SelectorItems {

    /**
     * @var AppInterface
     */
    private $app;

    /**
     * @var array<string, callable>
     */
    private $items;

    /**
     * @var DeployComposition
     */
    private $composition;

    /**
     * @param AppInterface|null $app
     * @param DeployComposition|null $composition null = sestava aktivní site
     */
    public function __construct(?AppInterface $app = null, ?DeployComposition $composition = null) {
        $this->app = $app;
        $this->composition = $composition ?? DeployComposition::forActiveSite();
        $allowed = array_flip($this->composition->selectorPrefixes());
        $this->items = array_intersect_key($this->defineAllStacks(), $allowed);
    }

    public function getComposition(): DeployComposition {
        return $this->composition;
    }

    /**
     * Všechny známé stacky (katalog). Deploy vybere podmnožinu.
     *
     * @return array<string, callable>
     */
    private function defineAllStacks(): array {
        $default = function () {
            return [
                new ResponseTime(),
                new SecurityStatus(),
                new Login(),
                new FlashStatus(),
                new PresentationStatus(),
                new UnlockStatus(),
                new Transformator(),
                new Web(),
            ];
        };

        return [
            '/web' => $default,
            '/ping' =>
            function () {
                return [
                    new ResponseTime(),
                    new Ping(),
                ];
            },
            '/red' =>
            function () {
                return [
                    new ResponseTime(),
                    new SecurityStatus(),
                    new FlashStatus(),
                    new PresentationStatus(),
                    new UnlockStatus(),
                    new Transformator(),
                    new Redactor(),
                ];
            },
            '/auth' =>
            function () {
                return [
                    new ResponseTime(),
                    new SecurityStatus(),
                    new FlashStatus(),
                    new PresentationStatus(),
                    new UnlockStatus(),
                    new Login(),
                ];
            },
            '/events' =>
            function () {
                return [
                    new ResponseTime(),
                    new SecurityStatus(),
                    new FlashStatus(),
                    new PresentationStatus(),
                    new ValidateUser(),
                    new UnlockStatus(),
                    new Events(),
                ];
            },
            '/sendmail' =>
            function () {
                return [
                    new SecurityStatus(),
                    new Firewall(new HasRole($this->app, RoleEnum::SUPERVISOR)),
                    new UnlockStatus(),
                    new Sendmail(),
                ];
            },
            '/build' =>
            function () {
                return [
                    new SecurityStatus(),
                    new Firewall(new HasRole($this->app, RoleEnum::SUPERVISOR)),
                    new UnlockStatus(),
                    new Build(),
                ];
            },
            '/consent' =>
            function () {
                return [
                    new ConsentLogger(),
                ];
            },
            '/' => $default,
            '/rs' =>
            function () {
                return [
                    new SecurityStatus(),
                    new Firewall(new IsLogged($this->app)),
                    new UnlockStatus(),
                    new \Middleware\Rs\Transformator(),
                    new \Middleware\Rs\Rs(),
                ];
            },
            '/edun' =>
            function () {
                return [
                    new SecurityStatus(),
                    new Firewall(new IsLogged($this->app)),
                    new UnlockStatus(),
                    new \Middleware\Edun\Transformator(),
                    new \Middleware\Edun\Edun(),
                ];
            },
            '/staffer' =>
            function () {
                return [
                    new SecurityStatus(),
                    new Firewall(new IsLogged($this->app)),
                    new UnlockStatus(),
                    new \Middleware\Staffer\Transformator(),
                    new \Middleware\Staffer\Staffer(),
                ];
            },
        ];
    }

    /**
     * @return SelectorInterface
     */
    public function addItems(SelectorInterface $selector) {
        $selector->addItemsArray($this->items);
        return $selector;
    }

    /**
     * @return list<string>
     */
    public function getSelectorPrefixes(): array {
        return array_keys($this->items);
    }

    /**
     * Id API katalogů pro whitelist ResourceRegistry.
     *
     * @return list<string>
     */
    public function getEnabledApiModuleIds(): array {
        return $this->composition->apiModuleIds();
    }
}
