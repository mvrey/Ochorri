<?php
require_once ('../../../public/lib/inclusion.php');
require_once ('../../../public/config/paths.php');
require ('../../../public/config/map.cfg.php');
require_once_model ('Sector', MODEL_ROUTE);
require_once_model ('Building', MODEL_ROUTE);

if (!isset($sectorConn))
    $sectorConn = new SectorDAO();
if (!isset($buildingConn))
    $buildingConn = new BuildingDAO();

$sectorsArr = $sectorConn->getAllSectors();

foreach ($sectorsArr as $sectorArr)
    {
    $sector = new Sector ($sectorArr[0], $sectorArr[1],$sectorArr[2],$sectorArr[3],$sectorArr[4],$sectorArr[5],$sectorArr[6],$sectorArr[7],explode(",",$sectorArr[8]),explode(",",$sectorArr[9]), $sectorArr[10]);
    $allSectors[] = $sector;
    }

$allSectors = Sector::indexByCoordinate($allSectors);

$suitableSectors = array();
$origins = array();
$minDistance = 3;

$suitableSectors = array_diff_key($allSectors, Sector::getForbidden($allSectors));

if (count($suitableSectors)>0)
    {
    $sector = $suitableSectors[array_rand($suitableSectors)];
    $rs = $sectorConn->updateSector($sector->getId(), $playerId, $playerId, 0);
    $startCoordinates = $sector->getCoordinateX().",".$sector->getCoordinateY();

    //Create Command center and capitol in initial sector
    $now = $_SERVER['REQUEST_TIME'];
    $sectorConn->insertBuilding(0, $sector->getId(), 0);
    $buildingConn->updateBuilding(1, 0, $sector->getId());
    $sectorConn->insertBuilding(1, $sector->getId(), 0);
    $buildingConn->updateBuilding(1, 1, $sector->getId());
    $sectorConn->updateSectorCosts ($sector->getId(), array(0,0,5,5,0), 0);

    $inserted = true;
    }
else
    $inserted = false;
?>
