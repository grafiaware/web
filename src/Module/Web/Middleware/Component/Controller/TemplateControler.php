<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Web\Middleware\Component\Controller;

use Psr\Http\Message\ServerRequestInterface;

use Site\Configuration;
use Configuration\TemplateControlerConfigurationInterface;

use FrontControler\FrontControlerAbstract;
use FrontControler\FrontControlerInterface;

use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\PaperAggregatePaperContent;

// komponenty
use Component\View\Authored\AuthoredComponentInterface;
use Component\View\Authored\SelectPaperTemplate\SelectedPaperTemplateComponent;
use Component\View\Authored\SelectPaperTemplate\SelectedPaperTemplateComponentInterface;

####################
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use TemplateService\TemplateSeekerInterface;

use \StatusManager\StatusPresentationManager;

use Pes\Text\Message;

####################
//use Pes\Debug\Timer;
use Pes\View\View;
use Pes\View\Template\PhpTemplate;
use \View\Includer;
/**
 * Description of GetController
 *
 * @author pes2704
 */
class TemplateControler extends FrontControlerAbstract {

    /**
     *
     * @var TemplateSeekerInterface
     */
    private $templateSeeker;

    public function __construct(StatusSecurityRepo $statusSecurityRepo, StatusFlashRepo $statusFlashRepo, StatusPresentationRepo $statusPresentationRepo, TemplateSeekerInterface $templateSeeker) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->templateSeeker = $templateSeeker;
    }

    ### action metody ###############

    /**
     * Vrací obsah šablony.
     * @param ServerRequestInterface $request
     * @param type $templateName
     * @return type
     */
    public function articletemplate(ServerRequestInterface $request, $templateName) {
        $filename = $this->templateSeeker->seekTemplate('article', $templateName);
        if ($filename) {
            $str = (new Includer())->protectedIncludeScope($filename);
//            $str = file_get_contents($filename);
            $this->statusPresentationRepo->get()->setLastTemplateName($templateName);
        } else {
            $this->statusPresentationRepo->get()->setLastTemplateName('');
            $str = '';
        }
        return $this->createResponseFromString($request, $str);
    }

    /**
     * Odesílá obsah vytvořený komponentou SelectPaperTemplateComponent. Ta renderuje požadovanou šablonu s použitím Paper odpovídajícího prezentované položce menu.
     * Připraveno pro TinyMce dialog pro výběr šablony. Teto dialog posílá GET request při každé změně výběru v selectoru šablon a ještě jednou po kliku na tlačítko 'Uložit'.
     *
     * @param ServerRequestInterface $request
     * @param type $templateName
     * @return type
     */
    public function papertemplate(ServerRequestInterface $request, $templateName) {
        $presentedMenuItem = $this->statusPresentationRepo->get()->getMenuItem();
        if (isset($presentedMenuItem)) {
            $filename = $this->templateSeeker->seekTemplate('paper', $templateName);

            $menuItemId = $presentedMenuItem->getId();
            /** @var SelectedPaperTemplateComponentInterface $view */
            $view = $this->container->get(SelectedPaperTemplateComponent::class);
            $view->setSelectedPaperTemplateFileName($filename);
            $view->setItemId($menuItemId);
            $this->statusPresentationRepo->get()->setLastTemplateName($templateName);

        } else {
            // není item - asi chyba
            $paperAggregate = new PaperAggregatePaperContent();
            $paperAggregate->exchangePaperContentsArray([])   //  ['content'=> Message::t('Contents')]
                    ->setTemplate($templateName)
                    ->setHeadline(Message::t('Headline'))
                    ->setPerex(Message::t('Perex'))
                    ;
            $view = $this->container->get(View::class)
                                    ->setTemplate($this->setTemplateByName($templateName))
                                    ->setData([
                                        'paperAggregate' => $paperAggregate,
                                    ]);
        }
        return $this->createResponseFromView($request, $view);
    }

    public function authorTemplate(ServerRequestInterface $request, $templateName) {
        // author šablony - jen v common a jméno je jméno souboru (ne složky)
        $filename = $this->templateSeeker->seekTemplate('author', $templateName);
        $view = $this->container->get(View::class);
        $view->setTemplate(new PhpTemplate($filename));  // exception
        return $this->createResponseFromView($request, $view);
    }

    #### private ######################


    private function setTemplateByName(AuthoredComponentInterface $component, $name) {
            try {
                $templatePath = $this->templateSeeker->seekTemplate('paper', $paperAggregate->getTemplate());
                $this->setTemplate(new PhpTemplate($templatePath));  // exception
                // renderery musí být definovány v Renderer kontejneru - tam mohou dostat classMap do konstruktoru
//                $this->addChildRendererName('headline', HeadlineRenderer::class);
//                $this->adoptChildRenderers($template);   // jako shared data do template objektu
            } catch (NoTemplateFileException $noTemplExc) {
                user_error("Neexistuje soubor šablony '$templatePath'", E_USER_WARNING);
                $this->setTemplate(null);
            }
        return new PhpTemplate(Configuration::templateController()['templates.paperFolder']."$name/".Configuration::templateController()['templates.defaultExtension']);
    }
}