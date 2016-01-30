<?php
require_once ('../../lib/inclusion.php');
require_once_model ('Resource');
require_once_model ('Sector');
require_once_model ('Unit');
require_once_model ('Division');
require_once_model ('DivisionMovement');
require_once_model ('Player');
require_once_model ('StaticData');
require_once ('../../config/paths.php');
require_once ('../../config/map.cfg.php');
session_start();

$staticData = $_SESSION['staticData'];
$allSectors = $staticData->getSectors();
$allUnits = $staticData->getUnits();
$allPlayers = $staticData->getPlayers();

$player = $_SESSION['player'];
$resources = $player->getResources();
$coordinateX = $_POST['coordinateX'];
$coordinateY = $_POST['coordinateY'];
$msg = "";
$divisionMovementConn = new DivisionMovementDAO();

$sector = $allSectors[$coordinateX.",".$coordinateY];

$divisionMovementsArr = $divisionMovementConn->getDivisionMovement($sector->getId(), $sector->getId(), 0);

$divisionMovements = array();

foreach ($divisionMovementsArr as $divisionMovementArr)
    {
    unset($startSector);
    unset($endSector);

//Get origin and destiny sectors of current DivisionMovement
    foreach ($allSectors as $targetSector)
        {
        if (!((isset($startSector)) && (isset($endSector))))
            {
            if ($divisionMovementArr[4]==$targetSector->getId())
                $startSector = $targetSector;
            elseif ($divisionMovementArr[5]==$targetSector->getId())
                $endSector = $targetSector;
            }
        else
            break;
        }

    if (($startSector->getOccupant()==$player->getId()) || ($endSector->getOccupant()==$player->getId()))
        {
        $divisionMovement = new DivisionMovement($divisionMovementArr[0], $divisionMovementArr[3], $startSector, $endSector, $divisionMovementArr[6], $divisionMovementArr[7]);
        $unitList = explode(",", $divisionMovementArr[1]);
        $quantityList = explode(",", $divisionMovementArr[2]);
        $timeLeft = $divisionMovement->getTime()-($_SERVER['REQUEST_TIME']-$divisionMovement->getStartDateTime());

//Create divisions and assign them to current DivisionMovement
        foreach ($unitList as $i=>$unitId)
            {
            if ($quantityList[$i]>0)
                {
                $division = new Division ($divisionMovementArr[0], $divisionMovementArr[3], $unitId, $quantityList[$i], 1);
                $divisions = $divisionMovement->getDivisions();
                $divisions[] = $division;
                $divisionMovement->setDivisions($divisions);
                }
            }
        $divisionMovements[] = $divisionMovement;
        }
    }

$detailType = 'details';
require ("../../views/detailBox/detailBoxView.php");
require ("../../views/detailBox/movementDetailsView.php");
?>