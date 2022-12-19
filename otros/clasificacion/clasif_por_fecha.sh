#!/bin/bash

DIR=/var/www/html/audio/*
DEST=/var/www/html/aup

proc(){
	# $1 es la fecha
	# $3 es el archivo
#	echo "<< $1 $2 >>"
	mkdir -p $DEST/$1
	archivo=$2
	archivo=${archivo##*/}
	echo "mv $2 $DEST/$1/$archivo"
	mv $2 $DEST/$1/$archivo
}

for x in $DIR/*.mp3; do
	re=".+\-([0-9]+)\.([0-9]+\.[0-9]+)\.mp3"
	if [[ $x =~ $re ]]; then
    	calledstation=${BASH_REMATCH[1]}
    	id=${BASH_REMATCH[2]}
	fi
#	sql="SELECT starttime proc FROM consolidado WHERE id='$id' AND calledstation='$calledstation'"
#	salida=$(mysql -u arnoldobr -pozemroland inspector -e "$sql")
#	if [[ "$salida" != "" ]]; then
#		$salida $x
#	fi
	# obtenser la fecha del archivo
	# crear el directorio de la fecha si no se ha creado
	# mover el archivo al directorio 
	# mv $x 
	fecha=$(date -d @$id +"%Y-%m-%d")
	proc $fecha $x
done

