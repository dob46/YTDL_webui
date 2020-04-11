#!/bin/bash
actual_date=$(date +%s)
create_date=$(cat $1)
file=$(echo $1)
difference=$((actual_date-create_date))
if [ "$difference" -gt 14400 ]; then
	dir=$(cut -f1,2,3,4,5 -d / <<< $file)
	rm -R $dir
fi
