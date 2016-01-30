<?php
$sectorArr = $sectorConn->getSectorByCoordinates($coordinateX,$coordinateY);
$sector = new Sector ($sectorArr[0], $sectorArr[1],$sectorArr[2],$sectorArr[3],$sectorArr[4],$sectorArr[5],$sectorArr[6],$sectorArr[7],explode(",",$sectorArr[8]),explode(",",$sectorArr[9]), $sectorArr[10]);
$distanceFromCapitol = $sector->getDistanceFromCapitolSector($playerId, $sectorConn);
if (!$distanceFromCapitol)
    {
    if (!isset($staticData))
        {
        require_once (HOME.'controllers/StaticData/initStaticData.php');
        }
    if (!($staticData->getSectors()))
        {
        $sectorsArr = $sectorConn->getAllSectors();
        foreach ($sectorsArr as $sectorArr)
            $allSectors[] = new Sector ($sectorArr[0], $sectorArr[1],$sectorArr[2],$sectorArr[3],$sectorArr[4],$sectorArr[5],$sectorArr[6],$sectorArr[7],explode(",",$sectorArr[8]),explode(",",$sectorArr[9]), $sectorArr[10]);
        $staticData->setSectors($allSectors);
        }
    $ownedSectors = Sector::getOwnedSectors($staticData->getSectors(), $playerId);
    $distanceFromCapitol = Sector::getMaxDistance($ownedSectors);
    }

if (!isset($allManteinances))
    {
    $unitsArr = $unitConn->getAllUnits();

    $allManteinances = array();

    foreach ($unitsArr as $unitArr)
        {
        $unit = new Unit ($unitArr[0], $unitArr[1], $unitArr[2],$unitArr[3],$unitArr[4],$unitArr[5],$unitArr[6],$unitArr[7],$unitArr[8],explode(",", $unitArr[9]),explode(",", $unitArr[10]),explode(",", $unitArr[11]),$unitArr[12],$unitArr[13],$unitArr[14],$unitArr[15]);
        $unitId = $unitArr[0];
        $manteinance = $unit->getEfectiveManteinanceCosts($distanceFromCapitol);
        $allManteinances[$unitId] = $manteinance;
        }
    }


$unitQueueArr = $sectorConn->getUnitQueueLists($coordinateX, $coordinateY, $playerId);

$queuedUnits = explode(",", $unitQueueArr[0]);
$queuedTimes = explode(",", $unitQueueArr[1]);
$startTime = $unitQueueArr[2];
$now = $_SERVER['REQUEST_TIME'];
$percent=0;
$auxUnits=implode(",",$queuedUnits);
$auxTimes=implode(",",$queuedTimes);

if ($queuedUnits[0]!='')
    {
    $done=0;
    foreach ($queuedTimes as $queuedTime)
        {
        $startTime = $startTime+$queuedTime;
        if ($now<$startTime)
            break;
        else
            $done++;
        }

    if ($done>0)
        {
        $doneUnits = array();
        if (count($queuedUnits)==$done)
            {
            $messageConn->insertMessage(0, $playerId, "Tropas entrenadas.", "El entrenamiento de tropas ha terminado en ".$sectorName." (".$coordinateX.",".$coordinateY.").");
            $sectorConn->deleteUnitQueue($coordinateX, $coordinateY, $playerId);
            $doneUnits=$queuedUnits;
            $queuedUnits = array();
            $auxUnits = "";
            $auxTimes = "";
            }
        else
            {
            $startTime = $startTime-$queuedTimes[$done];

            $auxUnits = $queuedUnits[$done];
            $auxTimes = $queuedTimes[$done];
            for ($i=0; $i<count($queuedUnits); $i++)
                {
                if ($i>$done)
                    {
                    $auxUnits.=",".$queuedUnits[$i];
                    $auxTimes.=",".$queuedTimes[$i];
                    }
                elseif ($i<$done)
                    {
                    array_push($doneUnits, $queuedUnits[$i]);
                    }
                }
            $sectorConn->updateUnitQueue($coordinateX, $coordinateY, $playerId, $auxUnits, $auxTimes, $startTime);

            $queuedUnits=explode(",",$auxUnits);

            $passed = $now-$startTime;
            $percent = (($passed*100)/$queuedTimes[$done]);
            }

        foreach ($doneUnits as $doneUnit)
            {
            $manteinance=$allManteinances[$doneUnit];
            $sectorConn->updateSectorCosts($sectorId, $manteinance, 1, '+');
            }
        }
    else
        {
        $startTime = $startTime-$queuedTimes[$done];
        $passed = $now-$startTime;
        $percent = ($passed*100)/$queuedTimes[$done];
        }
    }

if (isset($doneUnits)){
$doneNumbers = array();
foreach ($doneUnits as $doneUnit)
    {
    $doneNumbers[$doneUnit]=0;
    }
foreach ($doneUnits as $doneUnit)
    {
    $doneNumbers[$doneUnit]++;
    }

foreach ($doneNumbers as $unitId=>$quantity)
    {
    if ($divisionConn->getDivisionExists($playerId, $sectorId, $unitId))
        $divisionConn->updateDivision($playerId, $sectorId, $unitId, $quantity);
    else
        $divisionConn->insertDivision($playerId, $sectorId, $unitId, $quantity);
    }
}
?>