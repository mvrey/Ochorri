<?php
if (isset($absolute_path) && ($absolute_path))
    require_once ($path."DAO/DAO.class.php");
else
    require_once("../../models/DAO/DAO.class.php");

class BattleModDAO extends DAO {

public function getAllBattleMods () {
    $connection = $this->getConnection();
    $query = "SELECT * FROM BattleMod";
    $result = $connection->Execute ($query);
    return ($result->GetArray());
}

public function getAllBattleModLinks () {
    $connection = $this->getConnection();
    $query = "SELECT * FROM BattleModLink";
    $result = $connection->Execute ($query);
    return ($result->GetArray());
}

}
?>