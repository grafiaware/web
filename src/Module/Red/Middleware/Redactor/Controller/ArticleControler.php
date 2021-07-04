<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Middleware\Redactor\Controller;

use FrontController\PresentationFrontControllerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Application\AppFactory;
use Pes\Http\Request\RequestParams;
use Pes\Http\Response;
use Pes\Http\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;


use Red\Model\Entity\Article;
use Red\Model\Entity\ArticleInterface;

use Status\Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo
};
use Red\Model\Repository\ArticleRepo;

use UnexpectedValueException;

/**
 * Description of PostController
 *
 * @author pes2704
 */
class ArticleControler extends PresentationFrontControllerAbstract {

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
            $postParams = $request->getParsedBody();
            // jméno POST proměnné je vytvořeno v paper rendereru složením 'article_' a $article->getId()
            // jiné POST parametry nepoužije (pokud jsou renderovány elementy input např. z inputů show a hide - takové parametry musí vyhodnocovat jiná action metoda, t.j. musí se odesílat spolu
            // s jiným formaction (jiné REST uri))
            if (array_key_exists("article_$articleId", $postParams)) {
                $statusPresentation = $this->statusPresentationRepo->get();
                $templateName = $statusPresentation->getLastTemplateName();
                if (isset($templateName) AND $templateName) {
                    $statusPresentation->setLastTemplateName('');
                    $article->setTemplate($templateName);
                    $this->addFlashMessage("Article created from $templateName");
                } else {
                    $article->setContent($postParams["article_$articleId"]);
                    $this->addFlashMessage('Article updated');
                }
            }
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
            $postTemplate = (new RequestParams())->getParam($request, 'template_'.$articleId, 'default');
            $lastTemplate = $this->statusPresentationRepo->get()->getLastTemplateName();
            //TODO: -template je nutné nastavit ve všech jazykovžch verzích
            $article->setTemplate($lastTemplate);
            $this->addFlashMessage("Set template: $lastTemplate");
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
}
