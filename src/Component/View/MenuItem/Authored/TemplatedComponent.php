<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\MenuItem\Authored;

use Configuration\ComponentConfigurationInterface;
use Component\ViewModel\StatusViewModelInterface;

use Access\AccessPresentationInterface;
use Service\TemplateService\TemplateSeekerInterface;

use Service\TemplateService\Exception\Service\TemplateServiceExceptionInterface;
use Service\TemplateService\Exception\TemplateNotFoundException;

use Pes\View\Template\PhpTemplate;

use Red\Model\Enum\AuthoredTypeEnum;

use Component\ViewModel\MenuItem\Authored\AuthoredViewModelInterface;

use Component\View\MenuItem\Authored\AuthoredComponentAbstract;

use Pes\View\Template\ImplodeTemplate;

use Pes\View\ViewInterface;
use Pes\View\InheritDataViewInterface;

/**
 * Description of ArticleComponent
 *
 * @author pes2704
 */
class TemplatedComponent extends AuthoredComponentAbstract implements InheritDataViewInterface {

    private $templateSeeker;

    public function __construct(
            ComponentConfigurationInterface $configuration,
            TemplateSeekerInterface $templateSeeker
    ) {
        $this->templateSeeker = $templateSeeker;
        parent::__construct($configuration);
    }

    /**
     *
     * @var AuthoredViewModelInterface
     */
    protected $contextData;

    /**
     *
     * @param iterable $data
     * @return ViewInterface
     */
    public function inheritData(iterable $data): ViewInterface {
        return $this->setData($data);
    }

    /**
     * Vytvoří PhpTemplate template objekt z šablony nebo ImplodeTemplate template objekt, pokud šablona není nastavena nebo její soubor neexistuje (není čitelný).
     * Vytvořenou template nastaví jako template pro tuto komponentu.
     *
     * Soubor template hledá podle typu item a jména template.
     *
     * Tato metoda konroluje typ item vrácený metodou getItemType(). Přípustné jsou typy zadané ve výčtovém typu AuthoredEnum. Pokud metoda konkrétního modelu vrací
     * nepřípustný typ, tato metoda vyhodí výjimku.
     *
     * Pro vlastní hledámí použije objekt TemplateSeekerInterface zadaný do konstruktoru. Konkrétní strategie hledání
     * je dána objektem TemplateSeekerInterface.
     *
     * @return void
     * @throws InvalidItemTypeException
     * @throws ItemTemplateNotFoundException
     */
    public function beforeRenderingHook(): void {
        $itemType = $this->contextData->getAuthoredContentType();
        $templateName = $this->contextData->getAuthoredTemplateName();

        if (isset($templateName) AND $templateName) {
            try {
                $templatesType = (new AuthoredTypeEnum())($itemType);
            } catch (ValueNotInEnumException $exc) {
                throw new InvalidItemTypeException("Nepřípustný typ item. Typ '$itemType' vrácený metodou getItemType() není přípustný.", 0, $exc);
            }
            try {
                $templateFileName = $this->templateSeeker->seekTemplate($templatesType, $templateName);
                try {
                    // konstruktor PhpTemplate vyhazuje výjimku NoTemplateFileException pro neexistující (nečitený) soubor s template
                    $template = new PhpTemplate($templateFileName);
                } catch (NoTemplateFileException $noTemplExc) {
                    $template = new ImplodeTemplate();
                }
            } catch (Service\TemplateServiceExceptionInterface $exc) {
//                throw new ItemTemplateNotFoundException("Nenalezen soubor pro hodnoty vracené metodami ViewModel getItemTemplate(): '$templateName' a getItemType(): '$itemType'.", 0, $exc);
                $template = new ImplodeTemplate();
            }

        } else {
            $template = new ImplodeTemplate();
        }
        $this->setTemplate($template);   //TODO: otestuj fallback template (user error)
    }
}
