<?php
require_once("../../models/DAO/DAO.class.php");

class GlobalDAO extends DAO {

public function getAdminExists ($nick,$password="") {

    $connection = $this->getConnection();
    $query = "SELECT admin_nick FROM Admin where admin_nick='$nick'"." AND admin_password="."'$password'";
    $result = $connection->Execute ($query);

    if ($result->RecordCount())
        return true;
    else
        return false;
    }


public function truncateNonStaticData () {

    $nonStaticTables = array("Sector", "Building", "Battle", "BattleCosts", "BattleRound",
        "Division", "DivisionMovement", "Message", "TechnologyLink", "TrainingQueue");

    $connection = $this->getConnection();
    $success = true;
    foreach ($nonStaticTables as $nonStaticTable)
        {
        $query = "TRUNCATE TABLE ".$nonStaticTable.";";
        
        if (!$connection->Execute ($query))
            {
            $success = false;
            exit;
            }
        }
    
    return ($success);
}


public function restartPlayerData () {

    $connection = $this->getConnection();
    $query = "Update Player set player_lastMapHeight=5, player_lastMapOrigin='0,0', player_resources='100,1000,1000,1000,0', player_age=1, player_isLogged=0, player_lastUpdate=".$_SERVER["REQUEST_TIME"];
    $result = $connection->Execute ($query);

    return ($result);
}


public function getAllPlayerIds () {

    $connection = $this->getConnection();
    $query = "SELECT player_id FROM Player";
    $result = $connection->Execute ($query);

    return ($result->getArray());
}


}
?>