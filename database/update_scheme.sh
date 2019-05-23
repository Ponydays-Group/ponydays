#!/bin/bash

if [ $# -lt 2 ]
then
    echo "Usage: ./update_scheme.sh <username> <databasename>"
    exit $E_NOARGS
fi

mysqldump -u $1 -p --opt $2 -d --single-transaction > init.tmp

if [ $? -eq 0 ]
then
    sed -i 's/ AUTO_INCREMENT=[0-9]*\b//g' init.tmp
    cp -f init.tmp init.sql
    rm init.tmp

    echo "Done!"
else
    echo "Failed!"
fi
