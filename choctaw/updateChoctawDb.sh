#!/bin/bash

current=$(pwd);

# get the live (production) D7 mysql dump file
cd /mnt/www/html/choctawdev/
file1=$(ssh cno-prod ls /mnt/gfs/choctaw/backups | tail -1)
echo $file1

read -r -p "File exists? Proceed with scp and unzip? [y/N] " response
if [[ "$response" =~ ^([yY][eE][sS]|[yY])+$ ]]
then
    scp cno-prod:/mnt/gfs/choctaw/backups/${file1} .
else
    echo Aborted by user request
    exit 1
fi

# unzip downloaded file and reset filename
gunzip $file1
file1=$(ls *.sql)

# import into db
read -r -p "Import .sql file into db, overwriting earlier db entries? [y/N] " response
if [[ "$response" =~ ^([yY][eE][sS]|[yY])+$ ]]
then
    # update mysql
    mysql -u root -p choctawdev < $file1

    # clean up
    rm $file1
    unset $file1
else
    echo Aborted by user request
    exit 1
fi

# update git repository
# git pull

# disable the modules we do not want and commandeer the user 1 account
# cd /mnt/www/html/choctaw8dev/docroot
# needs testing:
# drush dis apache_solr acquia_agent copyprevention
 
# return user to last directory
cd $current



