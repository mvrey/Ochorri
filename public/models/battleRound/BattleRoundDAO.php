<?php
if (isset($absolute_path) && ($absolute_path))
    require_once ($path."DAO/DAO.class.php");
else
    require_once("../../models/DAO/DAO.class.php");

class BattleRoundDAO extends DAO {

public function getMaxRoundsByBattleId ($battleId) {

    $connection = $this->getConnection();
    $query = "SELECT MAX(battleRound_roundId) FROM BattleRound WHERE battleRound_battleId=".$battleId;
    $result = $connection->Execute ($query);
    return ($result->fields[0]);
}

public function insertBattleRound ($battleId, $attackLog, $defendLog, $new = false) {

    $connection = $this->getConnection();
    $query = "INSERT INTO BattleRound (battleRound_battleId, battleRound_roundId, battleRound_attackLog, battleRound_defendLog)
            SELECT ".$battleId.", ";
    if ($new)
        $query .= "0, ";
    else
        $query .= "(SELECT MAX(battleRound_roundId)+1 FROM BattleRound WHERE battleRound_battleId=".$battleId."), ";
    $query .= "'".$attackLog."', '".$defendLog."'";

    $result = $connection->Execute ($query);
    return ($result);
}

public function getBattleRound ($battleId, $roundId) {

    $connection = $this->getConnection();
    $query = "SELECT * FROM BattleRound WHERE battleRound_battleId=".$battleId." AND battleRound_roundId=".$roundId;
    $result = $connection->Execute ($query);
    return ($result->fields);
}
    
}
?>