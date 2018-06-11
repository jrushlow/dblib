# DbLib
DbLib is a PHP library designed to make it easier to work with MySQL/MariaDb 
databases that your project depends on.

Please note that this project is in initial development and as such, some documentation may be incomplete.

## Getting Started

DbLib is intended to be fully compliant with 
[PSR-1](https://www.php-fig.org/psr/psr-1/),
[PSR-2](https://www.php-fig.org/psr/psr-2/),
 & [PSR-4](https://www.php-fig.org/psr/psr-4/)

### Prerequisites

DbLib works with both MySQL and MariaDb.

* PHP 7.1+
* [PDO_MYSQL extension](http://php.net/manual/en/ref.pdo-mysql.php)

To check if the PDO MySQL driver is enabled, run the following command in the CLI or
  add it to a page in your web root...

```
phpinfo(); <-- Use with script in webroot.
php -i <-- Use with CLI
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

DbLib Configuration parameters are set using the Json format.

Copy the included dblibConfig_DIST.json to a secure location outside of your projects
 web root. 
 
Change the values to reflect your database configuration.

```
{
  "dblibConfig" : {
    "hostName" : "127.0.0.1",
    "port" : "3306",
    "username" : "myUsername",
    "password" : "SomePassword",
    "database" : "OptionalSeeDocumentation",
    "pdoAttributes" : [
      {
        "PDO::ATTR_ERRMODE" : "PDO::ERRMODE_EXCEPTION"
      }
    ]
  }
}
```
The ```"database"``` & ```"pdoAttributes"``` param's are not required. If the database
is not specified in the config file, you must explicitly declare the database to 
use in your SQL statements.

I.e. ```'SELECT * FROM database.tableName';```

[PDO Attributes](http://php.net/manual/en/pdo.setattribute.php) can be set in the config file
as demonstrated above. More than one attribute can be set as follows:

```
"pdoAttributes" : [
     {
       "PDO::ATTR_ERRMODE" : "PDO::ERRMODE_EXCEPTION"
     },
     {
       "PDO::ATTR_CASE" : "PDO::CASE_LOWER"
     }
]
```

[Persistent Connections](http://php.net/manual/en/pdo.connections.php) are planned in the future
for DbLib, but currently are not supported. Because DbLib does not explicitly close PDO connections,
it is possible to extend the DbLib class and override the ```connect()```method to set
```PDO::ATTR_PERSISTENT => true```.

### Documentation

API & usage documentation is soon to come.

### Authors

* **Jesse Rushlow** - *Lead developer* - [geeShoe Development](http://geeshoe.com)

Source available at (https://github.com/geeshoe)

For questions, comments, or rant's, drop me a line at 
```
jr (at) geeshoe (dot) com
```