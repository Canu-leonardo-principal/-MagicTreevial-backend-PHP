<?php
    
    require "vendor/autoload.php";

    use Buki\Router\Router;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    
    $router = new Router;

    srand(111237);
    
    $router -> get('/IHaveToStart', function(Request $request, Response $response)
    {
        $connection = new mysqli('localhost', 'root', '', 'word');       //connessione con il database
        $response -> headers -> set('Content-Type', 'application/json'); //settiamo i parametri base per la risposta


        //Ci salviamo la quantità di parole neò database
        $num_arr = array();
        $query_max = 'SELECT count(*) as max FROM parola';
        $res_max = $connection -> query($query_max);
        $max = $res_max -> fetch_assoc();

        // scegliamo la parola in maniera randomica
        $query = "SELECT * FROM parola WHERE parola.ID = " . rand(0, $max['max']) . ";";
        $result = $connection->query($query);
        $row = $result->fetch_assoc();
        $choosen_Word = $row['Contenuto'];
        
        //$num_arr[] = strlen($choosen_Word);
        echo $choosen_Word . "\n";
        
        for ($i = 0; $i < strlen($choosen_Word); $i++){
            
            //Ci salviamo la quantità di parole con l'iniziale uguale alla lettera della nostra parola scelta
            $query_max = 'SELECT count(*) as max FROM parola WHERE parola.Contenuto LIKE "' . $choosen_Word[$i] . '%"';
            $res_max = $connection -> query($query_max);
            $max = $res_max -> fetch_assoc();

            //selezioniamo randomicamente una di quelle parole
            $num_offset = rand(0, $max["max"]);
            $word_query = 'SELECT * FROM parola WHERE parola.Contenuto LIKE "' . $choosen_Word[$i] . '%" LIMIT 1 OFFSET ' . $num_offset;
            $word_res = $connection->query($word_query);
            $word_row = $word_res->fetch_assoc();
            $num_arr[] = strlen($word_row['Contenuto']);
            echo $word_row['Contenuto'] . "\n";
        }
        //si invia la risposta al client
        $response->setContent(json_encode($num_arr));    

        return $response;
    });

    $router -> get('try', function (Request $request, Response $response){

    });

    $router->run();
?>