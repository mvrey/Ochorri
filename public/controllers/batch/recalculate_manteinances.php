<?php
//Actualizar tanto los mantenimientos del sector como los BattleCosts
/* FALTA ACTUALIZAR LOS BATTLECOST
 */
//Ponemos los costes de todos los sectores a 0
$batchConn->resetSectorCosts($playerId);
//Idem para los battleCosts
$batchConn->resetBattleCosts($playerId);


//Extraemos los mantenimientos de unidades y edificios
$manteinancesArr = $batchConn->getManteinances($playerId);

$capitolSectorArr = $sectorConn->getCapitolSector($playerId);
if ($capitolSectorArr)
    {
    $capitolSectorX = $capitolSectorArr[1];
    $capitolSectorY = $capitolSectorArr[2];
    }
else
    $distanceFromCapitol = $maxDistance;

foreach ($manteinancesArr as $manteinanceArr)
    {
    $sectorId = $manteinanceArr["sector_id"];
    $costs = explode(",", $manteinanceArr["manteinanceCost"]);
    $multiplier = $manteinanceArr["multiplier"];
    $concept = $manteinanceArr["concept"];
    $battleId = $manteinanceArr["battle_id"];

    if (($concept=='Division') || ($concept=='BattleCosts'))
        {
        $sectorArr = $sectorConn->getSectorById($sectorId);
        $sector = new Sector ($sectorArr[0], $sectorArr[1], $sectorArr[2],$sectorArr[3],$sectorArr[4],$sectorArr[5],$sectorArr[6],$sectorArr[7],explode(",",$sectorArr[8]),explode(",",$sectorArr[9]));
        if ($capitolSectorArr)
            $distanceFromCapitol = $sector->getDistanceFrom($capitolSectorX, $capitolSectorY);

        $unit = new Unit (0, '', 0, 0, 0, 0, 0, '', 0, array(0,0,0,0,0), $costs, array(0,0,0,0,0), 0, '', NULL, 0);
        $costs = $unit->getEfectiveManteinanceCosts($distanceFromCapitol);
        }

    $newCosts = array();
    foreach ($costs as $index=>$cost)
        {
        $newCosts[$index] = $cost*$multiplier;
        }

    if ($concept=='BattleCosts')
        $battleConn->updateBattleCosts ($battleId, $playerId, $newCosts, 1, '+');
    else
        $sectorConn->updateSectorCosts ($sectorId, $newCosts, 1, '+');
    }
?>