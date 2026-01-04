<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Middleware\Redactor\Controler;

use FrontControler\FrontControlerAbstract;
use Psr\Http\Message\ServerRequestInterface;

use Pes\Application\AppFactory;
use Pes\Http\Request\RequestParams;
use Pes\Http\Response;
use Pes\Http\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;


use Red\Model\Entity\StaticItemClass;
use Red\Model\Entity\StaticItemInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Status\Model\Enum\FlashSeverityEnum;

use Red\Model\Repository\StaticItemRepo;
use View\Includer;

use UnexpectedValueException;

/**
 * Description of PostControler
 *
 * @author pes2704
 */
class StaticControler extends FrontControlerAbstract {

    const PATH_VAR_NAME = "path";
    const TEMPLATE_VAR_NAME = "template";
    
    private $staticRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StaticItemRepo $staticRepo) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->staticRepo = $staticRepo;
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param type $staticId
     * @return ResponseInterface
     */
    public function update(ServerRequestInterface $request, $staticId): ResponseInterface {
        
        $path = (new RequestParams())->getParam($request, self::PATH_VAR_NAME);
        $template = (new RequestParams())->getParam($request, self::TEMPLATE_VAR_NAME);        
        $static = $this->staticRepo->get($staticId);
        $static->setPath($path);
        $static->setTemplate($template);
        $static->setCreator($this->statusSecurityRepo->get()->getLoginAggregate()->getLoginName());
        
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
}
