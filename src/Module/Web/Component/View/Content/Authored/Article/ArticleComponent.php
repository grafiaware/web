<?php
namespace Web\Component\View\Content\Authored\Article;

use Web\Component\View\Content\Authored\AuthoredComponentAbstract;
use Web\Component\ViewModel\Content\Authored\Article\ArticleViewModelInterface;

use Web\Component\Renderer\Html\Content\Authored\SelectTemplateRenderer;
use Web\Component\Renderer\Html\Content\Authored\Article\ArticleRenderer;
use Web\Component\Renderer\Html\Content\Authored\Article\ArticleRendererEditable;
use Web\Component\Renderer\Html\Content\Authored\EmptyContentRenderer;

use Web\Component\Renderer\Html\NoPermittedContentRenderer;

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
