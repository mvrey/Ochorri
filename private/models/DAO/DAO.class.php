<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Database abstraction class
 *
 * @author blacloud
 */
if (isset($absolute_path) && ($absolute_path))
    require_once ($MPATH."DAO/adodb5/adodb.inc.php");
else
    require_once ("../../models/DAO/adodb5/adodb.inc.php");

abstract class DAO {

    private static $connection;

/*******************************************CONSTRUCTOR&GET&SET*******************************************/

function DAO ($connection=false) {
    if (!$connection)
        {
        $this->setConnection($connection);
        $this->connect();
        }
    else
        return ($this->getConnection());
}

public function getConnection() {
    return (DAO::$connection);
}

private function setConnection($connection) {
    DAO::$connection = $connection;
}

//Conecta a la base de datos
public function connect()
{
    require ("config.php");

    $connection = ADONewConnection($manager); # eg 'mysql' or 'postgres'
    $connection->debug = $debug;
    $connection->Pconnect($host, $user, $password, $database);
    $this->setConnection($connection);
}

}
?>