# Building Rest API
To get started, clone this repository to a proper document root. For XAMPP, this is htdocs. For private apache setup, its upto you how you configure.
This code is right now deployed at: https://apitest.jerlin.space

## Setup

Right outside the document root, create a file called env.json and keep the contents of the file similar to the following.

    {
        "database": "Database Name",
        "username": "Your Username",
        "password": "Your Password",
        "server": "Your Host",
        "email_api_key": "Your_Sendgrid_Key"
    }

This will be called by the API functions to get the database connection.

Virtual Host Apache Configuration:

    <VirtualHost *:80>
        ServerAdmin hello@jerlin.me       
        DocumentRoot "/var/www/Building-Restapi-PHP"
        ServerName apitest.jerlin.space 

            ErrorLog ${APACHE_LOG_DIR}/error.log
            CustomLog ${APACHE_LOG_DIR}/access.log combined

        <Directory "/var/www/Building-Restapi-PHP">
                Options Indexes FollowSymLinks ExecCGI Includes
                AllowOverride All
                Require all granted
        </Directory>
    </VirtualHost>
    
    In the above configuration, env.json should sit exactly /var/www/env.json here.

Configuring your own Ubuntu Setup
Reference: [Digital Ocean Ubuntu Setup](https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-on-ubuntu-20-04)

Update and upgrade the system first.


    $ sudo apt update && sudo apt -y upgrade



Install Apache, MySQL and PHP


    $ sudo apt install apache2 libapache2-mod-php mysql-server php-mysql



Secure MySQL Database


    $ sudo mysql_secure_installation


and follow the onscreen steps. For more info, check the above link.

Create a Database


    $ mysql -u root -p
    Password:


Enter the password you have given for root during mysql_secure_installation and you can see the following promot.

    mysql>


    From here, we need to create a database called

    mysql> CREATE DATABASE Your Database Name;


We also need to create a mysql username and password and give the database previleges for the database we created.

    mysql> CREATE USER 'root'@'localhost' IDENTIFIED BY 'password';
    Query OK, 0 rows affected (0.02 sec)

    mysql> GRANT ALL PRIVILEGES ON * . * TO 'apiuser'@'localhost';
    Query OK, 0 rows affected (0.00 sec)

    mysql> FLUSH PRIVILEGES;
    Query OK, 0 rows affected (0.01 sec)

    mysql> exit
    Bye
