# this script help to sync data from development env to live env
#!/bin/bash
cd /var/www/html/supportportal
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan clear-compiled
php artisan config:cache

SOURCE_DIR=/var/www/html/supportportal
DEST_DIR=/var/www/html/supportportal

exclude_dir='storage/'
exclude_firmware='public/firmware/'



sshpass -p 'Sm@rtctrl_321mail' rsync --delete --exclude=$exclude_dir --exclude=$exclude_firmware -avl --progress -e  'ssh -p 3022' $SOURCE_DIR/  root@gsmoffice.liveskytech.com:$DEST_DIR/

sshpass -p Sm@rtctrl_321mail ssh -p 3022 -t root@gsmoffice.liveskytech.com <<EOF
cd /var/www/html/supportportal/
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan clear-compiled
php artisan config:cache
EOF

sshpass -p 'Sm@rtctrl_321mail' rsync --delete --exclude=$exclude_dir --exclude=$exclude_firmware -avl --progress -e  'ssh -p 3022' $SOURCE_DIR/  root@gsmhome.liveskytech.com:$DEST_DIR/

sshpass -p Sm@rtctrl_321mail ssh -p 3022 -t root@gsmhome.liveskytech.com <<EOF
cd /var/www/html/supportportal/
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan clear-compiled
php artisan config:cache
EOF