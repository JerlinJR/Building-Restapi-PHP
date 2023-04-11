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
