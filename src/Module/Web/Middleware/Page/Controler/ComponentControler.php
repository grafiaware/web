<?php
namespace Web\Middleware\Page\Controler;

use FrontControler\ComponentControlerAbstract;

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
        
        // je jen jeden ConponentControler, proto mají VISITOR i REPRESENTATIVE stejná oprávnění ke všem komponentům
        return [
            RoleEnum::AUTHENTICATED => [AccessActionEnum::GET => true],
            RoleEnum::ANONYMOUS => [AccessActionEnum::GET => true]
        ];
    }   
}
