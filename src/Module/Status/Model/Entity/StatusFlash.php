<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Entity;

use Psr\Http\Message\ServerRequestInterface;

use Model\Entity\EntityAbstract;
use Status\Model\Enum\FlashSeverityEnum;
use Pes\Type\Exception\ValueNotInEnumException;
use Status\Model\Exception\UndefinedFlashMessageSeverityException;

/**
 * Description of StatusFlash
 *
 * @author pes2704
 */
class StatusFlash extends EntityAbstract implements StatusFlashInterface {

    private $preparedFlashMessage=[];
    private $storedFlashMessage=[];
    private $restoredFlashMessage=[];

    private $preparedFlashCommand;
    private $storedFlashCommand;
    private $restoredFlashCommand;

    private $restoredPostFlashCommand;
    private $storedPostFlashCommand;
    private $preparedPostFlashCommand;

    /**
     * @var FlashSeverityEnum
     */
    private $severityEnum;

    public function __construct() {
        $this->severityEnum = new FlashSeverityEnum();
    }

    /**
     * Vrací pole flash message.
     *
     * @return array Array Flash messages.
     */
    public function getMessages(): array {
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
     *
     * @param string $message
     * @return StatusFlashInterface
     */
    public function setMessage(string $message, $severity = FlashSeverityEnum::INFO): StatusFlashInterface {
        $en = $this->severityEnum;
        try {
            $this->preparedFlashMessage[] = ['severity'=>$en($severity), 'message'=>$message];
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
    public function setCommand($command): StatusFlashInterface {
        $this->preparedFlashCommand = $command;
        return $this;
    }

    /**
     * Nastaví command se životností do příštího POST requestu. Requesty jiného typu (typicky GET) nemají na životnost post command vliv.
     *
     * @param type $command
     * @return StatusFlashInterface
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
                $this->storedFlashMessage = [];
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
     * Je určens k volání po návratu z middleware metody handle(). Připraví StatusFlash pro uložení do session.
     * Po návratu z této metody múže být StatusFlash uložen, například serializován do session.
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function afterHandle(ServerRequestInterface $request): void {
        $this->storedFlashMessage = $this->preparedFlashMessage;
        $this->storedFlashCommand = $this->preparedFlashCommand;
        $this->preparedFlashMessage = [];
        $this->preparedFlashCommand = null;
        $method = $request->getMethod();
        switch ($method) {
            case 'GET':

                break;
            case 'POST':
                $this->storedPostFlashCommand = $this->preparedPostFlashCommand;
                $this->preparedPostFlashCommand = null;
                break;
            default:
                break;
        }
    }
}
