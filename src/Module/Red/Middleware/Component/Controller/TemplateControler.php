<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Middleware\Component\Controller;

use Psr\Http\Message\ServerRequestInterface;

use Site\Configuration;

use FrontControler\PresentationFrontControlerAbstract;

use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\PaperAggregatePaperContent;

// komponenty
use Component\View\Authored\AuthoredComponentInterface;
use Component\View\Authored\SelectPaperTemplate\SelectedPaperTemplateComponent;
use Component\View\Authored\SelectPaperTemplate\SelectedPaperTemplateComponentInterface;

####################

use Red\Model\Repository\{
    HierarchyAggregateRepo, MenuRootRepo, MenuItemRepo
};

use \StatusManager\StatusPresentationManager;

use Pes\Text\Message;

####################
//use Pes\Debug\Timer;
use Pes\View\View;
use Pes\View\Template\PhpTemplate;
use Pes\View\Template\InterpolateTemplate;
use \View\Includer;
/**
 * Description of GetController
 *
 * @author pes2704
 */
class TemplateControler extends PresentationFrontControlerAbstract {

    ### action metody ###############

    /**
     * Vrací obsah šablony.
     * @param ServerRequestInterface $request
     * @param type $templateName
     * @return type
     */
    public function articletemplate(ServerRequestInterface $request, $templateName) {
//        1 pokus - sůožka v site, kdy není readable -> 2. pokus složka v common
//                ?? více složek (v Comfiguration pole) postupně prohledávaných
        $filename = $this->seekTemplate(Configuration::templateController()['templates.articleFolder'], $templateName);
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
     * @param type $name
     * @return type
     */
    public function papertemplate(ServerRequestInterface $request, $name) {
        $presentedMenuItem = $this->statusPresentationRepo->get()->getMenuItem();
        if (isset($presentedMenuItem)) {
            $menuItemId = $presentedMenuItem->getId();
            /** @var SelectedPaperTemplateComponentInterface $view */
            $view = $this->container->get(SelectedPaperTemplateComponent::class);
            $view->setPaperTemplateName($name);
            $view->setItemId($menuItemId);
            $this->statusPresentationRepo->get()->setLastTemplateName($name);

        } else {
            // není item - asi chyba
            $paperAggregate = new PaperAggregatePaperContent();
            $paperAggregate->exchangePaperContentsArray([])   //  ['content'=> Message::t('Contents')]
                    ->setTemplate($name)
                    ->setHeadline(Message::t('Headline'))
                    ->setPerex(Message::t('Perex'))
                    ;
            $view = $this->container->get(View::class)
                                    ->setTemplate($this->setTemplateByName($name))
                                    ->setData([
                                        'paperAggregate' => $paperAggregate,
                                    ]);
        }
        return $this->createResponseFromView($request, $view);
    }

    public function authorTemplate(ServerRequestInterface $request, $folder, $name) {
        $filename = Configuration::templateController()['templates.authorFolder']."$folder/$name.php";
        $view = $this->container->get(View::class);
        if (is_readable($filename)) {
            $view->setTemplate(new PhpTemplate($filename));  // exception
//            $str = file_get_contents($filename);
        }
        return $this->createResponseFromView($request, $view);
    }

    #### private ######################


    private function setTemplateByName(AuthoredComponentInterface $component, $name) {
            try {
                $templatePath = $this->getTemplateFileFullname($this->configuration->getTemplatepathPaper(), $paperAggregate->getTemplate());
                $template = new PhpTemplate($templatePath);  // exception
                // renderery musí být definovány v Renderer kontejneru - tam mohou dostat classMap do konstruktoru
//                $this->addChildRendererName('headline', HeadlineRenderer::class);
//                $this->adoptChildRenderers($template);   // jako shared data do template objektu
                $this->setTemplate($template);
            } catch (NoTemplateFileException $noTemplExc) {
                user_error("Neexistuje soubor šablony '{$this->getTemplateFileFullname($paperAggregate->getTemplate())}'", E_USER_WARNING);
                $this->setTemplate(null);
            }
        return new PhpTemplate(Configuration::templateController()['templates.paperFolder']."$name/template.php");
    }

    private function seekTemplate($templatesFolders, $templateName) {
        foreach ($templatesFolders as $templatesFolder) {
            $filename = $templatesFolder."$templateName/template.php";
            if (is_readable($filename)) {
                return $filename;
            }
        }
        return false;
    }
}
