<?php

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\RegistrationInterface;


class RegistrationHydrator implements HydratorInterface {

    /**
     * 
     * @param EntityInterface $registration
     * @param type $row
     */
    public function hydrate(EntityInterface $registration, &$row) {
        /** @var RegistrationInterface $registration */        
        $registration
            ->setLoginNameFK($row['login_name_fk'])
            ->setEmail($row['email'] )
            ->setEmailTime($row['email_time'] ? \DateTime::createFromFormat('Y-m-d H:i:s', $row['email_time']) : NULL);
                ;
    }

   
    /**
     * 
     * @param EntityInterface $registration
     * @param type $row
     */    
    public function extract(EntityInterface $registration, &$row) {
        /** @var RegistrationInterface $registration */
        $row['login_name_fk'] = $registration->getLoginNameFk(); // hodnota pro where
        $row['email'] = $registration->getEmail();
        $row['email_time'] = $registration->getEmailTime()->format('Y-m-d H:i:s') ;
        
    }

}
