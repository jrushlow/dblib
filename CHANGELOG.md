## DbLib ChangeLog
*DbLib follows [Semantic Versioning 2.0.0](https://semver.org/)*

### v0.4.0
*Released - 2018-06-11*

* Config settings are now in the Json format.
* Added ability to set PDO Attributes in the config file.
* Exceptions are thrown if config file is malformed.
* All methods are now protected or public for easier extendability.
* Added additional Unit and Functional Tests.
* Updated composer development dependencies for testing.
* Updated README.md with new config file specifications.

### v0.3.0
*Released - 2018-05-19*

* PHP 7.1+ is now required.
* All methods now have set scalar types.
* Added missing DocBlock Comments.
* Minor refactoring to comply with PSR-2.
* Removed empty / unused deleteData method.
* Updated composer.json
* Updated README.md

### v0.2.0
*Released - 2018-05-09*

Fixed type error thrown when strict_types=1 is set

### v0.1.0
*Released - 2018-04-26*

Initial versioned release of DbLib