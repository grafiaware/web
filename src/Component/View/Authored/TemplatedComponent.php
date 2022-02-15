<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Authored;

use Pes\View\Renderer\RendererInterface;
use Pes\View\Template\PhpTemplate;

use Component\ViewModel\Authored\AuthoredViewModelInterface;

use Component\View\Authored\AuthoredComponentAbstract;
use Component\Renderer\Html\Authored\Paper\Buttons;
use Component\Renderer\Html\Authored\Paper\ElementWrapper;
/**
 * Description of ArticleComponent
 *
 * @author pes2704
 */
class TemplatedComponent extends AuthoredComponentAbstract {

    /**
     *
     * @var AuthoredViewModelInterface
     */
    protected $contextData;

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
        $itemType = (string) $this- $this->contextData->getItemType();
        $templateName = $this->contextData->getAuthoredTemplateName();
        try {
            $templatesType = (new AuthoredTypeEnum())($itemType);
        } catch (ValueNotInEnumException $exc) {
            throw new InvalidItemTypeException("Nepřípustný typ item. Typ '$itemType' vrácený metodou getItemType() není přípustný.", 0, $exc);
        }
        if (isset($templateName) AND $templateName) {
            try {
                $templateFileName = $this->templateSeeker->seekTemplate($templatesType, $templateName);
            } catch (TemplateServiceExceptionInterface $exc) {
                throw new ItemTemplateNotFoundException("Nenalezen soubor pro hodnoty vracené metodami ViewModel getItemTemplate(): '$templateName' a getItemType(): '$itemType'.", 0, $exc);
            }
        }
        try {
            // konstruktor PhpTemplate vyhazuje výjimku NoTemplateFileException pro neexistující (nečitený) soubor s template
            $template = new PhpTemplate($templateFileName);
        } catch (NoTemplateFileException $noTemplExc) {
            $template = new ImplodeTemplate();
        }
        $this->setTemplate();
    }
}
