#!/usr/bin/bash

if [ "$1" = "" ];then
    echo "error:Please specify a flag (database name)"
    exit
fi

a=`date +%Y%m%d%H%M%S`

if [ "$2" = "sql" ];then
	cd /data/bak/
	mysqldump -u $1 -p123456 $1>/data/bak/$1-$a.sql
	tar cfz $1-$a.sql.tar.gz $1-$a.sql
fi

cd /data/web/$1
tar cfz /data/bak/$1-logs-$a.tar.gz runtime/log

rm -rf runtime/log/*

if [ "$2" = "file" ];then
tar cfz /data/bak/$1-$a.tar.gz ./
fi
