<?php
if (isset($absolute_path) && ($absolute_path))
    require_once ($path."DAO/DAO.class.php");
else
    require_once("../../models/DAO/DAO.class.php");

class TechnologyDAO extends DAO {

public function getAllTechnologies() {
    $connection = $this->getConnection();
    $query = "select * from Technology";
    $result = $connection->Execute ($query);
    return ($result->GetArray());
    }

public function insertTechnologyLink ($technologyId, $playerId, $startTime, $endTime) {

    $connection = $this->getConnection();
    $query = "INSERT INTO TechnologyLink (technologyLink_technologyId, technologyLink_playerId, technologyLink_dateStartProgress, technologyLink_dateEndProgress)
            VALUES (".$technologyId.", ".$playerId.", ".$startTime.", ".$endTime.")";
    $result = $connection->Execute ($query);
    return ($result);
}

public function updateTechnologyLink ($technologyId, $playerId, $startTime, $endTime, $operator='+', $level=null, $progress=null) {

    $connection = $this->getConnection();
    $query = "UPDATE TechnologyLink SET technologyLink_dateStartProgress=technologyLink_dateStartProgress".$operator.$startTime.",
        technologyLink_dateEndProgress=technologyLink_dateEndProgress".$operator.$endTime;
    if (!($level===null) && !($progress===null))
        $query .= ",technologyLink_level=".$level.", technologyLink_progress=".$progress;
    $query .= " WHERE technologyLink_technologyId=".$technologyId." AND technologyLink_playerId=".$playerId;
    $result = $connection->Execute ($query);
    return ($result);
}

}
?>