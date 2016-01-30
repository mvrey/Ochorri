<?php
if (isset($absolute_path) && ($absolute_path))
    require_once ($path."DAO/DAO.class.php");
else
    require_once("../../models/DAO/DAO.class.php");

class SectorDAO extends DAO {

public function getMapDimensions () {

    $connection = $this->getConnection();
    $query = "SELECT MAX(sector_coordinateX), MAX(sector_coordinateY) FROM Sector";
    $result = $connection->Execute ($query);
    return ($result->fields);
}

public function getAllSectors() {
    $connection = $this->getConnection();
    $query = "SELECT * FROM Sector ORDER BY sector_coordinateX, sector_coordinateY";
    $result = $connection->Execute ($query);
    return ($result->GetArray());
    }

public function getSectorById($id) {
    $connection = $this->getConnection();
    $query = "select * from Sector where sector_id=".$id;
    $result = $connection->Execute ($query);
    return ($result->fields);
}

public function getSectorByCoordinates($coordinateX, $coordinateY) {
    $connection = $this->getConnection();
    $query = "select * from Sector where sector_coordinateX=".$coordinateX." AND sector_coordinateY=".$coordinateY;
    $result = $connection->Execute ($query);
    return ($result->fields);
}

public function getBattleBySectorId ($sectorId) {

    $connection = $this->getConnection();
    $query = "SELECT * FROM Battle WHERE battle_sectorId=".$sectorId." ORDER BY battle_isOver";
    $result = $connection->Execute ($query);
    return ($result->fields);
}

public function getSectorBuildings($sectorId) {

    $connection = $this->getConnection();
    $query = "SELECT * FROM Building WHERE building_sectorId=".$sectorId;
    $result = $connection->Execute ($query);
    return ($result->GetArray());
}


public function getBuildingExists($sectorId, $buildingId) {

    $connection = $this->getConnection();
    $query = "SELECT DISTINCT TRUE FROM Building WHERE EXISTS
        (SELECT * FROM Building WHERE building_buildingClassId=".$buildingId." AND building_sectorId=".$sectorId.")";
    $result = $connection->Execute ($query);
    if ($result)
        return (true);
    else
        return (false);
}

public function getAvailableBuildings ($sectorId) {

    global $player;

    $connection = $this->getConnection();
    $query = "SELECT x.*, y.building_level, y.building_dateStarted, y.building_dateStopped FROM (
                SELECT *
                FROM BuildingClass
                WHERE buildingClass_startAge<=".$player->getAge()."
                    AND buildingClass_endAge>".$player->getAge()."
                    AND buildingClass_id NOT IN (
                        SELECT requirement_targetId FROM Requirement WHERE requirement_targetClassId='Building')
                UNION
                SELECT buildingClass_id, buildingClass_nameId, buildingClass_pictureURL, buildingClass_health, buildingClass_startAge, buildingClass_endAge, buildingClass_upgradable, buildingClass_productionCost, buildingClass_incrementCost, buildingClass_manteinanceCost, buildingClass_advanceCost, buildingClass_time, buildingClass_incrementTime, buildingClass_description, buildingClass_upgradesTo, buildingClass_autoUpgrade
                FROM (
                    SELECT a.*, b.requirement_requirementId, b.requirement_requirementClass, b.requirement_level
                        FROM BuildingClass a INNER JOIN Requirement b ON ((b.requirement_targetId=a.buildingClass_id) AND (b.requirement_targetClassId='Building'))) e
                INNER JOIN
                    (SELECT d.technologyLink_technologyId, d.technologyLink_level
                        FROM Player c INNER JOIN TechnologyLink d ON (c.player_id=d.technologyLink_playerId)
                        WHERE c.player_id=".$player->getId().") f
                    ON ((e.requirement_requirementId=f.technologyLink_technologyId) AND (e.requirement_level=f.technologyLink_level))) x
                LEFT JOIN
                    (SELECT * FROM Building WHERE building_sectorId=".$sectorId.") y
                ON x.buildingClass_id=y.building_buildingClassId
            ORDER BY 1";
    $result = $connection->Execute ($query);
    return ($result->GetArray());
}

public function updateSector($sectorId, $occupantId, $ownerId, $isBattle) {

    $connection = $this->getConnection();
    $query = "UPDATE Sector SET ";
    if ($occupantId)
        $query .= "sector_occupantId=".$occupantId.", ";
    if ($ownerId)
        $query .= "sector_ownerId=".$ownerId.", ";
    if ($isBattle)
        $isBattle = "true";
    else
        $isBattle = "false";
    $query .= "sector_isBattle=".$isBattle." WHERE sector_id=".$sectorId;

    $result = $connection->Execute ($query);

    return ($result);
}

public function insertBuilding($buildingId, $sectorId, $date) {

    $connection = $this->getConnection();
    $query = "INSERT INTO Building (building_buildingClassId, building_sectorId, building_level, building_dateStarted)
            VALUES (".$buildingId.", ".$sectorId.", 0, ".$date.")";
    $result = $connection->Execute ($query);
    return ($result);
}

public function insertSector ($x, $y, $name, $isLand, $productions, $spends, $baseProductions) {

    $connection = $this->getConnection();
    $query = "INSERT INTO Sector (sector_coordinateX, sector_coordinateY, sector_name, sector_isLand, sector_productionId, sector_CostId, sector_productionBase)
        values (".$x.",".$y.", '".$name."', ".$isLand.", '".$productions."', '".$spends."', '".$baseProductions."');";
    $result = $connection->Execute ($query);
    return ($result);
}

public function updateSectorCosts ($sectorId, $newCosts = Array(), $mode=1, $operator='+') {
    //mode=0 substitute; mode=1 operate with existent
    $connection = $this->getConnection();
    if ($mode==1)
        {
        $query = "SELECT sector_CostId FROM Sector WHERE sector_id=".$sectorId;
        $result = $connection->Execute ($query);
        $costs = explode(",", $result->fields[0]);

        if ($operator=='+')
            {
            for($i=0; $i<count($costs); $i++)
                {
                $costs[$i] = $costs[$i]+$newCosts[$i];
                }
            }
        elseif ($operator=='-')
            {
            for($i=0; $i<count($costs); $i++)
                {
                $costs[$i] = $costs[$i]-$newCosts[$i];
                }
            }
        }
    else
        $costs = $newCosts;

    foreach ($costs as $index=>$cost)
        $costs[$index] = round($cost, 6);

    $query = "UPDATE Sector SET sector_CostId='".implode(",", $costs)."' WHERE sector_id=".$sectorId;
    $result = $connection->Execute ($query);
    return ($result);
}


public function updateSectorProductions ($sectorId, $newProductions = Array(), $mode=1, $operator='+')
{
    //mode=0 replace; mode=1 operate with existent
    $connection = $this->getConnection();
    if ($mode==1)
        {
        $query = "SELECT sector_productionId FROM Sector WHERE sector_id=".$sectorId;
        $result = $connection->Execute ($query);
        $productions = explode(",", $result->fields[0]);

        if ($operator=='+')
            {
            for($i=0; $i<count($productions); $i++)
                {
                $productions[$i] = $productions[$i]+$newProductions[$i];
                }
            }
        elseif ($operator=='-')
            {
            for($i=0; $i<count($productions); $i++)
                {
                $productions[$i] = $productions[$i]-$newProductions[$i];
                }
            }
        }
    else
        $productions = $newProductions;

    foreach ($productions as $index=>$production)
        $productions[$index] = round($production, 6);

    $query = "UPDATE Sector SET sector_productionId='".implode(",", $productions)."' WHERE sector_id=".$sectorId;
    $result = $connection->Execute ($query);
    return ($result);
}


public function updateSectorProductionsByNewBuilding($buildingId, $sectorId) {

    $connection = $this->getConnection();
    $query = "select ProductionMod_resourceId, ProductionMod_operation, ProductionMod_value from ProductionMod WHERE productionMod_targetClassId='Building' AND productionMod_targetId=".$buildingId;
    $rs = $connection->Execute ($query);

    $query = "SELECT sector_productionId FROM Sector WHERE sector_id=".$sectorId;
    $result = $connection->Execute ($query);
    $incomes = explode(",", $result->fields[0]);

    switch ($rs->fields[1])
        {
        case "*":   $incomes[$rs->fields[0]-1] = $incomes[$rs->fields[0]-1]*$rs->fields[2];
                    break;
        }

    foreach ($incomes as $index=>$income)
        $incomes[$index] = round($income, 6);

    $query = "UPDATE Sector SET sector_productionId='".implode(",", $incomes)."' WHERE sector_id=".$sectorId;
    $result = $connection->Execute ($query);
    return ($result);
}

public function getCapitolSector($playerId) {

    $connection = $this->getConnection();
    $query = "SELECT a.* FROM Sector a INNER JOIN Building b
        ON b.building_buildingClassId=0 AND b.building_level>0 AND b.building_sectorId=a.sector_id AND a.sector_ownerId=".$playerId;
    $result = $connection->Execute ($query);
    if ($result)
        return ($result->fields);
    else
        return (false);
}

public function getUnitQueueLists($coordinateX, $coordinateY, $playerId) {

    $connection = $this->getConnection();
    $query = "SELECT TrainingQueue_unitList, TrainingQueue_timeList, TrainingQueue_startDateTime
            FROM TrainingQueue
            WHERE TrainingQueue_sectorCoordinateX =".$coordinateX
            ." AND TrainingQueue_sectorCoordinateY =".$coordinateY
            ." AND TrainingQueue_ownerId =".$playerId;
    $result = $connection->Execute ($query);
    return ($result->fields);
}

public function updateUnitQueue($coordinateX, $coordinateY, $playerId, $unitList, $timeList, $startTime=-1) {

    if ($startTime<0) $timeSentence="";
    else $timeSentence=", trainingQueue_startDateTime=".$startTime;

    $connection = $this->getConnection();
    $query = "UPDATE TrainingQueue SET TrainingQueue_unitList='".$unitList."', TrainingQueue_timeList='".$timeList."'".$timeSentence
                ." WHERE ((TrainingQueue_sectorCoordinateX=".$coordinateX.") AND (TrainingQueue_sectorCoordinateY=".$coordinateY.") AND (TrainingQueue_ownerId=".$playerId."))";
    $result = $connection->Execute ($query);
    return ($result);
}

public function insertUnitQueue($coordinateX, $coordinateY, $playerId, $unitList, $timeList, $date) {

    $connection = $this->getConnection();
    $query = "INSERT INTO TrainingQueue (TrainingQueue_sectorCoordinateX, TrainingQueue_sectorCoordinateY, trainingQueue_ownerId, trainingQueue_unitList, TrainingQueue_timeList, TrainingQueue_startDateTime)
                VALUES (".$coordinateX.",".$coordinateY.",".$playerId.",'".$unitList."','".$timeList."','".$date."')";
    $result = $connection->Execute ($query);
    return ($result);
}

public function deleteUnitQueue($coordinateX, $coordinateY, $playerId) {

    $connection = $this->getConnection();
    $query = "DELETE FROM TrainingQueue
            WHERE TrainingQueue_sectorCoordinateX=".$coordinateX." AND TrainingQueue_sectorCoordinateY=".$coordinateY." AND TrainingQueue_ownerId=".$playerId;
    $result = $connection->Execute ($query);
    return ($result);
}

}
?>