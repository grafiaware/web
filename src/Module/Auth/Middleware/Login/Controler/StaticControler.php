<?php
namespace Auth\Middleware\Login\Controler;

use FrontControler\StaticComponentControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

// konfigurace
use Site\ConfigurationCache;

// enum
use Red\Model\Enum\AuthoredTypeEnum;
use Access\Enum\RoleEnum;
use Access\Enum\AccessActionEnum;

// view model
use Component\ViewModel\StaticItemViewModel;

use Component\View\StaticItemComponent;
use Component\View\StaticItemComponentInterface;

//use Pes\Debug\Timer;

/**
 * Description of StaticControler
 *
 * @author pes2704
 */
class StaticControler extends StaticComponentControlerAbstract {

    protected function getActionPermissions(): array {
        return [
            RoleEnum::AUTHENTICATED => [AccessActionEnum::GET => true],
            RoleEnum::ANONYMOUS => [AccessActionEnum::GET => true]
        ];
    }
    
}
