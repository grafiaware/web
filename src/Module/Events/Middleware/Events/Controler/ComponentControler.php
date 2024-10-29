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
use Access\Enum\AllowedActionEnum;

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
            RoleEnum::SUPERVISOR => [AllowedActionEnum::GET => self::class],
            RoleEnum::EDITOR => [AllowedActionEnum::GET => self::class],
            RoleEnum::AUTHENTICATED => [AllowedActionEnum::GET => self::class],
            RoleEnum::ANONYMOUS => [AllowedActionEnum::GET => self::class]
        ];
    }
    
    ### action metody ###############

    public function serviceComponent(ServerRequestInterface $request, $name) {
        if($this->isAllowed(AllowedActionEnum::GET)) {
            if (array_key_exists($name, ConfigurationCache::layoutController()['contextServiceMap'])) {
                $service = reset(ConfigurationCache::layoutController()['contextServiceMap'][$name]) ?? null;
            } else {
                $view = $this->errorView($request, "Component $name undefined in configuration of context service map.");
            }
            if($this->container->has($service)) {
                $view = $this->container->get($service);
            } else {
                $view = $this->errorView($request, "Component $service is not defined (configured) in container.");                    
            }
        } else {
            $view =  $this->getNonPermittedContentView(AllowedActionEnum::GET, AuthoredTypeEnum::PAPER);
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
