# ORM-Generator

[![Travis build status](https://api.travis-ci.org/pedro151/orm-generator.svg?branch=master)](https://travis-ci.org/pedro151/orm-generator)

ORM-Generator maps the entire database and then creates the DAO (Data Access Object) and ORM (Object-relational mapping) of the entire database to facilitate the development.

uses various types of databases like Postgres and Mysql and various types of ORM framework.

Install
-------

install via `composer` or download the contents of the directory `bin`.

#####Install via `composer`:
```
$ composer require pedro151/orm-generator`
$ composer install`
```

#####Download directories:

- bin\orm-generator.phar


Creating config file
--------------------
use the following command to create the configuration file needed to get started:

```cmd
$ php orm-generate.phar --init
```
OR
```cmd
$ php generator.php --init
```

Configuration
-------------

setting is in `configs/config.ini` if you do not use frameworks adapter, if you use the adapter configuration comes straight from the framework as Zend framework and others.

Configuration in Frameworks
---------------------------

in `config.ini` must put the `library` and the file `.ini` of the desired framework

```ini
framework-ini = "C:\Apache24\htdocs\project\application\configs\application.ini"
framework-path-library = "C:\Apache24\htdocs\project\library"
```

PHP Code Generation
-------------------

Open the prompt in the directory `ORM-Generator` and write:

```cmd
$ php generate.php
```

Or Open the prompt in the directory `bin` and write:

```cmd
$ php orm-generate.phar
```

Configurations optionals
------------------------
| Command        | description       |
|----------------|------------------|
| --init         | Creates the necessary configuration file to start using the ORM-Generator. |
| --config-ini   | reference to another .ini file configuration (relative path). |
| --config-env   | ORM-Generator configuration environment. |
| --framework    | name framework used, which has the contents of the database configurations and framework template. |
| --driver       | database driver name (Ex.: pgsql). |
| --database     | database name. |
| --schema       | database schema name (one or more than one). |
| --status       | show status of implementation carried out after completing the process. |
| --version      | shows the version of ORM-Generator. |
| --help         | help command explaining all the options and manner of use. |
| --path         | specify where to create the files (default is current directory). |


##### example:

in prompt

```cmd
$ php generate.php --framework=zend_framework --database=foo --driver=pgsql --status
```

Support Database 
----------------

- [x] Postgres
- [x] Mysql
- [ ] Dblib
- [ ] Mssql
- [ ] Sqlserver

Framework Class Generator ORM
-----------------------------

| Frameworks    | Config Name | Generate Classes |
|---------------|-------------|-------------|
|Zend Framework | zend_framework |DbTable, Entity, Model  |

