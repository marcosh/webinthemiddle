<?php

use Phly\Http\ServerRequestFactory;
use Phly\Http\Response;
use Phly\Http\Server;
use GuzzleHttp\Client;

chdir(dirname(__DIR__));

// Setup autoloading
include 'vendor/autoload.php';

//create request instance from superglobals
$request = ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

//create response instance
$response = new Response();

//serving the application
$server = new Server(
    function ($request, $response, $done) {
        parse_str($request->getUri()->getQuery(), $queryString);

        if (isset($queryString['forward'])) {
            forward($request, $queryString['forward']);
        }
    },
    $request,
    $response
);

//listen to incomping requests
$server->listen();

/**
 * forwards request to the given port
 *
 * @param Phly\Http\Request
 * @param int
 * @return GuzzleHttp\Message\Response
 */
function forward($request, $port)
{
    $client = new Client();

    $clientRequest = $client->createRequest(
        $request->getMethod(),
        $request->getUri()->withPort($port),
        [
            'headers' => $request->getHeaders(),
            'body' => $request->getBody()
        ]
    );
    return $client->send($clientRequest);
}
