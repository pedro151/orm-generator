# DAO-Generator

[![Travis build status](https://api.travis-ci.org/pedro151/DAO-Generator.svg?branch=master)](https://travis-ci.org/pedro151/DAO-Generator)

DAO-Generator Creates 'Data Access Object' frameworks as DbTable the ZF1, among others.
uses various types of databases like Postgres

Install
-------

install via `composer` or download the contents of the directory `bin`.

#####Install via `composer`:
```
$ composer require pedro151/dao-generator`
$ composer install`
```

#####Download directories:

- bin\configs\config.ini
- bin\dao-generator.phar


Creating config file
--------------------
use the following command to create the configuration file needed to get started:

```cmd
$ php dao-generate.phar --init
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

Open the prompt in the directory `DAO-Generator` and write:

```cmd
$ php generate.php
```

Or Open the prompt in the directory `bin` and write:

```cmd
$ php dao-generate.phar
```

Configurations optionals
------------------------
| Command        | description       |
|----------------|------------------|
| --init         | Creates the necessary configuration file to start using the DAO-Generator. |
| --config-ini   | reference to another .ini file configuration (relative path). |
| --config-env   | DAO-Generator configuration environment. |
| --framework    | name framework used, which has the contents of the database configurations and framework template. |
| --driver       | database driver name (Ex.: pgsql). |
| --database     | database name. |
| --schema       | database schema name (one or more than one). |
| --status       | show status of implementation carried out after completing the process. |
| --version      | shows the version of DAO-Generator. |
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

Framework Class Generator DAO
-----------------------------

| Frameworks    | Generate Classes |
|---------------|--------------|
|Zend Framework | DbTable, Entity, Model  |

