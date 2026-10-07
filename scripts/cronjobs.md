# Spport portal daily backup
30 1 * * * /usr/bin/sh /var/www/html/supportportal/scripts/backupdb.sh
30 2 * * * /usr/bin/sh /var/www/html/supportportal/scripts/backuphtml.sh
30 3 * * * /usr/bin/sh /var/www/html/supportportal/scripts/telegramSupportPortalTicket.sh
