<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Entity;

use Psr\Http\Message\ServerRequestInterface;

use Model\Entity\PersistableEntityAbstract;
use Status\Model\Enum\FlashSeverityEnum;
use Pes\Type\Exception\ValueNotInEnumException;
use Status\Model\Exception\UndefinedFlashMessageSeverityException;

/**
 * Description of StatusFlash
 *
 * @author pes2704
 */
class Flash extends PersistableEntityAbstract implements FlashInterface {

    private $preparedFlashMessages=[];
    private $storedFlashMessages=[];

//    private $preparedFlashCommand;
//    private $storedFlashCommand;

    private $preparedPostFlashCommand;
    private $storedPostFlashCommand;

    /**
     * @var FlashSeverityEnum
     */
    private $severityEnum;

    public function __construct() {
        $this->severityEnum = new FlashSeverityEnum();
    }

    /**
     * Vrací pole flash message. Všechny flash messages smaže.
     *
     * @return array Array Flash messages.
     */
    public function getMessages(): array {
        $messages = $this->storedFlashMessages;
        $this->storedFlashMessages = [];
        return $messages;
    }

    /**
     * Vrací command se životností do příštího requestu (standartní "flash" životnost). Command vždy smaže.
     */
//    public function getCommand() {
//        $command = $this->storedFlashCommand;
//        $this->storedFlashCommand = null;
//        return $command;
//    }

    /**
     * Vrací "post" command Viz setPostCommand. Command vždy smaže.
     * Typické použití je volání v metodě kontroleru při POST/PUT requestu, v takovém případě chci command smazat, příkaz nastavený pomocí command byl v kontroleru vykonán.
     * 
     */
    public function getPostCommand() {
        $command = $this->storedPostFlashCommand;
        $this->storedPostFlashCommand = null;
        return $command;
    }
    
    /**
     * Vrací "post" command Viz setPostCommand. Command nemaže, ponechá hodnotu nastavenou.
     * Typické použití je při vytváření zobrazeného obsahu při GET requestu. Pak se jen dotazuji na obsah commad (například pro renderování buttonů a ovládacích prvků), 
     * ale nechci command mazat. Ke smazání pak dojde voláním getPostCommand() v POST metodě kontroléru.
     * 
     * @return type
     */
    public function readPostCommand() {
        return $this->storedPostFlashCommand;
    }

    /**
     * Nastaví novou flash message.
     *
     * @param string $message
     * @return FlashInterface
     */
    public function setMessage(string $message, string $severity = FlashSeverityEnum::INFO): FlashInterface {
        $en = $this->severityEnum;
        try {
            $this->preparedFlashMessages[] = ['severity'=>$en($severity), 'message'=>$message];
        } catch (ValueNotInEnumException $e) {
            throw new UndefinedFlashMessageSeverityException("nepřípustná hodnota severity $severity", 0, $e);
        }
        return $this;
    }

    /**
     * Nastaví command se životností do příštího requestu (standartní "flash" životnost).
     *
     * @param type $command
     * @return StatusFlashInterface
     */
//    public function setCommand($command): StatusFlashInterface {
//        $this->preparedFlashCommand = $command;
//        return $this;
//    }

    /**
     * Nastaví command se životností do nastavení příštího command v POST nebo PUT requestu. 
     * Command je přepsán jen tehdy, když nastaven (připraven) command a jedná se o POST nebo PUT request. Pokud není takto přepsán nebo aktivně nastaven 
     * voláním setPostCommand($command) přetrvá do konce session.
     * Requesty jiného typu (typicky GET) nemají na životnost post command vliv.
     *
     * @param type $command
     * @return FlashInterface
     */
    public function setPostCommand($command): FlashInterface {
        $this->preparedPostFlashCommand = $command;
        return $this;
    }

    /**
     * Metoda slouží pro nastavení stavu objektu StatusFlash z middleware FlashStatus po zpracování requestu v dalších middleware.
     *
     * Je určen k volání po návratu z middleware metody handle(). Připraví StatusFlash pro uložení do session.
     * Po návratu z této metody múže být StatusFlash uložen, například serializován do session.
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function storeMessages(): void {
        $this->storedFlashMessages += $this->preparedFlashMessages;
        $this->preparedFlashMessages = [];
//        if (isset($this->preparedFlashCommand)) {
//            $this->storedFlashCommand = $this->preparedFlashCommand;
//            $this->preparedFlashCommand = null;
//        }
//        if ($request->getMethod() == 'POST' || $request->getMethod() == 'PUT') {
            if (isset($this->preparedPostFlashCommand)) {
                $this->storedPostFlashCommand = $this->preparedPostFlashCommand;
                $this->preparedPostFlashCommand = null;
            }
//        }

    }
}
