# DbLib
Replaces dbClass-php

DbLib is a PHP library designed to make it easier to work with MySQL/MariaDb 
databases that your project depends on.

Please note that this project is in initial development and as such, some documentation may be incomplete.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

Current development of the project is being done with PHP 7.2, however earlier versions of PHP that support the MySQL
 PDO extension should work. To check if the PDO MySQL driver is enabled, run the following command in the CLI or
  add it to a page in your web root...

```
phpinfo();
```
and ensure PDO drivers lists MySQL. If it doesn't or you cannot find any mention of PDO in phpinfo(). You may need to 
recompile PHP using:
```
./configure --with-pdo-mysql
```

### Installing

To add DbLib to your project, run:

```
composer require geeshoe/dblib
```

If you prefer to use the development branch of dblib-php, use following line of code in the composer.json file.

```
composer require geeshoe/dblib dev-develop
```

### Configure

Copy the included sample_config.ini to a secure location outside of your projects
 web root. 
 
Change the values in the mysql section to reflect your database configuration.

```
[config]
[mysql]
hostName = 127.0.0.1   //Points to the mysql server. Usually 127.0.0.1 or localhost 
port = 3306   //Typically the mysql port is 3306
dataBase = database   //The name of the database which you will be using.
userName = user   //Both the username and password for the mysql account used to manipulate the mysql database
passWord = password
```

### Documentation

API Documentation is soon to come.

### Authors

* **Jesse Rushlow** - *Lead developer* - [geeShoe Development](http://geeshoe.com)

Source available at (https://github.com/geeshoe)

For questions, comments, or rant's, drop me a line at 
```
jr (at) geeshoe (dot) com
```