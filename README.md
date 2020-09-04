A package for keeping your data tight.
There are a few general concepts here
Dto = Data transfer object.  
 This is mainly a php 8 pattern for ensuring your object gets the right data using contructor params.

Vto - Value transfer object.
 This is an object with just a 'value' property.  We'll try to ensure proper usage, but it isn't perfect. 
 It has some utilities to make it easier to work with.
 
Cfd - Correct for data object.  Like a Vto, but with multiple properties.

Setup (locally)
---------------
composer install

Roadmap
-------
9/20' Finish transition to php 8


php8-dev
    
    phpunit...
        Use composer 2.x
                -try-
            composer self-update --preview
        Tell composer to not freak on phpunit requirements
            See: https://github.com/sebastianbergmann/phpunit/issues/4325
                -try-
            composer require --dev phpunit/phpunit --ignore-platform-req=php phpunit/phpunit ^9.3\n
            (or maybe...
             composer require --dev phpunit/phpunit --ignore-platform-req=php
             composer update --ignore-platform-req=php