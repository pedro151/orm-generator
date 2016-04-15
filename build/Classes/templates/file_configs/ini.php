;=====================READ ME!================================
;
; Default settings if not specified in any Terminal.
;
; @copyright DAO Generator-Pedro151
; @license   The MIT License (MIT)
; @link      https://github.com/pedro151/DAO-Generator
; @version   <?= $version ?><?= "\n" ?>
;=============SETTING ENVIRONMENT OF DEFAULT==================

[main]

; name framework used, which has the contents of the database configurations
; and framework template
framework = "<?= $framework ?>"<?= "\n" ?>
; configuration environment you want to generate
environment = <?= $environment ?><?= "\n" ?>
; database driver name (Ex.: pgsql)
driver = '<?= $driver ?>'<?= "\n" ?>
; database host
host = <?= $host ?><?= "\n" ?>
; database name
database = "<?= isset( $database ) ? $database : '' ?>"<?= "\n" ?>
; database schema name (one or more than one)
<?= isset( $schema ) ? 'schema = ' . $schema : ';schema = public' ?><?= "\n" ?>
; database user
username = <?= isset( $username ) ? $username : '' ?><?= "\n" ?>
; database password
password = <?= isset( $password ) ? $password : '' ?><?= "\n" ?>
; show status of implementation carried out after completing the process
status = false
; specify where to create the files (default is current directory)
path = ""
;folder with the database driver name
folder-database = 0
;.ini file the framework configuration
framework-ini = ""
;the path to the directory of the framework library
framework-path-library = ""

namespace = ''

;=============TODO============================================
; table name (parameter can be used more then once)
;tables=""
; create classes for all the scripts in the database
;all-tables = 1
;


;=====================READ ME!================================
;
; Configurations 'none'
;
;=============================================================
;[none : main]
;
;.ini file the framework configuration
;framework-ini = ""
;the path to the directory of the framework library
;framework-path-library = ""
