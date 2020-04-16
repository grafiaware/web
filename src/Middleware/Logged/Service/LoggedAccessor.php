<?php

namespace Middleware\Logged\Service;

use Pes\Application\AppInterface;
use Model\Repository\StatusSecurityRepo;

/**
 * Description of Logged
 *
 * @author pes2704
 */
class LoggedAccessor implements AccessorInterface {

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
            $user = $securityStatus->getUser();
            if ($user) {
                return $user->getRole() ? TRUE : FALSE;
            }
        }
        return FALSE;
    }
}
