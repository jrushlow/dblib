# DbLib
DbLib is a PHP library intended to make it easier to work with MySQL/MariaDb 
databases within your project.

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
* [PDO_JSON extension](http://php.net/manual/en/book.json.php) - If using the supplied 
Json configuration adapter.

To check if the above PHP extension's are enabled, run the following command in the CLI or
  add it to a page in your web root...

```
phpinfo(); <-- Use with script in webroot.
php -i <-- Use with CLI
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

Note: The development branch of DbLib is unstable at times and therefor not recommended
in a production environment.

### Configure

DbLib Configuration parameters can be set using various formats. However, JSON is the
only format currently supported out of the box. Other formats such as .env, yaml, etc.. 
are soon to follow.

It is possible to brew your own config adapter using the AbstractConfigObject in the meantime.

Copy the included dblibConfig_DIST.json to a secure location outside of your projects
 web root. 
 
Change the values to reflect your database configuration.

```
{
  "dblibConfig" : {
    "hostName" : "127.0.0.1",
    "port" : "3306",
    "userName" : "myUsername",
    "password" : "SomePassword",
    "database" : "OptionalSeeDocumentation"
  }
}
```
The ```"database"``` param is not required. If the database is not specified in
the config file, you must explicitly declare the database to use in your SQL 
statements.

I.e. ```'SELECT * FROM database.tableName';```

**PDO Attributes are not fully supported in the current release. The ability to use
attributes is provided but has not yet been implemented in DbLib automatically. This is
currently at the top of the development list to bring back PDO Attributes as before.
Full support for attributes is intended for the next major release, if not sooner.**

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