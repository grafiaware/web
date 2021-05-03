<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Module\Status\Model\Entity;

use Psr\Http\Message\ServerRequestInterface;

use Model\Entity\EntityAbstract;

/**
 * Description of StatusFlash
 *
 * @author pes2704
 */
class StatusFlash extends EntityAbstract implements StatusFlashInterface {

    private $oldFlashMessage='';
    private $oldFlashCommand;
    private $newFlashMessage='';
    private $newFlashCommand;
    private $oldPostFlashCommand;
    private $newPostFlashCommand;

    /**
     * Vrací  a smaže flash message. První volání vrací poslední nastavenou message a snaže ji. Další volání pak již vrací prázdný řetězec
     * a také do session je po prvím volání getFlash() již uložen jenprázdný řetězec.
     * @return string Flash message string.
     */
    public function getMessage() {
        $lastFlash = $this->oldFlashMessage;
        $this->flashMessage = '';
        return $lastFlash;
    }

    /**
     * Vrací command se životností do příštího requestu (standartní "flash" životnost).
     */
    public function getCommand() {
        return $this->oldFlashCommand;
    }

    /**
     * Vrací command se životností do příštího POST requestu. Requesty jiného typu (typicky GET) nemají na životnost post command vliv.
     */
    public function getPostCommand() {
        return $this->oldPostFlashCommand;
    }
    /**
     * Nastaví novou flash message.
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): StatusFlashInterface {
        $this->newFlashMessage = $message;
        return $this;
    }

    /**
     * Připojí zadaný řerěze na konec flash message oddělený zalomením řádku.
     *
     * @param string $message
     * @return StatusFlashInterface
     */
    public function appendMessage(string $message): StatusFlashInterface {
        $this->newFlashMessage = (isset($message) AND $message) ? $this->newFlashMessage.PHP_EOL.$message : $message;
        return $this;
    }

    /**
     * Nastaví command se životností do příštího requestu (standartní "flash" životnost).
     *
     * @param type $command
     * @return \Module\Status\Model\Entity\StatusFlashInterface
     */
    public function setCommand($command): StatusFlashInterface {
        $this->newFlashCommand = $command;
        return $this;
    }

    /**
     * Nastaví command se životností do příštího POST requestu. Requesty jiného typu (typicky GET) nemají na životnost post command vliv.
     *
     * @param type $command
     * @return \Module\Status\Model\Entity\StatusFlashInterface
     */
    public function setPostCommand($command): StatusFlashInterface {
        $this->newPostFlashCommand = $command;
        return $this;
    }

    /**
     * Metoda slouží pro nastavení stavu vlastností objektu poté, kdy byl obnoven z uložených dat v dalším requestu,
     * např. deserilizován ze session. Objekt je v takovém okamžiku v identickém stavu, v jakém byl uložen v předcházejícím requestu.
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function beforeHandle(ServerRequestInterface $request): void {
        $method = $request->getMethod();
        switch ($method) {
            case 'GET':
                $this->switchGetFlash();
                break;
            case 'POST':
                $this->switchPostFlash();
                break;
            default:
                break;
        }
    }

    /**
     *
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function afterHandle(ServerRequestInterface $request): void {

    }

    private function switchGetFlash() {
        $this->oldFlashMessage = $this->newFlashMessage;
        $this->oldFlashCommand = $this->newFlashCommand;
        $this->newFlashMessage = null;
        $this->newFlashCommand = null;
    }

    private function switchPostFlash() {
            $this->oldPostFlashCommand = $this->newPostFlashCommand;
            $this->newPostFlashCommand = null;
    }
}
