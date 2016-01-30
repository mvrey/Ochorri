<?php
if (isset($absolute_path) && ($absolute_path))
    require_once ($path."DAO/DAO.class.php");
else
    require_once("../../models/DAO/DAO.class.php");

class BuildingDAO extends DAO {

public function getAllBuildings () {
    $connection = $this->getConnection();
    $query = "SELECT * FROM BuildingClass ORDER BY buildingClass_id";
    $result = $connection->Execute ($query);
    return ($result->GetArray());
}

//Used on raising building level and start building construction
public function updateBuilding($mode, $buildingId, $sectorId) {
/*$mode = 0 -> new dateStarted
  $mode = 1 -> level+1 */
    $connection = $this->getConnection();
    if ($mode==0)
        {
        $query = "SELECT * FROM Building WHERE building_buildingClassId=".$buildingId." AND building_sectorId=".$sectorId;
        $result = $connection->Execute ($query);
        $buildingArr = $result->fields;
        $dateStarted = $buildingArr[4];
        $dateStopped = $buildingArr[5];
        if (!$dateStopped)
            $time = $_SERVER["REQUEST_TIME"];
        else
            $time = $_SERVER["REQUEST_TIME"] - ($dateStopped-$dateStarted);
        $query = "UPDATE Building SET building_dateStarted=".$time.", building_dateStopped=NULL WHERE building_buildingClassId=".$buildingId." AND building_sectorId=".$sectorId;
        }
    elseif ($mode==1)
        $query = "UPDATE Building SET building_level=building_level+1, building_dateStarted=NULL, 
                    building_remainingHealth=(SELECT BuildingClass_health FROM BuildingClass WHERE buildingClass_id=".$buildingId.")
                WHERE building_buildingClassId=".$buildingId." AND building_sectorId=".$sectorId;
    $result = $connection->Execute ($query);
    return ($result);
}

//Used on setting new remainingHealth and level to a building
public function updateDamagedBuilding($buildingId, $sectorId, $remainingHealth, $level, $dateStarted=null, $dateStopped=null) {

    $connection = $this->getConnection();
    $query = "UPDATE Building SET building_level=".$level.",
                building_remainingHealth=".$remainingHealth;
    if ($dateStarted && $dateStopped)
        $query .= ", building_dateStarted=".$dateStarted.", building_dateStopped=".$dateStopped;
    $query .= " WHERE building_buildingClassId=".$buildingId." AND building_sectorId=".$sectorId;
    $result = $connection->Execute ($query);
    return ($result);
}


public function pauseBuilding($sectorId, $buildingId=NULL) {

    $now = $_SERVER["REQUEST_TIME"];

    $connection = $this->getConnection();
    $query = "SELECT * FROM Building a INNER JOIN BuildingClass b
        ON a.building_buildingClassId=b.buildingClass_id
        WHERE building_sectorId=".$sectorId;
    if ($buildingId!=NULL)
        $query .= " AND a.building_buildingClassId=".$buildingId;
    $result = $connection->Execute ($query);
    $buildingsArr = $result->GetArray();

    foreach ($buildingsArr as $buildingArr)
        {
        $realTime = $buildingArr['buildingClass_time']*(pow(1+$buildingArr['buildingClass_incrementTime'], $buildingArr['building_level']));
        $query = "UPDATE Building SET building_dateStopped=".$now
        .", building_remainingHealth=((".$now."-building_dateStarted)/".$realTime.")*".$buildingArr['buildingClass_health']."
            WHERE building_dateStarted IS NOT NULL AND building_dateStopped IS NULL AND building_sectorId=".$sectorId;
        if ($buildingId!=NULL)
            $query .= " AND building_buildingClassId=".$buildingId;
        $result = $connection->Execute ($query);
        }
    
    return ($result);
}

public function deleteBuilding ($sectorId, $buildingId) {

    $connection = $this->getConnection();
    $query = "DELETE FROM Building
        WHERE building_sectorId=".$sectorId." AND building_BuildingClassId=".$buildingId;
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

public function upgradeBuilding ($playerId, $buildingId) {

    $connection = $this->getConnection();
    $query = "SELECT buildingClass_upgradesTo FROM BuildingClass WHERE buildingClass_id=".$buildingId;
    $rs = $connection->Execute ($query);
    $upgrade = $rs->fields[0];
    if ($upgrade)
        {
        $query = "UPDATE Building set building_BuildingClassId=".$upgrade."
            WHERE building_BuildingClassId=".$buildingId." AND building_sectorId IN
        (SELECT sector_id FROM Sector WHERE sector_ownerId=".$playerId.")";
        $result = $connection->Execute ($query);
        }

    return;
}

}
?>