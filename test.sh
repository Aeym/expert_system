#!/usr/bin/env bash
echo -e "\n"
echo "Usage : type directory name to test expert system"
echo -e "\n"
echo "Available directories :"
echo "1) error"
echo "2) "
echo "3) "
echo -e "\n"
read -p "Enter name : " chosedir
red=`tput setaf 1`
green=`tput setaf 2`
cyan=`tput setaf 6`
reset=`tput sgr0`
PHP=$(which php)
CAT=$(which cat)
DIR1=$(pwd)
DIR2="/tests/$chosedir/*"
DIR3="$DIR1$DIR2"
for file in $DIR3; do
    echo $reset
    echo $file
    echo $cyan
    $CAT $file
    echo -e "\n"
    if [ "$chosedir" == "error" ]
        then
            echo $red
        else
            echo $green
    fi
    $PHP /Users/adacher/expert-sys/expert_system.php $file
    echo -e "\n"
done