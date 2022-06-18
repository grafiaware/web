<?php
namespace Component\View\Content\Authored\Article;

use Component\View\Content\Authored\AuthoredComponentAbstract;
use Component\ViewModel\Content\Authored\Article\ArticleViewModelInterface;

use Component\Renderer\Html\Content\Authored\SelectTemplateRenderer;
use Component\Renderer\Html\Content\Authored\Article\ArticleRenderer;
use Component\Renderer\Html\Content\Authored\Article\ArticleRendererEditable;
use Component\Renderer\Html\Content\Authored\EmptyContentRenderer;

use Component\Renderer\Html\NoPermittedContentRenderer;

use Access\Enum\AccessPresentationEnum;

/**
 * Description of ArticleComponent
 *
 * @author pes2704
 */
class ArticleComponent extends AuthoredComponentAbstract implements ArticleComponentInterface {

    const CONTENT = 'content';

    const SELECT_TEMPLATE = 'selectTemplate';


}
