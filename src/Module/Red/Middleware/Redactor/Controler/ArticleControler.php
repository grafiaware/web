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

use Red\Model\Repository\ArticleRepo;
use View\Includer;

use UnexpectedValueException;

/**
 * Description of PostControler
 *
 * @author pes2704
 */
class ArticleControler extends AuthoredControlerAbstract {

    const ARTICLE_TEMPLATE_NAME = 'article-template';
    const ARTICLE_CONTENT = 'article-content';

    private $articleRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            ArticleRepo $articleRepo) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->articleRepo = $articleRepo;
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param type $articleId
     * @return ResponseInterface
     */
    public function update(ServerRequestInterface $request, $articleId): ResponseInterface {
        /** @var ArticleInterface $article */
        $article = $this->articleRepo->get($articleId);
        if (!isset($article)) {
            user_error("Neexistuje article se zadaným id $articleId");
        } else {
            $namePrefix = self::ARTICLE_CONTENT.$articleId;
            $articlePost = $this->paramValue($request, $namePrefix);
            $statusPresentation = $this->statusPresentationRepo->get();
            $statusPresentation->setLastTemplateName('');
            $article->setContent($articlePost);
            $this->addFlashMessage('Article updated', FlashSeverityEnum::SUCCESS);
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param type $articleId
     * @return ResponseInterface
     */
    public function template(ServerRequestInterface $request, $articleId): ResponseInterface {
        $article = $this->articleRepo->get($articleId);
        if (!isset($article)) {
            user_error("Neexistuje article se zadaným id $articleId");
        } else {
            $postTemplateContent = (new RequestParams())->getParam($request, self::ARTICLE_TEMPLATE_CONTENT.$articleId, '');
            $statusPresentation = $this->statusPresentationRepo->get();
            $lastTemplateName = $statusPresentation->getLastTemplateName() ?? '';
            $statusPresentation->setLastTemplateName('');
            //TODO: -template je nutné nastavit ve všech jazykových verzích ?? možná ne
            $article->setTemplate($lastTemplateName);
            $article->setContent($postTemplateContent);
            $this->addFlashMessage("Set content with template: $lastTemplateName", FlashSeverityEnum::SUCCESS);
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
}
