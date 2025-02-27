<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\ViewModel\Manage;

use Component\ViewModel\ViewModelAbstract;

use Component\ViewModel\StatusViewModelInterface;
use Access\Enum\RoleEnum;

/**
 * Description of StatusBoardViewModel
 *
 * @author pes2704
 */
class InfoBoardViewModel extends ViewModelAbstract implements InfoBoardViewModelInterface {

    private $statusViewModel;

    public function __construct(
            StatusViewModelInterface $statusViewModel) {
        $this->statusViewModel = $statusViewModel;
    }

    private function getLanguageInfo() {
        $language = $this->statusViewModel->getPresentedLanguageCode();
        return [
            'jazyk' => $language->getName(),
            ];
    }

    private function getUserInfo() {
        return [
            'userName' => $this->statusViewModel->getUserLoginName(),
            'role' => $this->statusViewModel->getUserRole(),
            ];
    }
    
    private function getSecurityInfos() {
        return ['security info:' => $this->statusViewModel->getSecurityInfos()];
    }
    
    private function getPresentationInfos() {
        return ['presentation info:' => $this->statusViewModel->getPresentationInfos()];
    }
    
    public function getIterator(): \Traversable {
        $role = $this->statusViewModel->getUserRole();
            
        $boardInfo[] = $this->prettyDump($this->getLanguageInfo());
        $boardInfo[] = $this->prettyDump($this->getUserInfo());
        if ($role==RoleEnum::SUPERVISOR) {
            $boardInfo[] = $this->prettyDump($this->statusViewModel->getEditorActions());
            $boardInfo[] = $this->prettyDump($this->getSecurityInfos());            
            $boardInfo[] = $this->prettyDump($this->getPresentationInfos());            
//              $this->prettyDump($this->status->presentEditableContent()),
//              $this->prettyDump($this->status->presentEditableMenu()),
        }
        if ($role==RoleEnum::REPRESENTATIVE) {
            $boardInfo[] = $this->prettyDump($this->getSecurityInfos());            
        }
        $this->appendData(
                ['infos' => $boardInfo]
            );
        return parent::getIterator();
    }

    private function prettyDump($var) {
//        return htmlspecialchars(var_export($var, true), ENT_QUOTES, 'UTF-8', true);
//        return htmlspecialchars(print_r($var, true), ENT_QUOTES, 'UTF-8', true);
        return $this->pp($var);
    }

    private function pp($arr){
        if (is_object($arr)) {
            $cls = get_class($arr);
            $arr = (array) $arr;
        } else {
            $cls = '';
        }
        $retStr = $cls ? "<p>$cls</p>" : "";
        $retStr .= '<ul style="margin: 0.5rem; padding: 0;font-size: 1rem;line-height: 1;">';
        if (is_array($arr)){
            foreach ($arr as $key=>$val){
                if (is_object($val)) $val = (array) $val;
                if (is_array($val)){
                    $retStr .= '<li style="background: #cce5ff">' . str_replace('\0', ':', $key) . ' = [' . $this->pp($val) . ']</li>';
                }else{
                    $retStr .= '<li style="background: #ffe5cc">' . str_replace($cls, "", $key) . ' = ' . ($val == '' ? '""' : $val) . '</li>';
                }
            }
        }
        $retStr .= '</ul>';
        return $retStr;
    }
}
