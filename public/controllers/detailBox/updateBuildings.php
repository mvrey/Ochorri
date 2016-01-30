<?php
require_once('../../lib/inclusion.php');
require_once_model('Player');
require_once_model('Building');
require_once_model('Sector');
session_start();

$sectorConn = new SectorDAO();
$buildingConn = new BuildingDAO();
$playerConn = new PlayerDAO();

$player = $_SESSION['player'];
$action = $_POST['action'];
$buildingId = $_POST['buildingId'];
$sectorId = $_POST['sectorId'];
$pausing = $_POST['pausing'];

$playerSectors= Array();
foreach ($player->getSectors() as $playerSector)
    {
    $sector_id = $playerSector->getId();
    $playerSectors[] = $sector_id;
    if ($sector_id == $sectorId)
        {
        $coordinateX = $playerSector->getCoordinateX();
        $coordinateY = $playerSector->getCoordinateY();
        }
    }

$sectorOK = in_array($sectorId, $playerSectors);
$buildingOK = array_key_exists($buildingId, $player->getAvailableBuildings());

if (($pausing) && ($sectorOK) && ($buildingOK))
    {
    $success = $buildingConn->pauseBuilding($sectorId, $buildingId);
    //if ($success)
        echo "1;".$coordinateX.";".$coordinateY;
    die();
    }


$available_buildings = $player->getAvailableBuildings();
$sectorBuildingsArr = $sectorConn->getSectorBuildings($sectorId);
$percent = 0;
$sectorBuildings = array();
foreach ($sectorBuildingsArr as $sectorBuildingArr)
    {
    $startTime = $sectorBuildingArr[4];
    $stopTime = $sectorBuildingArr[5];
    if (($startTime!=null) && ($stopTime==null))
        exit("No caeré en un error tan evidente. Script abortado.");

    if ($sectorBuildingArr[1]==$buildingId)
        {
        if (($startTime!=null) && ($stopTime!=null))
            {
            $passed = $stopTime-$startTime;
            $percent = ($passed*100)/$available_buildings[$buildingId]->getTime();
            }
        else
            $percent = 0;
        }

    $sectorBuildings[] = $sectorBuildingArr[1];
    }

$resourcesOK = true;

if (!$percent)
    {
    $building_resources = $available_buildings[$buildingId]->getProductionCost();
    $player_resources = $player->getResources();
    $leftResources = array();
    for ($i=0; $i<count($building_resources); $i++)
        {
        $leftResources[$i] = $player_resources[$i]-$building_resources[$i];
        if ($leftResources[$i]<0 && $building_resources[$i]>0)
            $resourcesOK = false;
        }
    }

if (!$resourcesOK)
    die("0;");

if (($sectorOK) && ($buildingOK) && ($resourcesOK))
    {
    if (in_array($buildingId, $sectorBuildings))
        $buildingConn->updateBuilding (0, $buildingId, $sectorId);
    else
        $buildingConn->insertBuilding ($buildingId,$sectorId,$_SERVER['REQUEST_TIME']);
    //$unitList = $unitId;
    //$timeList = $available_buildings[$buildingId]->getTime();

    if (!$percent)
        {
        $playerConn->updatePlayerResources ($player->getId(), implode(",",$leftResources), $_SERVER['REQUEST_TIME']);
        $_SESSION['player']->setResources($leftResources);
        }
        
    echo $buildingId.";".$available_buildings[$buildingId]->getTime().";".implode(",",$building_resources).";".$percent;
    }
?>
