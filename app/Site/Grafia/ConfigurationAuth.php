<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\Grafia;

use Access\Enum\RoleEnum;

use Pes\Logger\FileLogger;

/**
 * Description of Configuration
 *
 * @author pes2704
 */
class ConfigurationAuth extends ConfigurationConstants {

    public static function auth() {
        ## HESLO - malé velké písmeno, číslice, min. 5 znaků
        $passwordPattern = "(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{5,}";
        $passwordInfo = "Heslo musí obsahovat nejméně jedno velké písmeno, jedno malé písmeno a jednu číslici. Jiné znaky než písmena a číslice nejsou povoleny. Délka musí být nejméně 5 znaků.";

        ## HESLO - malé velké písmeno, číslice, min. 3 znaky
//        $passwordPattern = "(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{3,}";
//        $passwordInfo = "Heslo musí obsahovat nejméně jedno velké písmeno, jedno malé písmeno a jednu číslici. Jiné znaky než písmena a číslice nejsou povoleny. Délka musí být nejméně 3 znaky.";

        $siteSpecificToken = str_replace('/', '', self::WEB_SITE);
        return [
                'fieldNameJmeno' => 'jmeno'.$siteSpecificToken,
                'fieldNameHeslo' => 'heslo'.$siteSpecificToken,
                'passwordPattern' => $passwordPattern,
                'passwordInfo' => $passwordInfo,
                'roleVisitor' => RoleEnum::VISITOR,
                'roleRepresentative' => RoleEnum::REPRESENTATIVE,
        ];
    }
}
