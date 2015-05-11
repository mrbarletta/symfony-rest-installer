# symfony-rest-installer
Automate Symfony Installation Packages for Rest+ support - JWT - JMS - HWIO - FOSRest - FOSUser

There are some instructions to get you all setup.

# Bootstrap a Symfony >2.6 Rest Backend in minutes, instead of hours
## What do you get with this module?

* HWIO Auth for Facebook / Google / Twitter auth
* FOS Userbundle for User managament
* JWT tokens for authentication
* FosRest for Rest support + JMS serialization of data

## Requirements - Read before install!

* LAMP working - Apache, MySQL and PHP should be working on your setup already 
* Get your facebook app ID and Secret developer.facebook.com (this is needed for Facebook Login)
* Make sure you have openssl installed - This command should work - openssl genrsa -out /tmp/test -aes256 4096
* Add "minimum-stability": "dev" to the composer.json of your symfony root dir.
* Get a tool to test the REST API - I use postman (https://chrome.google.com/webstore/detail/postman-rest-client/fdmmgilgnpjigdojojpjoooidkmcomcm?hl=en)


## ;TL;DR

        symfony new symrest
        cd symrest
Edit your composer.json file and add the line    
     
        "minimum-stability": "dev"
Continue:
        
        composer require "lionix/symfony-rest-installer":"@dev"
        php vendor/lionix/symfony-rest-installer/src/configurePackages.php

The package will give you additional steps to follow, follow them. 
The only requisite is to have 
        
        mysql -uUSER -pPASSWORD -e "create database symrest"
        mysql -uUSER -pPASSWORD -e "GRANT ALL ON symrest.* TO user@localhost IDENTIFIED BY password"
        php app/console doctrine:schema:update --force

Navigate to localhost/symrest/web/register and **create** a new account, user that user/pass to test this setup

        curl 'http://localhost/web/app_dev.php/api/v1/getToken' -H 'Content-Type: application/json' \
            -H 'Accept: */*' -H 'Connection: keep-alive' -H 'DNT: 1' --data-binary \
            '{"username":"USER@gmail.com","password":"PASS"}' --compressed

Check the ubuntu details to have a virtualhost setup easily with a symrest.vcap.me pretty name ;)


## Installation
Install symfony First!

        symfony new YourAppName
        cd YourAppName
        composer require "lionix/symfony-rest-installer":"@dev"
        php vendor/lionix/symfony-rest-installer/src/configurePackages.php

## Configuration

* Setup symfony database (In case you didn't)

        mysql -uUSER -pPASSWORD -e "create database symfony"
        mysql -uUSER -pPASSWORD -e "GRANT ALL ON symfony.* TO user@localhost IDENTIFIED BY password"
        
** Configure this DB parameters in app/config/parameters.yml
* Tell symfony to create the tables needed

        php app/console doctrine:schema:update --force
## Usage
- Surf to the /register URL of YourAppName (e.g.  localhost/symrest/web/register if you didn't use virtual hosts)
- Register into your site - remember user and pass
- Test the registration with CURL or POSTMAN - if you use curl do this on the console:

        curl 'http://localhost/web/app_dev.php/api/v1/getToken' -H 'Content-Type: application/json' -H 'Accept: */*' -H 'Connection: keep-alive' -H 'DNT: 1' --data-binary \
        '{"username":"USER@gmail.com","password":"PASS"}' --compressed

The response should be something like this:

        {"token":"eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXUyJ9.eyJleHAiOjE0Mjg4OTE5ODEsInVzZXJuYW1lIjoibXJiYXJsZXR0YUBnbWFpbC5jb20iLC\
        JpYXQiOiIxNDI4ODA1NTgxIn0.bng6c1bSz9k-2EN-aRRBttBSwM2v2KI8tXAOpFcPhsXYkvJdRFJLTec0x_6LKQrW7idQ-Cj4rbO0rSPdH9giDGprav8\
        NtbmFtfqhnvDDxBSfJvzEINmn_ckoEuD5tRPklW1o7p2FEX-GlEE8g9b0FpVe_1sUo3MP0H3lsEh23tvAgt_xp8B3fcw89OQrQfpbCyZdRtnsLIutzyLzk\
        make2iGdWPcODjPe-jIucpqrKD1hRrJBx6IdssJlDMZ1FEN_irPFGcZttq2NE9wkJJaPAd4y-3H8uc8x75RdI9Dw5LLhzS7n1Tvi-wqvbVTqXWxgJg4_Tm\
        xHOr4MBpCnlPJgtmeBnkYnEhICSWKFalHsc11Lycf7-z6thBhMdIgB9wCRugcCVbsy6W5vkM41mjVo1MugSXdlzDqCZD9cqnT6-7cKr6_3M3t_AreLDvVgl\
        AKrsApGEVyBl0UFRl7f9ZwO9ICETtV1dOEQ1SoQpuLs0jQaAqScZ6tmnlKBRf84xdTmSG1DW2riyclbUzhLFj9Fr0ujQCSaejP-ldpvsgFPw1YVkLovHhS7\
        8q4HE6ZFjO7uv--bRRkB8iyCgGBjj-Vhh82_Pzm3dpWx6Lxqbg46tyqnJpnmk8JSicycrSXXzicmSdn6iWVfNhmMjxPBZPeimYPd7Z-Ckp2TewX6NvIWFg"}

Thats your JWT token that you will use on your app or in POSTMAN to go around.


## Configuration with Virtual Host for a fancy name
We will use vcap.me or xip.io DNS Service - This service provides  DNS trick that will always return 127.0.0.1 for any address (vcap) or whatever IP you put on the domain (xio)

        symrest.127.0.0.1.xip.io
        symrest.vcap.me

UBUNTU/LINUX only - You can automatically create the virtualhost using this handy tool made by RoverWire

        wget -O virtualhost https://raw.githubusercontent.com/mrbarletta/virtualhost/master/virtualhost.sh
        chmod +x virtualhost
        sudo ./virtualhost create symrest.vcap.me symrest

 
## Included and ready to use calls
* api/v1/users  - Gets all the calls
* api/v1/users/1 - Gets a specific user
## Notes
* If you want to use this on a non-fresh symfony installation read and strip the parts needed.
* This is a WIP - we use it at our office to train candidates and bootstrap some prototypes.

## Known Issues
### no matching package found
 Add "minimum-stability": "dev" to the composer.json of your symfony root dir.
 
 Packages that require @dev will not be installed if this is not used, like hwioauthbundle (requires 0.4 for symfony>2.4).
### Unauthorized
Apache will strip Authorization headers, so be sure to have this lines in your .htaccess or in your hosts file.
Symfony already has this setup, but will require a AllowOverride all setting in  your virtualhost /apache conf.

        RewriteEngine On
        RewriteCond %{HTTP:Authorization} ^(.*)
        RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]


## TODO
* Put some facebook pre-made examples 
* Use a Symfony command instead of a plain php
* Make the table configurable - now uses fos_user table, it should be user-defined
* Add #failure_handler / success_handler support functions
