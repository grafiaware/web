<?php
namespace Sendmail\Middleware\Sendmail\Controler\Recipients;

/**
 *
 * @author vlse2610
 */
interface RecipientsValidatorInterface {
        
    public function validateEmail($email, $validationDegree);

}
