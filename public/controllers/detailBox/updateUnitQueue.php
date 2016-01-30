<?php
require_once ('../../lib/inclusion.php');
require_once('../../config/units.cfg.php');
require_once_model('Player');
require_once_model('Unit');
require_once_model('Sector');
session_start();

$sectorConn = new SectorDAO();
$playerConn = new PlayerDAO();

$player = $_SESSION['player'];
$action = $_POST['action'];
$unitId = $_POST['unitId'];
$coordinateX = $_POST['coordinateX'];
$coordinateY = $_POST['coordinateY'];
$quantity = $_POST['quantity'];

$playerSectors= Array();
foreach ($player->getSectors() as $playerSector)
    {
    $sectorC = $playerSector->getCoordinateX().",".$playerSector->getCoordinateY();
    $playerSectors[] = $sectorC;
    }

$sectorOK = in_array($coordinateX.",".$coordinateY, $playerSectors);
$unitOK = array_key_exists($unitId, $player->getAvailableUnits());
$resourcesOK = true;

$available_units = $player->getAvailableUnits();
$unit_resources = $available_units[$unitId]->getProductionCost();
$player_resources = $player->getResources();
$leftResources = array();
for ($i=0; $i<count($unit_resources); $i++)
    {
    $leftResources[$i] = $player_resources[$i]-($unit_resources[$i]*$quantity);
    if ($leftResources[$i]<0 && $unit_resources[$i]>0)
        $resourcesOK = false;
    }

if (!$resourcesOK)
    {
    die("0;");
    }

if (($sectorOK) && ($unitOK) && ($resourcesOK))
    {
    $unitQueueListArr = $sectorConn->getUnitQueueLists($coordinateX, $coordinateY, $player->getId());

    $sectorQuantity = 0;

    if ($unitQueueListArr)
        $sectorQuantity += count(explode(",", $unitQueueListArr[0]));

    
    if ($sectorQuantity+$quantity>$trainingQueueLenght)
        die("1;");

    $unitList = $unitId;
    $timeList = $available_units[$unitId]->getTime();
    for ($k=1; $k<$quantity; $k++)
        {
        $unitList .= ",".$unitId;
        $timeList .= ",".$available_units[$unitId]->getTime();
        }

    if ($unitQueueListArr)
        {
        $unitList = $unitQueueListArr[0].",".$unitList;
        $timeList = $unitQueueListArr[1].",".$timeList;
        $sectorConn->updateUnitQueue($coordinateX, $coordinateY, $player->getId(), $unitList, $timeList);
        }
    else
        {
        $sectorConn->insertUnitQueue($coordinateX,$coordinateY,$player->getId(),$unitList,$timeList,$_SERVER['REQUEST_TIME']);
        }

    $playerConn->updatePlayerResources ($player->getId(), implode(",",$leftResources), $_SERVER['REQUEST_TIME']);
    $_SESSION['player']->setResources($leftResources);

    echo "2;".$unitId.";".$available_units[$unitId]->getTime().";".implode(",",$unit_resources);
    }
?>
