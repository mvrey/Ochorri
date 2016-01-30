<?php

if (!isset($_SESSION['nick']))
    header("Location: ../index.php");
else
    $nick = $_SESSION['nick'];

$playerConn = new PlayerDAO();

$staticData = $_SESSION['staticData'];
$allPlayers = $staticData->getPlayers();
$allResources = $staticData->getResources();
$allUnits = $staticData->getUnits();
$allTechnologies = $staticData->getTechnologies();

/* Iniciamos los datos del jugador que ha iniciado sesión */
$playerArr = $playerConn->getPlayerByNick($nick);
$player = $allPlayers[$playerArr[0]];

//Set player as logged in
//$rs = $connection->setPlayerLogged($player->getId(), true);

$availableResourcesArr = $playerConn->getAvailableResources($player->getAge());
foreach ($availableResourcesArr as $availableResourceArr)
    {
    $availableResources[$availableResourceArr[0]] = $allResources[$availableResourceArr[0]];
    }
$player->setAvailableResources($availableResources);


$availableUnitsArr = $playerConn->getAvailableUnits($player);
foreach ($availableUnitsArr as $availableUnitArr)
    {
    $availableUnits[$availableUnitArr[0]] = $allUnits[$availableUnitArr[0]];
    }
$player->setAvailableUnits($availableUnits);


$availableTechnologiesArr = $playerConn->getAvailableTechnologies($player->getId(), $player->getAge());
foreach ($availableTechnologiesArr as $availableTechnologyArr)
    {
    $technology = clone $allTechnologies[$availableTechnologyArr[0]];
    $technology->setLevel($availableTechnologyArr[13]);
    $technology->setProgress($availableTechnologyArr[14]);
    $technology->setDateStartProgress($availableTechnologyArr[15]);
    $technology->setDateEndProgress($availableTechnologyArr[16]);
    $availableTechnologies[$availableTechnologyArr[0]] = $technology;
    }
$player->setAvailableTechnologies($availableTechnologies);


$lastMapViewArr = $playerConn->getLastMapView ($player->getId());
$player->setLastMapOrigin(explode(",", $lastMapViewArr[0]));
$player->setLastMapHeight($lastMapViewArr[1]);

$_SESSION['player'] = $player;
?>