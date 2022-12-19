#!/bin/bash
################### Config ################################
# Cambiar las variables 
# por los valores apropiados
########################################################### 
DIRORIGEN=/var/spool/asterisk/monitor
DIRDESTINO=/mnt/respaldo
DIRREMOTO=root@201.243.68.28:/var/www/html/audio
########################################################### 

AYER=$(date -d "yesterday" +"%Y-%m-%d")


# Borrar archivos de 0bytes de el origen
find $DIRORIGEN -size 0c -delete

# Desmonta el Sistema de archivos remoto si está montado
umount $DIRDESTINO

# Montaje del directorio remoto
sshfs $DIRREMOTO -p 22 $DIRDESTINO

for X in $DIRORIGEN/* # Directorio del cliente
do
	LARGO=${#DIRORIGEN}
	#CLIENTE=${X:$LARGO} # nombre del cliente. Ej. "/SOPA"
	#
	mkdir -p $DIRDESTINO$CLIENTE
	for Y in $X/* # Archivos en la carpeta del cliente
	do
    	FECHAARCH=$(date -r $Y +"%Y-%m-%d")
    	    if [ "$AYER" = "$FECHAARCH" ] ;then
	 		mkdir -p $DIRDESTINO/$FECHAARCH
    		mv -n $Y $DIRDESTINO/$FECHAARCH 
	    fi
	done
done

umount $DIRDESTINO
