<?php

namespace Firewall\Middleware\Rule;

use Pes\Application\AppInterface;
use Status\Model\Repository\StatusSecurityRepo;
use Access\Enum\RoleEnum;

/**
 * Description of Logged
 *
 * @author pes2704
 */
class HasRole implements RoleInterface {

    /**
     * @var AppInterface
     */
    private $app;
    
    /**
     * 
     * @var string
     */
    private $role;

    public function __construct(AppInterface $app, $role) {
        $this->app = $app;
        $enum = new RoleEnum();
        $this->role = $enum($role);
    }

    public function granted( ) {
        //session
        /* @var $securityStatusRepo StatusSecurityRepo */
        $securityStatusRepo = $this->app->getAppContainer()->get(StatusSecurityRepo::class);
        $securityStatus = $securityStatusRepo->get();
        if (isset($securityStatus)) {
            $loginAggregate = $securityStatus->getLoginAggregate();
            if (isset($loginAggregate)) {
                $loggedInRole = $securityStatus->getLoginAggregate()->getCredentials()->getRoleFk();
            }
            return (isset($loggedInRole) AND $loggedInRole==$this->role) ? TRUE : FALSE;
        }
        return FALSE;
    }
    
    public function restrictMessage() {
        return "Přístup mají uživatelé s určenou rolí.";
    }    
}
