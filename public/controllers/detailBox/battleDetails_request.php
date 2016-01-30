<?php
require_once ('../../lib/inclusion.php');
require_once ('../../config/paths.php');
require_once ('../../config/battle.cfg.php');

require_once_model ('StaticData');
require_once_model ('Division');
require_once_model ('Unit');
require_once_model ('Sector');
require_once_model ('Battle');
require_once_model ('BattleRound');
require_once_model ('Player');
session_start();

$battleConn = new BattleDAO();
$battleRoundConn = new BattleRoundDAO();
$divisionConn = new DivisionDAO();

$player = $_SESSION['player'];
$staticData = $_SESSION['staticData'];
$allUnits = $staticData->getUnits();
$allPlayers = $staticData->getPlayers();
$allSectors = $staticData->getSectors();

$coordinateX = $_POST['coordinateX'];
$coordinateY = $_POST['coordinateY'];
if (isset($_POST['noView']))
    $noView = $_POST['noView'];
else
    $noView = false;

$sector = $allSectors[$coordinateX.",".$coordinateY];

$battleArr = $battleConn->getBattleBySectorId($sector->getId());
$battle = new Battle($battleArr[0], 0, 0, $battleArr[2], $battleArr[3], $battleArr[4], $battleArr[5]);

$attackingDivisions = array();
$defendingDivisions = array();
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
//echo "num   ,";
    if (($defendingPlayer->getId()!=$ownerId) && ($player->getId()!=$defendingPlayer->getId()) && ($player->getId()!=$ownerId))
        {
        $isPlayerInvolved = false;
        $attackingPlayer = $allPlayers[$ownerId];
        break;
        }

    if ($quantity>0)
        {
        $division = new Division($divisionArr[0], $ownerId, $divisionArr[3], $quantity, 0, $divisionArr[5]);

        $division->setUnit($allUnits[$division->getUnitId()]);

        $isDivisionOwned = ($division->getOwnerId()==$player->getId());

        if ($isPlayerDefending XOR $isDivisionOwned)
            $attackingDivisions[] = $division;
        else
            $defendingDivisions[] = $division;
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
    $remainingTime = 0;

    if (count($attackingDivisions)<=0)
        {
        $battle->setDefendingDivisions($defendingDivisions);
        if ($noView)
            echo "2";
        }
    elseif (count($defendingDivisions)<=0)
        {
        $attacker = $allPlayers[array_shift($aux1)->getOwnerId()];
        if ($noView)
            echo "1";
        }
    else
        {
        if ($noView)
            echo "0";
        $attacker = $allPlayers[array_shift($aux1)->getOwnerId()];
        $defender = $allPlayers[array_shift($aux2)->getOwnerId()];

        $battle->setSector($sector);
        $battle->setAttackingDivisions($attackingDivisions);
        $battle->setDefendingDivisions($defendingDivisions);

        $attackDivisions = $battle->getAttackingDivisions();
        $defendDivisions = $battle->getDefendingDivisions();

        $passed = $now - $battle->getLastUpdate();
        $roundsMissed = floor($passed/($round_time/1000));
        $remainingTime = $passed%($round_time/1000);

        $battle->setAttackingDivisions($attackDivisions);
        $battle->setDefendingDivisions($defendDivisions);

        if ($noView)
            {
            echo "/";
            foreach ($attackDivisions as $index=>$division)
                {
                echo $division->getUnitId().":".$division->getQuantity();
                if ($index<count($attackDivisions)-1)
                    echo ",";
                }
            echo "/";
            foreach ($defendDivisions as $index=>$division)
                {
                echo $division->getUnitId().":".$division->getQuantity();
                if ($index<count($defendDivisions)-1)
                    echo ",";
                }
            $maxRound = $battleRoundConn->getMaxRoundsByBattleId ($battle->getId());
            echo "/".$battle->getId()."/".$maxRound;
            die();
            }
        }

    $maxRound = $battleRoundConn->getMaxRoundsByBattleId ($battle->getId());
    }
echo $remainingTime."^_^";

$detailType = 'battle';
require ("../../views/detailBox/detailBoxView.php");
require ('../../views/detailBox/battleDetailsView.php');
?>