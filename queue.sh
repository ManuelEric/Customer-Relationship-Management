#!/bin/bash

# Check if the process is running
if ! pgrep -f "queue:listen" > /dev/null; then
    # Start the process and redirect output
    /usr/local/bin/php /home/u5794939/public_html/github/production.crm/artisan queue:listen \
        --queue=default,inv-send-to-client,inv-email-request-sign,verifying-client,verifying-client-parent,verifying-client-teacher,imports-student,imports-parent,imports-teacher,imports-client-event,imports-school-merge,verifying_client,verifying_client_parent,verifying_client_teacher,define-category-client,get-took-ia,send-hold-program,insert-log-client,update-raw-client,update-grade-and-graduation-year-now \
        >> /home/u5794939/public_html/github/production.crm/storage/logs/queue_output.log 2>&1
fi