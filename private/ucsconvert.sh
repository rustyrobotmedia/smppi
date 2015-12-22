#!/bin/sh

# checkhandler for SMS Tools 3
# autoconverts cyrillic messages to UCS-2BE
# add checkhandler=/path/to/ucsautoconvert into global part of smsd.conf
# written by lexy (lexy@mrlexy.ru), 2008

FILE=`mktemp /tmp/smsd_XXXXXX`

if [ ! `grep '[А-Яа-я]' $1 > /dev/null` -o `grep 'Alphabet:\s*U' $1 > /dev/null` ]
    then exit 0
fi

cat $1 | awk '{if(NF==0) {s=1} if(s==0 && NF>0 && $0!~/Alphabet:[ \t]*U/){print}}' > $FILE
echo Alphabet: Unicode >> $FILE
cat $1 | awk '{if(NF==0) {s=1} if(s==1){print}}' | iconv -t UCS-2BE >> $FILE

mv $FILE $1
chmod 664 $1

