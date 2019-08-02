<?php
    $facts = [];
    $rules = [];
    $queries = [];
    $graph = [];
    
    if (isFileEmpty($argv[1]) != 0){
        $fileArr = file($argv[1]);
        delComms($fileArr);
        echo store($fileArr) . "\n";
        // code erreur : 1 => erreur de syntaxe
        //               2 => erreur de Facts
        //               3 => erreur de queries 
        //               4 => erreur de rules
        // print_r($GLOBALS["facts"]);
        // print_r($GLOBALS["queries"]);
        // print_r($GLOBALS["rules"]);
        
        // print_r(createGraphBis());
        $GLOBALS["graph"] = createGraphBis();
        callAlgo();
        // iterQueries();
    }
    else {        
        return 0;
    }

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

    function checkRules($arr) {
        $i = 0;
        $nbElem = count($arr);
        while ($i < $nbElem){
            // verifier que le premier et dernier caractere sont une lettre (ok)
            $firstCharacter = $arr[$i][0];
            $lastCharacter = substr($arr[$i], -1);
            if (!preg_match("#^[A-Z]$#", $firstCharacter) || (!preg_match("#^[A-Z]$#", $lastCharacter))) {
                echo "1";
                return 4;
            }            
            // verifier pas de double signe identique (ok)
            else if (preg_match("/(\|{2}|\+{2}|\!{2}|\^{2}|\={2}|\>{2}|\<{2})/", $arr[$i])){
                echo "2";
                return 4;
            }
            // verifier pas de double lettre (ok)
            else if (preg_match("/\w*[A-Z]\w*[A-Z]\w*/", $arr[$i])){
                echo "3";
                return 4;
            }           
            // verifier pas de ! apres lettre
            else if (preg_match("/[A-Z]\!/", $arr[$i])){
                echo $arr[$i];
                return 4;
            }
            // verifier pas de double signe (ou plus) non identique (sauf => et <=>)
            
            $i++;
        }
        storeRules($arr);
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
    

    // graph : adjacent list 

    function createGraph() {
        $graph = array();
        foreach($GLOBALS["rules"] as $rule) {
            if ($rule["signe"] == "=>" || $rule["signe"] == "<=>") {
                $lengthR = strlen($rule["right"]);
                for ($i = 0; $i < $lengthR; $i++) {
                    if (preg_match('#[A-Z]#', $rule["right"][$i])) {
                        $lengthL = strlen($rule["left"]);
                        for ($j = 0; $j < $lengthL; $j++) {
                            if (preg_match('#[A-Z]#', $rule["left"][$j])) {
                                if ($graph[$rule["right"][$i]] != null) {
                                    if (in_array($rule["left"][$j], $graph[$rule["right"][$i]]) == false) {
                                        $graph[$rule["right"][$i]][] = $rule["left"][$j];
                                    }
                                } else {
                                    $graph[$rule["right"][$i]][] = $rule["left"][$j];
                                }
                            }
                        }

                    }
                }
            }
            if ($rule["signe"] == "<=>") {
                $lengthL = strlen($rule["left"]);
                for ($i = 0; $i < $lengthL; $i++) {
                    if (preg_match('#[A-Z]#', $rule["left"][$i])) {
                        $lengthR = strlen($rule["right"]);
                        for ($j = 0; $j < $lengthR; $j++) {
                            if (preg_match('#[A-Z]#', $rule["right"][$j])) {
                                if ($graph[$rule["left"][$i]] != null) {
                                    if (in_array($rule["right"][$j], $graph[$rule["left"][$i]]) == false) {
                                        $graph[$rule["left"][$i]][] = $rule["right"][$j];
                                    }
                                } else {
                                    $graph[$rule["left"][$i]][] = $rule["right"][$j];
                                }
                            }
                        }

                    }
                }
            }
        }
        return $graph;
    }


    function createGraphBis() {
        $graph = array();
        foreach($GLOBALS["rules"] as $rule) {
            if ($rule["signe"] == "=>" || $rule["signe"] == "<=>") {
                $tmpArr = explode('+', $rule["right"]);
                foreach ($tmpArr as $elem) {
                    $graph[$elem][] = $rule["left"];
                    $i++; 
                }
            }
            if ($rule["signe"] == "<=>") {
                $tmpArr = explode('+', $rule["left"]);
                foreach ($tmpArr as $elem) {
                    $graph[$elem][] = $rule["right"];
                }
            }
        }
        return $graph;
    }

    // algo de resolution 


    function callAlgo() {
        foreach ($GLOBALS["queries"] as $query) {
            $ret = algo($query);
            if ($ret == 'T') {
                echo $query . " is true.\n";
            } else if ($ret == 'F' || $ret == 'U') {
                echo $query . " is false.\n";
            } else if ($ret == "C") {
                echo "There is a contradiction with query " . $query . "\n";
            }
        }
    }


    function algo($char) {
        // print_r($GLOBALS["graph"]);
        if (in_array($char, $GLOBALS["facts"]) && !(array_key_exists($char, $GLOBALS["graph"])) && !(array_key_exists("!" . $char, $GLOBALS["graph"]))) {
            return "T";
        } else {
            // echo $char . "\n";
            // echo array_key_exists($char, $GLOBALS["graph"]) . "\n";
            if (array_key_exists($char, $GLOBALS["graph"]) || array_key_exists("!" . $char, $GLOBALS["graph"])) { // || in_array("!" . $char, $GLOBALS["graph"]) gestion du ! dans la conclusion 
                echo "je suis laaaaaa\n";
                $arrDepRes = array("not!" => array(), "!" => array());
                if (in_array($char, $GLOBALS["facts"])) {
                    $arrDepRes["not!"][] = "T";
                }
                // echo "here\n";
                if (array_key_exists($char, $GLOBALS["graph"])) {
                    foreach ($GLOBALS["graph"][$char] as $depends) {
                        // echo $depends . "\n";
                        $length = strlen($depends);
                        $tmpStr = "";
                        for ($i = 0; $i < $length; $i++) {
                            if (preg_match('#[A-Z]#', $depends[$i])) {
                                $tmpStr .= algo($depends[$i]);
                            } else {
                                $tmpStr .= $depends[$i];
                            }
                        }
                        $arrDepRes["not!"][] = resolveStr($tmpStr);
                    }
                }
                if (array_key_exists("!" . $char, $GLOBALS["graph"])) {
                    foreach ($GLOBALS["graph"]["!" . $char] as $depends) {
                        // echo $depends . "\n";
                        $length = strlen($depends);
                        $tmpStr = "";
                        for ($i = 0; $i < $length; $i++) {
                            if (preg_match('#[A-Z]#', $depends[$i])) {
                                $tmpStr .= algo($depends[$i]);
                            } else {
                                $tmpStr .= $depends[$i];
                            }
                        }
                        $arrDepRes["!"][] = resolveStr($tmpStr);
                    }
                }
                return checkContrad($arrDepRes);
            } else {
                return "F";
            }
        }
    }


    function checkContrad($arr) {
        echo "on est dn checkcontrad\n";
        print_r($arr);
        $ret = "";
        // on check pour les conclusion en char
        if (in_array("T", $arr["not!"]) && in_array("F", $arr["not!"])) {
            $ret = "T";;
        } else if (in_array("T", $arr["not!"])) {
            $ret = "T";
        } else {
            $ret = "U";
        }
        echo "ret = " . $ret . "\n";
        // on check pour les conclusions en !char
        if (in_array("T", $arr["!"]) && $ret == "T") {
            $ret = "C";
        } else if (in_array("T", $arr["!"])) {
            $ret = "F";
        } else {
            $ret = "U";
        }
        echo "ret = " . $ret . "\n";
        return $ret;
    }

    function resolveStr($str) {
        // echo "je suis dans resolve \n";
        $str = str_replace("!F", "T", $str);
        $str = str_replace("!T", "F", $str);
        while(strlen($str) > 1) {
            // echo $str . "\n";
            while (($p = strpos($str, '+')) !== false) {
                // echo "ici\n";
                $tmpStr = substr($str, $p - 1, 3);
                // echo $tmpStr . "\n";
                if ((strpos($tmpStr, 'T') !== false) && (strpos($tmpStr, 'F') === false)) {
                    $str = str_replace($tmpStr, 'T', $str);
                } else {
                    $str = str_replace($tmpStr, 'F', $str);
                }
                // echo $str . "\n";
            }
            while (($p = strpos($str, '|')) !== false) {
                // echo "la\n";
                $tmpStr = substr($str, $p - 1, 3);
                if (strpos($tmpStr, 'T') !== false && strpos($tmpStr, 'F') !== false || strpos($tmpStr, 'T') !== false && strpos($tmpStr, 'F') === false) {
                    $str = str_replace($tmpStr, 'T', $str);
                } else {
                    $str = str_replace($tmpStr, 'F', $str);
                }
            } 
            while (($p = strpos($str, '^')) !== false) {
                // echo "la\n";
                $tmpStr = substr($str, $p - 1, 3);
                // echo $tmpStr. "\n";
                if (strpos($tmpStr, 'T') !== false && strpos($tmpStr, 'F') !== false) {
                    $str = str_replace($tmpStr, 'T', $str);
                } else {
                    $str = str_replace($tmpStr, 'F', $str);
                }
            } 
        }
        return $str;
    }
 
    // function iterQueries() {
    //     $iQueries = 0;
    //     $nbElem = count($GLOBALS["queries"]);
    //     while ($iQueries < $nbElem) {
    //         findRules($GLOBALS["queries"][$iQueries]);
    //         $iQueries++;
    //     }
    // }

    // function findRules($char) {
    //     foreach($GLOBALS["rules"] as $arrRule) {
    //         if ($arrRule["signe"] == "=>") {
    //             if (strpos($arrRule["right"], $char) !== false) {
    //                 array_push($arrResult, resolveRule($char, $arrRule));
    //             }
    //         } else if ($arrRule["signe"] == "<=>") {
    //             if (strpos($arrRule["right"], $char) !== false || strpos($arrRule["left"], $char) !== false) {
    //                 array_push($arrResult, resolveRule($char, $arrRule));
    //             } 
    //         }
    //     }
    //     // print_r($arrResult);
    //     // printResult($arrResult);
    // }

    // function resolveRule($char, $arrRule) {
         
    // }

    // function printResult($arrResult) {

    // }
?>