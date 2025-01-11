<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Status\Model\Enum;
use Pes\Type\Enum;

/**
 * Description of FlashSeverityEnum
 *
 * @author pes2704
 */
class FlashSeverityEnum extends Enum {
    // "warning", "info", "success"
    const ERROR = "error";
    const WARNING = "warning";
    const INFO = "info";
    const SUCCESS = "success";
}
