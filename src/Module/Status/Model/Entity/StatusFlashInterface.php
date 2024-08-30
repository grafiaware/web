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
interface StatusFlashInterface extends PersistableEntityInterface {

    /**
     * Vrací pole flash message. Všechny flash messages smaže.
     * @return array
     */
    public function getMessages(): array;

    /**
     * Vrací command se životností do příštího requestu (standartní "flash" životnost). Command vždy smaže.
     */
    public function getCommand();

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
     * @return StatusFlashInterface
     */
    public function setMessage(string $message, string $severity = FlashSeverityEnum::INFO): StatusFlashInterface;

    /**
     * Nastaví command se životností do příštího requestu (standartní "flash" životnost).
     *
     * @param type $command
     * @return \Status\Model\Entity\StatusFlashInterface
     */
    public function setCommand($command): StatusFlashInterface;

    /**
     * Nastaví command se životností do nastavení příštího command v POST nebo PUT requestu. 
     * Command je přepsán jen tehdy, když nastaven (připraven) command a jedná se o POST nebo PUT request. Pokud není takto přepsán nebo aktivně nastaven 
     * voláním setPostCommand($command) přetrvá do konce session.
     * Requesty jiného typu (typicky GET) nemají na životnost post command vliv.
     * 
     * @param type $command
     * @return \Status\Model\Entity\StatusFlashInterface
     */
    public function setPostCommand($command): StatusFlashInterface;

    /**
     * Metoda slouží pro nastavení stavu objektu StatusFlash z middleware FlashStatus před zpracováním requestu v dalších vnořených middleware.
     *
     * Musí být volána před voláním middleware metody handle().
     * Musí být volána poté, kdy byl StatusFlash obnoven z uložených dat, např. deserializován ze session.
     * Objekt je v takovém okamžiku v identickém stavu, v jakém byl uložen v předcházejícím requestu.
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function beforeHandle(ServerRequestInterface $request): void;

    /**
     * Metoda slouží pro nastavení stavu objektu StatusFlash z middleware FlashStatus po zpracování requestu v dalších middleware.
     *
     * Musí být volána po návratu z middleware metody handle().
     * Po návratu z této metody je StatusFlash uložen, například serializován do session.
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function afterHandle(ServerRequestInterface $request): void;
}
