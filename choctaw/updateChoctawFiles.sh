#1/bin/bash

cd /var/www/html/choctawdev/docroot

rsync -rmzulPpoEt cno-dev:/mnt/gfs/choctawdev/sites/default/files/ /mnt/gfs/choctawdev/sites/default/files/

drush cc all

