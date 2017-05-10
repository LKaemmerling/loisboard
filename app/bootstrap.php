<?php
use Illuminate\Database\Capsule\Manager as Capsule;


require_once __DIR__.'/../vendor/autoload.php';
require_once(__DIR__ . "/../config/db.php");
/**
 * Boot Eloquent
 * @see https://github.com/laracasts/Eloquent-Outside-of-Laravel/blob/master/config/database.php
 */
$capsule = new Capsule();
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $db["host"],
    'port' => array_key_exists('port', $db) ? $db['port'] : 3306,
    'database' => $db["db"],
    'username' => $db["user"],
    'password' => $db["pw"]
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();