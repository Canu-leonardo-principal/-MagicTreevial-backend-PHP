<?php

require "vendor/autoload.php";
require "src/word_gen.php";

use Buki\Router\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$router = new Router;

$router->get('/start', function (Request $request, Response $response) {
    $response -> headers -> set('Content-Type', 'application/json');
    $puzzle = new Puzzle($request->get('seed'));
    $response->setContent(json_encode($puzzle->get_lengths()));
    return $response;    
});


$router->get('/verify', function (Request $request, Response $response) {
    $response -> headers -> set('Content-Type', 'application/json');
    $puzzle = new Puzzle($request->get('seed'));
    
    //array di prova, da sostituire con quello inviato dal client
    $words = array(                            
        array('s', ' ', 'g', 'n', 'o', 'r', 'e'),
        array('c', 'o', 'n', 't', 'e'),
    );

    for ($i = 0; $i < count($words); $i++) {
        for ($j = 0; $j < count($words[$i]); $j++) {
            if ( ! $puzzle->isThisLetterTrue($i /*indice parola*/ , $j /*indice lettera*/, $words[$i][$j] /*lettera*/)) {
                $error[] =  ($i * 100) + $j;
            }
        }
    }

    $response->setContent(json_encode($error));

    return $response;
});

$router->run();