#!/bin/bash
# Run the MySQL query to get the status counts for today
pwd=Sm@rtctrl_321mail

todayTasks=$(mysql -u root -p$pwd liveskytech -e "
    SELECT
        CASE
            WHEN status = 1 THEN 'Open'
            WHEN status = 2 THEN 'Closed'
            WHEN status = 3 THEN 'Inprogress'
            WHEN status = 4 THEN 'QC'
            WHEN status = 5 THEN 'ReWork'
            WHEN status = 6 THEN 'Delivery'
            ELSE 'unknown'
        END as status_name,
        COUNT(*) as status_count
    FROM LRticketsAssignLog
    WHERE active = 1 AND status NOT IN (2, 6)
    GROUP BY status_name
    ORDER BY status_name;
")


openTickets=$(mysql -u root -p$pwd liveskytech -e "SELECT COUNT(*) as openTickets FROM LRtickets WHERE status = 1;" | grep -v "openTickets")
inprogressTickets=$(mysql -u root -p$pwd liveskytech -e "SELECT COUNT(*) as inprogressTickets FROM LRtickets WHERE status = 3;" | grep -v "inprogressTickets")
createdToday=$(mysql -u root -p$pwd liveskytech -e "SELECT COUNT(*) as CreatedToday FROM LRtickets WHERE DATE(created_at) = CURDATE() AND status = 1;" | grep -v "CreatedToday")

# Run the MySQL query to get the delivery pending count
Delivery_Pending=$(mysql liveskytech -uroot -p$pwd -e "SELECT count(*) FROM LRtickets WHERE status = 2 AND delivery_status = 1;" | grep -v "count")

# Determine the delivery pending result
if [[ "$Delivery_Pending" -gt 0 ]]; then
    deliveryResult="DeliveryPending $Delivery_Pending"
else
    deliveryResult="No items to Deliver"
fi


# Extract the header and the data from the status counts result
header=$(echo "$todayTasks" | head -n 1)
data=$(echo "$todayTasks" | tail -n +2)


date=$(date +%d-%m-%y)

# Extract the unique date (assuming all dates are the same)
unique_date=$(echo "$date" | awk 'NR==1{print $1}')

# Create the final result variable with all the output
result="SupportPortal:"
result+="\nDate: $unique_date"
result+="\nCreatedToday  $createdToday"
result+="\nOpenTickets  $openTickets"
result+="\nInprogress $inprogressTickets"
result+="\n$deliveryResult"

# Print the final result
echo -e "$result"

# URL-encode the result
encoded_result=$(echo -e "$result" | python3 -c "import sys, urllib.parse; print(urllib.parse.quote(sys.stdin.read().strip()))")


# Send the message to Telegram using Markdown
curl -s "https://api.telegram.org/bot7256031949:AAFlXPu7LrbUvL4KpYVsuEWsgwB7-uJcOyE/sendMessage?chat_id=-1002180511293&text="$encoded_result""

# crontab - job 
# 0 16 * * MON-SAT  /bin/bash /var/www/html/supportportal/telegramSupportPortalTicket.sh