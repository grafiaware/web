<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

// Initialize Guzzle client
$client = new GuzzleHttp\Client();

// Create a POST request
$response = $client->request(
    'POST',
    'http://example.org/',
    [
        'form_params' => [
            'key1' => 'value1',
            'key2' => 'value2'
        ]
    ]
);

// Parse the response object, e.g. read the headers, body, etc.
$headers = $response->getHeaders();
$body = $response->getBody();

// Output headers and body for debugging purposes
var_dump($headers, $body);