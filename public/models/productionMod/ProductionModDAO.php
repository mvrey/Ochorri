<?php
if (isset($absolute_path) && ($absolute_path))
    require_once ($path."DAO/DAO.class.php");
else
    require_once("../../models/DAO/DAO.class.php");

class ProductionModDAO extends DAO {

public function getAllProductionMods () {
    $connection = $this->getConnection();
    $query = "SELECT * FROM ProductionMod";
    $result = $connection->Execute ($query);
    return ($result->GetArray());
}

}
?>