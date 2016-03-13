# DAO-Generator

[![Travis build status](https://api.travis-ci.org/pedro151/DAO-Generator.svg?branch=master)](https://travis-ci.org/pedro151/DAO-Generator)

DAOGenerator Creates 'Data Access Object' frameworks as DbTable the ZF1, among others.
uses various types of databases like Postgres

Install
-------

`composer require pedro151/dao-generator`

`composer install`

Configuration
-------------

setting is in `configs/config.ini` if you do not use frameworks adapter, if you use the adapter configuration comes straight from the framework as Zend framework and others.

Configuration in Frameworks
---------------------------

in `config.ini` must put the `library` and the file `.ini` of the desired framework

`framework-ini = "C:\Apache24\htdocs\project\application\configs\application.ini"`

`framework-path-library = "C:\Apache24\htdocs\project\library"`


PHP Code Generation
-------------------

Open the prompt in the directory DAO-Generator and write:

`php generate.php`

Configurations optionals
------------------------
| Command        | description       |
|----------------|------------------|
|--help          | help command explaining all the options and manner of use |
|--config-ini    | reference to another .ini file configuration (relative path) |
|--status        | show status of implementation carried out after completing the process |
|--database      | database name     |
|--schema        | database schema name (one or more than one)    |
|--driver        | database driver name (Ex.: pgsql)|
|--framework     | name framework used, which has the contents of the database configurations and framework template. |
|--path          | specify where to create the files (default is current directory)|

##### example:

in prompt

<code>php generate.php --framework=zend_framework --database=foo --driver=pgsql --status=1</code>

Framework Class Generator DAO
-----------------------------

| Frameworks    | Generate Classes      |
|---------------|--------------|
|Zend Framework | DbTable, Entity, Model  |


