# Overview

This project is a translation website, which include 2 group member
- Translator group: include members who can create translation for english keywords.
- Viewer group: include members who can view all the translations of english keywords. Viewer with role **admin** can manage translators, languages and keywords.
	
Tech stack:
- Laravel framework
- Package manages: composer, npm
- Database: mysql, sqlite

# Installation

### Prerequisite
1. php >= 8.0

Install php 8.1

Add repository
```
$ sudo apt update
$ sudo apt install lsb-release ca-certificates apt-transport-https software-properties-common -y
$ sudo add-apt-repository ppa:ondrej/php
Press Enter to continue
```

Install php
```
$ sudo apt update
$ sudo apt install php8.1 -y
```

Check version
```
$ php -v
```

2. composer (latest version recommend)

To install composer, run the following commands
```
$ sudo php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
$ sudo php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
$ sudo php composer-setup.php
$ sudo php -r "unlink('composer-setup.php');"
```

Create sym link
```
$ sudo mv composer.phar /usr/local/bin/composer
```

Check version
```
$ composer -V
```

3. git

```
$ sudo apt install git -y
```

4. nodejs >= 14.x and npm >= 6.14.x

```
$ sudo apt update
$ curl -sL https://deb.nodesource.com/setup_14.x | sudo bash -
$ sudo apt -y install nodejs
```

Check version
```
$ npm -v
$ node -v
```

5. apache2 (for hosting laravel web)

Install apache2
```
$ sudo apt-get update
$ sudo apt-get install -y apache2
$ sudo service apache2 start
```
Check status

```
$ systemctl status apache2
```

6. Mysql

Install mysql
```
$ sudo apt update
$ sudo apt install mysql-server -y
```

Check status
```
$ systemctl status mysql
# if service not running, run the command
$ sudo systemctl start mysql
```

Access mysql client
```
$ sudo mysql
```

Create user (change your username and password)
```
mysql> CREATE USER 'bugsmaker'@'%' IDENTIFIED BY '1234';
mysql> GRANT ALL PRIVILEGES ON *.* TO 'bugsmaker'@'%';
mysql> FLUSH PRIVILEGES;
```
Create database
```
mysql> CREATE DATABASE laravel_translation;
```


### Install the website

1. Clone project to /var/www/html folder
```
# switch to root user
$ sudo -i
$ cd /var/www/html
# remove all files on /var/www/html if exist
$ remove -rf *
# clone 
$ git clone <git-repo> .
```
2. Create file .env from .env.example
```
$ cp .env.example .env
```

Update database connection info on file **.env**
```
DB_DATABASE_SQLITE=sqlite/language.sqlite

DB_CONNECTION=mysql
DB_HOST=<mysql-host>
DB_PORT=<mysql-port>
DB_DATABASE=<database>
DB_USERNAME=<username>
DB_PASSWORD=<passwd>
```

For example:
```
DB_DATABASE_SQLITE=sqlite/language.sqlite

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_translation
DB_USERNAME=bugsmaker
DB_PASSWORD=1234
```

3. Update file config/database.php with similar info, for example

```
 'connections' => [
				....
        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel_translation'),
            'username' => env('DB_USERNAME', 'bugsmaker'),
            'password' => env('DB_PASSWORD', '1234'),
            ]) : [],
        ],
```

4. Install dependencies with composer, npm

Install php extensions
```
$ sudo apt install php-curl -y
$ sudo apt install php-xml -y
$ sudo apt install zip unzip php-zip -y
$ sudo apt install php-mysql -y
$ sudo apt install php-mbstring -y
$ sudo apt install php-sqlite3
```

Install dependencies
```
$ composer install
$ npm install
```

5. Generate key for app
```
$ php artisan key:generate
```
This command will add value for key APP_KEY on .env file.

6. Migrate database 

Access mysql client
```
$ sudo mysql
```

Run command to create tables and import data
```
mysql> use laravel_translation;
mysql> source /var/www/html/sql/mysql.sql;
```

7. Test if website work well
```
$ php artisan serve
```
Then access to http://localhost:8000 to verify

Press Ctrl + C to cancel after finishing test.

8. Build project
```
$ npm run dev
```
This command will compile code into **public** folder

9. Config apache to host the website

Remove default config file
```
$ sudo rm /etc/apache2/sites-available/000-default.conf
```

Create an Apache virtual host config file
```
$ sudo vim /etc/apache2/sites-available/laravel-translate.conf
```

The content of file laravel-quiz.conf
```
<VirtualHost *:80>

    ServerAdmin admin@laravel-translate.com
    ServerName laravel-translate.com
    DocumentRoot /var/www/html/public
    
     <Directory /var/www/html/public>
       Options +FollowSymlinks
       AllowOverride All
       Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined

</VirtualHost>
```

Active Apache rewrite module and restart apache
```
$ sudo a2enmod rewrite
$ cd /etc/apache2/sites-available/
$ sudo a2ensite laravel-translate.conf
$ sudo service apache2 restart
```

Give permission so app can update file (for creating log, session files)
```
$ cd /var/www/html
$ sudo chmod -R 777 storage
```

Now access your server site http://localhost to check if the website work properly.

# Usage guide

### Translator

Access the main site to login with translator account (with name pattern: \<language>@iqualif.com, for example: Ukrainian@iqualif.com)

![a8d86c28cd37937c612025f93b380260](https://user-images.githubusercontent.com/27953500/192078562-2311a3b1-ddf6-4a4c-b87a-3bb01f325529.png)

Each translator account is correspond to one language.

Create translation for each keyword by filling on the textbox of Translation colum, it will save automatically

![30da9552e607e60fb0dd32d13180bba0](https://user-images.githubusercontent.com/27953500/192078557-a801e530-ad49-47f8-b293-9508da1948b3.png)

Click button to show/hide keywords

![48bdcfb9f2186a81a66adba974f63f63](https://user-images.githubusercontent.com/27953500/192078551-50c396c7-b8c0-4ad5-b996-817fedec6aa7.png)

### Viewer

Access the path: /admin (http://host:port/admin) to login as viewer

![713e4396d9ae2cb76e3eefef94cb9b67](https://user-images.githubusercontent.com/27953500/192078518-1a3aaddc-98ed-44da-b1ca-913a94851059.png)

**Normal viewer**

- View translate

After login, you will see the main page of viewer. Click to the textbox view translations on all languages for the keyword.

![59c9dc149fcbe0abb1e627a539044351](https://user-images.githubusercontent.com/27953500/192078511-4d0dc7cc-ed6b-4e77-b695-9a3577283dac.png)

**Admin viewer**

Account: admin

Password: eDf1r23g4tgfA

If your viewer account has role admin, it'll has the following functions

- Manage translator, language, keyword

After login as admin, you will see the main page like this. Here you can manage (add, delete, update)   translator accounts, languages and keywords

![42e56b84abb05cdf123b5711a23ba460](https://user-images.githubusercontent.com/27953500/192078479-2e4e13e4-61b9-4993-bb3a-fa643988ed49.png)



- Show/hide keywords

Click on **User Section** to access User Section page. Here you can view translation and hide/show keywords from translator.
![7383f6b038eff8c8fe8d6c2c1da3a0f2](https://user-images.githubusercontent.com/27953500/192078488-b0d12d50-b8e7-47d7-9688-017ea9f129bb.png)

- Backups

Click Backups to access the Backups page

![84ced2dc2ac789a02b651a998d994828](https://user-images.githubusercontent.com/27953500/192078491-85dd09e6-5297-4538-9167-7488a4a3a1d4.png)

Here you can create backups for language.sqlite database or push it on GitLab (the git repo which you pull from).

You can also download the backup created previous.


# Maintenance guide

### Data structure 

**Mysql data structure**

![45f70352f7178010893050973d4443c4](https://user-images.githubusercontent.com/27953500/192078496-6725a665-5fd6-43cd-9233-0ba327f216aa.png)


**Sqlite data structure**

Each tables of sqlite database correspond to one language, they all have 3 columns: id, key, translation

![26afb6d4a327061398e43be486c36b19](https://user-images.githubusercontent.com/27953500/192078505-848293bd-f602-422e-ab51-f6b9b245c8c4.png)
