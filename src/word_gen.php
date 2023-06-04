<?php

class Puzzle
{
    public $connection;
    public $seed;
    public $puzzle_words = array();

    function __construct($seed)
    {
        $this->connection = new mysqli('localhost', 'root', '', 'word');
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
        $this->puzzle_words[] = $row['Contenuto'];

        for ($i = 0; $i < strlen($this->puzzle_words[0]); $i++)
        {
            //massimo parole che iniziano con master_word[$i]
            $query_max = 'SELECT count(*) as max FROM parola WHERE parola.Contenuto LIKE "' . $this->puzzle_words[0][$i] . '%"';
            $res_max = $this->connection->query($query_max);
            $max = $res_max->fetch_assoc();

            //selezionare una delle parole
            $num_offset = rand(0, $max["max"]);
            $word_query = 'SELECT * FROM parola WHERE parola.Contenuto LIKE "' . $this->puzzle_words[0][$i] . '%" LIMIT 1 OFFSET ' . $num_offset;
            $word_res = $this->connection->query($word_query);
            $word_row = $word_res->fetch_assoc();
            $this->puzzle_words[] = $word_row['Contenuto'];
        }
    }

    //non prende la lungezza del primo
    function get_lengths()
    {
        $arr = array(); 
        
        for ($i = 1; $i < count($this->puzzle_words); $i++)
        {
            $arr[] = strlen($this->puzzle_words[$i]);
        }

        return $arr;
    }

    function get_word($index){  return $this->puzzle_words[$index];  }
    function isThisLetterTrue($index_word, $index_letter, $letter){
        if ($this->puzzle_words[$index_word+1][$index_letter] === $letter){  return true;  }
        return false;
    }
}