<?php
require_once ('../DAO/DAO.class.php');
require_once ('../class/Sector.class.php');
require ('../config/map.cfg.php');
require_once ("../test/register_functions.php");
$connection = new DAO();
$connection->connect();

$rs = $connection->getAllSectors();
$allSectors = array();

while (!$rs->EOF)
    {
    $sector = new Sector ($rs->fields[0], $rs->fields[1],$rs->fields[2],$rs->fields[3],$rs->fields[4],$rs->fields[5],$rs->fields[6],$rs->fields[7],explode(",",$rs->fields[8]),explode(",",$rs->fields[9]), $rs->fields[10]);
    array_push($allSectors, $sector);
    $rs->moveNext();
    }

$allSectors = Sector::indexByCoordinate($allSectors);

$suitableSectors = array();
$origins = array();
$minDistance = 3;

$suitableSectors = array_diff_key($allSectors, getForbidden($allSectors));

if (count($suitableSectors)>0)
    {
    $sector = $suitableSectors[array_rand($suitableSectors)];
    $rs = $connection->updateSector($sector->getId(), $playerId, $playerId, 0);
    $startCoordinates = $sector->getCoordinateX().",".$sector->getCoordinateY();

    //Create Command center and capitol in initial sector
    $now = $_SERVER['REQUEST_TIME'];
    $connection->insertBuilding(0, $sector->getId(), 0);
    $connection->updateBuilding(1, 0, $sector->getId());
    $connection->insertBuilding(1, $sector->getId(), 0);
    $connection->updateBuilding(1, 1, $sector->getId());
    $connection->updateSectorCosts ($sector->getId(), array(0,0,5,5,0), 0);

    $inserted = true;
    }
else
    $inserted = false;
?>
