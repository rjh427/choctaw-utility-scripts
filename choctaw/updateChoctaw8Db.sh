#!/bin/bash

current=$(pwd);

# get the live (production) D8 mysql dump file
cd /home/choctaw/sql-backups/choctaw8
# prod or dev?
read -r -p "Get production or development SQL backup file? [p/D] " response
if [[ "$response" =~ ^([dD][eE][vV]|[dD])+$ ]]
then
    platform=cno8-dev
    path=/mnt/gfs/choctaw8dev/backups
else
    platform=cno8-prod
    path=/mnt/gfs/choctaw8/backups
fi

echo platform: $platform

# reassign $file1 var
file1=$(ssh "${platform}" ls $path/*.gz | tail -1)
echo file: $file1

read -r -p "File name correct? ? Proceed with scp? [y/N] " response
if [[ "$response" =~ ^([yY][eE][sS]|[yY])+$ ]]
then
    scp ${platform}:${file1} .
else
    echo Aborted by user request
    exit 1
fi

# unzip downloaded file and reset filename
read -r -p "Unzip file? [y/N] " response
if [[ "$response" =~ ^([yY][eE][sS]|[yY])+$ ]]
then
    file2=$(basename "$file1")
    gunzip /home/choctaw/sql-backups/choctaw8/$file2
    file1="${file2%.*}"
fi

# import into db
read -r -p "Import .sql file into db, overwriting earlier db entries? [y/N] " response
if [[ "$response" =~ ^([yY][eE][sS]|[yY])+$ ]]
then
    # update mysql
    mysql -u root -p choctaw8dev < $file1
else
    echo Aborted by user request
    exit 1
fi

# clean up
#    rm $file1
#    unset $file1
# update git repository
# git pull

# disable the modules we do not want and commandeer the user 1 account
# cd /mnt/www/html/choctaw8dev/docroot
# drush dis // currently none
# drush cli

# return user to last directory
cd $current
echo This next step will only work if you ran this script from
echo a location where drush works.
echo -e '\t' $current
echo If you do not see the drupal directory or a subdirectory
echo listed 2 lines above, select "No".
read -r -p "Use drush to reset the password on an account? [y/N] " response
if [[ "$response" =~ ^([yY][eE][sS]|[yY])+$ ]]
    then
        read -r -p "Username of account to reset: " response
            drush uli $response
    else
        echo Aborted by user request
        exit 1
fi
