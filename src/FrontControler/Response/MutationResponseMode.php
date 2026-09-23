<?php

namespace FrontControler\Response;

use Pes\Core\Type\Enum;

/**
 * Režim klienta u mutace (POST / PUT / DELETE).
 * Určuje očekávaný {@see ResponseKind}, ne HTTP metodu samotnou.
 */
class MutationResponseMode extends Enum {

    /**
     * Klasický HTML formulář → flash + 303 PRG na last GET.
     * @see ResponseKind::REDIRECT_SEE_OTHER
     */
    const BROWSER_FORM = 'browser_form';

    /**
     * Fetch/XHR editor (menuSwap, TinyMCE upload) → JSON body.
     * @see ResponseKind::JSON
     */
    const EDITOR_FETCH = 'editor_fetch';

    /**
     * Machine / token API (registry, synchro, consent) → JSON (nebo 204 u DELETE).
     * @see ResponseKind::JSON
     * @see ResponseKind::NO_CONTENT
     */
    const MACHINE_API = 'machine_api';

    /**
     * Inline uložení obsahu bez UI refresh payloadu → 204.
     * @see ResponseKind::NO_CONTENT
     */
    const INLINE_SAVE = 'inline_save';
}
