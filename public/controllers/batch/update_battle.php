<?php
$allUnits = $staticData->getUnits();
$allPlayers = $staticData->getPlayers();
$allBuildings = $staticData->getBuildings();

$coordinateX = $_POST['coordinateX'];
$coordinateY = $_POST['coordinateY'];

$sectorArr = $sectorConn->getSectorByCoordinates($coordinateX, $coordinateY);
$sector = new Sector ($sectorArr[0], $sectorArr[1], $sectorArr[2],$sectorArr[3],$sectorArr[4],$sectorArr[5],$sectorArr[6],$sectorArr[7],explode(",",$sectorArr[8]),explode(",",$sectorArr[9]));

$battleArr = $battleConn->getBattleBySectorId($sector->getId());
$battle = new Battle($battleArr[0], 0, 0, $battleArr[2], $battleArr[3], $battleArr[4], $battleArr[5]);

$attackingDivisions = array();
$defendingDivisions = array();
$sectorNameString = $sector->getName()."(".$sector->getCoordinateX().",".$sector->getCoordinateY().")";
$isPlayerDefending = ($battle->getDefenderId()==$player->getId());

$owner = $sector->getOwner();
if (empty($owner))
    $defendingPlayer = new Player();
else
    $defendingPlayer = $allPlayers[$sector->getOwner()];

$isPlayerInvolved = true;

$divisionsArr = $divisionConn->getDivisionsBySector($sector->getId());

foreach ($divisionsArr as $divisionArr)
    {
    $quantity = $divisionArr[4];
    $ownerId = $divisionArr[1];

    if (($battle->getDefenderId()!=$ownerId) && ($player->getId()!=$battle->getDefenderId()) && ($player->getId()!=$ownerId))
        {
        $isPlayerInvolved = false;
        $attackingPlayer = $allPlayers[$ownerId];
        break;
        }

    if ($quantity>0)
        {
        $division = new Division($divisionArr[0], $ownerId, $divisionArr[3], $quantity, 0, $divisionArr[5]);

        //Again, absurd redundance: i'm running out of time
        $division->setUnit($allUnits[$division->getUnitId()]);

        $isDivisionOwned = ($division->getOwnerId()==$player->getId());

        if ($isPlayerDefending XOR $isDivisionOwned)
            $attackingDivisions[] = $division;
        else
            $defendingDivisions[] = $division;
        }
    }


if (count($defendingDivisions)>0)
    {
    $sectorBuildingsArr = $sectorConn->getSectorBuildings($sector->getId());
    foreach ($sectorBuildingsArr as $sectorBuildingArr)
        {
        $building = clone $allBuildings[$sectorBuildingArr[1]];
        $building->setLevel($sectorBuildingArr[3]);
        $building->setDateStarted($sectorBuildingArr[4]);
        $building->setDateStopped($sectorBuildingArr[5]);
        $building->setRemainingHealth($sectorBuildingArr[6]);
        $defendingDivisions[] = $building;
        }
    }


if ($isPlayerInvolved)
    {
    $sectorOwnerId = $sector->getOwner();
    $isSectorOwned = ($sectorOwnerId==$player->getId());

    if ($isSectorOwned)
        $colors = array("red", "green");
    else
        $colors = array("green", "red");

    $aux1 = $attackingDivisions;
    $aux2 = $defendingDivisions;
    $now = $_SERVER['REQUEST_TIME'];

    if (count($attackingDivisions)<=0)
        {
        $message = "Nuestro ataque en ".$sectorNameString." ha sido repelido por el enemigo.";
        $messageConn->insertMessage(0, $battle->getAttackerId(), "Derrota", $message);
        $message = "Hemos repelido a los invasores en ".$sectorNameString.".";
        $messageConn->insertMessage(0, $battle->getDefenderId(), "¡Victoria!", $message);

        $sectorConn->updateSector($sector->getId(), $sector->getOccupant(), $sector->getOwner(), false);
        $battleConn->updateBattle($battle->getId(), $now, true);
        $battleConn->deleteBattleCostsByBattleId ($battle->getId());
        $battle->setDefendingDivisions($defendingDivisions);
        $divisionConn->purgeDivisions();
        }
    elseif (count($defendingDivisions)<=0)
        {
        $message = "Nuestras tropas en ".$sectorNameString." han eliminado al enemigo. El sector está ahora a nuestra merced.";
        $messageConn->insertMessage(0, $battle->getAttackerId(), "¡Victoria!", $message);
        $message = "Nuestras tropas en ".$sectorNameString." han sido eliminadas. El sector está ahora a merced del enemigo.";
        $messageConn->insertMessage(0, $battle->getDefenderId(), "Derrota", $message);


        $deleteCapitol = $playerConn->deleteCapitolBuilding($battle->getDefenderId(), $sector->getId());

        $sectorConn->updateSector($sector->getId(), $battle->getAttackerId(), $battle->getAttackerId(), false);
        $battleConn->updateBattle($battle->getId(), $now, true);

        $sectorConn->deleteUnitQueue($sector->getcoordinateX(), $sector->getcoordinateY(), $battle->getDefenderId());

        $divisionConn->updateDivision (false, $sector->getId(), false, 1, "*", 0);
        $battleCosts = $battleConn->getBattleCosts ($battle->getAttackerId(), $sector->getId());
        if ($battleCosts->RecordCount()>0)
            {
            $battleCosts = explode(",", $battleCosts->fields[3]);
            $sectorConn->updateSectorCosts($sector->getId(), $battleCosts, 1, '+');
            $battleConn->deleteBattleCostsByBattleId($battle->getId());
            }
        $divisionConn->purgeDivisions();

        if ($deleteCapitol)
            {
            if (!($staticData->getSectors()))
                {
                $sectorsArr = $sectorConn->getAllSectors();
                foreach ($sectorsArr as $sectorArr)
                    $allSectors[] = new Sector ($sectorArr[0], $sectorArr[1],$sectorArr[2],$sectorArr[3],$sectorArr[4],$sectorArr[5],$sectorArr[6],$sectorArr[7],explode(",",$sectorArr[8]),explode(",",$sectorArr[9]), $sectorArr[10]);
                $staticData->setSectors($allSectors);
                }
            $defenderSectors = Sector::getOwnedSectors($staticData->getSectors(), $battle->getDefenderId());
            $maxDistance = Sector::getMaxDistance($defenderSectors);
            $playerId = $battle->getDefenderId();
            require (HOME.'controllers/batch/recalculate_manteinances.php');
            }

        $capitolSectorArr = $sectorConn->getCapitolSector($battle->getAttackerId());
        if (!$capitolSectorArr)
            {
            if (!($staticData->getSectors()))
                {
                $sectorsArr = $sectorConn->getAllSectors();
                foreach ($sectorsArr as $sectorArr)
                    $allSectors[] = new Sector ($sectorArr[0], $sectorArr[1],$sectorArr[2],$sectorArr[3],$sectorArr[4],$sectorArr[5],$sectorArr[6],$sectorArr[7],explode(",",$sectorArr[8]),explode(",",$sectorArr[9]), $sectorArr[10]);
                $staticData->setSectors($allSectors);
                }
            $attackerSectors = Sector::getOwnedSectors($staticData->getSectors(), $battle->getAttackerId());
            $maxDistance = Sector::getMaxDistance($attackerSectors);
            $playerId = $battle->getAttackerId();
            require (HOME.'controllers/batch/recalculate_manteinances.php');
            }
        }
    else
        {
        //$attacker = $allPlayers[array_shift($aux1)->getOwnerId()];
        //$defender = $allPlayers[array_shift($aux2)->getOwnerId()];

        $battle->setSector($sector);
        $battle->setAttackingDivisions($attackingDivisions);
        $battle->setDefendingDivisions($defendingDivisions);

        $attackDivisions = $battle->getAttackingDivisions();
        $defendDivisions = $battle->getDefendingDivisions();

        $passed = $now - $battle->getLastUpdate();
        $roundsMissed = floor($passed/($round_time/1000));
        $remainingTime = $passed%($round_time/1000);

        if ($roundsMissed>=1)
            {
            for ($i=0; $i<$roundsMissed; $i++)
                {
                $defendLog = $battle->doRound($defendDivisions, $attackDivisions, 1);
                $attackLog = $battle->doRound($attackDivisions, $defendDivisions, 0);
                if (!$defendLog)
                    $defendLog = array();
                if (!$attackLog)
                    $attackLog = array();
                $battleRoundConn->insertBattleRound($battle->getId(), implode("^_^", $attackLog), implode("^_^", $defendLog));
                if (($attackLog=="GTFO") || ($defendLog=="GTFO"))
                    break;
                }
            $battleConn->updateBattle($battle->getId(), $now);
            }

        $battle->setAttackingDivisions($attackDivisions);
        $battle->setDefendingDivisions($defendDivisions);
        }

    $maxRound = $battleRoundConn->getMaxRoundsByBattleId ($battle->getId());
    }

    if ($isPlayerInvolved)
        {
        if (!(($battle->getAttackingDivisions()) && ($battle->getDefendingDivisions())))
            {
            if ($battle->getAttackingDivisions()==0)
                {
                $divisionConn->updateDivision (false, $sector->getId(), false, 1, "*", 0);
                }
            elseif ($battle->getDefendingDivisions()==0)
                {
                $divisionConn->updateDivision (false, $sector->getId(), false, 1, "*", 0);
                }
            }
        }
    ?>