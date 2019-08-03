<?php 

function isFileEmpty($arr) {
    clearstatcache();
    if(filesize($arr)) {
        return 1;
    }
    return 0;
}



// fonction de check de l'input et suppression des comms 
// preg_match() retourne 1 si le pattern fourni correspond, 0 s'il ne correspond pas, ou FALSE si une erreur survient.
function checkFile($arr) {
    $i = 0;
    $checkQ = 0;
    $checkF = 0;
    $checkR = 0;
    while ($i < count($arr)) {
        if (preg_match("/^[A-Z\+\=\<\>\|\^\!\?\(\)]+$/", $arr[$i]) == 0) {                
            echo "erreur mauvais caractere";       
            return 1;
        }
        // verifier qu'il y a bien une regle
        if (preg_match("/\=\>/", $arr[$i]) || preg_match("/\<\=\>/", $arr[$i])){
            $checkR++;
        }
        // Verfier qu'il y a bien une query
        if ($arr[$i][0] == '='){
            $checkQ++;
        }
        // verifier qu'il y a bien un fact
        if ($arr[$i][0] == '?'){
            $checkF++;
        }
        $i++;
    }
    if ($checkQ != 1 || $checkF != 1 || $checkR < 1) {
        echo "erreur mauvais nombre de querry / facts / regles";
        return 1;
    }
    return 0;
}


function delComms(& $arr) {
    $i = 0;
    $nbElem = count($arr);
    while ($i < $nbElem) {
        $arr[$i] = preg_replace('/\s+/', '', $arr[$i]);
        if ($arr[$i][0] == '#' || $arr[$i] == NULL) {
            unset($arr[$i]);
            // $i--;                
        }
        else if (($pos = strpos($arr[$i], '#')) !== FALSE) {
            $arr[$i] = substr($arr[$i], 0 , $pos);
        }
        $i++;
    }
    $arr = array_values($arr);
    // echo store($arr);
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
    $countO = 0;
    $countC = 0;
    $countEq = 0;
    $nbElem = count($arr);
    while ($i < $nbElem){            
        $nbCharacter = strlen($arr[$i]);
        // verifier pas de '?'
        if (preg_match("/\?/", $arr[$i])){
            echo "erreur, '?' dans les rules";
            return 4;
        }
        // verifier que le premier caractere est un ! ou une lettre et que le dernier caractere est une lettre.
        $firstCharacter = $arr[$i][0];
        $lastCharacter = substr($arr[$i], -1);
        if (!preg_match("#^[\!\(A-Z]$#", $firstCharacter) || (!preg_match("#^[A-Z\)]$#", $lastCharacter))) {
            echo "Premier ou dernier caractere non alphabetique";
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
            if ($countC > $countO) {
                echo "erreur parentheses mauvais ordre";
                return 4;
            }
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
                   // Verifier si le caractere apres < est bien '='
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
                    $countEq++;
                    //echo $countEq . "\n";
                }
                // Verifier pas de '>' sans =
                if ($currentChar == '>') {
                    $prev = $n - 1;
                    $prevChar = substr($arr[$i], $prev, 1);
                    if ($prevChar != '=') {
                        echo "erreur > tout seul";
                        return 4;
                    }						
                }					
                // Verfier pas de mauvais signes cote à cote.          && ou || eventuellement a changer
                if ($currentChar != '=' && $currentChar != '<') {
                    if (is_Sign($nextChar) == 1 && $nextChar != '!') {
                        echo "erreur mauvais signes cotes a cotes";                    
                        return 4;
                    }
                }
            }
            // Gerer les parentheses
            if ($currentChar == '(') {
                $next = $n + 1;
                $nextChar = substr($arr[$i], $next, 1);
                if ($n != 0) {
                    $prev = $n - 1;
                    $prevChar = substr($arr[$i], $prev, 1);				
                    if (is_Sign($prevChar) == 0 && $prevChar != '(') {
                        echo "erreur pas de signe avant (";
                        return 4;
                    }
                }					
                if (!preg_match("/[A-Z]/", $arr[$i][$next]) && $nextChar != '!' && $nextChar != '('){
                    echo "erreur mauvais caractere apres (";
                    return 4;
                }
                $countO++;
            }
            if ($currentChar == ')') {
                $prev = $n - 1;
                $prevChar = substr($arr[$i], $prev, 1);
                if (!preg_match("/[A-Z]/", $arr[$i][$prev]) && $prevChar != ')') {
                    echo "erreur mauvais caractere avant )";
                    return 4;
                }
                $countC++;
            }
        }
        $i++;
        if ($countEq != 1) {
            echo "erreur trop ou pas de =";
            return 4;
        }
        $countEq = 0;
    }
    if ($countC != $countO) {
        echo $countEq;
        echo "erreur pas le meme nombre de parentheses.";
        return 4;
    }
    storeRules($arr);
    return 0;
}

?>