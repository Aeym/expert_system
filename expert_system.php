<?php
    $fileArr = file($argv[1]);
    $facts = [];
    $rules = [];
    $queries = [];

    if (isFileEmpty($argv[1]) != 0){
        delComms($fileArr);
    }
    else {        
        return 0;
    }
    // code erreur : 1 => erreur de syntaxe
    //               2 => erreur de Facts
    //               3 => erreur de queries 

    // echo checkFacts("=") . "\n";
    // echo checkFile($fileArr);
    //delComms($fileArr);
    // print_r($fileArr);
    // parsefile($tmpArr);

    function isFileEmpty($arr) {
        clearstatcache();
        if(filesize($arr)) {
            return 1;
        }
        return 0;
    }

    function parseFile($arr) {
        $i = 0;
        while ($i < count($arr)) {

            $i++;
        }
    }


    // fonction de check de l'input et suppression des comms 

    function checkFile($arr) {
        $i = 0;
        while ($i < count($arr)) {
            if (!preg_match("#^[A-Z\+\=\<\>\|\^\!\?\(\)]+$#", $arr[$i])) {
                return 0;
            }
            $i++;
        }
        return 1;
    }


    function delComms(& $arr) {
        $i = 0;
        $nbElem = count($arr);
        while ($i < $nbElem) {
            $arr[$i] = preg_replace('/\s+/', '', $arr[$i]);
            if ($arr[$i][0] == '#') {
                unset($arr[$i]);
                // $i--;                
            }
            else if (($pos = strpos($arr[$i], '#')) !== FALSE) {
                $arr[$i] = substr($arr[$i], 0 , $pos);
            }
            $i++;
        }
        $arr = array_values($arr);
         echo store($arr);
        // echo checkFile($arr);
    }

    function checkFacts($line) {
        $tmpStr = substr($line, 1, strlen($line));
        if (!preg_match("#^[A-Z]+$#", $tmpStr) && $tmpStr != '') {
            return 2;
        } else {
            storeFacts($line);
            return 0;
        }
    }

    function checkQueries($line) {
        $tmpStr = substr($line, 1, strlen($line));
        if (!preg_match("#^[A-Z]+$#", $tmpStr)) {
            return 3;
        } else {
            storeQueries($line);
            return 0;
        }
    }

    function checkRules($arr) {
        
    }

    

    // fonctions pour store les entrees dans les globales facts, rules et queries

    function store($arr) {
        $i = 0;
        $nbElem = count($arr);
        while ($i < $nbElem) {
            if ($arr[$i][0] == '=') {
                $cF = checkFacts($arr[$i]);
                unset($arr[$i]);
            } else if ($arr[$i][0] == '?') {
                $cQ = checkQueries($arr[$i]);
                unset($arr[$i]);
            }
            $i++;
        }
        if ($cF == 2) {  // return erreur de facts
            return $cF;
        } else if ($cQ == 3) { // return erreur de queries
            return $cQ;
        } else {
            return 0;
        }
        // $arr = array_values($arr);
        // storeRules($arr);
    }

    function storeFacts($line) {
        // echo $line . "\n";
        if ($line == '=') {
            $GLOBALS["facts"][0] = "false"; 
        } else {
            $iLine = 1;
            $iFacts = 0;
            $linelen = strlen($line);
            while ($iLine < $linelen) {
                $GLOBALS["facts"][$iFacts] = $line[$iLine];
                $iFacts++;
                $iLine++;
            }
        }
    }

    function storeRules($arr) {
        print_r($arr);
    }

    function storeQueries($line) {
        // echo $line . "\n";
    }
?>