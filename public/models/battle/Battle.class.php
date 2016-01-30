<?php

class Battle {

var $id;
var $sector;
var $attackingDivisions;
var $defendingDivisions;
var $lastUpdate;
var $attackerId;
var $defenderId;


function Battle($id=0, $attackingDivisions=array(), $defendingDivisions=array(), $lastUpdate=0, $isOver=false, $attackerId=0, $defenderId=0) {

    $this->id=$id;
    $this->attackingDivisions=$attackingDivisions;
    $this->defendingDivisions=$defendingDivisions;
    $this->lastUpdate=$lastUpdate;
    $this->isOver=$isOver;
    $this->attackerId=$attackerId;
    $this->defenderId=$defenderId;

    return $this;
}

public function getId() {
    return ($this->id);
}

public function getSector() {
    return ($this->sector);
}

public function setSector($value) {
    $this->sector = $value;
}

public function getAttackingDivisions() {
    return ($this->attackingDivisions);
}

public function setAttackingDivisions($value) {
    $this->attackingDivisions = $value;
}

public function getDefendingDivisions() {
    return ($this->defendingDivisions);
}

public function setDefendingDivisions($value) {
    $this->defendingDivisions = $value;
}

public function getLastUpdate() {
    return ($this->lastUpdate);
}

public function setLastUpdate($value) {
    $this->lastUpdate = $value;
}

public function getAttackerId() {
    return ($this->attackerId);
}

public function getDefenderId() {
    return ($this->defenderId);
}

private function sortDivisionsByUnitClass($divisions) {

    $aux = array();
    foreach ($divisions as $division)
        {
        if (get_class($division)=='Division')
            {
            if ($division->getQuantity()>0)
                {
                $unit = $division->getUnit();
                if (!isset($aux[$unit->getClass()]))
                    $aux[$unit->getClass()] = array();
                $aux[$unit->getClass()][$unit->getId()] = $division;
                }
            }
        elseif (get_class($division)=='Building')
            {
            $building = $division;
            $aux[4][$building->getId()] = $building;
            }
        }
    return($aux);
}


public function doRound($ofenders, $defenders, $mode) {

//mode=0 -> ofenders are sector attackers
//mode=1 -> ofenders are sector defenders
//owned=0 -> player IS NOT sector owner
//owned=1 -> player IS sector owner
    global $sectorConn, $battleConn, $divisionConn, $buildingConn;
    global $impactProbability;

    $log = array();
    $attackingClasses = $this->sortDivisionsByUnitClass($ofenders);
    $defendingClasses = $this->sortDivisionsByUnitClass($defenders);

    //$efectiveAttackingClasses = clone $attackingClasses;
    $efectiveAttackingClasses = $attackingClasses;
    unset($efectiveAttackingClasses[4]);
    //$efectiveDefendingClasses = clone $defendingClasses;
    $efectiveDefendingClasses = $defendingClasses;
    unset($efectiveDefendingClasses[4]);

    if ((count($efectiveAttackingClasses)<=0) || (count($efectiveDefendingClasses)<=0))
        return (false);
    //print_r($impactProbability);

foreach ($efectiveAttackingClasses as $attackingDivisions)
    {
    foreach ($attackingDivisions as $attackingDivision)
        {
        $unit = $attackingDivision->getUnit();
        $class = $unit->getClass();
        $probabilities = $impactProbability[$class];
        //array_push($probabilities,0);
        $counting = array();

        for ($i=0; $i<count($probabilities); $i++)
            array_push($counting, 0);

        $battleProbabilities = array();

        $battleProb = array($probabilities[0]);

        foreach ($defendingClasses as $defendingClass=>$defendingDivisions)
            {
            //print_r($defendingDivision);
            /*$defendingDivision = $defendingClass[0];
            $defendingUnit = $defendingDivision->getUnit();
            $defendingClass = $defendingUnit->getClass();*/
            $battleProb[$defendingClass] = $probabilities[$defendingClass];
            }

        $SumadeProb = 0;
        foreach ($battleProb as $prob)
            $SumadeProb = $SumadeProb + $prob;

        $n=0;
        $sumProb=0;
    /*echo "la prob total es ".$SumadeProb."<br>";
    echo "battleprob es: <br>";
    print_r($battleProb);*/

        $num = mt_rand(1, $SumadeProb);

        foreach ($battleProb as $index=>$nextProb)
            {
            $n=$index;
            if (($sumProb<$SumadeProb) && ($num>($sumProb+$nextProb)))
                $sumProb += $nextProb;
            else
                break;
            }


        $attackingUnit=$attackingDivision->getUnit();
        $attackingUnitName=$attackingUnit->getName();

        if ($n==0)
            {
            array_push ($log, "<span>".$attackingUnitName."s</span> se emocionan en exceso y FALLAN!");
            }
        else
            {
            $unitBattleMods = $unit->getBattleMods();
            $baseDamage = $unit->getAttack();
            $efectiveDamage = 0;

            if (isset($unitBattleMods[$n]))
                {
                $battleMod = $unitBattleMods[$n];
                switch ($battleMod->getOperation())
                    {
                    case '+':
                        $efectiveDamage += $battleMod->getValue();
                        break;
                    case '*':
                        $efectiveDamage *= $battleMod->getValue();
                        break;
                    }
                }
            else
                $efectiveDamage = $baseDamage;

            $defendingDivisions = $defendingClasses[$n];
            $defendingDivision = $defendingDivisions[array_rand($defendingDivisions)];

            $damage = $efectiveDamage*$attackingDivision->getQuantity();
            $casualties=0;

            //AQUI EMPIEZA LA DIFERENCIA ENTRE UNIDADES Y EDIFICIOS
            if (get_class($defendingDivision)=='Division')
                {
                $defendingQuantity = $defendingDivision->getQuantity();
                $defendingUnit=$defendingDivision->getUnit();
                $remainingHealth = $defendingDivision->getRemainingHealth();
                /*
                if ($remainingHealth==0)
                    $defendingDivisionHealth = $defendingUnit->getHealth()*$defendingDivision->getQuantity();
                else
                    $defendingDivisionHealth = ($defendingUnit->getHealth()*($defendingDivision->getQuantity()-1))+$defendingDivision->getRemainingHealth();
                */
                $unitName=$defendingUnit->getName();

                if ($remainingHealth==0)
                    {
                    $casualties = floor($damage / $defendingUnit->getHealth());
                    $casualties = min($casualties, $defendingQuantity);
                    $remainingHealth = $defendingUnit->getHealth()-$damage;
                    if ($remainingHealth<0)
                        {
                        while ($remainingHealth<0)
                            $remainingHealth += $defendingUnit->getHealth();
                        }
                    }
                else
                    {
                    if ($damage<$remainingHealth)
                        {
                        $remainingHealth = $remainingHealth-$damage;
                        $casualties=0;
                        }
                    else
                        {
                        $remainingDamage = $damage-$remainingHealth;
                        $casualties = floor($damage / $defendingUnit->getHealth());
                        $casualties = min($casualties, $defendingQuantity);
                        if (!$casualties)
                            $casualties++;
                        $remainingHealth = $defendingUnit->getHealth()-$remainingDamage;
                        if ($remainingHealth<0)
                            {
                            while ($remainingHealth<0)
                                $remainingHealth += $defendingUnit->getHealth();
                            }
                        }
                    }

                array_push ($log, "<span>".$attackingUnitName."s</span> hacen ".$damage." puntos de daño a <span>".$unitName."s</span>, causando ".$casualties." bajas");

                $unitCosts = $defendingUnit->getManteinanceCost();
                $casualtiesCosts = array();


                $sector = $this->getSector();
                $distanceFromCapitol = $sector->getDistanceFromCapitolSector($defendingDivision->getOwnerId(), $sectorConn);

                echo "        ownerId:  ".$defendingDivision->getOwnerId();
                echo "        distancefromcapitol: ".$distanceFromCapitol;
                $unitCosts= $defendingUnit->getEfectiveManteinanceCosts($distanceFromCapitol);


                foreach ($unitCosts as $index=>$unitCost)
                    {
                    $casualtiesCosts[$index] =$unitCost*$casualties;
                    }

                $defendingDivision->setRemainingHealth($remainingHealth);
                $defendingDivision->setQuantity($defendingDivision->getQuantity()-$casualties);
                //echo "Quedan ".$defendingDivision->getRemainingHealth()." puntos de daño<br><br>";
                $defendingDivisions[$n]=$defendingDivision;

                $sector = $this->getSector();
                $divisionConn->updateDivision ($defendingDivision->getOwnerId(), $sector->getId(),
                    $defendingDivision->getUnitId(), $casualties, $operation='-', $defendingDivision->getRemainingHealth());

                // ESTO DE ABAJO SI EL JUGADOR ES PROPIETARIO DEL SECTOR
                // SI NO LO ES SE APLICA A UPDATEBATTLECOSTS
                if ($defendingDivision->getOwnerId()==$sector->getOwner())
                    $sectorConn->updateSectorCosts ($sector->getId(), $casualtiesCosts, 1, '-');
                else
                    $battleConn->updateBattleCosts ($this->getId(), $defendingDivision->getOwnerId(), $casualtiesCosts, 1, '-');
                }



//START BUILDING BLOCK
            elseif (get_class($defendingDivision)=='Building')
                {
                //$defendingQuantity = $defendingDivision->getQuantity();
                $defendingBuilding = $defendingDivision;

                $remainingHealth = $defendingBuilding->getRemainingHealth();

                $buildingName=$defendingBuilding->getName();
/*
                if ($remainingHealth<=0)
                    {
                    $casualties = floor($damage / $defendingUnit->getHealth());
                    $casualties = min($casualties, $defendingQuantity);
                    $remainingHealth = $defendingUnit->getHealth()-$damage;
                    if ($remainingHealth<0)
                        {
                        while ($remainingHealth<0)
                            $remainingHealth += $defendingUnit->getHealth();
                        }
                    }
                else
                    {*/
                    if ($damage<$remainingHealth)
                        {
                        $remainingHealth = $remainingHealth-$damage;
                        $casualties=0;
                        array_push ($log, "<span>".$attackingUnitName."s</span> hacen ".$damage." puntos de daño a <span>".$buildingName."</span>");
                        }
                    else
                        {
                        //Update sector manteinances
                        $defendingBuilding->updateManteinanceCost();
                        $prevBuildingCosts= $defendingBuilding->getManteinanceCost();

                        $remainingHealth = $defendingBuilding->getHealth();
                        $casualties = 1;
                        if ((!$defendingBuilding->getUpgradable()) || ($defendingBuilding->getLevel()==1))
                            {
                            array_push ($log, "<span>".$attackingUnitName."s</span> hacen ".$damage." puntos de daño a <span>".$buildingName."</span>, destruyéndolo.");
                            $diffBuildingCosts = $prevBuildingCosts;
                            unset($defendingDivisions[$n]);
                            $sector = $this->getSector();
                            $buildingConn->deleteBuilding($sector->getId(), $defendingBuilding->getId());
                            }
                        else
                            {
                            $defendingBuilding->setLevel($defendingBuilding->getLevel()-1);
                            array_push ($log, "<span>".$attackingUnitName."s</span> hacen ".$damage." puntos de daño a <span>".$buildingName."</span>, bajándolo a nivel ".$defendingBuilding->getLevel());

                            $defendingBuilding->updateManteinanceCost();
                            $nextBuildingCosts= $defendingBuilding->getManteinanceCost();
                            $diffBuildingCosts = array();
                            var_dump($prevBuildingCosts);
                            foreach ($prevBuildingCosts as $i=>$prevBuildingCost)
                                {
                                $diffBuildingCosts[$i] = $prevBuildingCost - $nextBuildingCosts[$i];
                                }
                            }
                        $sector = $this->getSector();
echo "vamos a actualizar los costes de sector tras destruir el edificio";
var_dump($diffBuildingCosts);
                        $sectorConn->updateSectorCosts ($sector->getId(), $diffBuildingCosts, 1, '-');

                        //Update sector productions if necessary

                        // Actualizamos los datos del sector tras bajar de nivel/destruir el edificio
                        // Hay que acceder a las producciones, no a los costes de producción.
                        // A éstas se accede con getProductionMods
                        if ($defendingBuilding->getProductionMods())
                            {
                            $productionMods = $defendingBuilding->getProductionMods();
                            $sectorProductions = $sector->getProductions();
                            foreach ($productionMods as $productionMod)
                                {
                                $resourceId = $productionMod->getResourceId();
                                switch ($productionMod->getOperation())
                                    {
                                    case '*':
                                        $sectorProductions[$resourceId] /= $productionMod->getValue();
                                        break;
                                    }
                                }
                            $sectorConn->updateSectorProductions ($sector->getId(), $sectorProductions, 0);
                            }
                        }
                    //}


                $defendingBuilding->setRemainingHealth($remainingHealth);
                $defendingDivisions[$n]=$defendingBuilding;

                $sector = $this->getSector();
                $defendingBuilding->updateTime();

                //Calculate new dateStarted and dateStopped
                $percent = $defendingBuilding->getRemainingHealth()/$defendingBuilding->getHealth();
                $dateStopped = $_SERVER['REQUEST_TIME'];
                //Next instruction can update unix time as float.
                //Using floor() turns out on losing precission for small values.
                $dateStarted = $dateStopped-($defendingBuilding->getTime()*$percent);

                $buildingConn->updateDamagedBuilding($defendingBuilding->getId(), $sector->getId(), $remainingHealth, $defendingBuilding->getLevel(), $dateStarted, $dateStopped);
                //Now we update sector productions if necessary
                }
//END BUILDING BLOCK



                
            }

        $counting[$n]++;

        //print_r($counting);echo "<br>";
        }
    }

    if (!$mode)
        $this->setDefendingDivisions($defendingDivisions);
    elseif ($mode)
        $this->setAttackingDivisions($defendingDivisions);

    return ($log);
}

}
?>