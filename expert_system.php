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
        if (($ret = checkFile($fileArr)) == 0)
        {
            if (($newfact = inputFact($argv[2])) == 1){
                return 0;
            }
            else if (($err = store($fileArr, $newfact)) == 0) {
                $GLOBALS["graph"] = createGraph();
                callAlgo();
            }
            else {                
                return checkRetValue_one($err);
            }
        }
        else {
            return checkRetValue_two($ret);
        }
    }
    else {
        echo "Size file error" . "\n";
        return 1;
    }
?>