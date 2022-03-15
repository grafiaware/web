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

// view modely
use Component\ViewModel\MenuItem\Authored\Paper\PaperViewModel;
use Component\ViewModel\MenuItem\Authored\Multipage\MultipageViewModel;

// komponenty
use Component\View\MenuItem\Authored\AuthoredComponentInterface;
use Component\View\MenuItem\Authored\PaperTemplate\PaperTemplateComponent;
use Component\View\MenuItem\Authored\PaperTemplate\PaperTemplateComponentInterface;

use Component\View\MenuItem\Authored\Multipage\MultipageTemplatePreviewComponent;
use Component\ViewModel\MenuItem\Authored\Multipage\MultipageTemplatePreviewViewModel;

####################
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use TemplateService\TemplateSeekerInterface;
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
                [ 'title' => 'template multipage test', 'description' => 'popis',       'url' => 'web/v1/multipagetemplate/test'],
                [ 'title' => 'template multipage carousel', 'description' => 'popis',       'url' => 'web/v1/multipagetemplate/carousel'],
            ];
       // [ 'title' => 'template article test', 'description' => 'popis',       'url' => 'web/v1/articletemplate/test'],
       // [ 'title' => 'Prázdná šablona', 'description' => 'Tato šablona nemá předepsaný styl. Po uložení šablony využijte editační lištu pro formátování.',       'url' => 'web/v1/articletemplate/empty'],
      // [ 'title' => 'Vzor - Úvod', 'description' => 'popis',       'url' => 'web/v1/static/uvod'],
        $templates['article'] = [
        [ 'title' => 'Šablona pro příspěvek', 'description' => 'Jednoduchá šablona pro vložení textu a obrázku.',       'url' => 'web/v1/articletemplate/web_post'],
        [ 'title' => 'Šablona pro popis knihy', 'description' => 'Popis knihy i autora, obrázky a důležité informace.',       'url' => 'web/v1/articletemplate/book_description'],
        [ 'title' => 'Kousíčková šablona', 'description' => 'Popis knihy i autora, obrázky a důležité informace.',       'url' => 'web/v1/articletemplate/book_description_Lebenhart'],
        [ 'title' => 'Šablona pro kurz', 'description' => 'Hlavní stránka kurzu. Napište lidem základní informace i recenze od účastníků.',       'url' => 'web/v1/articletemplate/retraining_course'],
        [ 'title' => 'Šablona pro produkt / službu', 'description' => 'Zviditelněte svůj produkt či službu.',       'url' => 'web/v1/articletemplate/product_page'],
        [ 'title' => 'Šablona pro produkt / službu 2', 'description' => 'Zviditelněte svůj produkt či službu.',       'url' => 'web/v1/articletemplate/introduce_subject'],
             ];

       // [ 'title' => 'template paper test', 'description' => 'popis',       'url' => 'web/v1/papertemplate/test'],
       // [ 'title' => 'Test - namedpaper a1', 'description' => 'rendered component',       'url' => 'red/v1/nameditem/a1'],
       // [ 'title' => 'Test - namedpaper a2', 'description' => 'rendered component',       'url' => 'red/v1/nameditem/a2'],
       // [ 'title' => 'Test - namedpaper a3', 'description' => 'rendered component',       'url' => 'red/v1/nameditem/a3'],
        $templates['paper'] = [
        [ 'title' => 'template paper default', 'description' => 'popis',       'url' => 'web/v1/papertemplate/default'],
        [ 'title' => 'template paper columns perex and contents', 'description' => 'popis',       'url' => 'web/v1/papertemplate/columns_perex_contents'],
        [ 'title' => 'template paper columns in circle with js', 'description' => 'popis',       'url' => 'web/v1/papertemplate/contents_in_circle_js'],
        [ 'title' => 'template paper with images', 'description' => 'popis',       'url' => 'web/v1/papertemplate/images_paper'],
        [ 'title' => 'template paper column cards', 'description' => 'popis',       'url' => 'web/v1/papertemplate/column_cards'],
        [ 'title' => 'template paper columns', 'description' => 'popis',       'url' => 'web/v1/papertemplate/columns'],
        [ 'title' => 'template paper divided_rows', 'description' => 'popis',       'url' => 'web/v1/papertemplate/divided_rows'],
        [ 'title' => 'template paper bordered_rows', 'description' => 'popis',       'url' => 'web/v1/papertemplate/bordered_rows'],
        [ 'title' => 'template paper rows', 'description' => 'popis',       'url' => 'web/v1/papertemplate/rows'],
        [ 'title' => 'template paper carousel', 'description' => 'popis',       'url' => 'web/v1/papertemplate/carousel'],
            ];
        $templates['author'] = [
        [ 'title' => 'Kontakt', 'description' => 'Grafia web - kontakt',       'url' => 'web/v1/authortemplate/kontakt'],
        [ 'title' => 'Publikace - novinka', 'description' => 'Grafia web - publikace',   'url' => 'web/v1/authortemplate/eshop_nove'],
        [ 'title' => 'Publikace - 2', 'description' => 'Vložení publikací na stránku', 'url' => 'web/v1/authortemplate/eshop_radka'],
        [ 'title' => 'Obrázek vlevo a text', 'description' => 'Bez obtékání. Dva sloupce', 'url' => 'web/v1/authortemplate/obrazekVlevo_blok'],
        [ 'title' => 'Obrázek vpravo a text', 'description' => 'Bez obtékání. Dva sloupce', 'url' => 'web/v1/authortemplate/obrazekVpravo_blok'],
        [ 'title' => 'Blok pro citaci', 'description' => 'Bez obtékání. Dva sloupce', 'url' => 'web/v1/authortemplate/citation'],
        [ 'title' => 'Vnitřní ohraničení bloků', 'description' => 'Bez obtékání. Dva sloupce', 'url' => 'web/v1/authortemplate/celled_blocks'],
        [ 'title' => 'Ohraničený obsah s odkazem - 1 položka', 'description' => 'Vložení ohraničené položky na stránku. Položka obsahuje odkaz, připojte správnou adresu či odkaz odeberte', 'url' => 'web/v1/authortemplate/menu_1polozka_2'],
        [ 'title' => 'Ohraničený obsah s odkazem - 1 položka rozdělená na sloupce', 'description' => 'Vložení ohraničené položky na stránku. Položka obsahuje odkaz, připojte správnou adresu či odkaz odeberte', 'url' => 'web/v1/authortemplate/menu_1polozka'],
        [ 'title' => 'Ohraničený obsah s odkazem - 2 položky', 'description' => 'Vložení 2 položek na stránku. Každá položka obsahuje odkaz, připojte správnou adresu či odkaz odeberte', 'url' => 'web/v1/authortemplate/menu_2polozky'],
        [ 'title' => 'Ohraničený obsah s odkazem - 3 položky', 'description' => 'Vložení 3 položek menu na stránku. Každá položka obsahuje odkaz, připojte správnou adresu či odkaz odeberte', 'url' => 'web/v1/authortemplate/menu_3polozky'],
        [ 'title' => 'Upoutávka na kurz', 'description' => 'popis',       'url' => 'web/v1/authortemplate/uvod_kurzu'],
        [ 'title' => '3 stejně vysoké obrázky  v řádce', 'description' => 'popis',       'url' => 'web/v1/authortemplate/stretched-images'],
        [ 'title' => '3 stejně vysoké obsahy v řádce', 'description' => 'popis',       'url' => 'web/v1/authortemplate/stretched-blocks'],
        [ 'title' => 'template two columns divided', 'description' => 'popis',       'url' => 'web/v1/authortemplate/two_columns_divided'],
        [ 'title' => 'template two blocks styled', 'description' => 'popis',       'url' => 'web/v1/authortemplate/two_blocks_styled'],
        [ 'title' => 'template img & text styled', 'description' => 'popis',       'url' => 'web/v1/authortemplate/img_text_styled'],
        [ 'title' => 'template job', 'description' => 'popis',       'url' => 'web/v1/authortemplate/job'],
        [ 'title' => 'Lorem ipsum', 'description' => 'Vložení lorem ipsum', 'url' => 'web/v1/authortemplate/lorem_ipsum'],
            ];

        return $this->createResponseFromString($request, json_encode($templates[$templatesType]));
    }

    /**
     * Uloží jméno požadované šablony do presentatin statusu a vrací obsah šablony (pro zobrazení v náhledu šablony).
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
        return $this->createResponseFromString($request, $str);
    }

    /**
     * Uloží jméno požadované šablony do presentatin statusu a odesílá obsah vytvořený komponentou SelectPaperTemplateComponent.
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
            $filename = $this->templateSeeker->seekTemplate(AuthoredTemplateTypeEnum::PAPER, $templateName);

            $menuItemId = $presentedMenuItem->getId();
            /** @var PaperViewModel $paperViewModel */
            $paperViewModel = $this->container->get(PaperViewModel::class);
            $paperViewModel->setMenuItemId($menuItemId);
            /** @var PaperTemplateComponentInterface $view */
            $view = $this->container->get(PaperTemplateComponent::class);
            $view->setSelectedPaperTemplateFileName($filename);
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
        return $this->createResponseFromView($request, $view);
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
        return $this->createResponseFromView($request, $view);
    }

    #### private ######################


    private function setTemplateByName(AuthoredComponentInterface $component, $name) {
            try {
                $templatePath = $this->templateSeeker->seekTemplate(AuthoredTemplateTypeEnum::PAPER, $paperAggregate->getTemplate());
                $this->setTemplate(new PhpTemplate($templatePath));  // exception
            } catch (NoTemplateFileException $noTemplExc) {
                user_error("Neexistuje soubor šablony '$templatePath'", E_USER_WARNING);
                $this->setTemplate(null);
            }
        return new PhpTemplate(Configuration::templateController()['templates.paperFolder']."$name/".Configuration::templateController()['templates.defaultExtension']);
    }
}
