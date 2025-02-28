<?php
namespace Sendmail\Middleware\Sendmail\Controler\Recipients;

use Pes\Type\Enum;

class ValidationDegreeEnum extends Enum {
    const SYNTAX = "validate address syntax";
    const DOMAIN = "validate if domain exists";
    const USER = "validate if user exists";
}

