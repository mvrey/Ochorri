<?php
if (isset($absolute_path) && ($absolute_path))
    require_once ($path."DAO/DAO.class.php");
else
    require_once("../../models/DAO/DAO.class.php");

class DivisionMovementDAO extends DAO {

public function getDivisionMovement ($startId=0, $endId=0, $mode=0) {

    // $mode==1 AND, mode==0 OR

    if ($mode==0)
        $operator = " OR ";
    else
        $operator = " AND ";

    $connection = $this->getConnection();
    $query = "SELECT * FROM DivisionMovement WHERE";
    if (!((empty($startId)) XOR (empty($endId))))
        $query .= " divisionMovement_startSectorId=".$startId.$operator."divisionMovement_endSectorId=".$endId;
    elseif (empty($startId))
        $query .= " divisionMovement_endSectorId=".$endId;
    elseif (empty($endId))
        $query .= " divisionMovement_startSectorId=".$startId;

    $result = $connection->Execute ($query);
    return ($result->GetArray());
}

public function insertDivisionMovement ($unitList, $quantityList, $playerId, $startId, $endId, $startDateTime, $time) {

    $connection = $this->getConnection();
    $query = "INSERT INTO DivisionMovement (divisionMovement_unitList, divisionMovement_quantityList, divisionMovement_ownerId, divisionMovement_startSectorId, divisionMovement_endSectorId, divisionMovement_startDateTime, divisionMovement_time)
            VALUES ('".$unitList."', '".$quantityList."', ".$playerId.", ".$startId.", ".$endId.", ".$startDateTime.", ".$time.")";
    $result = $connection->Execute ($query);
    return ($result);
}

public function deleteDivisionMovement ($DivisionMovementId) {

    $connection = $this->getConnection();
    $query = "DELETE FROM DivisionMovement WHERE divisionMovement_id=".$DivisionMovementId;
    $result = $connection->Execute ($query);
    return ($result);
}

}
?>