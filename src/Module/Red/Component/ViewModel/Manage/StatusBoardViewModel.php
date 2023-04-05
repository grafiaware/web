<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\ViewModel\Manage;

use Component\ViewModel\ViewModelAbstract;

use Component\ViewModel\StatusViewModelInterface;

/**
 * Description of StatusBoardViewModel
 *
 * @author pes2704
 */
class StatusBoardViewModel extends ViewModelAbstract implements StatusBoardViewModelInterface {

    private $status;

    public function __construct(
            StatusViewModelInterface $status) {
        $this->status = $status;
    }

    private function getLanguageInfo() {
        $language = $this->status->getPresentedLanguage();
        return [
            'code' => $language->getLangCode(),
            'name' => $language->getName(),
            'state' => $language->getState(),
            'locale' => $language->getLocale(),
            ];
    }

    private function getSecurityInfo() {
        return [
            'userName' => $this->status->getUserLoginName(),
            'role' => $this->status->getUserRole(),
            ];
    }

    public function getIterator(): \Traversable {
        $this->appendData(
                [
                'languageInfo' => $this->prettyDump($this->getLanguageInfo()),
                'securityInfo' => $this->prettyDump($this->getSecurityInfo()),
                'userActions' => $this->prettyDump($this->status->getUserActions()),
//                'presentEditableContent' => $this->prettyDump($this->status->presentEditableContent()),
//                'presentEditableMenu' => $this->prettyDump($this->status->presentEditableMenu()),
                ]
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
