Easy-admin Demo
=================

To do

Install
=======

    $ git clone https://github.com/john-dufrene-dev/api-easyadmin.git

    $ openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
    $ openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout

And go to https://localhost.

Loading Fixtures
================