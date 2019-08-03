<?php 

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
    $iLine = 1;
    $iQueries= 0;
    $linelen = strlen($line);
    while ($iLine < $linelen) {
        $GLOBALS["queries"][$iQueries] = $line[$iLine];
        $iQueries++;
        $iLine++;
    }
}


// graph : adjacent list 

function createGraph() {
    $graph = array();
    foreach($GLOBALS["rules"] as $rule) {
        if ($rule["signe"] == "=>") {
            $tmpArr = explode('+', $rule["right"]);
            foreach ($tmpArr as $elem) {
                $graph[$elem][] = $rule["left"];
                $i++; 
            }
        }
    }
    return $graph;
}


?>