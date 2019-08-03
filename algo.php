<?php 


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
    if (in_array($char, $GLOBALS["facts"]) && !(array_key_exists("!" . $char, $GLOBALS["graph"]))) {
        return "T";
    } else {
        if (array_key_exists($char, $GLOBALS["graph"]) || array_key_exists("!" . $char, $GLOBALS["graph"])) {
            $arrDepRes = array("not!" => array(), "!" => array());
            if (in_array($char, $GLOBALS["facts"])) {
                $arrDepRes["not!"][] = "T";
            }
            if (array_key_exists($char, $GLOBALS["graph"])) {
                foreach ($GLOBALS["graph"][$char] as $depends) {
                    $length = strlen($depends);
                    $tmpStr = "";
                    for ($i = 0; $i < $length; $i++) {
                        if (preg_match('#[A-Z]#', $depends[$i])) {
                            $tmpStr .= algo($depends[$i]);
                        } else {
                            $tmpStr .= $depends[$i];
                        }
                    }
                    echo "retour de bracket dans algo : " . bracket($tmpStr) . " ? \n";
                    $arrDepRes["not!"][] = bracket($tmpStr);
                }
            }
            if (array_key_exists("!" . $char, $GLOBALS["graph"])) {
                foreach ($GLOBALS["graph"]["!" . $char] as $depends) {
                    $length = strlen($depends);
                    $tmpStr = "";
                    for ($i = 0; $i < $length; $i++) {
                        if (preg_match('#[A-Z]#', $depends[$i])) {
                            $tmpStr .= algo($depends[$i]);
                        } else {
                            $tmpStr .= $depends[$i];
                        }
                    }
                    $arrDepRes["!"][] = bracket($tmpStr);
                }
            }
            return checkContrad($arrDepRes);
        } else {
            return "F";
        }
    }
}


function checkContrad($arr) {
    $ret = "";
    if (in_array("T", $arr["not!"])) {
        $ret = "T";;
    } else {
        $ret = "U";
    }
    if (in_array("T", $arr["!"]) && $ret == "T") {
        $ret = "C";
    } else if (in_array("T", $arr["!"])) {
        $ret = "F";
    } else if ($ret == 'U') {
        $ret = "U";
    }
    return $ret;
}



function bracket($str) {
    $i = 0;
    $length = strlen($str);
    $openB = [];
    $closeB = [];
    while ($i < $length) {
        if ($str[$i] == '(') {
            $openB[] = $i;
        } else if ($str[$i] == ')') {
            $closeB[] = $i;
        }
        $c = count($openB);
        if ($i == ($length - 1) && $c != 0) {
            $tmp = substr($str, $openB[$c - 1], $closeB[0] - $openB[$c -1] + 1);
            $res = resolveStr(substr($tmp, 1, strlen($tmp) - 2));
            $str = str_replace($tmp, $res, $str);
            $openB = [];
            $closeB = [];
            $i = 0;
        }
        $i++;
    }
    
    return resolveStr($str);
}


function resolveStr($str) {
    $str = str_replace("!F", "T", $str);
    $str = str_replace("!T", "F", $str);
    while(strlen($str) > 1) {
        while (($p = strpos($str, '+')) !== false) {
            $tmpStr = substr($str, $p - 1, 3);
            if ((strpos($tmpStr, 'T') !== false) && (strpos($tmpStr, 'F') === false)) {
                $str = str_replace($tmpStr, 'T', $str);
            } else {
                $str = str_replace($tmpStr, 'F', $str);
            }
        }
        while (($p = strpos($str, '|')) !== false) {
            $tmpStr = substr($str, $p - 1, 3);
            if (strpos($tmpStr, 'T') !== false && strpos($tmpStr, 'F') !== false || strpos($tmpStr, 'T') !== false && strpos($tmpStr, 'F') === false) {
                $str = str_replace($tmpStr, 'T', $str);
            } else {
                $str = str_replace($tmpStr, 'F', $str);
            }
        } 
        while (($p = strpos($str, '^')) !== false) {
            $tmpStr = substr($str, $p - 1, 3);
            if (strpos($tmpStr, 'T') !== false && strpos($tmpStr, 'F') !== false) {
                $str = str_replace($tmpStr, 'T', $str);
            } else {
                $str = str_replace($tmpStr, 'F', $str);
            }
        } 
    }
    return $str;
}

?>