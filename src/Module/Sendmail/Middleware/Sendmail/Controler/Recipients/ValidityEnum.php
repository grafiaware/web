<?php
namespace Sendmail\Middleware\Sendmail\Controler\Recipients;

use Pes\Type\Enum;

class ValidityEnum extends Enum {
    const NO_MAIL = "no address";
    const SYNTAX = "valid address syntax";
    const DOMAIN = "valid domain";
    const USER = "valid user";
}

