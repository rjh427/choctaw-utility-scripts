#1/bin/bash

cd /var/www/html/choctaw8dev/docroot

# prod cno8-prod:/mnt/gfs/choctaw8.prod/sites/default/files/
# dev cno8-dev:/mnt/gfs/choctaw8dev/sites/default/files/

read -r -p "Syncronize Dev or Prod /files/ directory? [d/P] " response
if [[ "$response" =~ ^([pP][rR][oO][dD]|[pP])+$ ]]
then
    platform=cno8-prod
    path=/mnt/gfs/choctaw8.prod/sites/default/files/
else
    platform=cno8-dev
    path=/mnt/gfs/choctaw8dev/sites/default/files/
fi

read -r -p "Use --delete switch? [y|N] " delete
if [[ "$delete" =~ ^([nN][oO]|[nN])+$ ]]
then
    delete=''
else
    delete=' --delete '
fi

rsync -rmzulPpoEt $(delete) $(platform:path) /mnt/gfs/choctaw8dev/sites/default/files/

drush cr

cd -
