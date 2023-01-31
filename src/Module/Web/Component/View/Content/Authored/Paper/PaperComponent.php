<?php
namespace Web\Component\View\Content\Authored\Paper;

use Web\Component\View\Content\Authored\AuthoredComponentAbstract;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

use Web\Component\Renderer\Html\Content\Authored\Paper\PaperRenderer;
use Web\Component\Renderer\Html\Content\Authored\Paper\HeadlineRenderer;
use Web\Component\Renderer\Html\Content\Authored\Paper\PerexRenderer;
use Web\Component\Renderer\Html\Content\Authored\Paper\SectionsRenderer;

use Web\Component\Renderer\Html\Content\Authored\Paper\PaperRendererEditable;
use Web\Component\Renderer\Html\Content\Authored\Paper\HeadlineRendererEditable;
use Web\Component\Renderer\Html\Content\Authored\Paper\PerexRendererEditable;
use Web\Component\Renderer\Html\Content\Authored\Paper\SectionsRendererEditable;

use Web\Component\Renderer\Html\Manage\EditContentSwitchRenderer;

use Web\Component\Renderer\Html\NoPermittedContentRenderer;

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
