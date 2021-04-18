<?php

class DB extends PDO
{
// Allows implementation of the singleton pattern -- ndg 5/24/2008
private static $instance;

// Public static variables for configuring the DB class for a particular database -- ndg 6/16/2008
public static $error_table;
public static $host_name;
public static $db_name;
public static $username;
public static $password;
public static $driver_options;
public static $db_config_path;



function __construct($dsn="", $username="", $password="", $driver_options=array())
{
if(isset(self::$db_config_path))
{
try
{
if(!require_once self::$db_config_path)
{
throw new error('Failed to require file: ' . self::$db_config_path);
}
}
catch(error $e)
{
$e->emailAdmin();
}
}
elseif(isset($_ENV['DB']))
{
self::$db_config_path = 'config.db.php';

try
{
if(!require_once self::$db_config_path)
{
throw new error('Failed to require file: ' . self::$db_config_path);
}
}
catch(error $e)
{
$e->emailAdmin();
}
}

parent::__construct("mysql:host=" . self::$host_name . ";dbname=" .self::$db_name, self::$username, self::$password, self::$driver_options);
$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('QueryStatement', array($this)));

if(!isset(self::$error_table))
{
self::$error_table = 'errorlog_rtab';
}
}

/**
* Return a DB Connection Object
*
* @return DB
*/
public static function connect()
{

// New PDO Connection to be used in NEW development and MAINTENANCE development
try
{
if(!isset(self::$instance))
{
if(!self::$instance =  new DB())
{
throw new error('PDO DB Connection failed with error: ' . self::errorInfo());
}
}

return self::$instance;
}
catch(error $e)
{
$e->printErrMsg();
}
}

/**
* Returns a QueryBuilder object which can be used to build dynamic queries
*
* @return QueryBuilder
*
*/
public function createQuery()
{
return new QueryBuilder();
}

public function executeStatement($statement, $params = null, $FETCH_MODE = null)
{
if($FETCH_MODE == 'scalar')
{
return $this->executeScalar($statement, $params);
}


try {
try {
if(!empty($params))
{
$stmt = $this->prepare($statement);
$stmt->execute($params);
}
else
{
$stmt = $this->query($statement);
}
}
catch(PDOException $pdo_error)
{
throw new error("Failed to execute query:\n" . $statement . "\nUsing Parameters:\n" . print_r($params, true) . "\nWith Error:\n" . $pdo_error->getMessage());
}
}
catch(error $e)
{
$this->logDBError($e);
$e->emailAdmin();
return false;
}

try
{
if($FETCH_MODE == 'all')
{
$tmp =  $stmt->fetchAll();
}
elseif($FETCH_MODE == 'column')
{
$arr = $stmt->fetchAll();

foreach($arr as $key => $val)
{
foreach($val as $var => $value)
{
$tmp[] = $value;
}
}
}
elseif($FETCH_MODE == 'row')
{
$tmp =  $stmt->fetch();
}
elseif(empty($FETCH_MODE))
{
return true;
}
}
catch(PDOException $pdoError)
{
return true;
}

$stmt->closeCursor();

return $tmp;

}

public function executeScalar($statement, $params = null)
{
$stmt = $this->prepare($statement);

if(!empty($this->bound_params) && empty($params))
{
$params = $this->bound_params;
}

try {
try {
if(!empty($params))
{
$stmt->execute($params);
}
else
{
$stmt = $this->query($statement);
}
}
catch(PDOException $pdo_error)
{
throw new error("Failed to execute query:\n" . $statement . "\nUsing Parameters:\n" . print_r($params, true) . "\nWith Error:\n" . $pdo_error->getMessage());
}
}
catch(error $e)
{
$this->logDBError($e);
$e->emailAdmin();
}

$count = $stmt->fetchColumn();

$stmt->closeCursor();

//echo $count;
return $count;
}

protected function logDBError($e)
{
$error = $e->getErrorReport();

$sql = "
INSERT INTO " . self::$error_table . " (message, time_date)
VALUES (:error, NOW())";

$this->executeStatement($sql, array(':error' => $error));
}
}

class QueryStatement extends PDOStatement
{
public $conn;

protected function __construct()
{
$this->conn = DB::connect();
$this->setFetchMode(PDO::FETCH_ASSOC);
}

public function execute($bound_params = null)
{
return parent::execute($bound_params);
}
}
