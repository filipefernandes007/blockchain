echo "Installing dependencies..."

sudo apt install php-pear
sudo apt-get install python-software-properties
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
sudo apt-get install --allow-unauthenticated -y php7.1
sudo apt install --allow-unauthenticated php7.1-cli php7.1-mbstring php7.1-zip php7.1-sqlite3 php7.1-pdo-sqlite
sudo apt install --allow-unauthenticated php7.1-xml php7.1-curl
sudo update-alternatives --set php /usr/bin/php7.1

sudo service apache2 restart