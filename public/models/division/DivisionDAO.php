<?php
if (isset($absolute_path) && ($absolute_path))
    require_once ($path."DAO/DAO.class.php");
else
    require_once("../../models/DAO/DAO.class.php");

class DivisionDAO extends DAO {

public function getOwnDivisionsBySector ($coordinateX, $coordinateY) {

    global $player;

    $connection = $this->getConnection();
    $query = "SELECT a.*
            FROM Division a INNER JOIN Sector b
            ON a.division_sectorId=b.sector_id
            WHERE a.division_ownerId=".$player->getId()."
            AND b.sector_coordinateX=".$coordinateX." AND b.sector_coordinateY=".$coordinateY."
            ORDER BY b.sector_id, a.division_unitId";
    $result = $connection->Execute ($query);
    return ($result->GetArray());
}

public function getDivisionExists ($playerId, $sectorId, $unitId) {

    $connection = $this->getConnection();
    $query = "SELECT division_id FROM Division
            WHERE division_ownerId=".$playerId." AND division_sectorId=".$sectorId." AND division_unitId=".$unitId;
    $result = $connection->Execute ($query);

    if ($result->RecordCount()>0)
        return true;
    else
        return false;
}

public function getDivisionsExists($playerId, $startId, $unitList, $quantityList){

    $connection = $this->getConnection();
    $query = "";
    while (count($unitList)>0)
        {
        if (!is_array($quantityList))
            $quantity=0;
        else
            $quantity = array_shift($quantityList);

        if (($quantity==0) && (is_array($quantityList)))
            array_shift($unitList);
        else
            {
            if (!empty($query))
                $query .= " UNION ";
            $query .= "SELECT exists (SELECT * from Division WHERE division_ownerId=".$playerId;
            if ($startId)
                $query .= " AND division_sectorId=".$startId;
            $query .= " AND ((division_unitId=".array_shift($unitList).") AND (division_quantity>=".$quantity.")))";
            }
        }

    $result = $connection->Execute ($query);
    if (($result->RecordCount()==1) && ($result->fields[0]))
        return (true);
    else
        return (false);
}

public function updateDivision ($playerId, $sectorId, $unitId, $number, $operation='+', $remainingHealth=0) {

    $connection = $this->getConnection();
    if (!(($playerId) && ($unitId)))
        $query = "UPDATE Division
            SET division_quantity=division_quantity".$operation.$number.", division_remainingHealth=".$remainingHealth."
            WHERE division_sectorId=".$sectorId;
    else
        $query = "UPDATE Division
            SET division_quantity=division_quantity".$operation.$number.", division_remainingHealth=".$remainingHealth."
            WHERE division_ownerId=".$playerId." AND division_sectorId=".$sectorId." AND division_unitId=".$unitId;
    $result = $connection->Execute ($query);
    return ($result);
}

public function insertDivision ($playerId, $sectorId, $unitId, $number, $operation='+') {

    $connection = $this->getConnection();
    $query = "INSERT INTO Division (division_ownerId, division_sectorId, division_unitId, division_quantity)
            VALUES (".$playerId.", ".$sectorId.", ".$unitId.", ".$number.")";
    $result = $connection->Execute ($query);
    return ($result);
}

public function getDivisionsBySector ($sectorId) {

    global $player;

    $connection = $this->getConnection();
    $query = "SELECT * FROM Division WHERE division_sectorId=".$sectorId." ORDER BY division_ownerId, division_unitId";
    $result = $connection->Execute ($query);
    return ($result->GetArray());
}

public function purgeDivisions () {

    $connection = $this->getConnection();
    $query = "DELETE FROM Division WHERE division_quantity<=0";
    $result = $connection->Execute ($query);
    return ($result);
}

}
?>