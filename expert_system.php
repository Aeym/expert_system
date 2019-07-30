<?php
    $fileArr = file("test.txt");

    // echo checkFile($fileArr);
    delComms($fileArr);
    // print_r($fileArr);
    // parsefile($tmpArr);

    function parseFile($arr) {
        $i = 0;
        while ($i < count($arr)) {

            $i++;
        }
    }


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
        // store($arr);
        echo checkFile($arr);
    }


    function store($arr) {
        $i = 0;
        $nbElem = count($arr);
        while ($i < $nbElem) {
            if ($arr[$i][0] == '=') {
                storeFacts($arr[$i]);
                unset($arr[$i]);
            } else if ($arr[$i][0] == '?') {
                storeQueries($arr[$i]);
                unset($arr[$i]);
            }
            $i++;
        }
        $arr = array_values($arr);
        storeRules($arr);
    }

    function storeFacts($line) {
        echo $line . "\n";
    }

    function storeRules($arr) {
        print_r($arr);
    }

    function storeQueries($line) {
        echo $line . "\n";
    }
?>