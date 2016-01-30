<?php
if (!(isset($absolute_path) && ($absolute_path)))
    {
    require_once ('../../lib/inclusion.php');

    if (isset($absolute_path) && ($absolute_path))
        {
        require_once (HOME.'DAO/DAO.class.php');
        require_once (HOME.'class/Unit.class.php');
        require_once (HOME.'class/Building.class.php');
        require_once (HOME.'class/Player.class.php');
        require_once (HOME.'class/Sector.class.php');
        require_once (HOME.'class/StaticData.class.php');
        require_once (HOME.'class/ProductionMod.class.php');
        require_once (HOME.'class/BattleMod.class.php');
        }
    else
        {
        require_once('../../models/DAO/DAO.class.php');
        require_once_model ('Term');
        require_once_model ('Unit');
        require_once_model ('Building');
        require_once_model ('Technology');
        require_once_model ('Sector');
        require_once_model ('Player');
        require_once_model ('Resource');
        require_once_model ('StaticData');
        require_once_model ('ProductionMod');
        require_once_model ('BattleMod');
        }
    }
//session_start();

$staticData = StaticData::singleton();
$termConn = new TermDAO();
$unitConn = new UnitDAO();
$buildingConn = new BuildingDAO();
$technologyConn = new TechnologyDAO();
$playerConn = new PlayerDAO();
$resourceConn = new ResourceDAO();
$productionModConn = new ProductionModDAO();
$battleModConn = new BattleModDAO();


Term::setLang($_SESSION['language']);
$termsArr = $termConn->getAllTerms($_SESSION['language']);
foreach ($termsArr as $termArr)
    {
    $term = new Term ($termArr[0], $termArr[1]);
    $terms[$termArr[0]] = $term;
    }

$battleModsArr = $battleModConn->getAllBattleMods();

foreach ($battleModsArr as $battleModArr)
    {
    $battleModName = $terms[$battleModArr[1]];
    $battleMod = new BattleMod ($battleModArr[0], $battleModName, $battleModArr[2], $battleModArr[3], $battleModArr[4], $battleModArr[5]);
    $battleMods[$battleMod->getId()] = $battleMod;
    }

//Saves battleModLinks as $posessorUnit->targetUnit->battleMod applied to it
$battleModLinksArr = $battleModConn->getAllBattleModLinks();
foreach ($battleModLinksArr as $battleModLinkArr)
    {
    $battleModLinks[$battleModLinkArr[2]][$battleMods[$battleModLinkArr[1]]->getTargetClassId()] = $battleMods[$battleModLinkArr[1]];
    }


$unitsArr = $unitConn->getAllUnits();
foreach ($unitsArr as $unitArr)
    {
    $unitName = $terms[$unitArr[1]]->getString();
    $unitDescription = $terms[$unitArr[13]]->getString();
    $unit = new Unit ($unitArr[0], $unitName, $unitArr[2],$unitArr[3],$unitArr[4],$unitArr[5],$unitArr[6],$unitArr[7],$unitArr[8],explode(",", $unitArr[9]),explode(",", $unitArr[10]),explode(",", $unitArr[11]),$unitArr[12],$unitDescription,$unitArr[14],$unitArr[15]);
    if (isset($battleModLinks[$unit->getId()]))
        $unit->setBattleMods($battleModLinks[$unit->getId()]);
    $units[$unit->getId()] = $unit;
    }
$staticData->setUnits($units);


$technologiesArr = $technologyConn->getAllTechnologies();
foreach ($technologiesArr as $technologyArr)
    {
    $techName = $terms[$technologyArr[1]]->getString();
    $techDescription = $terms[$technologyArr[11]]->getString();
    $technology = new Technology ($technologyArr[0], $techName, $technologyArr[2], $technologyArr[3], $technologyArr[4], explode(",", $technologyArr[5]), explode(",", $technologyArr[6]), $technologyArr[7], $technologyArr[8], $technologyArr[9], $technologyArr[10], $techDescription, $technologyArr[12]);
    $technologies[$technology->getId()] = $technology;
    }
$staticData->setTechnologies($technologies);


$productionModsArr = $productionModConn->getAllProductionMods();
$productionModLinks = array();
foreach ($productionModsArr as $productionModArr)
    {
    $productionMod = new ProductionMod ($productionModArr[0], $productionModArr[1], $productionModArr[2], $productionModArr[3], $productionModArr[4], $productionModArr[5], $productionModArr[6]);
    $productionMods[$productionMod->getId()] = $productionMod;
    if ($productionMod->getTargetClassId()=='Building')
        $productionModLinks[$productionMod->getTargetId()][] = $productionMod;
    }


$buildingsArr = $buildingConn->getAllBuildings();
foreach ($buildingsArr as $buildingArr)
    {
    $buildingName = $terms[$buildingArr[1]]->getString();
    $buildingDescription = $terms[$buildingArr[13]]->getString();
    $building = new Building ($buildingArr[0], $buildingName, $buildingArr[2],$buildingArr[3],$buildingArr[4],$buildingArr[5],$buildingArr[6],explode(",",$buildingArr[7]),explode(",",$buildingArr[8]),explode(",",$buildingArr[9]),explode(",",$buildingArr[10]),$buildingArr[11],$buildingArr[12],$buildingDescription,$buildingArr[14],$buildingArr[15]);
    if (isset($productionModLinks[$building->getId()]))
        $building->setProductionMods($productionModLinks[$building->getId()]);
    $buildings[$building->getId()] = $building;
    }
$staticData->setBuildings($buildings);


$playersArr = $playerConn->getAllPlayers();
foreach ($playersArr as $playerArr)
    {
    $player = new Player ($playerArr[0],$playerArr[1],$playerArr[2],$playerArr[3],$playerArr[4],$playerArr[5],$playerArr[6],$playerArr[7],explode(",",$playerArr[8]), $playerArr[9], $playerArr[10]);
    
    $players[$player->getId()] = $player;
    }
$staticData->setPlayers($players);


$resourcesArr = $resourceConn->getAllResources();
foreach ($resourcesArr as $resourceArr)
    {
    $resourceName = $terms[$resourceArr[1]]->getString();
    $resource = new Resource ($resourceArr[0],$resourceName,$resourceArr[2],$resourceArr[3],$resourceArr[4],$resourceArr[5]);
    $resources[$resource->getId()] = $resource;
    }
$staticData->setResources($resources);


$_SESSION['staticData'] = $staticData;
?>