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

use Red\Model\Repository\PaperAggregateContentsRepo;

use UnexpectedValueException;

/**
 * Description of PostControler
 *
 * @author pes2704
 */
class PaperControler extends AuthoredControlerAbstract {

    const PEREX_CONTENT = 'perex_content';
    const HEADLINE_CONTENT = 'headline_content';
    const SECTION_CONTENT = 'section_content';


    private $paperAggregateRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            PaperAggregateContentsRepo $paperAggregateRepo) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->paperAggregateRepo = $paperAggregateRepo;
    }


    /**
     *
     * @param ServerRequestInterface $request
     * @param type $paperId
     * @return ResponseInterface
     */
    public function update(ServerRequestInterface $request, $paperId): ResponseInterface {
        /** @var PaperAggregatePaperSectionInterface $paperAggregate */
        $paperAggregate = $this->paperAggregateRepo->get($paperId);
        if (!isset($paperAggregate)) {
            user_error("Neexistuje paper se zadaným id.$paperId");
        } else {
            $postParams = $request->getParsedBody();
            // jméno POST proměnné je vytvořeno v paper rendereru složením 'headline_' nebo 'perex_' nebo 'content_' a $paper->getId()
            // jiné POST parametry nepoužije (pokud jsou renderovány elementy input např. z inputů show a hide - takové parametry musí vyhodnocovat jiná action metoda, t.j. musí se odesílat spolu
            // s jiným formaction (jiné REST uri))
            if (array_key_exists("template_$paperId", $postParams)) {
                $paperAggregate->setHeadline($postParams["template_$paperId"]);
                $this->addFlashMessage('Template updated', FlashSeverityEnum::SUCCESS);
            }
            if (array_key_exists("headline_$paperId", $postParams)) {
                $paperAggregate->setHeadline($postParams["headline_$paperId"]);
                $this->addFlashMessage('Headline updated', FlashSeverityEnum::SUCCESS);
            }
            if (array_key_exists("perex_$paperId", $postParams)) {
                $paperAggregate->setPerex($postParams["perex_$paperId"]);
                $this->addFlashMessage('Perex updated', FlashSeverityEnum::SUCCESS);
            }
            foreach ($paperAggregate->getPaperContentsArray() as $content) {
                if (array_key_exists("content_{$content->getId()}", $postParams)) {
                    $content->setContent($postParams["content_{$content->getId()}"]);
                    $this->addFlashMessage('Content updated', FlashSeverityEnum::SUCCESS);
                }
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
    public function updateHeadline(ServerRequestInterface $request, $paperId): ResponseInterface {
        /** @var PaperAggregatePaperSectionInterface $paperAggregate */
        $paperAggregate = $this->paperAggregateRepo->get($paperId);
        if (!isset($paperAggregate)) {
            user_error("Neexistuje paper se zadaným id.$paperId");
        } else {
            $postParams = $request->getParsedBody();
            $name = self::HEADLINE_CONTENT."$paperId";
            foreach ($postParams as $key => $value) {
                if (strpos($key, $name)===0) {
                    $paperAggregate->setHeadline($value);
                    $this->addFlashMessage('Headline updated', FlashSeverityEnum::SUCCESS);
                }
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
    public function updatePerex(ServerRequestInterface $request, $paperId): ResponseInterface {
        /** @var PaperAggregatePaperSectionInterface $paperAggregate */
        $paperAggregate = $this->paperAggregateRepo->get($paperId);
        if (!isset($paperAggregate)) {
            user_error("Neexistuje paper se zadaným id.$paperId");
        } else {
            $postParams = $request->getParsedBody();
            $name = self::PEREX_CONTENT."$paperId";
            foreach ($postParams as $key => $value) {
                if (strpos($key, $name)===0) {
                    $paperAggregate->setPerex($value);
                    $this->addFlashMessage('Perex updated', FlashSeverityEnum::SUCCESS);
                }
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
    public function updateSection(ServerRequestInterface $request, $paperId, $contentId): ResponseInterface {
        /** @var PaperAggregatePaperSectionInterface $paperAggregate */
        $paperAggregate = $this->paperAggregateRepo->get($paperId);
        if (!isset($paperAggregate)) {
            user_error("Neexistuje paper se zadaným id.$paperId");
        } else {
            $section = $paperAggregate->getPaperSection($contentId);
            $postParams = $request->getParsedBody();
            $name = self::SECTION_CONTENT."$paperId";
            foreach ($postParams as $key => $value) {
                if (strpos($key, $name)===0) {
                    $section->setContent($value);
                    $this->addFlashMessage('Section updated', FlashSeverityEnum::SUCCESS);
                }
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
            $postTemplateName = (new RequestParams())->getParam($request, 'template_'.$paperId, '');
            $postTemplateContent = (new RequestParams())->getParam($request, 'article_'.$paperId, '');
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
