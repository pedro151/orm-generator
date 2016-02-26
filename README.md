# DAO-Generator

![alt tag](https://api.travis-ci.org/pedro151/DAO-Generator.svg?branch=master)

DAOGenerator Creates 'Data Access Object' frameworks as DbTable the ZF1, among others.
uses various types of databases like Postgres

PHP Code Generation
-------------------

Open the prompt in the directory DAO-Generator and write:

<code>php DAO-generator.php</code>

Configurations optionals
------------------------
| Command        | description       |
|----------------|------------------|
|--help          | help command explaining all the options and manner of use |
|--database      | database name     |
|--driver        | database driver|
|--framework     | framework template|
|--path          | specify where to create the files (default is current directory)|

##### example:

in prompt

<code>php DAO-generator.php --framework=zend_framework --database=foo --driver=pgsql</code>
