#!/bin/bash

if [ "$#" -lt 1 ]; then
	echo "$0 <sourceFile>"
	exit 1
fi
#DestinationIP To QA(Alex)
sourceFile=$1
username="aligmoney21"
destinationIP="10.144.132.77"
destinationDir="/home/aligmoney21/FINAL/packages"
if [ ! -f "$sourceFile" ]; then
	echo "Error: Source file '$sourceFile' doesn't exist."
	exit 1
fi
scp "$sourceFile" "$username@$destinationIP:$destinationDir"
sourceFile=$(basename "$1")
ssh "$username@$destinationIP" "cd FINAL/packages && sudo -S tar -xzvf /home/aligmoney21/FINAL/packages/$sourceFile -C /var/www/html" 


