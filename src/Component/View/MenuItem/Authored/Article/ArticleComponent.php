<?php
namespace Component\View\MenuItem\Authored\Article;

use Component\View\MenuItem\Authored\AuthoredComponentAbstract;
use Component\ViewModel\MenuItem\Authored\Article\ArticleViewModelInterface;

use Component\Renderer\Html\Authored\SelectTemplateRenderer;
use Component\Renderer\Html\Authored\Article\ArticleRenderer;
use Component\Renderer\Html\Authored\Article\ArticleRendererEditable;
use Component\Renderer\Html\Authored\EmptyContentRenderer;

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
