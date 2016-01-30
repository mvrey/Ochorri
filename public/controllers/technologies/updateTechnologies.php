<?php
require_once ('../../lib/inclusion.php');
require_once_model('Player');
require_once_model('Technology');
require_once_model('StaticData');
session_start();

$playerConn = new PlayerDAO();
$technologyConn = new TechnologyDAO();

$techId = $_POST['techId'];
$percentOrder = $_POST['percentOrder'];
$player = $_SESSION['player'];
$staticData = $_SESSION['staticData'];
$allTechnologies = $staticData->getTechnologies();

//SAFETY CHECK ON NON-UPGRADABLE TECHNOLOGIES
$avail = $player->getAvailableTechnologies();
$tech = $avail[$techId];
$techOver = ((!$tech->getUpgradable()) && ($tech->getLevel()>0));
if ($techOver) die("Esta tecnología no es nivelable. Recarga la pestaña de Tecnologías para ver los cambios.");

//COPYPASTA FROM TECHNOLOGIES_REQUEST
$availableTechsArr = $playerConn->getAvailableTechnologies($player->getId(), $player->getAge());

$technologies = array();
foreach ($availableTechsArr as $technologyArr)
    {
    $technology = clone $allTechnologies[$technologyArr[0]];
   /* $technology = new Technology($rs->fields[0], $name, $rs->fields[2], $rs->fields[3], $rs->fields[4],
            $costs, $increments, $rs->fields[7], $rs->fields[8], $rs->fields[9], $rs->fields[10], $rs->fields[11],
            $rs->fields[12], $rs->fields[13], $rs->fields[14], $rs->fields[15], $rs->fields[16]);
*/
    $technology->setLevel($technologyArr[13]);
    $technology->setProgress($technologyArr[14]);
    $technology->setDateStartProgress($technologyArr[15]);
    $technology->setDateEndProgress($technologyArr[16]);

    $now = $_SERVER['REQUEST_TIME'];
    $start = $technology->getDateStartProgress();
    $end = $technology->getDateEndProgress();
    $timeLeft = $end-$now;
    if ($timeLeft<0)
        $timeLeft = 0;
    if (($end>$now))
        $end = $now;

    $passed = $end-$start;
    if (($technology->getLevel()) && ($technology->getUpgradable()))
        $realTime += $technology->getTime()*$technology->getIncrementTime()*$technology->getLevel();
    else
        $realTime = $technology->getTime();
    $researched = $passed/$realTime;
    $progress = $technology->getProgress()+$researched;

    if ($researched>0)
        {
        if ($progress>=100)
            {
            $technologyConn->updateTechnologyLink ($technology->getId(), $player->getId(), 0, 0, '*', $technology->getLevel()+1, 0);
            $technology->setLevel($technology->getLevel()+1);
            $technology->setProgress(0);
            $technology->setDateStartProgress(0);
            $technology->setDateEndProgress(0);
            }
        else
            {
            $technologyConn->updateTechnologyLink ($technology->getId(), $player->getId(), $passed, 0, '+', $technology->getLevel(), $progress);
            $technology->setProgress($progress);
            $technology->setDateStartProgress($technology->getDateStartProgress()+$passed);
            if ($timeLeft<=0)
                {
                $technologyConn->updateTechnologyLink ($technology->getId(), $player->getId(), 0, 0, '*');
                $technology->setDateStartProgress(0);
                $technology->setDateEndProgress(0);
                }
            }
        }

    $technologies[$technologyArr[0]] = $technology;
    }

$player->setAvailableTechnologies($technologies);

//END OF COPYPASTA


$availableTechnologies = $player->getAvailableTechnologies();

$technologyOK = array_key_exists($techId, $availableTechnologies);
$resourcesOK = true;

if ($technologyOK)
    {
    $technology = $availableTechnologies[$techId];

    if ($technology->getProgress()+$percentOrder<0)
        $percentOrder = 0;
    elseif ($technology->getProgress()+$percentOrder>100)
        {
        $percentOrder = 100-$technology->getProgress();
        }

    $technology_resources = $availableTechnologies[$techId]->getCosts();

    if ($technology->getIsAge())
        {
        $advanceCosts = $playerConn->getAgeAdvanceCosts ($player->getId());
        $totalCosts = array();
        foreach ($advanceCosts as $index=>$advanceCost)
            {
            $totalCosts[$index] = $technology_resources[$index]+$advanceCost;
            }
        $technology->setCosts($totalCosts);
        }
    $technology_resources = $technology->getCosts();
    

    $player_resources = $player->getResources();
    $leftResources = array();
    for ($i=0; $i<count($technology_resources); $i++)
        {
        $leftResources[$i] = $player_resources[$i]-$technology_resources[$i]*$percentOrder;
        if ($leftResources[$i]<0 && $technology_resources[$i]>0)
            $resourcesOK = false;
        }
    }

if (!$resourcesOK)
    die("1");

if (($technologyOK) && ($resourcesOK))
    {
    $now = $_SERVER['REQUEST_TIME'];
    
    if (($technology->getLevel()) && ($technology->getUpgradable()))
        $realTime = $technology->getTime()*$technology->getIncrementTime()*$technology->getLevel();
    else
        $realTime = $technology->getTime();

    if ($technology->getLevel() === NULL)
        $technologyConn->insertTechnologyLink ($techId, $player->getId(), $now, $now+$technology->getTime()*$percentOrder);
    else
        {
        if ($technology->getDateStartProgress()==0)
            {
            $startTime = $_SERVER['REQUEST_TIME'];
            $endTime = $startTime+$realTime*$percentOrder;
            }
            else
            {
            $startTime = 0;
            $endTime = $technology->getTime()*$percentOrder;
            }
        $technologyConn->updateTechnologyLink ($techId, $player->getId(), $startTime, $endTime, '+');
        }

    $playerConn->updatePlayerResources ($player->getId(), implode(",",$leftResources), $_SERVER['REQUEST_TIME']);
    $_SESSION['player']->setResources($leftResources);
    }
?>
