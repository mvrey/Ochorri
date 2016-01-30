<?php
require_once ('../../lib/inclusion.php');
require_once ('../../config/paths.php');
require_once ('../../config/map.cfg.php');
require_once ('../../config/buildings.cfg.php');


require_once_model ('Resource');
require_once_model ('Sector');
require_once_model ('Building');
require_once_model ('Battle');
require_once_model ('Unit');
require_once_model ('Division');
require_once_model ('Player');
require_once_model ('StaticData');
session_start();

$sectorConn = new SectorDAO();
$divisionConn = new DivisionDAO();

$staticData = $_SESSION['staticData'];
$player = $_SESSION['player'];
$resources = $staticData->getResources();
$coordinateX = $_POST['coordinateX'];
$coordinateY = $_POST['coordinateY'];
$allSectors = $staticData->getSectors();

$sector = $allSectors[$coordinateX.",".$coordinateY];

$sectorBuildings = $sector->getBuildings();
$haveHeadquarters = isset($sectorBuildings[COMMAND_CENTER_ID]) && ($sectorBuildings[COMMAND_CENTER_ID]->getLevel()>=1);
$haveBarracks = isset($sectorBuildings[BARRACKS_ID]) && ($sectorBuildings[BARRACKS_ID]->getLevel()>=1);

$unitQueueListArr = $sectorConn->getUnitQueueLists($coordinateX, $coordinateY, $player->getId());

$auxUnits = $unitQueueListArr[0];
$auxTimes = $unitQueueListArr[1];
$queuedUnits = explode(",", $auxUnits);
$queuedTimes = explode(",", $auxTimes);
$startTime = $unitQueueListArr[2];
$now = $_SERVER['REQUEST_TIME'];
$percent=0;

if ($queuedUnits[0]!='')
    {
    foreach ($queuedTimes as $queuedTime)
        {
        $startTime = $startTime+$queuedTime;
        if ($now<$startTime)
            break;
        }

    $startTime = $startTime-$queuedTimes[0];
    $passed = $now-$startTime;
    $percent = ($passed*100)/$queuedTimes[0];
    }


$availableUnits = array();
$speeds = array();
foreach ($player->getAvailableUnits() as $unit)
    {
    $availableUnits[] = $unit->getId();
    $speeds[] = $unit->getSpeed();
    }

echo "^_^".$percent."^_^".$auxUnits."^_^".$auxTimes."^_^".implode(",",$availableUnits)."^_^".implode(",",$speeds)."^_^";



$queuedNumbers = array();
foreach ($player->getAvailableUnits() as $unit)
    {
    $queuedNumbers[$unit->getId()] = 0;
    }

if ($unitQueueListArr)
    {
    foreach ($queuedUnits as $queuedUnit)
        {
        $queuedNumbers[$queuedUnit]++;
        }
    }

$divisionsArr = $divisionConn->getOwnDivisionsBySector($coordinateX, $coordinateY);

$divisions = array();

foreach ($divisionsArr as $divisionArr)
    {
    $division = new Division ($divisionArr[0],$divisionArr[1],$divisionArr[3],$divisionArr[4]);
    $divisions[$division->getUnitId()] = $division;
    }

$player->setVisibleSectors(Sector::indexByCoordinate($player->getVisibleSectors()));

$reachableSectors = array();
$startX = $sector->getCoordinateX();
$startY = $sector->getCoordinateY();

Sector::getReachables($player->getVisibleSectors(), $sector->getCoordinateX(), $sector->getCoordinateY(), 0);
$reachableSectors = Sector::sortByCoordinate($reachableSectors);

$player->setReachableSectors($reachableSectors);

$distances = array();
foreach ($player->getReachableSectors() as $reachableSector)
    $distances[] = $reachableSector->getDistance();

echo implode(",",$distances)."^_^";

$detailType = 'units';
require ("../../views/detailBox/unitsDetailsView.php");
?>