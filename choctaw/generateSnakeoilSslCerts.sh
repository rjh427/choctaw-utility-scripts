#!/bin/bash
# borrowd from https://www.akadia.com/services/ssh_test_certificate.html

# verify a domain name was provided
if [[ -z "$1" ]]
then
	echo You must provide a domain name \(without the extension\) for cert naming purposes.
	exit 1
fi



# generate a private key
echo You must provide a password, it will be removed presently.
openssl genrsa -des3 -out $1.key 1024

# remove passphrase
cp $1.key $1.key.org
openssl rsa -in $1.key.org -out $1.key

# generate a csr (certificate signing request)
openssl req -new -key $1.key -out $1.csr

# generate a self-signed cert that is good for 999 days
#openssl x509 -req -days 999 -in $1.csr -signkey $1.key -out $1.crt
openssl req -x509 -nodes -sha256 -days 999 -newkey rsa:2048 -keyout $1.key -out $1.crt
# https://itigloo.com/security/generate-an-openssl-certificate-request-with-sha-256-signature/

# install private key and cert
#sudo cp $1.crt /etc/ssl/snakeoil/$1.crt
#sudo cp $1.key /etc/ssl/snakeoil/$1.key

# add to ssl-enabled vhosts:
#    SSLEngine on
#    SSLCertificateFile /usr/local/apache/conf/ssl.crt/server.crt
#    SSLCertificateKeyFile /usr/local/apache/conf/ssl.key/server.key
#    SetEnvIf User-Agent ".*MSIE.*" nokeepalive ssl-unclean-shutdown
#    CustomLog logs/ssl_request_log \
#       "%t %h %{SSL_PROTOCOL}x %{SSL_CIPHER}x \"%r\" %b"

# make entries to /etc/hosts as necessary

# restart apache
#sudo apache2ctl restart
#echo restarting Apache2

# test 
