#!/bin/sh
# Health check script for MPWA container

set -e

# Check if PHP-FPM is running
if ! pgrep -x php-fpm > /dev/null; then
    echo "PHP-FPM is not running"
    exit 1
fi

# Check if Nginx is running
if ! pgrep -x nginx > /dev/null; then
    echo "Nginx is not running"
    exit 1
fi

# Check if Node.js is running
if ! pgrep -x node > /dev/null; then
    echo "Node.js is not running"
    exit 1
fi

# Check if web server responds
if ! curl -f -s -o /dev/null http://localhost:80; then
    echo "Web server is not responding"
    exit 1
fi

echo "All services are healthy"
exit 0
