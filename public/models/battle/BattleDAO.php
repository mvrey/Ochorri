<?php
if (isset($absolute_path) && ($absolute_path))
    require_once ($path."DAO/DAO.class.php");
else
    require_once("../../models/DAO/DAO.class.php");

class BattleDAO extends DAO {

public function insertBattle ($sectorId, $lastUpdate, $attackerId, $defenderId) {

    $connection = $this->getConnection();
    $query = "INSERT INTO Battle (battle_sectorId, battle_lastUpdate, battle_isOver, battle_attackerId, battle_defenderId)
        VALUES (".$sectorId.", ".$lastUpdate.", false, ".$attackerId.", ".$defenderId.")";
    $result = $connection->Execute ($query);
    return ($result);
}

public function updateBattle($battleId, $lastUpdate, $isOver=false) {

    $connection = $this->getConnection();
    if ($isOver)
        $isOver = "true";
    else
        $isOver = "false";
    $query = "UPDATE Battle SET battle_lastUpdate=".$lastUpdate.", battle_isOver=".$isOver." WHERE battle_id=".$battleId;
    $result = $connection->Execute ($query);
    return ($result);
}

public function getBattleBySectorId ($sectorId) {

    $connection = $this->getConnection();
    $query = "SELECT * FROM Battle WHERE battle_sectorId=".$sectorId." ORDER BY battle_isOver";
    $result = $connection->Execute ($query);
    return ($result->fields);
}

public function insertBattleCosts ($battleId, $playerId, $costs) {

    $connection = $this->getConnection();
    $query = "INSERT INTO BattleCosts (battleCosts_battleId, battleCosts_ownerId, battleCosts_costs)
        VALUES (".$battleId.", ".$playerId." ,'".$costs."')";
    $result = $connection->Execute ($query);
    return ($result);
}

public function updateBattleCosts ($battleId, $playerId, $newCosts = array(), $mode=1, $operator='+')
{
    //mode=0 substitute; mode=1 operate with existent
    $connection = $this->getConnection();
    if ($mode==1)
        {
        $query = "SELECT battleCosts_costs FROM BattleCosts
            WHERE battleCosts_battleId=".$battleId." AND battleCosts_ownerId=".$playerId;
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

    $query = "UPDATE BattleCosts SET battleCosts_costs='".implode(",", $costs)."'
        WHERE battleCosts_battleId=".$battleId." AND battleCosts_ownerId=".$playerId;
    $result = $connection->Execute ($query);
    return ($result);
}

public function getBattleCosts ($playerId, $sectorId=false) {

    $connection = $this->getConnection();
    $query = "SELECT * FROM BattleCosts a INNER JOIN Battle b ON a.battleCosts_battleId=b.battle_id
WHERE a.battleCosts_ownerId=".$playerId;
    if ($sectorId)
        $query .= " AND b.battle_sectorId=".$sectorId." ORDER BY battle_isOver";
    $result = $connection->Execute ($query);
    return ($result);
}

public function deleteBattleCostsByBattleId ($battleId) {

    $connection = $this->getConnection();
    $query = "DELETE FROM BattleCosts WHERE battleCosts_battleId=".$battleId;
    $result = $connection->Execute ($query);
    return ($result);
}

}
?>