<?php

require "vendor/autoload.php";
require "src/word_gen.php";

use Buki\Router\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$router = new Router;

$router->get('/start', function (Request $request, Response $response) {
    //settiamo i parametri dell'header di risposta
    $response -> headers -> set('Access-Control-Allow-Origin', '*');
    $response -> headers -> set('Content-Type', 'application/json');
    //creaiamo il puzzle
    $puzzle = new Puzzle($request->get('seed'));
    //creaiamo la risposta
    $response->setContent(json_encode($puzzle->get_lengths()));
    return $response;
});


$router->post('/verify', function (Request $request, Response $response) {
    //settiamo i parametri dell'header di risposta
    $response -> headers -> set('Access-Control-Allow-Origin', 'http://localhost:5173');
    $response -> headers -> set('Content-Type', 'application/json');
    //ricreaiamo il puzzle
    $puzzle = new Puzzle($request->get('seed'));

    $words = json_decode($request->getContent());

    $error = array();

    for ($i = 0; $i < count($words); $i++) {
        for ($j = 0; $j < count($words[$i]); $j++) {
            if ( !$puzzle->isThisLetterTrue($i /*indice parola*/ , $j /*indice lettera*/, strval($words[$i][$j]) /*lettera*/)) { 
                $error[] =  ($i * 100) + $j;
            }
        }
    }
    $response->setContent(json_encode($error));

    return $response;
});

$router->get('/debug', function (Request $request, Response $response){
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response -> headers -> set('Content-Type', 'application/json');
    $puzzle = new Puzzle($request->get('seed'));
    $response->setContent(json_encode($puzzle->get_solution()));
    return $response;
});

$router->run();