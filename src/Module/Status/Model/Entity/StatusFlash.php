<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Entity;

use Psr\Http\Message\ServerRequestInterface;

use Model\Entity\EntityAbstract;

/**
 * Description of StatusFlash
 *
 * @author pes2704
 */
class StatusFlash extends EntityAbstract implements StatusFlashInterface {

    private $preparedFlashMessage=null;
    private $storedFlashMessage=null;
    private $restoredFlashMessage=null;

    private $preparedFlashCommand;
    private $storedFlashCommand;
    private $restoredFlashCommand;

    private $restoredPostFlashCommand;
    private $storedPostFlashCommand;
    private $preparedPostFlashCommand;

    /**
     * Vrací flash message.
     *
     * @return string Flash message string.
     */
    public function getMessage() {
        return $this->restoredFlashMessage;
    }

    /**
     * Vrací command se životností do příštího requestu (standartní "flash" životnost).
     */
    public function getCommand() {
        return $this->restoredFlashCommand;
    }

    /**
     * Vrací command se životností do příštího POST requestu. Requesty jiného typu (typicky GET) nemají na životnost post command vliv.
     */
    public function getPostCommand() {
        return $this->restoredPostFlashCommand;
    }

    /**
     * Nastaví novou flash message.
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): StatusFlashInterface {
        $this->preparedFlashMessage = $message;
        return $this;
    }

    /**
     * Připojí zadaný řerěze na konec flash message oddělený zalomením řádku.
     *
     * @param string $message
     * @return StatusFlashInterface
     */
    public function appendMessage(string $message): StatusFlashInterface {
        $this->preparedFlashMessage = (isset($message) AND $message) ? $this->preparedFlashMessage.PHP_EOL.$message : $message;
        return $this;
    }

    /**
     * Nastaví command se životností do příštího requestu (standartní "flash" životnost).
     *
     * @param type $command
     * @return \Status\Model\Entity\StatusFlashInterface
     */
    public function setCommand($command): StatusFlashInterface {
        $this->preparedFlashCommand = $command;
        return $this;
    }

    /**
     * Nastaví command se životností do příštího POST requestu. Requesty jiného typu (typicky GET) nemají na životnost post command vliv.
     *
     * @param type $command
     * @return \Status\Model\Entity\StatusFlashInterface
     */
    public function setPostCommand($command): StatusFlashInterface {
        $this->preparedPostFlashCommand = $command;
        return $this;
    }

    /**
     * Metoda slouží pro nastavení stavu objektu StatusFlash z middleware FlashStatus před zpracováním requestu v dalších vnořených middleware.
     *
     * Je volána před voláním middleware metody handle().
     * Je volána poté, kdy byl StatusFlash obnoven z uložených dat, např. deserializován ze session.
     * Objekt je v takovém okamžiku v identickém stavu, v jakém byl uložen v předcházejícím requestu.
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function beforeHandle(ServerRequestInterface $request): void {
        $this->restoredFlashMessage = $this->storedFlashMessage;
        $this->restoredFlashCommand = $this->storedFlashCommand;
        $this->restoredPostFlashCommand = $this->storedPostFlashCommand;

        $method = $request->getMethod();
        switch ($method) {
            case 'GET':
                $this->storedFlashMessage = null;
                $this->storedFlashCommand = null;
                break;
            case 'POST':
                $this->storedPostFlashCommand = null;
                break;
            default:
                break;
        }
    }

    /**
     * Metoda slouží pro nastavení stavu objektu StatusFlash z middleware FlashStatus po zpracování requestu v dalších middleware.
     *
     * Je volána po návratu z middleware metody handle().
     * Po návratu z této metody je StatusFlash uložen, například serializován do session.
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function afterHandle(ServerRequestInterface $request): void {
        $this->storedFlashMessage = $this->preparedFlashMessage;
        $this->storedFlashCommand = $this->preparedFlashCommand;
        $method = $request->getMethod();
        switch ($method) {
            case 'GET':

                break;
            case 'POST':
                $this->storedPostFlashCommand = $this->preparedPostFlashCommand;
                break;
            default:
                break;
        }
    }
}
