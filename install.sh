----- install.sh -----
#!/bin/sh

TMP_FILE=${tr.f_v($application.name)}

META=
if [ "$1" = "all" ]
then
META=meta/
fi

USERNAME=sirjoe
HOSTNAME=virgo.ayz.pl
SSH_PORT=59184

tar -cpzf $TMP_FILE.tar.gz portlets/ $META
ncftpput -u $USERNAME $HOSTNAME portal $TMP_FILE.tar.gz
ssh $USERNAME@$HOSTNAME -p 59184 "tar -zxvf portal/$TMP_FILE.tar.gz -C portal; rm portal/$TMP_FILE.tar.gz"
rm $TMP_FILE.tar.gz


