<?php
namespace Red\Component\View\Content\Authored\Paper;

use Red\Component\View\Content\Authored\AuthoredComponentAbstract;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

use Red\Component\Renderer\Html\Content\Authored\Paper\PaperRenderer;
use Red\Component\Renderer\Html\Content\Authored\Paper\HeadlineRenderer;
use Red\Component\Renderer\Html\Content\Authored\Paper\PerexRenderer;
use Red\Component\Renderer\Html\Content\Authored\Paper\SectionsRenderer;

use Red\Component\Renderer\Html\Content\Authored\Paper\PaperRendererEditable;
use Red\Component\Renderer\Html\Content\Authored\Paper\HeadlineRendererEditable;
use Red\Component\Renderer\Html\Content\Authored\Paper\PerexRendererEditable;
use Red\Component\Renderer\Html\Content\Authored\Paper\SectionsRendererEditable;

use Red\Component\Renderer\Html\Manage\EditContentSwitchRenderer;

use Component\Renderer\Html\NoPermittedContentRenderer;

/**
 * Description of PaperComponent
 *
 * @author pes2704
 */
class PaperComponent extends AuthoredComponentAbstract implements PaperComponentInterface {

    // hodnoty těchto konstant určují, jaká budou jména proměnných použitých v kontejneru, zde a v rendereru nebo šabloně
    const CONTENT = 'content';
    const PEREX = 'perex';
    const HEADLINE = 'headline';
    const SECTIONS = 'sections';

}
