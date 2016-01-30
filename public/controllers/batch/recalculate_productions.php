<?php
//REINICIAR PRODUCCIONES
$batchConn->resetSectorProductions($playerId);

$productionModsArr = $batchConn->getProductionMods($playerId);

foreach ($productionModsArr as $productionModArr)
    {
    $sectorId = $productionModArr["sector_id"];
    $buildingId = $productionModArr["buildingClass_id"];
    $level = $productionModArr["building_level"];

    for ($i=0; $i<$level; $i++)
        $sectorConn->updateSectorProductionsByNewBuilding($buildingId, $sectorId);
    }

?>