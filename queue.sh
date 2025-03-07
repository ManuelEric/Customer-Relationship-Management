#!/bin/bash

# Check if the process is running
if ! pgrep -f "queue:listen" > /dev/null; then
    # Start the process and redirect output
    /usr/bin/php /crm/artisan queue:listen \
        --queue=default,inv-send-to-client,inv-email-request-sign,verifying-client,verifying-client-parent,verifying-client-teacher,imports-student,imports-parent,imports-teacher,imports-client-event,imports-school-merge,verifying_client,verifying_client_parent,verifying_client_teacher,define-category-client,get-took-ia,send-hold-program,insert-log-client,update-raw-client,update-grade-and-graduation-year-now,email-confirmation-event \
        >> /crm/storage/logs/queue_output.log 2>&1
fi
