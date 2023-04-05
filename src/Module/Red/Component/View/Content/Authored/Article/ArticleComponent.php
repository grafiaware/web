<?php
namespace Red\Component\View\Content\Authored\Article;

use Red\Component\View\Content\Authored\AuthoredComponentAbstract;
use Red\Component\ViewModel\Content\Authored\Article\ArticleViewModelInterface;

use Red\Component\Renderer\Html\Content\Authored\SelectTemplateRenderer;
use Red\Component\Renderer\Html\Content\Authored\Article\ArticleRenderer;
use Red\Component\Renderer\Html\Content\Authored\Article\ArticleRendererEditable;
use Red\Component\Renderer\Html\Content\Authored\EmptyContentRenderer;

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
