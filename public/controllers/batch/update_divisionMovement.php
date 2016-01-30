<?php
$msg = "";
$allSectors = $staticData->getSectors();

$startSectorString = $startSectorName."(".$startSectorX.",".$startSectorY.")";
$endSectorString = $endSectorName."(".$endSectorX.",".$endSectorY.")";

$divisionMovement = new DivisionMovement($startedDivisionMovement[0], $startedDivisionMovement[3], $startSectorId, $endSectorId, $startedDivisionMovement[6], $startedDivisionMovement[7]);
$unitList = explode(",", $startedDivisionMovement[1]);
$quantityList = explode(",", $startedDivisionMovement[2]);

$timeLeft = $divisionMovement->getTime()-($_SERVER['REQUEST_TIME']-$divisionMovement->getStartDateTime());

$isAttack = ($divisionMovement->getOwnerId()!=$endSectorOwnerId);

foreach ($unitList as $i=>$unitId)
    {
    if ($quantityList[$i]>0)
        {
        if ($timeLeft<=0)
            {
            $sectorArr = $sectorConn->getSectorByCoordinates($endSectorX,$endSectorY);
            $endSector = new Sector ($sectorArr[0], $sectorArr[1],$sectorArr[2],$sectorArr[3],$sectorArr[4],$sectorArr[5],$sectorArr[6],$sectorArr[7],explode(",",$sectorArr[8]),explode(",",$sectorArr[9]), $sectorArr[10]);
            $sectorArr = $sectorConn->getSectorByCoordinates($startSectorX,$startSectorY);
            $startSector = new Sector ($sectorArr[0], $sectorArr[1],$sectorArr[2],$sectorArr[3],$sectorArr[4],$sectorArr[5],$sectorArr[6],$sectorArr[7],explode(",",$sectorArr[8]),explode(",",$sectorArr[9]), $sectorArr[10]);

            if (!isset($allUnits))
                {
                $allUnitsArr = $unitConn->getAllUnits();

                $allUnits = array();
                $trainingTimes = array();

                foreach ($allUnitsArr as $unitArr)
                    {
                    $unit = new Unit ($unitArr[0], $unitArr[1], $unitArr[2],$unitArr[3],$unitArr[4],$unitArr[5],$unitArr[6],$unitArr[7],$unitArr[8],explode(",", $unitArr[9]),explode(",", $unitArr[10]),explode(",", $unitArr[11]),$unitArr[12],$unitArr[13],$unitArr[14],$unitArr[15]);
                    $allUnits[$unit->getId()] = $unit;
                    }
                }

            $unit = $allUnits[$unitId];

            $capitolSectorArr = $sectorConn->getCapitolSector($playerId);

            if ($capitolSectorArr)
                {
                $capitolSector = new Sector ($capitolSectorArr[0], $capitolSectorArr[1],$capitolSectorArr[2],$capitolSectorArr[3],$capitolSectorArr[4],$capitolSectorArr[5],$capitolSectorArr[6],$capitolSectorArr[7],explode(",",$capitolSectorArr[8]),explode(",",$capitolSectorArr[9]), $capitolSectorArr[10]);
                $capitolX = $capitolSector->getCoordinateX();
                $capitolY = $capitolSector->getCoordinateY();

                $distanceFromCapitol = $startSector->getDistanceFrom($capitolX, $capitolY);
                //var_dump($distanceFromCapitol);
                }
            else
                {
                $ownedSectors = Sector::getOwnedSectors($allSectors, $playerId);
                $distanceFromCapitol = Sector::getMaxDistance($ownedSectors);
                }

            $unitStartCosts = $unit->getEfectiveManteinanceCosts($distanceFromCapitol);
            for ($j=0; $j<count($unitStartCosts); $j++)
                {
                $unitStartCosts[$j] = $unitStartCosts[$j]*$quantityList[$i];
                }
                
            if ($capitolSectorArr)
                $distanceFromCapitol = $endSector->getDistanceFrom($capitolX, $capitolY);

            $unitEndCosts = $unit->getEfectiveManteinanceCosts($distanceFromCapitol);
            for ($j=0; $j<count($unitEndCosts); $j++)
                {
                $unitEndCosts[$j] = $unitEndCosts[$j]*$quantityList[$i];
                }

            $sectorConn->updateSectorCosts($startSectorId, $unitStartCosts, 1, '-');
            if (!$isAttack)
                $sectorConn->updateSectorCosts($endSectorId, $unitEndCosts, 1, '+');

            $aux1 = array($unitId);
            $aux2 = array($quantityList[$i]);
            if ($divisionConn->getDivisionsExists($playerId, $endSectorId, $aux1, false))
                $divisionConn->updateDivision ($playerId, $endSectorId, $unitId, $quantityList[$i]);
            else
                $divisionConn->insertDivision ($playerId, $endSectorId, $unitId, $quantityList[$i]);

            if ($isAttack)
                {
                if (!$endSectorIsBattle)
                    {
                    if ($endSectorOwnerId==NULL)
                        $endSectorOwnerId = 0;
                    $battleConn->insertBattle($endSectorId, $_SERVER['REQUEST_TIME'], $playerId, $endSectorOwnerId);
                    $buildingConn->pauseBuilding($endSectorId);
                    $endSectorIsBattle = true;

                    $message = "Nuestras tropas han llegado desde ".$startSectorString." a ".$endSectorString.", comenzando el ataque.";
                    $messageConn->insertMessage(0, $playerId, "Comienzo del ataque", $message);
                    $message = "El enemigo ha comenzado un ataque en ".$endSectorString.".";
                    $messageConn->insertMessage(0, $endSectorOwnerId, "¡Nos atacan!", $message);
                    }
                else
                    {
                    $message = "Han llegado refuerzos aliados desde ".$startSectorString." a ".$endSectorString.".";
                    $messageConn->insertMessage(0, $playerId, "Refuerzos aliados", $message);
                    $message = "Han llegado refuerzos enemigos desde ".$startSectorString." a ".$endSectorString.".";
                    $messageConn->insertMessage(0, $endSectorOwnerId, "Refuerzos enemigos", $message);
                    }
                $sectorConn->updateSector($endSectorId, false, false, true);
                $battleArr = $battleConn->getBattleBySectorId($endSectorId);
                $battleId = $battleArr[0];
                $battleRoundConn->insertBattleRound ($battleId, "(empty)", "(empty)", true);
                $battleCosts = $playerConn->getBattleCosts($playerId, $endSectorId);

                if (count($battleCosts))
                    $playerConn->updateBattleCosts ($battleId, $playerId, $unitEndCosts, 1, '+');
                else
                    $playerConn->insertBattleCosts ($battleId, $playerId, implode(",", $unitEndCosts));
                }
            else
                {
                $message = "Nuestras tropas han llegado desde ".$startSectorString." a ".$endSectorString.".";
                $messageConn->insertMessage(0, $playerId, "Las tropas han llegado.", $message);
                }
            }
        }
    }
$divisionMovementConn->deleteDivisionMovement ($divisionMovementId);
?>