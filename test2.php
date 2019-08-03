<?php 

    bracket($argv[1]);

    function bracket($str) {
        $i = 0;
        $length = strlen($str);
        $openB = [];
        echo "In bracket1, str = " . $str . "\n";
        $closeB = [];
        while ($i < $length) {
            // print_r($openB);
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
                echo "str after replace : " . $str . "\n";
                $openB = [];
                $closeB = [];
                $i = 0;
            }
            $i++;
        }
        echo "str fim de bracket " . $str . "\n";
    }

    function resolveStr($str) {
        echo "str dans resolve : " . $str . "\n";
        return "T";
    }
?>