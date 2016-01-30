<?php
require_once ('../../lib/inclusion.php');
require_once('../../config/units.cfg.php');
require_once_model('Player');
require_once_model('Unit');
require_once_model('Division');
require_once_model('DivisionMovement');
require_once_model('Sector');
session_start();

$divisionConn = new DivisionDAO();
$divisionMovementConn = new DivisionMovementDAO();

$player = $_SESSION['player'];
$unitList = explode(",", $_POST['unitList']);
$quantityList = explode(",", $_POST['quantityList']);
$startX = $_POST['startX'];
$startY = $_POST['startY'];
$endX = $_POST['endX'];
$endY = $_POST['endY'];
$speed = $_POST['speed'];

$startOK = false;
$endOK = false;
$divisionsOK = false;
$speedOK = false;

$playerSectors= Array();
foreach ($player->getSectors() as $playerSector)
    {
    if (($playerSector->getCoordinateX()==$startX) && ($playerSector->getCoordinateY()==$startY))
        {
        $startOK = true;
        $startId = $playerSector->getId();
        }
    }
$endOK = array_key_exists($endX.",".$endY, $player->getReachableSectors());


if (($startOK) && ($endOK))
    {
    $reachableSectors = $player->getReachableSectors();
    $endSector = $reachableSectors[$endX.",".$endY];
    $endId = $endSector->getId();

    $divisionOK = $divisionConn->getDivisionsExists($player->getId(), $startId, $unitList, $quantityList);

    $availableUnits = $player->getAvailableUnits();
    $minSpeed=9999;
    $i=0;
    foreach ($unitList as $unitId)
        {
        $unit = $availableUnits[$unitId];
        if (($unit->getSpeed()<$minSpeed) && ($quantityList[$i]>0))
            $minSpeed = $unit->getSpeed();
        $i++;
        }

    $speedOK = ($speed == $minSpeed);
    }

if (($startOK) && ($endOK) && ($divisionOK) && ($speedOK))
    {
    for ($i=0; $i<count($unitList); $i++)
        $divisionConn->updateDivision ($player->getId(), $startId, $unitList[$i], $quantityList[$i], $operation='-');

    $time = ($endSector->getDistance()*$distanceMultiplier)/$speed;
    $divisionMovementConn->insertDivisionMovement(implode(",",$unitList), implode(",",$quantityList), $player->getId(), $startId, $endId, $_SERVER['REQUEST_TIME'], $time);

    echo implode(",", $unitList)."^_^".implode(",", $quantityList);
    }
?>
