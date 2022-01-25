<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Manage;

use Component\ViewModel\StatusViewModel;

/**
 * Description of StatusBoardViewModel
 *
 * @author pes2704
 */
class StatusBoardViewModel extends StatusViewModel implements StatusBoardViewModelInterface {

    public function getLanguageInfo() {
        $language = $this->statusPresentationRepo->get()->getLanguage();
        return [
            'code' => $language->getLangCode(),
            'name' => $language->getName(),
            'state' => $language->getState(),
            'locale' => $language->getLocale(),
            ];
    }

    public function getEditableInfo() {
        $userActions = $this->statusPresentationRepo->get()->getUserActions();
        return [
            'article' => $userActions->presentEditableContent(),
            ];
    }

    public function getSecurityInfo() {
        return [
            'userName' => $this->statusSecurityRepo->get()->getLoginAggregate()->getCredentials()->getLoginNameFk(),
            'role' => $this->statusSecurityRepo->get()->getLoginAggregate()->getCredentials()->getRole(),
            ];
    }

    public function getIterator(): \Traversable {
        $this->appendData(
                [
                'languageInfo' => $this->prettyDump($this->getLanguageInfo()),
                'editableInfo' => $this->prettyDump($this->getEditableInfo()),
                'securityInfo' => $this->prettyDump($this->getSecurityInfo()),
                'menuItem'=> $this->prettyDump($this->statusPresentationRepo->get()->getMenuItem(), true)
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
