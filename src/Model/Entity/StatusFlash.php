<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Description of StatusFlash
 *
 * @author pes2704
 */
class StatusFlash implements StatusFlashInterface {

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
     * Nastaví flash message.
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): StatusFlashInterface {
        $this->newFlashMessage = $message;
        return $this;
    }

    public function appendMessage(string $message): StatusFlashInterface {
        $this->newFlashMessage .= PHP_EOL.$message;
        return $this;
    }

    /**
     * Nastaví command se životností do příštího requestu (standartní "flash" životnost).
     *
     * @param type $command
     * @return \Model\Entity\StatusFlashInterface
     */
    public function setCommand($command): StatusFlashInterface {
        $this->newFlashCommand = $command;
        return $this;
    }

    /**
     * Nastaví command se životností do příštího POST requestu. Requesty jiného typu (typicky GET) nemají na životnost post command vliv.
     *
     * @param type $command
     * @return \Model\Entity\StatusFlashInterface
     */
    public function setPostCommand($command): StatusFlashInterface {
        $this->newPostFlashCommand = $command;
        return $this;
    }

    /**
     * Metoda slouží peo nastavení stavu vlastností objektu poté, kdy byl obnoven z uložených dat v dalším requestu,
     * např. deserilizován ze session. Objekt je v takovém okamžiku v identickém stavu, v jakém byl uložen v předcházejícím requestu.
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function renew(ServerRequestInterface $request): void {
        $this->oldFlashMessage = $this->newFlashMessage;
        $this->oldFlashCommand = $this->newFlashCommand;
        $this->oldPostFlashCommand = $this->newPostFlashCommand;
        $this->newFlashMessage = null;
        $this->newFlashCommand = null;
        if ($request->getMethod() == 'POST') {
            $this->newPostFlashCommand = null;
        }
    }
}
