Sao đây là hướng dẫn cách mà bạn sẽ tạo ra 1 con server LEMP trên AWS chạy dự án Laravel có cấu hình như sau: 
+ OS Amazon Linux: Centos 7, WebService: Apache, PHP8, DB: MariaDB

* Apache:  Là một máy chủ proxy mã nguồn mở (open source reverse proxy server) sử dụng phổ biến giao thức HTTP, HTTPS, SMTP, POP3 và IMAP. Cũng như dùng làm cân bằng tải(load balancer), HTTP casche và máy chủ web server.


Đầu tiên chúng ta cần có tài khoản AWS 


# 1. Cài đặt PHP

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

# 2. Cài đặt Apache
## 2.1 Đầu tiên là nên update hết các dependence của OS:
```
sudo yum update -y
```

## 2.2 Dùng câu lệnh dưới để tải và cài đặt apache

```
sudo yum install -y httpd
```
## 2.3 Sau đó khởi chạy Apache webserver
```
[ec2-user ~]$ sudo systemctl start httpd
```

## 2.4 Tiếp theo chúng ta sẽ cấu hình cho hệ thống khởi động Apache Webservice tự động mỗi khi hệ thống khởi động lại:
```
[ec2-user ~]$ sudo systemctl enable httpd
```
Chúng ta có thể kiểm tra trạng thái của **httpd**

```
[ec2-user@ip-172-31-10-22 ~]$ systemctl is-enabled httpd
enabled
```

## 2.5 Cấu hình cho httpd
File cấu hình mặc định của Apache sẽ nằm ở ``/etc/httpd/conf/httpd.conf``. Chúng ta hạn chế động vào file này, khi chúng ta muốn cấu hình cho một website hay nhiều website thì chúng ta nên tạo ra 1 file conf riêng trong thư mục ``/etc/httpd/conf.d/*.conf``. Việc config mình sẽ có bài viết riêng, còn hiện tại chúng ta sẽ để cấu hình mặc định.   
** Lưu ý: Mỗi lần thay đổi cấu hình thì chúng ta phải gọi lệnh restart apache để nó có thể lấy được cấu hình mới chúng ta vừa sửa hoặc thêm
```
sudo systemctl restart httpd
```

# 3. Mở port cho server 
Vậy là chúng ta đã cài đặt xong Apache, nhưng chúng ta vẫn còn 1 bước nữa để khi mở brower và nhập Public IP Address hoặc Public IP DNS có thể thấy được dịch vụ web của chúng ta.
AWS có 1 bộ phận security nó giống như tưởng lửa firewall vậy. Các bạn cần phải public port cho phép bên ngoài có thể truy cập vào máy chủ của chúng ta qua các port mà chúng ta cho phép. Nó được gọi là Sercurity Group
Vậy chúng ta cần làm gì, rất đơn giản, mỗi lần bạn tạo ra 1 instance EC2 thì nó sẽ được gán mặc định 1 cái group security. Chúng ta sẽ mở port cho chúng thôi. Cách làm như sau:  
1. Mở AWS console tại [https://console.aws.amazon.com/ec2/](https://console.aws.amazon.com/ec2/).
2. Phần instance, chọn instance mà bạn đã tạo cho web service
3. Phần **Security** tab, ở phần Inbound rules, bạn sẽ thấy mặc định như sau:  

| Port range | Protocol | Source    |
|------------|----------|-----------|
| 22         | tcp      | 0.0.0.0/0 |

Đây là inbound rules mặc định để các bạn có thể truy cập SSH vào server được. Giờ chúng ta sẽ tạo 1 một rule nữa để cho phép truy cập vào web Apache.  
Chọn vào link Security groups trên và nhấn vào edit Inbound rules, sau đó chọn như dưới và lưu lại:  
**Type**: HTTP  
**Protocol**: TCP  
**Port** Range: 80  
**Source**: Custom  

Bây giờ các bạn có thể mở trình duyệt và truy cập vào Ip Public hoặc Ip public DNS được rồi. mặc định source web sẽ được lưu ở /var/www/html. Nếu thư mục này bị trống thì nó sẽ hiển thị template mặc định của Apache. 
Vậy là chúng ta đã public xong. Tiếp theo chúng ta sẽ cài đặt Database 
# 4. Cài đặt MariaDB
## 4.1 Cũng tương tự như cài đặt PHP, ta sử dụng câu lệnh sau:
```
sudo yum install -y mariadb-server
```

## 4.2 Khởi động MariaDB server:

```
[ec2-user@ip-172-31-10-22 ~]$ sudo systemctl start mariadb
```
## 4.3 Chạy mysql_secure_installation.

```
[ec2-user@ip-172-31-10-22 ~]$ sudo mysql_secure_installation
Enter current password for root (enter for none): 
```
### a. Mặc định password sẽ là trống nên các bạn cứ nhấn Enter
### b. Nhấn **Y** để thiếp lập mật khẩu sau đó nhập lại mật khẩu lần nữa
### c. Nhấn **Y** để xóa tài khoản anonymous.
### d. Nhân **Y** để chặn logic bằng root
### e. Nhấn **Y** để xóa DB Test
### f. Nhấn **Y** để reload và lưu thay đổi

Vây là đã setup xong MariaDB, các bạn có thể truy cập cmd để check DB hoặc cài đặt phpMyAdmin. Sau đó tạo 1 db để run project.
## 4.4 Tạo DB mới chưa dự án

... 
# 5. Triển khai dự án PHP (Laravel)
Việc deploy dự án PHP Laravel có thể deploy bằng tay, nhưng mình nên ưu tiên sử dụng git để thuận tiện cũng như nhanh gọn lẹ.
## 5.1 Cài đặt git
Vẫn sử dung cmd đơn giản
```
sudo yum install -y git
```

## 5.2 Cài đặt composer
```
cd ~
sudo curl -sS https://getcomposer.org/installer | sudo php
sudo mv composer.phar /usr/local/bin/composer
sudo ln -s /usr/local/bin/composer /usr/bin/composer
```
sau đó chạy

```
sudo composer install
```

Kiểm tra composer đã cài đặt thành công chưa

```
[ec2-user@ip-172-31-10-22 ~]$ composer --version
Composer version 2.4.2 2022-09-14 16:11:15
```

5.3 Pull source and config
Chúng ta truy cập vào đường dẫn chưa web (mặc định là /var/www/html)
```
cd /var/www/html
```
Sau đó dùng git cmd để clone dự án source về

```
git clone link_repository
```

Download các thư viện của PHP cho dự án
```
cd folder_project
composer update
```

Tiếp theo các bạn có thể copy file .env.example ra file .env, rồi generate key
```
cp .env.example .env
php artisan key:generate
```
Các bạn tiếp tục cấu hình DB trong file .env với thông tin mà đã cài đặt ở MariaDB