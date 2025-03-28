<?php
namespace Sendmail\Middleware\Sendmail\Recipients;

use Pes\Type\Enum;

class ValidityEnum extends Enum {
    const NO_MAIL = 0;
    const SYNTAX = 1;
    const DOMAIN = 2;
    const USER = 3;
}

