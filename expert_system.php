<?php
    $facts = [];
    $rules = [];
    $queries = [];
    
    if (isFileEmpty($argv[1]) != 0){
        $fileArr = file($argv[1]);
        delComms($fileArr);
        echo store($fileArr) . "\n";
        print_r($GLOBALS["facts"]);
        print_r($GLOBALS["queries"]);
        print_r($GLOBALS["rules"]);
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
        //  echo store($arr);
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


    function is_Sign($char){
        if ($char == '+' || $char == '=' || $char == '<' || $char == '>' || $char == '|' || $char == '!' || $char == '^') {
            return 1;
        }
        else {
            return 0;
        }
    }

    function checkRules($arr) {
        $i = 0;
        $n = 0;       
        $nbElem = count($arr);
        while ($i < $nbElem){            
            $nbCharacter = strlen($arr[$i]);           
            // verifier que le premier et dernier caractere sont une lettre (ok)
            $firstCharacter = $arr[$i][0];
            $lastCharacter = substr($arr[$i], -1);
            if (!preg_match("#^[A-Z]$#", $firstCharacter) || (!preg_match("#^[A-Z]$#", $lastCharacter))) {
                echo "Premier ou dernier caracter non alphabetique";
                return 4;
            }            
            // verifier pas de double signe identique (ok)
            else if (preg_match("/(\|{2}|\+{2}|\!{2}|\^{2}|\={2}|\>{2}|\<{2})/", $arr[$i])){
                echo "double signe";
                return 4;
            }
            // verifier pas de double lettre (ok)
            else if (preg_match("/\w*[A-Z]\w*[A-Z]\w*/", $arr[$i])){
                echo "double lettre";
                return 4;
            }           
            // verifier pas de ! apres lettre
            else if (preg_match("/[A-Z]\!/", $arr[$i])){
                echo "! apres lettre";
                return 4;
            }
            // verifier pas de double signe (ou plus) non identique (sauf => et <=>)
            for ($n = 0; $n < $nbCharacter; $n++){
                $next = 0;
                $currentChar = substr($arr[$i], $n, 1);
                if (is_Sign($currentChar) == 1){ // Signe trouvé
                    $next = $n + 1;
                    $nextChar = substr($arr[$i], $next, 1);  
                    // Verifier triple signes de forme <=>, (si different de '>', pas bon)
                    if ($currentChar == '<') {
                       if ($nextChar == '='){
                           $x = $next + 1;
                           $xChar = substr($arr[$i], $x, 1);
                            if ($xChar != '>') {
                                echo "erreur <=?";
                                return 4;
                            }
                       }
                       if ($nextChar != '='){
                           echo "erreur <?>";
                           return 4;
                       }
                    }
                    // Verifier double signe de forme =>, (si different de '>', pas bon)
                    if ($currentChar == '=') {
                        if ($nextChar != '>') {
                            echo "erreur =?";
                            return 4;
                        }
                    }
                }
            }
            $i++;
        }
        return 0;
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
        //checkRules() check la valeur de retour et return 4 si erreur de rule ou 0 si tout va bien
           $cR = checkRules($arr);          
           if ($cR == 4) {
               return $cR;
           }
           else {
               return 0;
           }
        }
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
        // print_r($arr);
        $iRules = 0;
        $nbElem = count($arr);
        while ($iRules < $nbElem) {
            if (strpos($arr[$iRules], "<=>") !== false) {
                $tmpArr = preg_split("#\<\=\>#", $arr[$iRules]);
                $GLOBALS["rules"][$iRules]["left"] = $tmpArr[0];
                $GLOBALS["rules"][$iRules]["signe"] = "<=>";
                $GLOBALS["rules"][$iRules]["right"] = $tmpArr[1];
            } else if (strpos($arr[$iRules], "=>") !== false) {
                $tmpArr = preg_split("#\=\>#", $arr[$iRules]);
                $GLOBALS["rules"][$iRules]["left"] = $tmpArr[0];
                $GLOBALS["rules"][$iRules]["signe"] = "=>";
                $GLOBALS["rules"][$iRules]["right"] = $tmpArr[1];                
            } 
            $iRules++;
        }
    }

    function storeQueries($line) {
        // echo $line . "\n";
        $iLine = 1;
        $iQueries= 0;
        $linelen = strlen($line);
        while ($iLine < $linelen) {
            $GLOBALS["queries"][$iQueries] = $line[$iLine];
            $iQueries++;
            $iLine++;
        }
    }
?>