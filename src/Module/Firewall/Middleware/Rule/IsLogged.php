<?php

namespace Firewall\Middleware\Rule;

use Pes\Application\AppInterface;
use Status\Model\Repository\StatusSecurityRepo;

/**
 * Description of Logged
 *
 * @author pes2704
 */
class IsLogged implements RoleInterface {

    /**
     * @var AppInterface
     */
    private $app;

    public function __construct(AppInterface $app) {
        $this->app = $app;
    }

    public function granted( ) {
        //session
        /* @var $securityStatusRepo StatusSecurityRepo */
        $securityStatusRepo = $this->app->getAppContainer()->get(StatusSecurityRepo::class);
        $securityStatus = $securityStatusRepo->get();
        if (isset($securityStatus)) {
            $loginAggregate = $securityStatus->getLoginAggregate();
            return (isset($loginAggregate) AND $loginAggregate->getCredentials()) ? TRUE : FALSE;
        }
        return FALSE;
    }
    
    public function restrictMessage() {
        return "Přístup mají pouze přihlášení uživatelé.";
    }
}
