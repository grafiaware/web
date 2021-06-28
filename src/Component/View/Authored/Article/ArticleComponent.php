<?php
namespace Component\View\Authored\Article;

use Component\View\Authored\AuthoredComponentAbstract;
use Component\ViewModel\Authored\Article\ArticleViewModelInterface;

/**
 * Description of ArticleComponent
 *
 * @author pes2704
 */
class ArticleComponent extends AuthoredComponentAbstract implements ArticleComponentInterface {
    //TODO:
//    vytvořit ArticleComponent (a interface)
//    ?? Menu přesunout?
//    ContentComponent -> TemplateSelectComponent
//    AuthoredComponentAbstract o úroveň výš


    /**
     *
     * @var ArticleViewModelInterface
     */
    protected $contextData;

    /**
     * Přetěžuje metodu View. Generuje PHP template z názvu template objektu Paper a použije ji.
     */
    public function beforeRenderingHook(): void {
        $article = $this->contextData->getArticle();
        if (isset($article)) {
            try {
                $template = $this->resolveTemplateAndShared($article->getTemplate());
                $this->setTemplate($template);
            } catch (NoTemplateFileException $noTemplExc) {
                user_error("Neexistuje soubor šablony '{$this->getTemplatePath($article->getTemplate())}'", E_USER_WARNING);
                $this->setTemplate(null);
            }
        }
    }

}
