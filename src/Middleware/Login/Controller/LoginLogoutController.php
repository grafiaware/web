<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Login\Controller;

use Pes\Container\ContainerSettingsAwareInterface;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Request\RequestParams;

// model
use StatusModel\StatusSecurityModelInterface;
use Model\Repository\UserRepo;
use Model\Entity\UserInterface;
use Pes\Database\Handler\AccountInterface;
use Pes\Database\Handler\Handler;

use Pes\Application\AppFactory;
use Pes\Http\Response;
use Pes\Http\Response\RedirectResponse;


/**
 * Description of PostController
 *
 * @author pes2704
 */
class LoginLogoutController {

    const JMENO_FIELD_NAME = "jmenowwwgrafia";
    const HESLO_FIELD_NAME = "heslowwwgrafia";

    private $statusSecurityModel;
    private $SecurityContextObjects = [];

    private $userRepo;

    /**
     *
     * @param ContainerSettingsAwareInterface $container
     */
    public function __construct(StatusSecurityModelInterface $statusSecurityModel, UserRepo $userRepo) {
        $this->statusSecurityModel = $statusSecurityModel;
        $this->userRepo = $userRepo;

    }

    public function login(ServerRequestInterface $request) {
        $requestParams = new RequestParams();
        $login = $requestParams->getParsedBodyParam($request, 'login', FALSE);

        if ($login) {
            $loginJmeno = $requestParams->getParsedBodyParam($request, self::JMENO_FIELD_NAME, FALSE);
            $loginHeslo = $requestParams->getParsedBodyParam($request, self::HESLO_FIELD_NAME, FALSE);
            if ($loginJmeno AND $loginHeslo) {
                $authenticatedUser = $this->userRepo->getByAuthentication($loginJmeno, $loginHeslo);  // user z databáze
                if (isset($authenticatedUser) AND $authenticatedUser) {
                    $this->changeSecurityContext($authenticatedUser);
                }
            }
        }
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
    }

    public function logout(ServerRequestInterface $request) {
        $logout = (new RequestParams())->getParsedBodyParam($request, 'logout', FALSE);
        if ($logout) {
            $this->changeSecurityContext();  // bez parametru User
        }
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
    }

    /**
     * Při změně bezpečnostního kontextu je nutné odstranit údaje bezpečnostního kontextu a objekty vzniklé s použitím bezpečnostního kontextu
     *
     * @throws \LogicException
     */
    private function changeSecurityContext(UserInterface $authenticatedUser=NULL) {
        // smaž starý security status a vytvoř nový
        $this->statusSecurityModel->regenerateSecurityStatus();

        // nový security status
        if (isset($authenticatedUser)) {
            $this->statusSecurityModel->getStatusSecurity()->setUser($authenticatedUser);   // ulož user do security statusu (uložen v session)
        }
    }
}
