<?php
/**
 * Created by PhpStorm.
 * User: Sylvain
 * Date: 24/11/2017
 * Time: 23:04
 */

require './conf/db_conf.php';

try {
    $db = new PDO('mysql:host='.$db_conf["host"].';dbname='.$db_conf["db_name"], $db_conf["user"], $db_conf["pass"]);

} catch (PDOException $e) {

    print "Erreur !: " . $e->getMessage() . "<br/>";
}