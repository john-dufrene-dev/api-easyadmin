Easy-admin Demo
=================

To do

Install
=======

1. 
    $ git clone https://github.com/john-dufrene-dev/api-easyadmin.git

2. 
    $ openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
    $ openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout

    Or just use this command :
    $ php bin/console lexik:jwt:generate-keypair

And go to https://localhost.

Loading Fixtures
================