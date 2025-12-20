<?php
namespace Auth\Middleware\Login\Controler;

use FrontControler\ComponentControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

// enum
use Access\Enum\RoleEnum;
use Access\Enum\AccessActionEnum;

//use Pes\Debug\Timer;

/**
 * Description of ComponentControler
 *
 * @author pes2704
 */
class ComponentControler extends ComponentControlerAbstract {

    protected function getActionPermissions(): array {
        
        // prakticky má oprávnění každý - "GET" controler 
        return [
            RoleEnum::AUTHENTICATED => [AccessActionEnum::GET => true],
            RoleEnum::ANONYMOUS => [AccessActionEnum::GET => true]
        ];
    }
    
}
