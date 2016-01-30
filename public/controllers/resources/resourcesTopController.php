<?php
$player = $_SESSION['player'];

//$playerConn = new PlayerDAO();

//En el init_player, meter los recursos disponibles
//$rs = $playerConn->getAvailableResources($player->getAge());
//var_dump($player);
$resources = array();

while (!$rs->EOF)
    {
    $resourceName = $connection->getTranslation($rs->fields[1],$_SESSION['language']);
    $resource = new Resource ($rs->fields[0], $resourceName, $rs->fields[2],$rs->fields[3],$rs->fields[4],$rs->fields[5]);
    array_push($resources,$resource);
    $rs->moveNext();
    }

$_SESSION["resources"] = $resources;
?>