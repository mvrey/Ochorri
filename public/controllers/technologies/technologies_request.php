<?php
require_once ('../../lib/inclusion.php');
require_once ('../../config/paths.php');
require_once_model ('Technology');
require_once_model ('Building');
require_once_model ('Unit');
require_once_model ('Resource');
require_once_model ('Player');
require_once_model ('StaticData');
session_start();

$playerConn = new PlayerDAO();

$player = $_SESSION["player"];
$staticData = $_SESSION['staticData'];
$resources = $staticData->getResources();
$allTechnologies = $staticData->getTechnologies();

$playerConn->getAgeAdvanceCosts ($player->getId());

$availableTechsArr = $playerConn->getAvailableTechnologies($player->getId(), $player->getAge());

$technologies = array();
foreach ($availableTechsArr as $technologyArr)
    {
    $technology = clone $allTechnologies[$technologyArr[0]];

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
        $realTime = $technology->getTime()*$technology->getIncrementTime()*$technology->getLevel();
    else
        $realTime = $technology->getTime();
    $researched = $passed/$realTime;
    $progress = $technology->getProgress()+$researched;

    $technology->setProgress($progress);
    $technologies[$technologyArr[0]] = $technology;

    $_SESSION['player'] = $player;

    echo $technology->getId()."/".(float)($technology->getProgress())."/".$timeLeft/$realTime."-";
    }

echo "^_^";
$player->setAvailableTechnologies($technologies);

require ("../../views/technology/technologyView.php");
?>