<?php
    require_once("./algo.php");
    require_once("./parsing.php");
    require_once("./store.php");
    $facts = [];
    $rules = [];
    $queries = [];
    $graph = [];
    
    if (isFileEmpty($argv[1]) != 0){
        $fileArr = file($argv[1]);        
        delComms($fileArr);
        if (checkFile($fileArr) == 0)
        {           
            if (store($fileArr) == 0) {               
                // code erreur : 1 => erreur de syntaxe
                //               2 => erreur de Facts
                //               3 => erreur de queries 
                //               4 => erreur de rules

                $GLOBALS["graph"] = createGraph();
                callAlgo();
            }            
        }
    }
    else {
        return 0;
    }
?>