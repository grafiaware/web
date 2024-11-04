<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Events\Middleware\Events\Controler;

use FrontControler\PresentationFrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

// konfigurace
use Site\ConfigurationCache;

// enum
use Red\Model\Enum\AuthoredTypeEnum;
//TODO: oprávnění pro routy
use Access\Enum\RoleEnum;
use Access\Enum\AccessActionEnum;

// komponenty
use Events\Component\View\Data\CompanyComponent;

// renderery
use Pes\View\Renderer\ImplodeRenderer;

####################

use Pes\Text\Html;

####################
//use Pes\Debug\Timer;
use Pes\View\View;

/**
 * Description of ComponentControler
 *
 * @author pes2704
 */
class ComponentControler extends PresentationFrontControlerAbstract {

    protected function getActionPermissions(): array {
        
        // je jen jeden ConponentControler, proto mají VISITOR i REPRESENTATIVE stejná oprávnění ke všem komponentům
        return [
            RoleEnum::SUPERVISOR => [AccessActionEnum::GET => self::class],
            RoleEnum::EDITOR => [AccessActionEnum::GET => self::class],
            RoleEnum::AUTHENTICATED => [AccessActionEnum::GET => self::class],
            RoleEnum::ANONYMOUS => [AccessActionEnum::GET => self::class]
        ];
    }
    
    ### action metody ###############

    public function serviceComponent(ServerRequestInterface $request, $name) {
        if($this->isAllowed(AccessActionEnum::GET)) {
            if (array_key_exists($name, ConfigurationCache::layoutController()['contextServiceMap'])) {
                $service = reset(ConfigurationCache::layoutController()['contextServiceMap'][$name]) ?? null;
                if($this->container->has($service)) {
                    $view = $this->container->get($service);
                } else {
                    $view = $this->errorView($request, "Component $service is not defined (configured) in container.");                    
                }
            } else {
                $view = $this->errorView($request, "Component $name undefined in configuration of context service map.");
            }
        } else {
            $view =  $this->getNonPermittedContentView(AccessActionEnum::GET);
        }
        return $this->createStringOKResponseFromView($view);
    }

    public function component(ServerRequestInterface $request, $name) {
        if($this->isAllowed(AccessActionEnum::GET)) {
            if($this->container->has($name)) {   // musí být definován alias
                $view = $this->container->get($name);
            } else {
                $view = $this->errorView($request, "Component $name is not defined (configured) or have no alias in container.");                    
            }
        } else {
            $view =  $this->getNonPermittedContentView(AccessActionEnum::GET);
        }
        return $this->createStringOKResponseFromView($view);
    }
    
###################
    private function errorView(ServerRequestInterface $request, $message = '') {
        $view = $this->container->get(View::class);
        $view->setData([Html::tag('div', ['style'=>'display: none;' ], $message)]);
        $view->setRenderer(new ImplodeRenderer());
        return $view;
    }    
}
