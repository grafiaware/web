<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Login\Controller;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Request\RequestParams;
use Security\Auth\NamePasswordAuthenticatorInterface;

// controller
use Controller\StatusFrontControllerAbstract;

// model
use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusFlashRepo;
use Model\Entity\StatusSecurity;
use Model\Entity\StatusSecurityInterface;
use Model\Repository\UserRepo;

use Pes\Application\AppFactory;
use Pes\Http\Response;
use Pes\Http\Response\RedirectResponse;


/**
 * Description of PostController
 *
 * @author pes2704
 */
class LoginLogoutController extends StatusFrontControllerAbstract {

    // konstanty individualizované pro jeden web
    const JMENO_FIELD_NAME = "jmenowwwgrafia";
    const HESLO_FIELD_NAME = "heslowwwgrafia";

    private $authenticator;

    private $userRepo;

    /**
     *
     */
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            UserRepo $userRepo,
            NamePasswordAuthenticatorInterface $authenticator) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo);
        $this->userRepo = $userRepo;
        $this->authenticator = $authenticator;
    }

    public function login(ServerRequestInterface $request) {
        $requestParams = new RequestParams();
        $login = $requestParams->getParsedBodyParam($request, 'login', FALSE);

        if ($login) {
            // používá konstanty třídy pro omezení množství našeptávaných jmen při vypl%nování formuláře v prohlížečích
            $loginJmeno = $requestParams->getParsedBodyParam($request, self::JMENO_FIELD_NAME, FALSE);
            $loginHeslo = $requestParams->getParsedBodyParam($request, self::HESLO_FIELD_NAME, FALSE);
            if ($loginJmeno AND $loginHeslo) {
                if ($this->authenticator->authenticate($loginJmeno, $loginHeslo)) {  // z databáze
                    $this->setLoggedUser($loginJmeno);
                }
            }
        }
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
    }

    public function logout(ServerRequestInterface $request) {
        $logout = (new RequestParams())->getParsedBodyParam($request, 'logout', FALSE);
        if ($logout) {
            $this->removeLoggedUser();  // bez parametru User
        }
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
    }

    /**
     * Při změně bezpečnostního kontextu je nutné odstranit údaje bezpečnostního kontextu a objekty vzniklé s použitím bezpečnostního kontextu
     *
     * @throws \LogicException
     */
    private function setLoggedUser($loginJmeno) {
        $dbUser = $this->userRepo->get($loginJmeno);
        if ($dbUser) {
            $this->statusSecurityRepo->get()->renewSecurityStatus($dbUser);
        } else {
            $this->statusSecurityRepo->get()->renewSecurityStatus(null);
            user_error("Pro ověřené login jméno nebyl následně načten user z databáze.", E_USER_WARNING);
        }
    }

    private function removeLoggedUser() {
        $this->statusSecurityRepo->get()->renewSecurityStatus(null);
    }
}
