<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace FrontControler;

use Pes\Type\Enum;

/**
 * Description of StatusEnum
 *
 * @author pes2704
 */
class StatusEnum extends Enum {
    //Informational 1xx
    const _100_Continue = 100;
    const _101_SwitchingProtocols = 101;
    const _102_Processing = 102;
    //Successful 2xx
    const _200_OK = 200;
    const _201_Created = 201;
    const _202_Accepted = 202;
    const _203_NonAuthoritativeInformation = 203;
    const _204_NoContent = 204;
    const _205_ResetContent = 205;
    const _206_PartialContent = 206;
    const _207_MultiStatus = 207;
    const _208_AlreadyReported = 208;
    const _226_IMUsed = 226;
    //Redirection 3xx
    const _300_MultipleChoices = 300;
    const _301_MovedPermanently = 301;
    const _302_Found = 302;
    const _303_SeeOther = 303;
    const _304_NotModified = 304;
    const _305_UseProxy = 305;
//    const _(Unused) = 306;
    const _307_TemporaryRedirect = 307;
    const _308_PermanentRedirect = 308;
    //Client Error 4xx
    const _400_BadRequest = 400;
    const _401_Unauthorized = 401;
    const _402_PaymentRequired = 402;
    const _403_Forbidden = 403;
    const _404_NotFound = 404;
    const _405_MethodNotAllowed = 405;
    const _406NotAcceptable = 406;
    const _407_ProxyAuthenticationRequired = 407;
    const _408_RequestTimeout = 408;
    const _409_Conflict = 409;
    const _410_Gone = 410;
    const _411_LengthRequired = 411;
    const _412_PreconditionFailed = 412;
    const _413_RequestEntityTooLarge = 413;
    const _414_RequestURITooLong = 414;
    const _415_UnsupportedMediaType = 415;
    const _416_RequestedRangeNotSatisfiable = 416;
    const _417_ExpectationFailed = 417;
//    const _I\'m a teapot = 418;
    const _422_UnprocessableEntity = 422;
    const _423_Locked = 423;
    const _424_FailedDependency = 424;
    const _426_UpgradeRequired = 426;
    const _428_PreconditionRequired = 428;
    const _429_TooManyRequests = 429;
    const _431_RequestHeaderFieldsTooLarge = 431;
    const _451_UnavailableForLegalReasons = 451;
    //Server Error 5xx

                
}
