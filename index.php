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
        $response->getBody()->write("Hello world!");

        parse_str($request->getUri()->getQuery(), $queryString);

        if (isset($queryString['forward'])) {
            $client = new Client();

            $clientRequest = $client->createRequest(
                $request->getMethod(),
                $request->getUri()->withPort((int) $queryString['forward'])//remove cast when pull request accepted
            );
            //$clientResponse = $client->send($clientRequest);
        }
    },
    $request,
    $response
);

//listen to incomping requests
$server->listen();
