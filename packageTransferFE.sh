#!/bin/bash

if [ "$#" -lt 1 ]; then
	echo "$0 <absolute path to the sourceFILE>"
	exit 1
fi
#destinationIP = QA IP
sourceFile=$1
username="malanka"
destinationIP="10.144.1.228"
destinationDir="/home/malanka/FINAL/packages"

if [ ! -f "$sourceFile" ]; then
	echo "Error: Source file '$sourceFile' doesn't exist."
	exit 1
fi
php DevSend.php "$sourceFile" "$username" "$destinationIP" "$destinationDir"
