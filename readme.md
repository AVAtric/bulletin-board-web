## TCP/IP Bulletin board

## Prerequisite
Install php with the following modules:
```
php php-cli php-common php-mbstring php-gd php-intl php-xml php-zip php-curl php-mcrypt
```

### Install composer

```
$ php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
$ php -r "if (hash_file('sha384', 'composer-setup.php') === '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
$ php composer-setup.php
$ php -r "unlink('composer-setup.php');"
```

### Install node.js

Ubuntu:
```
$ sudo apt install nodejs
$ sudo apt install npm
```

Windows: https://nodejs.org/dist/v10.14.1/node-v10.14.1-x64.msi

Other distributions: https://nodejs.org/

## Installation

Download the application
```
$ git clone https://github.com/AVAtric/bulletin-board-web.git
```

First of all change into the installation folder

```
$ cd bulletin-board-web
```

Install packages

```
$ php composer install
$ npm install
$ npm run dev
```

Open a new terminal session

```
$ php artisan serve
```

## Use

Open address given after serve
