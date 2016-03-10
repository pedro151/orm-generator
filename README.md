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

setting is in `DAO-Generator/configs/config.ini` if you do not use frameworks adapter, if you use the adapter configuration comes straight from the framework as Zend framework and others.

Configuration in Frameworks
---------------------------

Zend Framework - `project__zend/configs/application.ini`


PHP Code Generation
-------------------

Open the prompt in the directory DAO-Generator and write:

`php generate.php`

Configurations optionals
------------------------
| Command        | description       |
|----------------|------------------|
|--help          | help command explaining all the options and manner of use |
|--database      | database name     |
|--driver        | database driver|
|--framework     | name framework used, which has the contents of the database configurations and framework template. |
|--path          | specify where to create the files (default is current directory)|

##### example:

in prompt

<code>php generate.php --framework=zend_framework --database=foo --driver=pgsql</code>

Framework Class Generator DAO
-----------------------------

| Frameworks    | Generate Classes      |
|---------------|--------------|
|Zend Framework | DbTable, Entity, Model  |


