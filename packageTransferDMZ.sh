#!/bin/bash

if [ "$#" -lt 1 ]; then
	echo "$0 <sourceFile>"
	exit 1
fi

sourceFile=$1
username="devon"
destinationIP="10.144.57.135"
destinationDir="/home/devon"

if [ ! -f "$sourceFile" ]; then
	echo "Error: Source file '$sourceFile' doesn't exist."
	exit 1
fi
php DevSend.php "$sourceFile" "$username" "$destinationIP" "$destinationDir"
