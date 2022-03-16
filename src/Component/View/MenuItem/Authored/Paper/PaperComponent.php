<?php
namespace Component\View\MenuItem\Authored\Paper;

use Component\View\MenuItem\Authored\AuthoredComponentAbstract;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

use Component\Renderer\Html\Authored\Paper\PaperRenderer;
use Component\Renderer\Html\Authored\Paper\HeadlineRenderer;
use Component\Renderer\Html\Authored\Paper\PerexRenderer;
use Component\Renderer\Html\Authored\Paper\SectionsRenderer;

use Component\Renderer\Html\Authored\Paper\PaperRendererEditable;
use Component\Renderer\Html\Authored\Paper\HeadlineRendererEditable;
use Component\Renderer\Html\Authored\Paper\PerexRendererEditable;
use Component\Renderer\Html\Authored\Paper\SectionsRendererEditable;

use Component\Renderer\Html\Manage\EditContentSwitchRenderer;

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
