<?php

use function PHPSTORM_META\type;

class Puzzle
{
    public $connection;
    public $seed;
    public $master_word;
    public $puzzle_words = array();

    function __construct($seed)
    {
        $this->connection = new mysqli('mysql', 'root', 'root', 'main');
        $this->seed = $seed;

        srand($this->seed);

        //massimo parole
        $query_max = 'SELECT count(*) as max FROM parola';
        $res_max = $this->connection->query($query_max);
        $max = $res_max->fetch_assoc();

        //scelta parola
        $query = "SELECT * FROM parola WHERE parola.ID = " . rand(0, $max['max']) . ";";
        $result = $this->connection->query($query);
        $row = $result->fetch_assoc();

        //parola principale
        $this->master_word = $row['Contenuto'];

        for ($i = 0; $i < strlen($this->master_word); $i++)
        {
            //massimo parole che iniziano con master_word[$i]
            $query_max = 'SELECT count(*) as max FROM parola WHERE parola.Contenuto LIKE "' . $this->master_word[$i] . '%"';
            $res_max = $this->connection->query($query_max);
            $max = $res_max->fetch_assoc();

            //selezionare una delle parole
            $num_offset = rand(0, $max["max"]-1);
            $word_query = 'SELECT * FROM parola WHERE parola.Contenuto LIKE "' . $this->master_word[$i] . '%" LIMIT 1 OFFSET ' . $num_offset;
            $word_res = $this->connection->query($word_query);
            $word_row = $word_res->fetch_assoc();
            $this->puzzle_words[] = $word_row['Contenuto'];
            //echo gettype($word_row['Contenuto']);
        }
    }

    //non prende la lungezza del master
    function get_lengths()
    {
        $arr = array(); 
        
        for ($i = 0; $i < count($this->puzzle_words); $i++)
        {
            $arr[] = strlen($this->puzzle_words[$i]);
        }

        return $arr;
    }

    function get_word($index){  return $this->puzzle_words[$index];  }

    function isThisLetterTrue($index_word, $index_letter, $letter){
        try {
            if (strval($this->puzzle_words[$index_word][$index_letter]) === $letter){  return true;  }
            return false;
        }
        catch (Exception $error)
        {
            return false;
        }
        
    }

    //DEBUG - to be removed
    function get_solution()
    {
        return array_merge([$this->master_word], $this->puzzle_words);
    }
}