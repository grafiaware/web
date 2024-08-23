<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Middleware\Redactor\Controler;

use Psr\Http\Message\ServerRequestInterface;

use Site\ConfigurationCache;
use Configuration\TemplateControlerConfigurationInterface;

use FrontControler\FrontControlerAbstract;
use FrontControler\FrontControlerInterface;

use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\PaperAggregatePaperSection;

// view modely
use Red\Component\ViewModel\Content\Authored\Paper\PaperViewModel;
use Red\Component\ViewModel\Content\Authored\Multipage\MultipageViewModel;
use Red\Component\ViewModel\Content\Authored\Paper\PaperTemplatePreviewViewModel;
use Red\Component\ViewModel\Content\Authored\Paper\PaperTemplatePreviewViewModelInterface;
use Red\Component\ViewModel\Content\Authored\Multipage\MultipageTemplatePreviewViewModel;
use Red\Component\ViewModel\Content\Authored\Multipage\MultipageTemplatePreviewViewModelInterface;

// komponenty
use Red\Component\View\Content\Authored\AuthoredComponentInterface;
use Red\Component\View\Content\Authored\Paper\PaperTemplatePreviewComponent;
use Red\Component\View\Content\Authored\Multipage\MultipageTemplatePreviewComponent;

use Template\Seeker\Exception\TemplateServiceExceptionInterface;

####################
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Template\Seeker\TemplateSeekerInterface;
use Red\Model\Enum\AuthoredTemplateTypeEnum;

use Pes\Text\Message;

####################
//use Pes\Debug\Timer;
use Pes\View\View;
use Pes\View\Template\PhpTemplate;
use \View\Includer;

use Pes\View\Template\ImplodeTemplate;

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

    /**
     * Kontroler pro obsluhu požadavků tinyMce.
     *
     * Metody odesílají obsah šablon pro TinyMce.
     *
     * @param StatusSecurityRepo $statusSecurityRepo
     * @param StatusFlashRepo $statusFlashRepo
     * @param StatusPresentationRepo $statusPresentationRepo
     * @param TemplateSeekerInterface $templateSeeker
     */
    public function __construct(StatusSecurityRepo $statusSecurityRepo, StatusFlashRepo $statusFlashRepo, StatusPresentationRepo $statusPresentationRepo, TemplateSeekerInterface $templateSeeker) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->templateSeeker = $templateSeeker;
    }

    ### action metody ###############

    public function templatesList(ServerRequestInterface $request, $templatesType) {
        $templates['multipage'] = [
                [ 'title' => 'template multipage test', 'description' => 'popis',       'url' => 'red/v1/multipagetemplate/test'],
                [ 'title' => 'template multipage carousel', 'description' => 'popis',       'url' => 'red/v1/multipagetemplate/carousel'],
            ];
       // [ 'title' => 'template article test', 'description' => 'popis',       'url' => 'red/v1/articletemplate/test'],
       // [ 'title' => 'Prázdná šablona', 'description' => 'Tato šablona nemá předepsaný styl. Po uložení šablony využijte editační lištu pro formátování.',       'url' => 'red/v1/articletemplate/empty'],
      // [ 'title' => 'Vzor - Úvod', 'description' => 'popis',       'url' => 'red/v1/static/uvod'],
        $templates['article'] = [
        [ 'title' => 'Šablona pro příspěvek', 'description' => 'Jednoduchá šablona pro vložení textu a obrázku.',       'url' => 'red/v1/articletemplate/web_post'],
        [ 'title' => 'Šablona pro popis knihy', 'description' => 'Popis knihy i autora, obrázky a důležité informace.',       'url' => 'red/v1/articletemplate/book_description'],
        [ 'title' => 'Kousíčková šablona *', 'description' => 'Popis knihy i autora, obrázky a důležité informace.',       'url' => 'red/v1/articletemplate/book_description_Lebenhart'],
        [ 'title' => 'Šablona pro kurz', 'description' => 'Hlavní stránka kurzu. Napište lidem základní informace i recenze od účastníků.',       'url' => 'red/v1/articletemplate/retraining_course'],
        [ 'title' => 'Šablona pro produkt / službu', 'description' => 'Zviditelněte svůj produkt či službu.',       'url' => 'red/v1/articletemplate/product_page'],
        [ 'title' => 'Šablona pro produkt / službu 2', 'description' => 'Zviditelněte svůj produkt či službu.',       'url' => 'red/v1/articletemplate/introduce_subject'],
             ];

       // [ 'title' => 'template paper test', 'description' => 'popis',       'url' => 'red/v1/papertemplate/test'],
       // [ 'title' => 'Test - namedpaper a1', 'description' => 'rendered component',       'url' => 'red/v1/nameditem/a1'],
       // [ 'title' => 'Test - namedpaper a2', 'description' => 'rendered component',       'url' => 'red/v1/nameditem/a2'],
       // [ 'title' => 'Test - namedpaper a3', 'description' => 'rendered component',       'url' => 'red/v1/nameditem/a3'],
        $templates['paper'] = [
        [ 'title' => 'template paper default', 'description' => 'popis',       'url' => 'red/v1/papertemplate/default'],
        [ 'title' => 'template paper columns perex and contents', 'description' => 'popis',       'url' => 'red/v1/papertemplate/columns_perex_contents'],
        [ 'title' => 'template paper columns in circle with js', 'description' => 'popis',       'url' => 'red/v1/papertemplate/contents_in_circle_js'],
        [ 'title' => 'template paper with images', 'description' => 'popis',       'url' => 'red/v1/papertemplate/images_paper'],
        [ 'title' => 'template paper column cards', 'description' => 'popis',       'url' => 'red/v1/papertemplate/column_cards'],
        [ 'title' => 'template paper columns', 'description' => 'popis',       'url' => 'red/v1/papertemplate/columns'],
        [ 'title' => 'template paper divided_rows *', 'description' => 'popis',       'url' => 'red/v1/papertemplate/divided_rows'],
        [ 'title' => 'template paper bordered_rows *', 'description' => 'popis',       'url' => 'red/v1/papertemplate/bordered_rows'],
        [ 'title' => 'template paper rows', 'description' => 'popis',       'url' => 'red/v1/papertemplate/rows'],
        [ 'title' => 'template paper carousel', 'description' => 'popis',       'url' => 'red/v1/papertemplate/carouselForPaper'],
            ];
        $templates['author'] = [
        [ 'title' => 'Kontakt', 'description' => 'Grafia web - kontakt',       'url' => 'red/v1/authortemplate/contact'],
        [ 'title' => 'Publikace - novinka', 'description' => 'Grafia web - publikace',   'url' => 'red/v1/authortemplate/eshop_new'],
        [ 'title' => 'Publikace - 2', 'description' => 'Vložení publikací na stránku', 'url' => 'red/v1/authortemplate/eshop_row'],
        [ 'title' => 'Obrázek vlevo a text', 'description' => 'Bez obtékání. Dva sloupce', 'url' => 'red/v1/authortemplate/imgleft_block'],
        [ 'title' => 'Obrázek vpravo a text', 'description' => 'Bez obtékání. Dva sloupce', 'url' => 'red/v1/authortemplate/imgright_block'],
        [ 'title' => 'Blok pro citaci', 'description' => 'Bez obtékání. Dva sloupce', 'url' => 'red/v1/authortemplate/citation'],
        [ 'title' => 'Vnitřní ohraničení bloků', 'description' => 'Bez obtékání. Dva sloupce', 'url' => 'red/v1/authortemplate/celled_blocks'],
        [ 'title' => 'Ohraničený obsah s odkazem - 1 položka', 'description' => 'Vložení ohraničené položky na stránku. Položka obsahuje odkaz, připojte správnou adresu či odkaz odeberte', 'url' => 'red/v1/authortemplate/menu_1polozka_2'],
        [ 'title' => 'Ohraničený obsah s odkazem - 1 položka rozdělená na sloupce', 'description' => 'Vložení ohraničené položky na stránku. Položka obsahuje odkaz, připojte správnou adresu či odkaz odeberte', 'url' => 'red/v1/authortemplate/menu_1polozka'],
        [ 'title' => 'Ohraničený obsah s odkazem - 2 položky', 'description' => 'Vložení 2 položek na stránku. Každá položka obsahuje odkaz, připojte správnou adresu či odkaz odeberte', 'url' => 'red/v1/authortemplate/menu_2polozky'],
        [ 'title' => 'Ohraničený obsah s odkazem - 3 položky', 'description' => 'Vložení 3 položek menu na stránku. Každá položka obsahuje odkaz, připojte správnou adresu či odkaz odeberte', 'url' => 'red/v1/authortemplate/menu_3polozky'],
        [ 'title' => 'Upoutávka na kurz', 'description' => 'popis',       'url' => 'red/v1/authortemplate/teaser_about_item'],
        [ 'title' => '3 stejně vysoké obrázky  v řádce', 'description' => 'popis',       'url' => 'red/v1/authortemplate/stretched_images'],
        [ 'title' => '3 stejně vysoké obsahy v řádce', 'description' => 'popis',       'url' => 'red/v1/authortemplate/stretched_blocks'],
        [ 'title' => 'template two columns divided', 'description' => 'popis',       'url' => 'red/v1/authortemplate/two_columns_divided'],
        [ 'title' => 'template two blocks styled *', 'description' => 'popis',       'url' => 'red/v1/authortemplate/two_blocks_styled'],
        [ 'title' => 'template img & text styled', 'description' => 'popis',       'url' => 'red/v1/authortemplate/img_text_styled'],
        [ 'title' => 'template job', 'description' => 'popis',       'url' => 'red/v1/authortemplate/job'],
        [ 'title' => 'Lorem ipsum', 'description' => 'Vložení lorem ipsum', 'url' => 'red/v1/authortemplate/lorem_ipsum'],
        [ 'title' => 'Profil OA', 'description' => 'Vložení lorem ipsum', 'url' => 'red/v1/authortemplate/umelec'],
        [ 'title' => 'Podprofil OA', 'description' => 'Vložení lorem ipsum', 'url' => 'red/v1/authortemplate/umelec_vnoreny'],
            ];

        return $this->createJsonOKResponse($templates[$templatesType]);
    }

    /**
     * Uloží jméno požadované šablony do presentation statusu a vrací obsah šablony (pro zobrazení v náhledu šablony).
     * Připraveno pro TinyMce dialog pro výběr šablony. Teto dialog posílá GET request při každé změně výběru v selectoru šablon a ještě jednou po kliku na tlačítko 'Uložit'.
     *
     * @param ServerRequestInterface $request
     * @param type $templateName
     * @return type
     */
    public function articletemplate(ServerRequestInterface $request, $templateName) {
        $filename = $this->templateSeeker->seekTemplate(AuthoredTemplateTypeEnum::ARTICLE, $templateName);
        if ($filename) {
            $str = (new Includer())->protectedIncludeScope($filename);
//            $str = file_get_contents($filename);
            $this->statusPresentationRepo->get()->setLastTemplateName($templateName);
        } else {
            $this->statusPresentationRepo->get()->setLastTemplateName('');
            $str = '';
        }
        return $this->createStringOKResponse($str);
    }

    /**
     * Uloží jméno požadované šablony do presentation statusu a odesílá obsah vytvořený komponentou SelectPaperTemplateComponent.
     * Ta renderuje požadovanou šablonu s použitím Paper odpovídajícího prezentované položce menu.
     * Připraveno pro TinyMce dialog pro výběr šablony. Teto dialog posílá GET request při každé změně výběru v selectoru šablon a ještě jednou po kliku na tlačítko 'Uložit'.
     *
     * @param ServerRequestInterface $request
     * @param type $templateName
     * @return type
     */
    public function papertemplate(ServerRequestInterface $request, $templateName) {
        $presentedMenuItem = $this->statusPresentationRepo->get()->getMenuItem();
        if (isset($presentedMenuItem)) {
                $menuItemId = $presentedMenuItem->getId();
                /** @var PaperTemplatePreviewViewModel $templatePreviewiewModel */
                $templatePreviewiewModel = $this->container->get(PaperTemplatePreviewViewModel::class);
                $templatePreviewiewModel->setMenuItemId($menuItemId);
                $templatePreviewiewModel->setPreviewTemplateName($templateName);
                /** @var PaperTemplatePreviewComponent $view */
                $view = $this->container->get(PaperTemplatePreviewComponent::class);
                $this->statusPresentationRepo->get()->setLastTemplateName($templateName);
        } else {
            // není item - asi chyba
            $paperAggregate = new PaperAggregatePaperSection();
            $paperAggregate->setPaperSectionsArray([])   //  ['content'=> Message::t('Contents')]
                    ->setTemplate($templateName)
                    ->setHeadline(Message::t('Headline'))
                    ->setPerex(Message::t('Perex'))
                    ;
            $view = $this->container->get(View::class)
                                    ->setTemplate($this->getTemplateByName($paperAggregate))
                                    ->setData([
                                        'paperAggregate' => $paperAggregate,
                                    ]);
        }
        return $this->createStringOKResponseFromView($view);
    }

    public function multipagetemplate(ServerRequestInterface $request, $templateName) {
        $presentedMenuItem = $this->statusPresentationRepo->get()->getMenuItem();
        if (isset($presentedMenuItem)) {
//            $filename = $this->templateSeeker->seekTemplate(AuthoredTemplateTypeEnum::MULTIPAGE, $templateName);

            $menuItemId = $presentedMenuItem->getId();
            /** @var MultipageTemplatePreviewViewModel $templatePreviewModel */
            $templatePreviewModel = $this->container->get(MultipageTemplatePreviewViewModel::class);
            $templatePreviewModel->setMenuItemId($menuItemId);
            $templatePreviewModel->setPreviewTemplateName($templateName);
            /** @var MultipageTemplatePreviewComponent $view */
            $view = $this->container->get(MultipageTemplatePreviewComponent::class);
//            $view->setSelectedPaperTemplateFileName($filename);
            $this->statusPresentationRepo->get()->setLastTemplateName($templateName);
        } else {
            $view = $this->container->get(View::class)
                                    ->setTemplate(new ImplodeTemplate)
                                    ->setData([
                                        'Neumím to, multipage jsou ee.',
                                    ]);
        }
        return $this->createStringOKResponseFromView($view);
    }

    /**
     * Odesílá obsah authoringové šablony. Obsah načte ze souboru a renderuje jako PhpTemplate.
     * @param ServerRequestInterface $request
     * @param type $templateName
     * @return type
     */
    public function authorTemplate(ServerRequestInterface $request, $templateName) {
        // author šablony - jen v common a jméno je jméno souboru (ne složky)
        $filename = $this->templateSeeker->seekTemplate(AuthoredTemplateTypeEnum::AUTHOR, $templateName);
        $view = $this->container->get(View::class);
        $view->setTemplate(new PhpTemplate($filename));  // exception
        return $this->createStringOKResponseFromView($view);
    }

    #### private ######################


    private function getTemplateByName($paperAggregate) {
            try {
                $templatePath = $this->templateSeeker->seekTemplate(AuthoredTemplateTypeEnum::PAPER, $paperAggregate->getTemplate());
                $this->setTemplate(new PhpTemplate($templatePath));  // exception
            } catch (NoTemplateFileException $noTemplExc) {
                user_error("Neexistuje soubor šablony '$templatePath'", E_USER_WARNING);
                $this->setTemplate(null);
            }
        return new PhpTemplate(ConfigurationCache::redTemplates()['templates.paperFolder']."$name/".ConfigurationCache::redTemplates()['templates.defaultExtension']);
    }
}
