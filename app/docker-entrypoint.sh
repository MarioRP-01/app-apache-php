#!/bin/bash

# Start Apache
/usr/local/bin/apache2-foreground &

# Start apache_exporter
/usr/bin/apache_exporter --scrape_uri=http://localhost/server-status &

# Wait for any process to exit
wait -n

# Exit with status of process that exited first
exit $?
