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
            if (($newfact = inputFact($argv[2])) == 1){
                return 0;
            }
            else if (($err = store($fileArr, $newfact)) == 0) {
                $GLOBALS["graph"] = createGraph();
                callAlgo();
            }
            else {                
                if ($err == 2){
                    echo "Fact error" . "\n";
                }
                else if ($err == 3){
                    echo "Query error" . "\n";
                }
                else {
                    echo "Rule error" . "\n";                    
                }
                return $err;
            }
        }
        else {
            echo "Syntax error" . "\n";
            return 1;
        }
    }
    else {
        echo "Size file error" . "\n";
        return 1;
    }

    function inputFact($argv)
    {
        if ($argv == "Fact"){
            echo "Enter new fact: ";
            $input = rtrim(fgets(STDIN));            
            if (preg_match("/^\=[A-Z]+$/", $input) || ($input[0] == '=' && $input[1] == NULL)) {
                return $input;                
            }
            else {
                echo "New facts are not valid." . "\n";
                return 1;
            }
        }
        return 0;
    }
?>