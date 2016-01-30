<?php
if (isset($absolute_path) && ($absolute_path))
    require_once ($path."DAO/DAO.class.php");
else
    require_once("../../models/DAO/DAO.class.php");

class BatchDAO extends DAO {

public function getStartedTasks () {

    $connection = $this->getConnection();
    $query = "SELECT battle_id as id, 'Battle' as tableName, battle_lastUpdate as startTime, NULL as endTime
            FROM Battle
            WHERE battle_isOver=0
        UNION
            SELECT building_id as id, 'Building' as tableName, building_dateStarted as startTime, NULL as endTime
            FROM Building
            WHERE building_dateStarted>0 and building_dateStarted IS NOT NULL AND building_dateStopped IS NULL
        UNION
            SELECT divisionMovement_id as id, 'DivisionMovement' as tableName, divisionMovement_startDateTime as startTime, divisionMovement_startDateTime+divisionMovement_time as endTime
            FROM DivisionMovement
            WHERE divisionMovement_startDateTime>0 AND divisionMovement_startDateTime IS NOT NULL
        UNION
            SELECT technologyLink_id as id, 'TechnologyLink' as tableName, technologyLink_dateStartProgress as startTime, technologyLink_dateEndProgress as endTime
            FROM TechnologyLink
            WHERE technologyLink_dateStartProgress>0 AND technologyLink_dateStartProgress IS NOT NULL
        UNION
            SELECT trainingQueue_id as id, 'TrainingQueue' as tableName, trainingQueue_startDateTime as startTime, trainingQueue_timeList as endTime
            FROM TrainingQueue
            WHERE trainingQueue_startDateTime>0 AND trainingQueue_startDateTime IS NOT NULL";
    $result = $connection->Execute ($query);

    return ($result->GetArray());
    }

public function getStartedBuilding ($id) {

    $connection = $this->getConnection();
    $query = "SELECT * FROM Player y INNER JOIN (SELECT * FROM BuildingClass a INNER JOIN
                ( SELECT * FROM Building b INNER JOIN Sector d ON b.building_sectorId=d.sector_id WHERE b.building_id=".$id." ) c
                ON a.buildingClass_id=c.building_BuildingClassId) z ON y.player_id=z.sector_ownerId";
    $result = $connection->Execute ($query);
    return ($result->fields);
    }

public function getStartedDivisionMovement ($id) {

    $connection = $this->getConnection();
    $query = "SELECT * FROM DivisionMovement a INNER JOIN Sector b ON a.divisionMovement_startSectorId=b.sector_id WHERE divisionMovement_id=".$id."
            UNION
            SELECT * FROM DivisionMovement a INNER JOIN Sector b ON a.divisionMovement_endSectorId=b.sector_id WHERE divisionMovement_id=".$id;
    $result = $connection->Execute ($query);
    return ($result->GetArray());
    }

public function getStartedTrainingQueue ($id) {

    $connection = $this->getConnection();
    $query = "SELECT * FROM Player a
            INNER JOIN
            (SELECT * FROM Sector b INNER JOIN TrainingQueue c
            ON c.trainingQueue_sectorCoordinateX=b.sector_coordinateX
            AND c.trainingQueue_sectorCoordinateY=b.sector_coordinateY
            AND c.trainingQueue_id=".$id.") d
            ON d.sector_ownerId=a.player_id";
    $result = $connection->Execute ($query);
    return ($result->fields);
    }

public function getStartedTechnologyLink ($id) {

    $connection = $this->getConnection();
    $query = "SELECT * FROM Technology c INNER JOIN
            (SELECT * FROM TechnologyLink a INNER JOIN Player b
            ON technologyLink_playerId=b.player_id WHERE technologyLink_id=".$id.") d
            ON c.technology_id=d.technologyLink_technologyId";
    $result = $connection->Execute ($query);
    return ($result->fields);
    }

public function getStartedBattle ($id) {

    $connection = $this->getConnection();
    $query = "SELECT * FROM Battle a INNER JOIN Sector b ON a.battle_sectorId=b.sector_id WHERE a.battle_id=".$id;
    $result = $connection->Execute ($query);
    return ($result->fields);
    }

public function resetSectorProductions ($playerId) {

    $connection = $this->getConnection();
    $query = "UPDATE Sector set sector_productionId=sector_productionBase WHERE sector_occupantId=".$playerId;
    $result = $connection->Execute ($query);
    return ($result);
    }

public function getProductionMods ($playerId) {

    $connection = $this->getConnection();
    $query = "SELECT * FROM Sector a
        INNER JOIN
            (SELECT * FROM Building d
            INNER JOIN
                (SELECT * FROM BuildingClass b INNER JOIN ProductionMod c ON c.productionMod_targetId=b.buildingClass_id
                WHERE productionMod_targetClassId='Building') e
            ON d.building_buildingClassId=e.buildingClass_id) f
        ON ((a.sector_id=f.building_sectorId) AND (a.sector_occupantId=".$playerId."))
        WHERE f.building_dateStarted is NULL";
    $result = $connection->Execute ($query);
    return ($result->GetArray());
    }

public function resetSectorCosts ($playerId) {

    $connection = $this->getConnection();
    $query = "UPDATE Sector set sector_CostId='0,0,0,0,0' WHERE sector_occupantId=".$playerId;
    $result = $connection->Execute ($query);
    return ($result);
    }

public function resetBattleCosts ($playerId) {

    $connection = $this->getConnection();
    $query = "UPDATE BattleCosts set battleCosts_costs='0,0,0,0,0' WHERE battleCosts_ownerId=".$playerId;
    $result = $connection->Execute ($query);
    return ($result);
    }

//This extracts building and unit manteinances, as well as battleCosts.
//When concept==BattleCosts, sector_id actually refers to battle_id
public function getManteinances ($playerId) {

    $connection = $this->getConnection();
    $query = "SELECT a.sector_id, d.buildingClass_manteinanceCost as manteinanceCost, d.building_level as multiplier, d.concept, NULL as battle_id FROM Sector a
        INNER JOIN
        (SELECT 'Building' as concept, b.*, c.* FROM BuildingClass b INNER JOIN Building c ON b.buildingClass_id=c.building_buildingClassId
            WHERE b.buildingClass_manteinanceCost IS NOT NULL) d
        ON ((a.sector_id=d.building_sectorId) AND (a.sector_occupantId=".$playerId."))
        UNION ALL
        SELECT a.sector_id, d.unit_manteinanceCost, d.division_quantity, 'Division' as concept, NULL as battle_id FROM Sector a
        INNER JOIN
        (SELECT * FROM Unit b INNER JOIN Division c ON b.unit_id=c.division_unitId) d
        ON ((a.sector_id=d.division_sectorId) AND (a.sector_occupantId=".$playerId."))

        UNION ALL
        SELECT a.battle_sectorId as sector_id, d.unit_manteinanceCost, d.division_quantity, 'BattleCosts' as concept, a.battle_id FROM Battle a
        INNER JOIN
        (SELECT * FROM Unit b INNER JOIN Division c ON b.unit_id=c.division_unitId AND c.division_ownerId=".$playerId.") d
        ON d.division_sectorId=a.battle_sectorId AND a.battle_isOver=false AND a.battle_attackerId=".$playerId;
    $result = $connection->Execute ($query);
    return ($result->GetArray());
    }

}
?>