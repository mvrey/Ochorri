<?php
require_once ('../../lib/inclusion.php');
require_once ('../../config/paths.php');
require ('../../config/map.cfg.php');
require_once_model ('StaticData');
require_once_model ('Sector');
require_once_model ('Building');
require_once_model ('Technology');
require_once_model ('Battle');
require_once_model ('Player');
require_once_model ('Message');
require_once_model ('Resource');
session_start();

$staticData = $_SESSION['staticData'];
$allPlayers = $staticData->getPlayers();
$allBuildings = $staticData->getBuildings();

$sessionPlayer = $_SESSION['player'];

$playerConn = new PlayerDAO();
$sectorConn = new SectorDAO();

$newMessages = false;
$messagesArr = $playerConn->getMessages($sessionPlayer->getId(), true, 0);
if (count($messagesArr))
    $newMessages = true;

if (($_POST['height'] != 'undefined') && ($_POST['width'] != 'undefined'))
    {
    $height = $_POST['height'];
    $width = $_POST['width'];
    }
else
    {
    $lastMapViewArr = $playerConn->getLastMapView($sessionPlayer->getId());
    $coordinates = explode(",", $lastMapViewArr[0]);
    $originX = $coordinates[0];
    $originY = $coordinates[1];
    $height = $lastMapViewArr[1];
    $width = $height*2;
    }

$ratio = $MAP_HEIGHT/$height;
$MAP_HEIGHT = $height;
$MAP_WIDTH = $width;
$HEX_SCALED_HEIGHT = $HEX_HEIGHT * $ratio;
$HEX_SIDE = $HEX_SCALED_HEIGHT / 2;


if (isset($_POST['originX']) && isset($_POST['originY']))
    {
    $originX = $_POST['originX'];
    $originY = $_POST['originY'];
    }

if ($originX%2>0)
    {
    $originX++;
    $originY++;
    }
    
while (!(($MAP_WIDTH+$originX<=$MAX_WIDTH) && ($MAP_HEIGHT+$originY<=$MAX_HEIGHT)))
    {
    if ($originX>=2)
        $originX -= 2;
    if ($originY>=1)
        $originY -= 1;
    }

$playerConn->setLastMapView ($sessionPlayer->getId(), $originX.",".$originY, $MAP_HEIGHT);

/* Iniciamos los datos de los sectores con los nicks de ocupante y propietario */
//Con lo siguiente tenemos el problema del cálculo de recursos al recargar
//$rs = $connection->getSectors(0, $MAP_WIDTH-1, 0, $MAP_HEIGHT-1);
$sectorsArr = $sectorConn->getAllSectors();
$allSectors = array();
$visibleSectors = array();
$owned_sectors = array();
unset($_SESSION['capitolSector']);

foreach ($sectorsArr as $sectorArr)
    {
    if (isset($allPlayers[$sectorArr[4]]))
        $occupantId = $allPlayers[$sectorArr[4]]->getNick();
    else
        $occupantId = NULL;
    if (isset($allPlayers[$sectorArr[5]]))
        $ownerId = $allPlayers[$sectorArr[5]]->getNick();
    else
        $ownerId = NULL;

    $sector = new Sector ($sectorArr[0], $sectorArr[1],$sectorArr[2],$sectorArr[3],$sectorArr[4],$sectorArr[5],$sectorArr[6],$sectorArr[7],explode(",",$sectorArr[8]),explode(",",$sectorArr[9]), $sectorArr[10], explode(",",$sectorArr[11]));

    $battleArr = $sectorConn->getBattleBySectorId($sector->getId());
    if (count($battleArr))
        {
        $battle = new Battle($battleArr[0], 0, 0, $battleArr[2], $battleArr[3], $battleArr[4], $battleArr[5]);
        $sector->setBattle($battle);
        }
    
    $sectorBuildingsArr = $sectorConn->getSectorBuildings($sector->getId());
    $sectorBuildings = array();

    foreach ($sectorBuildingsArr as $sectorBuildingArr)
        {
        $buildingClass = $sectorBuildingArr[1];
        $building = clone $allBuildings[$buildingClass];

        $building->setLevel($sectorBuildingArr[3]);
        $building->setDateStarted($sectorBuildingArr[4]);
        $building->setDateStopped($sectorBuildingArr[5]);

        $sectorBuildings[$building->getId()] = $building;
        }

    $sector->setBuildings($sectorBuildings);

    $isCapitol = isset($sectorBuildings[0]) && $sectorBuildings[0]->getLevel();
    if ($isCapitol)
        {
        $sector->setIsCapitol(true);
        if ($sector->getOwner()==$sessionPlayer->getId())
            $_SESSION['capitolSector'] = $sector;
        }

    if ($sectorArr[5]==$sessionPlayer->getId())
        array_push($owned_sectors, $sector);
    $allSectors[$sector->getCoordinateX().",".$sector->getCoordinateY()] = $sector;

    $x = $sector->getCoordinateX();
    $y = $sector->getCoordinateY();

    if (($x>=0) && ($x<=($MAP_WIDTH-1+$originX))
    && ($y>=0) && ($y<=($MAP_HEIGHT-1+$originY))
    && ($x>=$originX) && ($y>=$originY))
        array_push($visibleSectors, $sector);
    }
    
$sessionPlayer->setSectors($owned_sectors);
$sessionPlayer->setVisibleSectors($visibleSectors);
$staticData->setSectors($allSectors);



//Productions and expenses refresh from this point
$productions = Array(0,0,0,0,0);
$spends = Array(0,0,0,0,0);
$balances = Array();
$totalBalances = Array(0,0,0,0,0);
$now = $_SERVER['REQUEST_TIME'];
$availableResources = $sessionPlayer->getAvailableResources();

foreach ($owned_sectors as $ownedSector)
    {
    $sectorProductions = $ownedSector->getProductions();
    $sectorSpends = $ownedSector->getSpends();

    for ($i=0; $i<count($sectorProductions); $i++)
        {
        if (isset($availableResources[$i+1]))
            {
            $productions[$i] = $productions[$i]+$sectorProductions[$i];
            $spends[$i] = $spends[$i]+$sectorSpends[$i];
            }
        }
    $playerResources = $sessionPlayer->getResources();

    for ($i=0; $i<count($productions); $i++)
        {
        if (isset($availableResources[$i+1]))
            {
            $balances[$i] = $sectorProductions[$i]-$sectorSpends[$i];
            $totalBalances[$i] += $balances[$i];
            $playerResources[$i] = sprintf ($playerResources[$i]+(($balances[$i]/3600)*($now-$sessionPlayer->getLastUpdate())));
            }
        }
    $sessionPlayer->setBalances($totalBalances);
    $sessionPlayer->setResources($playerResources);
    }


$battleCostsArr = $playerConn->getBattleCosts ($sessionPlayer->getId());

foreach ($battleCostsArr as $battleCostArr)
    {
    $battleCosts = explode(",", $battleCostArr[3]);
    $playerResources = $sessionPlayer->getResources();

    for ($i=0; $i<count($battleCosts); $i++)
        {
        if (isset($availableResources[$i+1]))
            {
            $playerResources[$i] = sprintf ($playerResources[$i]-(($battleCosts[$i]/3600)*($now-$sessionPlayer->getLastUpdate())));
            $totalBalances[$i] -= $battleCosts[$i];
            }
        }

    $sessionPlayer->setBalances($totalBalances);
    $sessionPlayer->setResources($playerResources);
    }
    
$playerConn->updatePlayerResources ($sessionPlayer->getId(), implode(",",$playerResources), $now);

$sessionPlayer->setResources($playerResources);
$sessionPlayer->setLastUpdate($now);

$_SESSION['player'] = $sessionPlayer;

echo "^_^".$originX."^_^".$originY."^_^".implode(",", $sessionPlayer->getBalances())."^_^".implode(",", $sessionPlayer->getResources())."^_^";
echo (int)$newMessages."^_^";

require ("../../views/map/mapView.php");
?>