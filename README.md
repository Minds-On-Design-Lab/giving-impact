Giving Impact
=============

Giving Impact is copyright Minds on Design Lab, Inc. and licensed under XXXX.

INSTALL
=======

Base Requirements
-----------------

Giving Impact began life as a hosted service. As such this initial version of the open source product may have some strict requirements. Please bear with us as we update the base infrastructure to support a wider array of technology.

Giving Impact requires:

* PHP version 5.3 or greater
* PHP-cURL extension
* PHP-MySQL extension
* PHP-GD extension
* PHP-OpenSSL extension
* Composer

The installer will check requirements and warn you if any are missing.

Additionally, if you're running Apache (which the rest of the guide assumes you are), you should enable Apache's mod_rewrite module.

Vagrant Install
---------------

You can use Vagrant to build a testing/development environment quickly and easily.

Tested with Vagrant 1.7. This setup **is not** intended to be used in production.

After cloning the repository, start Vagrant:

    $] cd gi-app
    $] vagrant up

After the virtual machine starts, it'll install the base requirements, including Composer. Then just log in and run the installer:

    $] vagrant ssh
    vagrant@precise32:~$ cd /vagrant
    vagrant@precise32:~$ php ./install.php

During install, provide `root` for both database user and password and make sure to provide your Stripe test keys. All other settings can be left as the default.

Open [http://192.168.10.11/](http://192.168.10.11/) in your browser and you're ready to go.

Guided Install
-------------

First, unpack the application. The only directory that should be viewable via the web server is the `www` directory. A basic Apache virtual host config may look something like:

    <VirtualHost *:80>
        DocumentRoot /path/to/giving-impact/www

        <Directory "/path/to/giving-impact/www">
            AllowOverride All
        </Directory>
    </VirtualHost>

Next, from the application's root directory, install Composer requirements:

    $] cd giving-impact
    $] composer install

Finally, run the installer:

    $] php install.php

Answer a few questions and you're ready to roll.

Manual Install
------------------

First, unpack the application. The only directory that should be viewable via the web server is the `www` directory. A basic Apache virtual host config may look something like:

    <VirtualHost *:80>
        DocumentRoot /path/to/giving-impact/www

        <Directory "/path/to/giving-impact/www">
            AllowOverride All
        </Directory>
    </VirtualHost>

Next, from the application's root directory, install Composer requirements:

    $] cd giving-impact
    $] composer install

Then, set up the application configs. Copy the `application/config/database-example.php` file to `application/config/database.php` and set your connection information.

Copy `application/config/config-example.php` to `application/config/config.php` and set your Stripe keys and other, optional, settings.

Next, run the migrations to build the initial database. Open `phinx.yml` and set your database settings again. Run phinx:

    php ./vendor/bin/phinx migrate

Finally, set up your rewrite rules. Copy `www\htaccess.txt` to `www\.htaccess`, or place the rewrite rules in your Apache virtual host config. This part is especially important in making the application and the api bits talk to each other properly.

Install Questions
-----------------

##### What if I need to run the web accessible directory in an entirely different place than the rest of the application?

If you need to run the web accessible directory in an entirely different place than the rest of the app, be sure to update the paths in both the `www/index.php` file and `www/api.php` with the full path to the application.

##### Do I really need to have the MySQL plugin installed?

Yes. This version of Giving Impact runs on the 2.x version of the Code Igniter framework, which does not support PDO. You can use the MySQLi extension, though. Just update the driver listed in `application/config/database.php` and the one listed in `phinx.yml` to `"mysqli"` and you should be good to go.

##### I like living on the edge and only use PHP 7. Does GI work?

No. No it does not. Pull requests are welcome, though.
