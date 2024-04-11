#!/bin/bash
read -p "Do you want to rollback a version? (yes/no): " choice
if [[ $choice == "yes" ]]; then
    result=$(mysql -u testUser -p -D testdb -se "SELECT * FROM Packages WHERE status='2';")
    if [ -z "$result" ]; then
        echo "No files found with status code '2'."
    else
        echo "Files available for rollback (status code 2):"
        echo "$result"
        read -p "Enter the source file name to rollback: " sourceFileName
        read -p "Enter the version number: " versionNumber
        fileName="${sourceFileName}_${versionNumber}.tar.gz"
        echo $fileName
        selectedFile=$(mysql -u testUser -p -D testdb -se "SELECT * FROM Packages WHERE packageName='$sourceFileName' AND versionNum='$versionNumber' AND status='1' LIMIT 1;")
        if [ -z "$selectedFile" ]; then
            echo "Selected file not found or status code is not '1'."
        else
            foundFile=$(find /home/malanka/FINAL/packages -name "$fileName" 2>/dev/null)
            echo "$foundFile"
            if [ -z "$foundFile" ]; then
                echo "File not found."
                echo "$foundFile"
            else
            	./TransferToQa.sh "$foundFile"
                ./TransferToProd.sh "$foundFile"
            fi
        fi
    fi
else
    echo "Done"
fi
