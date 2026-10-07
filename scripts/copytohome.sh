#!/bin/bash

#sshpass -p "Sm@rtctrl_321mail" rsync -auv --progress -e "ssh -p 22" /var/www/html/backup/backup_db/liveskytech-13-03-23-02-30.sql*.*  root@gsmhome.liveskytech.com:/var/www/html/copy
sshpass -p "Sm@rtctrl_321mail" rsync -auv --progress -e "ssh -p 22" /var/www/html/backup/live_backup_html/html-10-02-23-02-50.tar*.*  root@gsmhome.liveskytech.com:/var/www/html/copy_html
#sshpass -p "Sm@rtctrl_321mail" rsync -auv --progress -e "ssh -p 22" /var/apiserver/*  root@gsmhome.liveskytech.com:/var/apiserver
#sshpass -p "Sm@rtctrl_321mail" rsync -auv --progress -e "ssh -p 22" /var/python_server/*  root@gsmhome.liveskytech.com:/var/python_server --exclude client_logs