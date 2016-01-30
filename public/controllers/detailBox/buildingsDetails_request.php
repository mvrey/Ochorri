<?php
require_once ('../../lib/inclusion.php');
require_once ('../../config/paths.php');
require_once ('../../config/buildings.cfg.php');
require_once_model ('StaticData');
require_once_model ('Resource');
require_once_model ('Sector');
require_once_model ('Building');
require_once_model ('Player');
session_start();

$player = $_SESSION['player'];
$staticData = $_SESSION['staticData'];
$allSectors = $staticData->getSectors();
$allBuildings = $staticData->getBuildings();

$resources = $staticData->getResources();

if (isset($_SESSION['capitolSector']))
    $capitolSector = $_SESSION['capitolSector'];
$coordinateX = $_POST['coordinateX'];
$coordinateY = $_POST['coordinateY'];

$sector = $allSectors[$coordinateX.",".$coordinateY];
$sectorConn = new SectorDAO();

//NEW SYSTEM STARTPOINT
/*$buildings = $staticData->getBuildings();
foreach ($sector->getBuildings() as $building)
        $buildings[$building->getId()] = $building;

$activeBuildingId = -1;
$activeBuildingTime = 0;
$percentList = array();*/
//NEW SYSTEM END


$availableBuildingsArr = $sectorConn->getAvailableBuildings($sector->getId());

$buildings = array();
$startTime = 0;
$now = $_SERVER['REQUEST_TIME'];
$percentList = array();
$activeBuildingId = -1;
$activeBuildingTime = 0;

foreach ($availableBuildingsArr as $availableBuildingArr)
    {
/*
    $buildingName = $connection->getTranslation($rs->fields[1],$_SESSION['language']);
    $buildingDescription = $connection->getTranslation($rs->fields[13],$_SESSION['language']);
    $building = new Building ($rs->fields[0], $buildingName, $rs->fields[2],$rs->fields[3],$rs->fields[4],$rs->fields[5],$rs->fields[6],explode(",",$rs->fields[7]),explode(",",$rs->fields[8]),explode(",",$rs->fields[9]),explode(",",$rs->fields[10]),$rs->fields[11],$rs->fields[12],$buildingDescription,$rs->fields[14], $rs->fields[15], $rs->fields[16], $rs->fields[17], $rs->fields[18]);
*/
    $building = clone $allBuildings[$availableBuildingArr[0]];

    $building->setLevel($availableBuildingArr[16]);
    $startTime = $availableBuildingArr[17];
    $stopTime = $availableBuildingArr[18];
    
    $building->setDateStarted($startTime);
    $building->setDateStopped($stopTime);
    $percent = 0;
    $building->updateTime();

    if (!empty($startTime))
        {
        if ($stopTime==NULL)
            {
            $passed = $now-$startTime;
            $activeBuildingId = $building->getId();
            $activeBuildingTime = $building->getTime();
            }
        else
            $passed = $stopTime-$startTime;

        $percent = (($passed*100)/$building->getTime());
        }

    $building->setTime($availableBuildingArr[11]);
    $building->updateTime();

    if ($building->getLevel()>0)
        $building->updateResourceIncrements();

    $building->setPercent($percent);
    $percentList[] = $percent;

    $buildings[$building->getId()] = $building;
    }

/* If there is no command center show only the first building on list */
$command_center = $buildings[COMMAND_CENTER_ID];
if ($command_center->getLevel()<1)
    $buildings = array($command_center->getId()=>$command_center);
/************/

$player->setAvailableBuildings($buildings);

echo "^_^".$activeBuildingId."^_^".$activeBuildingTime."^_^".implode(",",$percentList)."^_^".$sector->getId()."^_^";

$detailType = 'buildings';
require ("../../views/detailBox/detailBoxView.php");
require ('../../views/detailBox/buildingDetailsView.php');
?>