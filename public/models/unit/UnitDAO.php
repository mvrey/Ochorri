<?php
if (isset($absolute_path) && ($absolute_path))
    require_once ($path."DAO/DAO.class.php");
else
    require_once("../../models/DAO/DAO.class.php");

class UnitDAO extends DAO {

public function getAllUnits () {
    $connection = $this->getConnection();
    $query = "SELECT * FROM Unit";
    $result = $connection->Execute ($query);
    return ($result->GetArray());
}

public function upgradeUnit ($playerId, $unitId, $divisionConn) {

    $connection = $this->getConnection();
    $query = "SELECT unit_upgradesTo FROM Unit WHERE unit_id=".$unitId;
    $rs = $connection->Execute ($query);
    $upgrade = $rs->fields[0];
    if ($upgrade)
        {
        //get sum of unit+upgrade quantity and group the result by sectorId
        $query = "SELECT division_sectorId, SUM(division_quantity) FROM Division
            WHERE division_ownerId=".$playerId." AND division_unitId IN (".$unitId.",".$upgrade.")
                GROUP BY division_sectorId";
        $rs = $connection->Execute ($query);
        while (!$rs->EOF)
            {
            $sectorId = $rs->fields[0];
            $sum = $rs->fields[1];
            $existsUpgrade = $divisionConn->getDivisionsExists($playerId, $sectorId, array($upgrade), 0);
            if ($existsUpgrade)
                {
                //Update upgraded units quantity change
                $divisionConn->updateDivision($playerId, $sectorId, $upgrade, "0+".$sum, '*', 0);
                //Set obsolete units quantity to 0
                $divisionConn->updateDivision($playerId, $sectorId, $unitId, 0, '*', 0);
                //Purge all empty divisions
                $divisionConn->purgeDivisions();
                }
            else
                {
                $query = "UPDATE Division set division_unitId=".$upgrade."
                    WHERE division_unitId=".$unitId." AND division_sectorId=".$sectorId." AND division_ownerId=".$playerId;
                $result = $connection->Execute ($query);
                }
            $rs->MoveNext();
            }
        }
    return;
}

}
?>