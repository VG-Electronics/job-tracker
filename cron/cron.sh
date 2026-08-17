#!/bin/sh

php85 artisan schedule:run >> storage/logs/cron.log 2>&1
