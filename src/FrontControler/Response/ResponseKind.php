<?php

namespace FrontControler\Response;

use Pes\Core\Type\Enum;

/**
 * Typ HTTP response, který FrontControler skládá (status + hlavičky + body).
 *
 * Odděleno od HTTP metody: stejný POST může být Json i RedirectSeeOther.
 */
class ResponseKind extends Enum {

    /** 200 HTML / text body (typicky GET prezentace, cascade fragmenty). */
    const HTML = 'html';

    /** 200/201/4xx s Content-Type: application/json. */
    const JSON = 'json';

    /** 204 No Content — úspěch bez body (inline save, DELETE API). */
    const NO_CONTENT = 'no_content';

    /** 303 See Other + Location — PRG po mutaci z HTML formuláře. */
    const REDIRECT_SEE_OTHER = 'redirect_see_other';

    /** 401 Unauthorized — prázdné body. */
    const UNAUTHORIZED = 'unauthorized';
}
