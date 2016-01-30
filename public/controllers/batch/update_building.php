<?php
require_once (HOME."config/buildings.cfg.php");
echo "sfadasgghgadfhrtsabaerhra";
if ($buildingId == CAPITOL_ID)
    {
    $playerConn->deleteCapitolBuilding($playerId);
    }

$buildingName = $termConn->getTranslation ($startedBuilding['buildingClass_nameId'], $_SESSION['language']);

if ($buildingUpgradable)
    $buildingName .= "(".($buildingLevel+1).")";

$message = "Ha terminado la construcción de ".$buildingName." en ".$sectorName."(".$coordinateX.",".$coordinateY.").";
$messageConn->insertMessage(0, $playerId, "Edificio construído.", $message);

if (count($buildingManteinanceCost)>1)
    {
    $sectorConn->updateSectorCosts($sectorId, $buildingManteinanceCost, 1, '+');
    }
$buildingConn->updateBuilding(1, $buildingId, $sectorId);
$sectorConn->updateSectorProductionsByNewBuilding($buildingId, $sectorId);

if ($buildingId == CAPITOL_ID)
    {
    require (HOME."controllers/batch/recalculate_manteinances.php");
    }
?>