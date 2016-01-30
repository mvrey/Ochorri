<?php
$now = $_SERVER['REQUEST_TIME'];
$timeLeft = $endTime-$now;
if ($timeLeft<0)
    $timeLeft = 0;
if (($endTime>$now))
    $endTime = $now;


if (($techLevel) && ($techUpgradable))
    $realTime = $techTime*$techIncrementTime*$techLevel;
else
    $realTime = $techTime;

$passed = $endTime-$startTime;
$researched = $passed/$realTime;
$progress = $techProgress+$researched;

if ($researched>0)
    {
    if ($progress>99.99)
        {
        $techName = $termConn->getTranslation ($techName, $_SESSION['language']);
        if ($techUpgradable)
            $techName .= "(".($techLevel+1).")";
        $message = "La investigación de ".$techName." ha concluído.";
        $messageConn->insertMessage(0, $playerId, "Tecnología investigada.", $message);

        $technologyConn->updateTechnologyLink ($techId, $playerId, 0, 0, '*', $techLevel+1, 0);
        if ($techIsAge)
            {
            $playerConn->updatePlayerAge ($playerId, $techEndAge);

            if (!isset($allUnits))
                {
                $unitsArr = $unitConn->getAllUnits();

                $allUnits = array();
                $trainingTimes = array();

                foreach ($unitsArr as $unitArr)
                    {
                    $unit = new Unit ($unitArr[0], $unitArr[1], $unitArr[2],$unitArr[3],$unitArr[4],$unitArr[5],$unitArr[6],$unitArr[7],$unitArr[8],$unitArr[9],$unitArr[10],$unitArr[11],$unitArr[12],$unitArr[13],$unitArr[14],$unitArr[15]);
                    $allUnits[$unit->getId()] = $unit;
                    }
                }

            foreach ($allUnits as $unit)
                {
                if (($unit->getUpgradesTo()>0) && ($unit->getAutoUpgrade()) && ($unit->getEndAge()==$techEndAge))
                    {
                    $unitConn->upgradeUnit($playerId, $unit->getId(), $divisionConn);
                    require (HOME."controllers/batch/recalculate_productions.php");
                    }
                }
            //NO BUILDINGS AUTOUPGRADE CODE SINCE THERE IS NONE YET. I'LL DO IT WHEN THE TIME IS RIGHT.
            }
        //Now we switch technologyId and apply aditional technology effects
            switch ($techId)
                {
                //On farms(4) turns every existent hunt camp into farms
                case 4:
                    $buildingConn->upgradeBuilding ($playerId, 6);
                    require (HOME."controllers/batch/recalculate_productions.php");
                    break;
                }
        }
    else
        {
        $technologyConn->updateTechnologyLink ($techId, $playerId, $passed, 0, '+', $techLevel, $progress);
        /*$technology->setProgress($progress);
        $technology->setDateStartProgress($technology->getDateStartProgress()+$passed);*/
        if ($timeLeft<=0)
            {
            $technologyConn->updateTechnologyLink ($techId, $playerId, 0, 0, '*');
            }
        }
    }
?>