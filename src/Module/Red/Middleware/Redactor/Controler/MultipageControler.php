<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Middleware\Redactor\Controler;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Application\AppFactory;
use Pes\Http\Request\RequestParams;
use Pes\Http\Response;
use Pes\Http\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;


use Red\Model\Entity\Article;
use Red\Model\Entity\ArticleInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Status\Model\Enum\FlashSeverityEnum;

use Red\Model\Repository\MultipageRepo;
use View\Includer;

use UnexpectedValueException;

/**
 * Description of PostControler
 *
 * @author pes2704
 */
class MultipageControler extends AuthoredControlerAbstract {

    const MULTIPAGE_TEMPLATE_NAME = 'multipage_template';

    private $multipageRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            MultipageRepo $multipageRepo) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->multipageRepo = $multipageRepo;
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param type $multipageId
     * @return ResponseInterface
     */
    public function template(ServerRequestInterface $request, $multipageId): ResponseInterface {
        $multipage = $this->multipageRepo->get($multipageId);
        if (!isset($multipage)) {
            user_error("Neexistuje multipage se zadaným id $multipageId");
        } else {
            $statusPresentation = $this->statusPresentationRepo->get();
            $lastTemplateName = $statusPresentation->getLastTemplateName() ?? '';
            $statusPresentation->setLastTemplateName('');
            //TODO: -template je nutné nastavit ve všech jazykových verzích ?? možná ne
            $multipage->setTemplate($lastTemplateName);
            $this->addFlashMessage("Set multipage template: $lastTemplateName", FlashSeverityEnum::SUCCESS);
        }
        return $this->createJsonPutNoContentResponse(["refresh"=>"norefresh"]);
//        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param type $multipageId
     * @return ResponseInterface
     */
    public function templateRemove(ServerRequestInterface $request, $multipageId): ResponseInterface {
        $multipage = $this->multipageRepo->get($multipageId);
        if (!isset($multipage)) {
            user_error("Neexistuje multipage se zadaným id $multipageId");
        } else {
            $oldTemplate = $multipage->getTemplate();
            $multipage->setTemplate('');
            $this->addFlashMessage("Removed multipage template $oldTemplate.", FlashSeverityEnum::SUCCESS);
        }
        return $this->createJsonPutNoContentResponse(["refresh"=>"norefresh"]);
//        return $this->redirectSeeLastGet($request); // 303 See Other
    }
}
