<?php
echo $_SERVER['REQUEST_TIME'];
//HOME = ("/home/blacloud/htdocs/");
define ("HOME", "/opt/lampp/htdocs/ochmvc/public/");
$MPATH = HOME."models/";

$absolute_path=true;
$_SESSION['language'] = "spanish";
require_once (HOME."lib/inclusion.php");
require_once (HOME."config/paths.php");
require_once (HOME.'config/sector.cfg.php');
require_once ($MPATH."DAO/DAO.class.php");

require_once_model ('Sector', $MPATH, $absolute_path);
require_once_model ('Building', $MPATH, $absolute_path);
require_once_model ('Unit', $MPATH, $absolute_path);
require_once_model ('Division', $MPATH, $absolute_path);
require_once_model ('DivisionMovement', $MPATH, $absolute_path);
require_once_model ('Technology', $MPATH, $absolute_path);
require_once_model ('Player', $MPATH, $absolute_path);
require_once_model ('Battle', $MPATH, $absolute_path);
require_once_model ('BattleRound', $MPATH, $absolute_path);
require_once_model ('Message', $MPATH, $absolute_path);
require_once_model ('Resource', $MPATH, $absolute_path);
require_once_model ('Term', $MPATH, $absolute_path);
require_once_model ('StaticData', $MPATH, $absolute_path);
require_once_model ('Batch', $MPATH, $absolute_path);
require_once_model ('ProductionMod', $MPATH, $absolute_path);
require_once_model ('BattleMod', $MPATH, $absolute_path);

$sectorConn = new SectorDAO();
$buildingConn = new BuildingDAO();
$unitConn = new UnitDAO();
$divisionConn = new DivisionDAO();
$divisionMovementConn = new DivisionMovementDAO();
$technologyConn = new TechnologyDAO();
$playerConn = new PlayerDAO();
$battleConn = new BattleDAO();
$battleRoundConn = new BattleRoundDAO();
$messageConn = new MessageDAO();
$resourceConn = new ResourceDAO();
$termConn = new TermDAO();
$batchConn = new BatchDAO();

/*Battle
 * Building
 * DivisionMovement
 * TechnologyLink
 * TrainingQueue
 */

//Extraemos de todas el id de tabla, tiempo inicial y tiempo final, teniendo a la salida tableId, startTime, endTime
$startedTasks = $batchConn->getStartedTasks();

foreach ($startedTasks as $startedTask)
    {
    $now = $_SERVER['REQUEST_TIME'];
    $id = $startedTask['id'];
    $tableName = $startedTask['tableName'];
    $startTime = $startedTask['startTime'];
    $endTime = $startedTask['endTime'];

    switch ($tableName)
        {
        case 'Battle':  //FINALMENTE VA, INEFICIENTE PERO FUNCIONAL
            {
            $startedBattle = $batchConn->getStartedBattle($id);

            if (!(isset($allPlayers)))
                {
                require_once (HOME.'controllers/StaticData/initStaticData.php');
                $allPlayers = $staticData->getPlayers();
                }
                
            require_once (HOME.'config/battle.cfg.php');
            if ($now>=$startTime+$round_time/1000)
                {
                $player = $allPlayers[$startedBattle['battle_attackerId']];
                $playerId = $startedBattle['battle_attackerId'];
                $_POST['coordinateX'] = $startedBattle['sector_coordinateX'];
                $_POST['coordinateY'] = $startedBattle['sector_coordinateY'];
                require (HOME.'controllers/batch/update_battle.php');
                }
            }
            break;
        case 'Building':    //AHORA SI VA COMO $DEITY MANDA
            echo "pasando por el cronned parte de edificios";
            $startedBuilding = $batchConn->getStartedBuilding($id);
        
            $time = $startedBuilding['buildingClass_time']*(pow(1+$startedBuilding['buildingClass_incrementTime'],$startedBuilding['building_level']));

            if ($now>=$startTime+$time)
                {
                $playerId = $startedBuilding['player_id'];
                $coordinateX = $startedBuilding['sector_coordinateX'];
                $coordinateY = $startedBuilding['sector_coordinateY'];
                $sectorName = $startedBuilding['sector_name'];
                $buildingId = $startedBuilding['buildingClass_id'];
                $buildingUpgradable = $startedBuilding['buildingClass_upgradable'];
                $buildingLevel = $startedBuilding['building_level'];
                $sectorId = $startedBuilding['sector_id'];
                $buildingManteinanceCost = explode(",", $startedBuilding['buildingClass_manteinanceCost']);
                require (HOME."controllers/batch/update_building.php");
                }
            break;
        case 'DivisionMovement':    //PARECE QUE AHORA VA BIEN
            if ($now>=$endTime)
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

                $startedDivisionMovements = $batchConn->getStartedDivisionMovement($id);
                $startedDivisionMovement = $startedDivisionMovements[0];

                $divisionMovementId = $id;
                $playerId = $startedDivisionMovement['divisionMovement_ownerId'];
                $startSectorId = $startedDivisionMovement['sector_id'];
                $startSectorName = $startedDivisionMovement['sector_name'];
                $startSectorX = $startedDivisionMovement['sector_coordinateX'];
                $startSectorY = $startedDivisionMovement['sector_coordinateY'];
                $startSectorOwnerId = $startedDivisionMovement['sector_ownerId'];
                
                $startedDivisionMovement = $startedDivisionMovements[1];

                $endSectorId = $startedDivisionMovement['sector_id'];
                $endSectorName = $startedDivisionMovement['sector_name'];
                $endSectorX = $startedDivisionMovement['sector_coordinateX'];
                $endSectorY = $startedDivisionMovement['sector_coordinateY'];
                $endSectorOwnerId = $startedDivisionMovement['sector_ownerId'];
                $endSectorIsBattle = $startedDivisionMovement['sector_isBattle'];

                require (HOME."controllers/batch/update_divisionMovement.php");
                }
            break;
        case 'TechnologyLink':  //PARECE QUE AL FIN VA
            echo "<br>".$now."<br>".$endTime;
            if ($now>=$endTime)
                {
                $startedTechnologyLink = $batchConn->getStartedTechnologyLink($id);

                $playerId = $startedTechnologyLink['player_id'];
                $techId = $startedTechnologyLink['technology_id'];
                $techName = $startedTechnologyLink['technology_nameId'];
                $techUpgradable = $startedTechnologyLink['technology_upgradable'];
                $techTime = $startedTechnologyLink['technology_time'];
                $techIncrementTime = $startedTechnologyLink['technology_incrementTime'];
                $techProgress = $startedTechnologyLink['technologyLink_progress'];
                $techLevel = $startedTechnologyLink['technologyLink_level'];
                $techIsAge = $startedTechnologyLink['technology_isAge'];
                $techEndAge = $startedTechnologyLink['technology_endAge'];

                require (HOME.'controllers/batch/update_technologyLink.php');
                }
            break;
        case 'TrainingQueue':   //BIEN BIEN
            $times = explode(",", $endTime);

            if ($now>=$startTime+$times[0])
                {
                $startedTrainingQueue = $batchConn->getStartedTrainingQueue($id);

                $playerId = $startedTrainingQueue['trainingQueue_ownerId'];
                $sectorId = $startedTrainingQueue['sector_id'];
                $coordinateX = $startedTrainingQueue['sector_coordinateX'];
                $coordinateY = $startedTrainingQueue['sector_coordinateY'];
                $sectorName = $startedTrainingQueue['sector_name'];
                require (HOME.'controllers/batch/update_trainingQueue.php');
                }
            break;
        }
    }
?>