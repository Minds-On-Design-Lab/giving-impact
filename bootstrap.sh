sudo debconf-set-selections <<< 'mysql-server-5.5 mysql-server/root_password password root'
sudo debconf-set-selections <<< 'mysql-server-5.5 mysql-server/root_password_again password root'
sudo apt-get update
sudo apt-get -y install git-core curl mysql-server-5.5 php5-mysql apache2 php5 php5-curl php5-gd

if [ ! -f /var/log/databasesetup ];
then
    echo "CREATE DATABASE gi CHARACTER SET utf8 COLLATE utf8_general_ci" | mysql -uroot -proot

    touch /var/log/databasesetup
fi

if [ ! -h /var/www ];
then
    rm -rf /var/www
    sudo ln -s /vagrant/www /var/www

    a2enmod rewrite

    sed -i '/AllowOverride None/c AllowOverride All' /etc/apache2/sites-available/default

    service apache2 restart
fi

if [ ! -f /vagrant/composer.phar ]
then
    ( cd /vagrant ; curl -sS https://getcomposer.org/installer | php )
fi

( cd /vagrant ; php composer.phar install )
