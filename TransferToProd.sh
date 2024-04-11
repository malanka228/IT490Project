#!/bin/bash

if [ "$#" -lt 1 ]; then
	echo "$0 <sourceFile>"
	exit 1
fi
#DestinationIP To QA(Alex)
sourceFile=$1
username="devon"
destinationIP="10.144.57.135"
destinationDir="/home/devon/FINAL/packages"
if [ ! -f "$sourceFile" ]; then
	echo "Error: Source file '$sourceFile' doesn't exist."
	exit 1
fi
scp "$sourceFile" "$username@$destinationIP:$destinationDir"
sourceFile=$(basename "$1")
ssh "$username@$destinationIP" "cd FINAL/packages && sudo -S tar -xzvf /home/devon/FINAL/packages/$sourceFile -C /var/www/html" 


