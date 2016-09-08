# git-php
Small web tool for managing files in git repo


Installation
=========

Project is written in Symfony2 framework, after pulling repository simply run ```composer install``` to install all the dependencies.
Composer will ask you for additional app details such as repository-path or database details

After that you need to install all assets -- run ```php app/console assetic:dump``` to dump them.

Project is written in PHP7, and should work on any HTTP server supporting PHP7. As a standard Symfony2 project, a local php server is also sufficient (e.g. ``` php ./app/console server:start 127.0.0.1:8000``` command).
