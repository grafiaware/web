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


use Red\Model\Entity\Paper;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Status\Model\Enum\FlashSeverityEnum;

use Red\Model\Repository\PaperAggregateSectionsRepo;

use UnexpectedValueException;

/**
 * Description of PostControler
 *
 * @author pes2704
 */
class PaperControler extends AuthoredControlerAbstract {

    const PEREX_CONTENT = 'perex-content';
    const HEADLINE_CONTENT = 'headline-content';

    private $paperAggregateRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            PaperAggregateSectionsRepo $paperAggregateRepo) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->paperAggregateRepo = $paperAggregateRepo;
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param type $paperId
     * @return ResponseInterface
     */
    public function updateHeadline(ServerRequestInterface $request, $paperId): ResponseInterface {
        /** @var PaperAggregatePaperSectionInterface $paperAggregate */
        $paperAggregate = $this->paperAggregateRepo->get($paperId);
        if (!isset($paperAggregate)) {
            $errorMessage = "Neexistuje paper se zadaným id.$paperId";
            user_error($errorMessage, E_USER_WARNING);
            $this->addFlashMessage($errorMessage, FlashSeverityEnum::WARNING);            
        } else {
            $namePrefix = implode("_", [self::HEADLINE_CONTENT, $paperId]);
            $headlinePost = $this->paramValue($request, $namePrefix);
            if (false===$headlinePost) {
                $errorMessage = "Pokušíte se uložit prázdný obsah!";
                $this->addFlashMessage($errorMessage, FlashSeverityEnum::WARNING);
            } else {    
                $paperAggregate->setHeadline($headlinePost);
                $this->addFlashMessage('Headline updated', FlashSeverityEnum::SUCCESS);
            }
        }
        return $this->createResponseFromString("");
//        $json = json_encode(['saved'=>$headlinePost]);  //
//        if (false!==$json) {
//            return $this->createJsonResponse($request, $json);
//        } else {
//            $this->addFlashMessage('ERROR Chyba serveru - Headline nelze uložit.', FlashSeverityEnum::WARNING);            
//            return $this->redirectSeeLastGet($request); // 303 See Other - pro zobrazení flash
//        }
//        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param type $paperId
     * @return ResponseInterface
     */
    public function updatePerex(ServerRequestInterface $request, $paperId): ResponseInterface {
        /** @var PaperAggregatePaperSectionInterface $paperAggregate */
        $paperAggregate = $this->paperAggregateRepo->get($paperId);
        if (!isset($paperAggregate)) {
            $errorMessage = "Neexistuje paper se zadaným id.$paperId";
            $this->addFlashMessage($errorMessage, FlashSeverityEnum::WARNING); 
        } else {
            $namePrefix = implode("_", [self::PEREX_CONTENT, $paperId]);
            $perexPost = $this->paramValue($request, $namePrefix);
            if (false===$perexPost) {
                $errorMessage = "Pokušíte se uložit prázdný obsah!";
                $this->addFlashMessage($errorMessage, FlashSeverityEnum::WARNING);
            } else {            
                $paperAggregate->setPerex($perexPost);
                $this->addFlashMessage('Perex updated', FlashSeverityEnum::SUCCESS);
            }
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param type $paperId
     * @return ResponseInterface
     */
    public function template(ServerRequestInterface $request, $paperId): ResponseInterface {
        $paper = $this->paperAggregateRepo->get($paperId);
        if (!isset($paper)) {
            user_error("Neexistuje paper se zadaným id $paperId");
        } else {
            $statusPresentation = $this->statusPresentationRepo->get();
            $templateName = $statusPresentation->getLastTemplateName();
            if (isset($templateName) AND $templateName) {
                $statusPresentation->setLastTemplateName('');
                $paper->setTemplate($templateName);
                $this->addFlashMessage("Set paper template: $templateName", FlashSeverityEnum::SUCCESS);
            }
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param type $paperId
     * @return ResponseInterface
     */
    public function templateRemove(ServerRequestInterface $request, $paperId): ResponseInterface {
        $paper = $this->paperAggregateRepo->get($paperId);
        if (!isset($paper)) {
            user_error("Neexistuje paper se zadaným id $paperId");
        } else {
            $oldTemplate = $paper->getTemplate();
            $paper->setTemplate('');
            $this->addFlashMessage("Removed paper template $oldTemplate.", FlashSeverityEnum::SUCCESS);
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

}
