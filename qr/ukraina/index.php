<?php

/* 
 * redirect - temporary redirect 
 */

//http_response_code(302)  - temporary redirect 
header('Location: https://praci.najdisi.cz/web/v1/page/item/65aff921a7caa', true, '302');
exit;
