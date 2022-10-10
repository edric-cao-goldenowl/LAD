Here is a guide on how you will create a LEMP server on AWS running a Laravel project with the following configuration:
+ Amazon Linux OS: Centos 7, WebService: Apache, PHP8, DB: MariaDB

* Apache: Is an open source reverse proxy server that uses popular HTTP, HTTPS, SMTP, POP3 and IMAP protocols. Also used as load balancer, HTTP cache and web server.


First we need an AWS account

# 1. Install PHP

Confirm that the amazon-linux-extras package is installed:
```
$ which amazon-linux-extras
/usr/bin/amazon-linux-extras
```
If the command doesn’t return any output, then install the package that will configure the repository:

```
sudo yum install -y amazon-linux-extras
```

Let’s confirm that PHP 7.x topic is available in our Amazon Linux 2 machine:
```
$ sudo  amazon-linux-extras | grep php
 42  php7.4                   available    [ =stable ]
 51  php8.0                   available    [ =stable ]
 ```
As we can see all PHP topics, in this example we’ll enable php8.0 topic.

```
sudo amazon-linux-extras enable php8.0
```
Now install PHP packages from the repository, include pear, mysqllnd,json....

```
sudo yum clean metadata
sudo yum install php php-{pear,cgi,common,curl,mbstring,gd,mysqlnd,gettext,bcmath,json,xml,fpm,intl,zip,imap}
```

Check default PHP version:

```
[ec2-user@ip-172-31-10-22 ~]$ php -v
PHP 8.0.20 (cli) (built: Jun 23 2022 20:34:07) ( NTS )
Copyright (c) The PHP Group
Zend Engine v4.0.20, Copyright (c) Zend Technologies
```

Bisdie, To install PHP 7.4, make sure you disable 8.0 then enable 7.4.

```
sudo amazon-linux-extras disable php8.0
sudo amazon-linux-extras enable php7.4
sudo yum install php php-{pear,cgi,common,curl,mbstring,gd,mysqlnd,gettext,bcmath,json,xml,fpm,intl,zip,imap}
```
Confirm version of PHP.
```
[ec2-user@ip-172-31-10-22 ~]$ php -v
PHP 7.4.31 (cli) (built: Jul  2 2020 23:17:00) ( NTS )
Copyright (c) 1997-2018 The PHP Group
Zend Engine v4.0.20, Copyright (c) Zend Technologies
```

# 2. Install Apache
## 2.1 First we need update all dependence of OS:
```
sudo yum update -y
```

## 2.2 CMD download and install Apache

```
sudo yum install -y httpd
```
## 2.3 After that we start Apache webserver
```
[ec2-user ~]$ sudo systemctl start httpd
```

## 2.4 Next we will configure the system to start Apache Webservice automatically every time the system reboots:
```
[ec2-user ~]$ sudo systemctl enable httpd
```
We can check the status of **httpd**

```
[ec2-user@ip-172-31-10-22 ~]$ systemctl is-enabled httpd
enabled
```

## 2.5 Managing the Apache Process
Now that you have your web server up and running, let’s go over some basic management commands.

To stop your web server, type:
```
sudo systemctl stop httpd
```
To start the web server when it is stopped, type:

````
sudo systemctl start httpd
````
To stop and then start the service again, type:
```
sudo systemctl restart httpd
```
If you are simply making configuration changes, Apache can often reload without dropping connections. To do this, use this command:
```
sudo systemctl reload httpd
```
By default, Apache is configured to start automatically when the server boots. If this is not what you want, disable this behavior by typing:
```
sudo systemctl disable httpd
```
To re-enable the service to start up at boot, type:
```
sudo systemctl enable httpd
```
Apache will now start automatically when the server boots again.

The default configuration for Apache will allow your server to host a single website. If you plan on hosting multiple domains on your server, you will need to configure virtual hosts on your Apache web server.


## 2.6 Config file httpd
The default Apache configuration file is located at ``/etc/httpd/conf/httpd.conf``. We restrict the dynamics of this file, when we want to configure a website or multiple websites, we should create a separate conf file in the directory ``/etc/httpd/conf.d/*.conf`` . The config will have its own article, but for now we will leave the default configuration.
** Note: Every time we change the configuration, we have to call the command restart apache so that it can get the new configuration we just edited or added
```
sudo systemctl restart httpd
```

# 3. Open port public for server 
So we have installed Apache, but we still have 1 more step to open the browser and enter Public IP Address or Public IP DNS to be able to see our web service.
AWS has a security department, it's like a firewall. You need a public port to allow outsiders to access our server through the ports we allow. It's called Security Group
So what do we need to do, very simple, every time you create an EC2 instance, it will be assigned a security group by default. We'll just open the port for them. Here's how to do it:
1. Open AWS console link [https://console.aws.amazon.com/ec2/](https://console.aws.amazon.com/ec2/).
2. Instance part, choose instance that you create for web service
3. Tab **Security** tab,  Inbound rules, you will see the config default:  

| Port range | Protocol | Source    |
|------------|----------|-----------|
| 22         | tcp      | 0.0.0.0/0 |

This is default _inbound rules_  so you can have SSH access to the server. Now we will create another rule to allow access to the Apache web.
Select the Security groups link above and click edit Inbound rules, then choose as below and save:  
**Type**: HTTP  
**Protocol**: TCP  
**Port** Range: 80  
**Source**: Custom  

Now you can open a browser and access Ip Public or Ip public DNS. By default web source will be saved at /var/www/html. If this directory is empty, it will display the default Apache template.
So we're done publicizing. Next we will install Database 
# 4. Install MariaDB
## 4.1 Similar to installing PHP, we use the following command:
```
sudo yum install -y mariadb-server
```

## 4.2 Start MariaDB server:

```
[ec2-user@ip-172-31-10-22 ~]$ sudo systemctl start mariadb
```
## 4.3 Run mysql_secure_installation.

```
[ec2-user@ip-172-31-10-22 ~]$ sudo mysql_secure_installation
Enter current password for root (enter for none): 
```
### a. By default, the password will be blank, so just press Enter
### b. Press **Y** to set the password then re-enter the password again
### c. Press **Y** to delete the anonymous account.
### d. Press **Y** to block logic with root
### e. Press **Y** to delete DB Test
### f. Press **Y** to reload and save changes

Now that MariaDB has been installed, you can access cmd to check the DB or install phpMyAdmin. Then create a db to run the project.
## 4.4 Create a new DB with a project

... 
# 5. Deploy PHP (Laravel) Project
Deploying PHP Laravel projects can be deployed manually, but I should prioritize using git for convenience and speed.
## 5.1 Install git
Still using simple cmd
```
sudo yum install -y git
```

## 5.2 Install Composer
```
cd ~
sudo curl -sS https://getcomposer.org/installer | sudo php
sudo mv composer.phar /usr/local/bin/composer
sudo ln -s /usr/local/bin/composer /usr/bin/composer
```
then run

```
sudo composer install
```

Check if composer installed successfully

```
[ec2-user@ip-172-31-10-22 ~]$ composer --version
Composer version 2.4.2 2022-09-14 16:11:15
```

5.3 Pull source and config
We access the web path (default is /var/www/html)
```
cd /var/www/html
```
Then use git cmd to clone the source project

```
git clone link_repository
```

Download the PHP libraries for the project
```
cd folder_project
composer update
```

Next you can copy the .env.example file to the .env file, then generate the key
```
cp .env.example .env
php artisan key:generate
```
You continue to configure the DB in the .env file with the information that was installed in MariaDB