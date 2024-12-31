<?php

/* 
 * https://stackoverflow.com/questions/5647461/how-do-i-send-a-post-request-with-php
 */

$url = 'http://localhost/web/auth/v1/testmail';
$data = ['key1' => 'value1', 'key2' => 'value2'];

// use key 'http' even if you send the request to https://...
$options = [
    'http' => [
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($data),
    ],
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === false) {
    /* Handle error */
}

var_dump($result);
echo $result;