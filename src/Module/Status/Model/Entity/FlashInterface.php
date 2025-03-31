<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Entity;

use Model\Entity\PersistableEntityInterface;
use Status\Model\Enum\FlashSeverityEnum;

use Psr\Http\Message\ServerRequestInterface;

/**
 *
 * @author pes2704
 */
interface FlashInterface extends PersistableEntityInterface {

    /**
     * Vrací pole flash messages, které byly předtím vyzvednuty metodou retrieveMessages(). Všechny vyzvednuté flash messages smaže.
     * @return array
     */
    public function getMessages(): array;

    /**
     * Vrací command se životností do příštího requestu (standartní "flash" životnost). Command vždy smaže.
     */
//    public function getCommand();

    /**
     * Vrací "post" command Viz setPostCommand. Command vždy smaže.
     * Typické použití je volání v metodě kontroleru při POST/PUT requestu, v takovém případě chci command smazat, příkaz nastavený pomocí command byl v kontroleru vykonán.
     */
    public function getPostCommand();
    
    /**
     * Vrací "post" command Viz setPostCommand. Command nemaže, ponechá hodnotu nastavenou.
     * Typické použití je při vytváření zobrazeného obsahu při GET requestu. Pak se jen dotazuji na obsah commad (například pro renderování buttonů a ovládacích prvků), 
     * ale nechci command mazat. Ke smazání pak dojde voláním getPostCommand() v POST metodě kontroléru.
     */
    public function readPostCommand();
    /**
     * Nastaví message a severity
     *
     * @param string $message
     * @param string $severity
     * @return FlashInterface
     */
    public function setMessage(string $message, string $severity = FlashSeverityEnum::INFO): FlashInterface;

    /**
     * Nastaví command se životností do příštího requestu (standartní "flash" životnost).
     *
     * @param type $command
     * @return \Status\Model\Entity\StatusFlashInterface
     */
//    public function setCommand($command): StatusFlashInterface;

    /**
     * Nastaví command se životností do nastavení příštího command v POST nebo PUT requestu. 
     * Command je přepsán jen tehdy, když nastaven (připraven) command a jedná se o POST nebo PUT request. Pokud není takto přepsán nebo aktivně nastaven 
     * voláním setPostCommand($command) přetrvá do konce session.
     * Requesty jiného typu (typicky GET) nemají na životnost post command vliv.
     * 
     * @param type $command
     * @return FlashInterface
     */
    public function setPostCommand($command): FlashInterface;

    /**
     * Metoda slouží pro nastavení stavu objektu StatusFlash z middleware FlashStatus po zpracování requestu v dalších middleware.
     * 
     * Typicky je použita v procesu zpracování POST, PUT, DELETE requestu, kdy dochází k vytváření flash messages.
     * 
     * Musí být volána po návratu z middleware metody handle().
     * Po volání této metody může být StatusFlash uložen, například serializován do session.
     *
     * @return void
     */
    public function storeMessages(): void;
    
}
